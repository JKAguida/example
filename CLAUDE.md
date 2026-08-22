Personal PHP web app to practice hexagonal architecture + DDD.

**Features planned:**
- Login / authentication
- Training routine manager
- Personal finance tracker
- Password manager (sensitive!)

**Why:** Learning exercise. Priorities: architecture correctness first, then OWASP security.
Motivación real: migrar un proyecto laboral existente de "código espagueti" a hexagonal + DDD. Por eso Auth se quiere production-ready, no de juguete.

**How to apply:** Treat every feature as a teaching opportunity. Push the user to identify bounded contexts, ports, adapters, and domain entities themselves before confirming.

---

## Estado (2026-08-22)

**Bounded Context Auth — TERMINADO.** Identidad, roles y autorización completos, con frontend funcionando end-to-end.

```
Registro + confirmación por email + reenvío     ✓
Login (rehash silencioso, cuenta verificada)     ✓
Logout / LogoutAll (idempotente, por cookie)     ✓
Refresh con rotación de token                    ✓
Recuperación y cambio de contraseña              ✓
Roles: entidades, repos, seed y CLI de admin     ✓
Verificación de JWT + middleware de auth         ✓
RegisterUserWithRole (admin crea usuarios)       ✓
Interceptor 401 en el frontend                   ✓
```

**Siguiente bounded context:** Training (rutinas de ejercicio + progreso).

---

## Mapa de la arquitectura

```
src/
  Shared/
    Domain/           UUIDv7, DomainEventInterface, excepciones compartidas,
                      ExceptionContextInterface
    Application/Port/ Mailer, EventDispatcher, TransactionManager, CookieManager
    Infrastructure/   Http (Request, Response, HandlerExceptions, PayloadValidator,
                        CookieManager, excepciones HTTP)
                      Di (Container, ContainerConfig, SharedContainerConfig)
                      Router, Mailer, Persistence, EventDispatcher, Bootstrap,
                      Middleware (CORS, RequiresAuthenticationInterface), Exception
  Auth/
    Domain/           User, VerificationToken, RefreshToken, Role + sus VOs,
                      repositorios (interfaces), servicios de dominio, eventos,
                      excepciones de dominio
    Application/      UseCase (11), Service (CreateUserService), DTO,
                      Security (TokenGenerator/TokenValidator + excepciones)
    Infrastructure/   Controllers (11), Persistence, Security (JWT, Argon2id),
                      EventListener, Router, Middleware, Di

public/index.php      bootstrap HTTP
bin/cli.php           bootstrap CLI (seed-roles, create-admin)
database/migrations/  006 archivos SQL
frontend/             Auth/{Register,Login,...} + shared/{api,toast,validations}
```

---

## Decisiones de diseño

### Dominio
- `PasswordHashInterface` en `Auth/Domain/Service/` (invariante de dominio)
- `TokenGeneratorInterface` / `TokenValidatorInterface` en `Auth/Application/Security/` — JWT es infraestructura
- `RawPassword::create()` valida 8 chars; `fromString()` sin validación (login, no revelar política)
- `RefreshToken` entidad separada de `VerificationToken` (ciclos de vida distintos); guarda userAgent e ip
- `User::register()` recibe el `UserId` desde el Use Case (idempotencia)
- Domain events: `User` acumula en `$domainEvents[]`, el llamador hace `pullDomainEvents()`
- `VerificationToken::ensureTokenValid(TokenType)` — la entidad hace cumplir su invariante.
  **Orden: tipo antes que expiración**, porque `TOKEN_EXPIRED` es una fuga aceptada a propósito y
  solo tiene sentido revelarla si el token era del flujo correcto
- `RefreshToken` se validó inline (1 llamador, 1 chequeo): consolidar sin duplicación es ceremonia

### Aplicación
- DTOs: uno por caso de uso; sin DTO cuando recibe un solo parámetro
- **Los casos de uso no se componen entre sí.** Cada uno es dueño de su transacción y sus eventos.
  Para compartir lógica se extrae un colaborador (`CreateUserService`) que **no** abre transacción,
  **no** despacha eventos, y **devuelve** lo que creó
- `CreateUserService` asigna siempre `RoleType::User` → invariante "todo usuario tiene rol base",
  imposible de saltarse desde cualquier punto de entrada
- Transacciones vía `TransactionManagerInterface`; eventos y cookies **después** del commit
  (son efectos irreversibles)
- El repositorio reconstituye entidades; el Use Case nunca llama a `reconstitute()`

### Manejo de errores
Tabla de decisión: cada situación → status + código + mensaje al cliente. El número de salidas
distintas determinó cuántas excepciones crear.

| Situación | status | code |
|---|---|---|
| access token vencido | 401 | ACCESS_TOKEN_EXPIRED |
| token de verificación vencido | 401 | TOKEN_EXPIRED |
| token no existe / tipo incorrecto | 401 | TOKEN_INVALID (colapsados por OWASP) |
| credenciales malas | 401 | BAD_CREDENTIALS |
| cuenta sin verificar | 401 | NOT_VERIFIED |
| email ya registrado | 409 | UNIQUE_EXCEPTION |
| payload incompleto | 400 | INCOMPLETE_PAYLOAD (+ campos en `data`) |
| valor inválido del cliente | 400 | INVALID_INPUT |
| ruta no encontrada | 404 | NOT_FOUND |
| no autorizado | — | (NotAuthorizedException) |
| dato corrupto / config / inesperado | 500 | genérico + log |

Principios que sostienen esa tabla:
- **Dos audiencias**: el mensaje de la excepción se escribe para el log (detalle máximo, con
  `previous` encadenado); lo que ve el cliente lo decide el handler desde el mapa. En 5xx divergen siempre
- **Diseñar desde el catch**, no desde el throw: la pregunta no es "qué salió mal" sino "cuántas
  salidas distintas necesita el handler"
- **Clasificar por origen del dato**: `Email::create($input)` es culpa del cliente (400);
  `TokenExpiration::fromString($filaBD)` es dato corrupto (500). La convención `create()` vs
  `fromString()` ya codificaba la distinción
- **El front actúa sobre el `code`, muestra el `msg`.** Ramificar leyendo texto no es contrato
- `ExceptionContextInterface` (en `Shared/Domain/`): el handler pregunta *"¿traes contexto?"* sin
  conocer ninguna clase concreta. La **excepción** etiqueta sus datos (`['missingFields' => ...]`),
  no el handler
- Los adaptadores traducen las excepciones de sus librerías (`JWTVerify` no deja escapar Firebase)

### HTTP
- `Request` (`Shared/Infrastructure/Http/`) concentra toda lectura del entorno: método, ruta, body,
  query, ip, userAgent, cabecera Authorization. Ningún controller toca superglobales
- Mutable solo en `setUserId()` (lo puebla el middleware). Es singleton por petición vía el Container,
  por eso middleware y controller comparten el objeto
- `create()` desde superglobales / `reconstitute()` para pruebas — mismo patrón que las entidades
- Controllers = adaptadores primarios: validar payload → traducir a DTO → invocar Use Case → responder.
  Sin try/catch: las excepciones suben al handler del bootstrap
- `PayloadValidator` valida **presencia**; los VOs validan **validez**
- Rutas: `Router` orquestador + un router por bounded context. Cada ruta declara controlador y
  `middlewares` (opción "opt-in"; ver deuda)
- `RequiresAuthenticationInterface` (marcador, en Shared): el bootstrap avisa si un controller que
  exige autenticación quedó en una ruta sin middleware

### Autorización
- Autorización **gruesa** (¿hay sesión?) en el middleware; **fina** (¿este usuario puede esto?) en el
  caso de uso, porque depende de los datos
- Los tres endpoints de sesión (Logout, LogoutAll, Refresh) derivan la identidad del refresh token en
  cookie httpOnly. **Nunca de un id que mande el cliente** (así se cerró un IDOR en LogoutAll)
- `RegisterUser` fija `RoleType::User` en su propio código, sin parámetro: un endpoint público no
  debe tener ningún camino que otorgue privilegios
- `RegisterUserWithRole` sí recibe el rol, pero verifica `hasRole(Admin)` del solicitante
- Un admin lleva también el rol de usuario (redundancia en datos a cambio de comprobaciones simples)

### Contenedor de DI
- Config dividida: `ContainerConfig` orquesta; `SharedContainerConfig` (6 bindings transversales) y
  `AuthContainerConfig`. `Shared` conoce que Auth existe, no sus 40 clases
- **Auto-wiring**: lo no registrado se resuelve por Reflection. Solo se registran decisiones reales
  (puerto → adaptador) y callables con configuración
- Resolución de parámetros — invariante: **exactamente un valor por parámetro** (`newInstanceArgs`
  es posicional). Cascada: tipo simple no nativo → resolver / valor por defecto / null si es nulable /
  excepción nombrando clase y parámetro
- `getName()` sobre `ReflectionNamedType`, no el cast: `__toString()` devuelve `"?Foo"` — sirve para
  mostrar, no para buscar
- `isInstantiable()` cubre interfaz, abstracta y constructor privado de una vez
- Detección de ciclos con `$inProgress[]` limpiado en un `finally`; la clave es `$targetClass` porque
  `$implementation` puede ser un Closure
- Instancias cacheadas por petición (PHP es shared-nothing: no hay estado entre peticiones)

### Infraestructura y operación
- UTC en toda la app (`date_default_timezone_set` en ambos bootstraps) y en la BD
- `.env` con valores entrecomillados si llevan espacios (App Password de Gmail)
- Claves RSA fuera del repo (`*.pem` en `.gitignore`); la pública se **deriva** con
  `openssl pkey -in private.pem -pubout`, nunca se copia el archivo
- CLI en `bin/`, nunca en `public/` (raíz web). Contraseñas por entrada interactiva con eco apagado,
  no por argumento (historial de shell y `ps aux`)
- El CLI y los controllers invocan los mismos casos de uso: dos adaptadores primarios, un dominio
- Experimento planeado: MySQL en un equipo y PostgreSQL en otro. Nota: las migraciones **ya no son
  portables** (`ENGINE=InnoDB`, `UNIQUE KEY`) — la arquitectura aísla el dominio, no el SQL

---

## Deuda técnica conocida (decidida, no olvidada)

- **Rutas opt-in (fail-open)**: una ruta nueva sin la llave `middlewares` queda pública. Se eligió
  sabiendo el riesgo; la red parcial es `RequiresAuthenticationInterface`
- **Timing enumeration** en Login, PasswordRecovery y ResendConfirmation: el camino "no existe" es
  mucho más rápido que el que verifica contraseña o manda correo
- **Correo síncrono dentro de la petición**: un SMTP caído tumba el registro. La salida es una cola
- **`'refreshTokenJKApp'`** literal en 7 lugares
- **Logs de depuración**: `[DEBUG_ORIGIN]` por request, 2 `console.log` en `api.js` (uno imprime el
  access token). Se dejan a propósito durante el desarrollo
- **Fugas aceptadas a propósito** (UX > OWASP estricto): `TOKEN_EXPIRED` y "cuenta no confirmada"
  revelan que el token/la cuenta existen
