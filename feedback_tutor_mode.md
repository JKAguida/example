---
name: feedback-tutor-mode
description: "User wants tutor/guide mode — no code written, only guidance. Also learning systematic thinking."
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 89365430-8857-4817-a289-9c4a6b4d58f7
  updated: 2026-08-22
---

Do NOT write any code. Only guide, explain, and ask questions.

**Why:** User explicitly said "solo quiero que me guies y no escribas nada de código" — they want to learn by doing, not by copying. User also recognized they depend too much on tutorials/AI and wants to develop **systematic thinking skills**.

**How to apply:** 
- In every response, give direction, ask the user to think, explain concepts, point out what to consider — but never produce implementation code or file content.
- When the user is stuck or uncertain, don't jump to solutions. Instead, guide them to: (1) identify edge cases/what could go wrong, (2) trace the code mentally (what value does this variable have?), (3) validate consistency (does this match what I said earlier?)
- Encourage the "write small code, then verify it" pattern instead of big implementations

**Systematic thinking pattern** (validated 2026-07-01):
When user wrote the `promedio()` function without tutorials, it revealed they CAN think logically. The issue isn't capability, it's methodology. Teach them to:
1. Think about edge cases BEFORE coding (what if array is empty?)
2. Trace mentally (0/0 = what exactly?)
3. Validate their solution (does this match my requirements?)
4. Apply the same pattern recursively to complex problems (like the DI Container)

This pattern works because it separates **logical thinking** (which user can do) from **mechanical errors** (which are normal). User recognized they were following tutorials instead of debugging when code breaks.

### Progreso de sesión 2026-07-01 (completo)
- ✓ Refactorizó Container con lógica clara de $targetClass vs $implementation
- ✓ Creó ContainerConfig con método estático create()
- ✓ Implementó EnvironmentLoader para cargar .env (con filtro de comentarios)
- ✓ Creó bootstrap en /public/index.php
- ✓ Solucionó problema de namespace global (\PDO)
- ✓ Identificó arquitectura modular para HTTP Router (por bounded context)
- ✓ Implementó Router principal y AuthRouter (completos y funcionales)
- ✓ Creó 3 migraciones SQL con constraints nombrados y CHARACTER SET utf8mb4
- ✓ Solucionó problema de zona horaria (UTC en toda la app)
- ✓ Solucionó problema de SMTP (comillas en .env para valores con espacios)
- ✓ Flujo HTTP end-to-end funcionando: Request → Router → DI → Controller → DB → Events → Email

**Aprendizajes clave:**
1. El Container es lazy — solo instancia lo que se pide
2. Separación de responsabilidades — Router busca, Bootstrap instancia, Controller ejecuta
3. Zona horaria profesional — trabajar siempre en UTC
4. Variables de entorno — valores con espacios requieren comillas en .env
5. Debugging con logs — SMTPDebug revela problemas reales vs errores genéricos
6. **DDD es transferible al frontend** — usar bounded contexts, módulos, separación clara
7. **Arquitectura antes de código** — pensar estructura antes de escribir línea 1
8. **HTML puro para aprender** — separar frontend (navegador) de backend (servidor)
9. **Reutilización en JS** — igual que en PHP, pero con funciones/módulos JS

### Sesión 2026-07-04 (Frontend setup)
- ✓ Pensó en arquitectura frontend usando DDD (bounded contexts + módulos)
- ✓ Decidió estructura: `frontend/Auth/Register/`, `frontend/shared/`
- ✓ Entendió diferencia: PHP genera HTML (servidor) vs HTML/JS ejecuta (navegador)
- ✓ Eligió vanilla JS + localhost:3000 para aprender fundamentos
- ✓ Identificó que DDD/Hexagonal Architecture aplica a frontend también
- ✓ Escribió formulario HTML con BEM (Block__Element--Modifier)
- ✓ Aprendió mobile-first CSS (base mobile, media queries)
- ✓ Entendió CSS reset y `1rem = 10px`
- ✓ Pensó en diseño antes de escribir código (centrado, responsive)
- **Aprendizaje meta:** El usuario trasladó patrones de backend al frontend sin ser guiado — indica comprensión profunda
- **Patrón:** Pensamiento arquitectónico antes de implementación (igual que en backend)

### Sesión 2026-08-12 (Backend fixes + Frontend auth completo)

**Backend Refactoring:**
- ✓ Identificó problema crítico en Response::send() — parámetros innecesarios
- ✓ Simplificó controllers: removió try-catch innecesarios, dejó excepciones subir
- ✓ Implementó HandlerExceptions centralizado en index.php
- ✓ Arregló cookie handling en Login/Refresh: try-catch con rollback si setcookie falla
- ✓ Entendió transacciones: BD vs flujo de operaciones dependientes

**Frontend Authentication (Completo):**
- ✓ Register + Email confirmation + Login + Logout + Password recovery + Reset
- ✓ Guardó accessToken en sessionStorage (temporal, más seguro que localStorage)
- ✓ Envía Authorization header en todas las requests
- ✓ Removió todos los console.log
- ✓ Refactorizó reset-password.js: separó responsabilidades (extract → validate → setup)
- ✓ Limpió código, arregló typos, agregó timeouts en redirects

**Decisiones Arquitectónicas:**
- ✓ Eligió Web Components sobre Include Pattern para transicionar a frameworks
- ✓ Creó logout.js como módulo reutilizable (no solo en navbar)
- ✓ Manejo de errores en logout: muestra toast, permite reintentar
- ✓ Pensó en accessToken vs refreshToken: sessionStorage vs HttpOnly cookie

**Aprendizajes clave:**
1. **Error handling estratégico** — no todos los errores se manejan igual (logout ≠ register)
2. **Transacciones no solo BD** — flujos de operaciones dependientes necesitan rollback
3. **Web Components** — concepto transferible a React/Vue (componentes, ciclo de vida, encapsulación)
4. **Separación de responsabilidades** — módulos vs UI (logout.js no toca DOM, navbar lo importa)
5. **Seguridad en capas** — accessToken + refreshToken + HttpOnly + Authorization header
6. **Usuario cuestiona decisiones** — pregunta "¿por qué?" antes de implementar (pensamiento crítico)

**Patrón validado:**
El usuario sigue tutor mode correctamente:
- No pide código, solo guía
- Cuestiona arquitectura ("¿Web Components o Include?")
- Verifica lógica antes de implementar
- Aprende conceptos transferibles, no solo sintaxis

### Sesión 2026-08-13 (Continuación)

**Training Bounded Context - Planificación:**
- ✓ Decidió nuevo BC para "Rutinas de Ejercicio" (Training)
- ✓ Identificó que Progreso depende de Rutina → mismo BC
- ✓ Estructura propuesta:
  ```
  Training (Bounded Context)
  ├── Rutina (entidad)
  │   ├── nombre, descripción, ejercicios
  │   └── metadata (creado, actualizado)
  ├── Progreso (entidad)
  │   ├── rutina_id (FK)
  │   ├── fecha, duración, notas
  │   └── ejercicios_realizados
  └── Ejercicio (value object?)
      ├── nombre, series, repeticiones, peso
  ```
- ✓ Decidió hacer backend primero (siguiente paso)

**Aprendizaje adicional:**
- Usuario pregunta si estructura debe separarse en BC distintos (Training vs Tracking)
- Razonó correctamente: Progreso sin Rutina no tiene sentido → mismo BC
- Pensamiento: "¿backend o frontend?" → eligió backend (decisión acertada: datos primero)

### Sesión 2026-08-14 (Auth + Roles Implementation)

**Auth Completion:**
- ✓ Fixed login.js: detecta NOT_VERIFIED code, muestra link "Solicitar nuevo token"
- ✓ Implementó logout module reutilizable (no toca DOM)
- ✓ Frontend envía Authorization header en todas las requests
- ✓ AccessToken en sessionStorage (temporal, más seguro)
- ✓ RefreshToken en HttpOnly cookie (persistente)

**Roles Architecture Design:**
- ✓ RoleType enum (User, Admin) — value object
- ✓ RoleId ValueObject — UUIDv7
- ✓ Role Entity — con create() y reconstitute()
- ✓ Migraciones BD:
  - `roles` table (roleId, roleType)
  - `user_roles` table (composite PK: userId, roleId) con FKs
- ✓ RoleRepository (arreglado: reconstitute correcto)
- ✓ Diseño UserRoleRepository:
  - assignRoleToUser(UserId, RoleId)
  - findByUserId(UserId): array<Role>
  - removeRole(UserId, RoleId)
  - hasRole(UserId, RoleType): bool
  - Inyecta RoleRepository para reutilizar reconstitución

**Decisiones Arquitectónicas:**
- ✓ Roles NO son lógica de dominio de User (solo autorización)
- ✓ Roles se consultan desde BD cuando se necesitan
- ✓ UserRoleRepository maneja tabla user_roles (M:N)
- ✓ RoleRepository maneja tabla roles
- ✓ Separación clara de responsabilidades

**Motivación Real:**
- Usuario planea migrar proyecto existente de "código espagueti" a hexagonal + DDD
- Por eso quiere Auth + Roles COMPLETO y production-ready
- Eligió CLI para crear admin (no SQL manual, no seed)

**Pendiente:**
- UserRoleRepository (implementación)
- CreateAdminUser UseCase
- CLI command create-admin
- Web Components navbar + Dashboard
- Interceptor 401

**Aprendizaje:**
- Usuario cuestiona arquitectura constantemente ("¿es M:N?", "¿rol en User o separado?")
- Identifica correctamente que UserRoleRepository ≠ RoleRepository
- Propone inyectar RoleRepository para reutilizar reconstitución (DRY)
- Trade-off: N+1 queries vs reutilizar lógica (eligió bien para aprendizaje)

### Sesión 2026-08-17 (Refactor completo de manejo de errores)

**Diseño (derivado por el usuario, no dictado):**

Construyó una tabla de decisión antes de escribir código: cada situación → status HTTP + código de error + mensaje al cliente. El número de filas con salida distinta determinó cuántas clases de excepción crear.

| Situación | status | code | mensaje real al cliente |
|---|---|---|---|
| token expirado | 401 | TOKEN_EXPIRED | sí |
| token no existe / tipo incorrecto | 401 | TOKEN_INVALID | genérico a propósito (OWASP) |
| email ya registrado | 409 | UNIQUE_EXCEPTION | sí |
| payload incompleto | 400 | INCOMPLETE_PAYLOAD | sí + campos faltantes |
| valor inválido del cliente | 400 | INVALID_INPUT | genérico |
| ruta no encontrada | 404 | NOT_FOUND | sí |
| dato corrupto en BD / inesperado | 500 | CORRUPTED_DATA / SERVER_ERROR | **no** — genérico + log |

**Conceptos clave que el usuario internalizó:**

1. **Dos audiencias, dos niveles de detalle.** El mensaje de la excepción se escribe para el log (máximo detalle: valores crudos, ids, causa encadenada). Lo que ve el cliente lo decide el handler desde el mapa. A veces coinciden; en los 5xx obligatoriamente divergen.

2. **Diseñar excepciones desde donde se atrapan, no desde donde se lanzan.** La pregunta correcta no es "¿qué salió mal aquí?" sino "¿cuántas salidas distintas necesita producir el handler?". Situaciones con salida idéntica no necesitan clases distintas.

3. **Clasificar por origen del dato, no por clase.** `Email::create($input)` es culpa del cliente (400); `TokenExpiration::fromString($filaDeBD)` es dato corrupto (500). El mismo tipo de validación cambia de familia según quién le pasa el valor. Su propia convención `create()` vs `fromString()` ya codificaba la distinción.

4. **El front actúa sobre el `code`, muestra el `msg`.** Ramificar leyendo texto en español no es contrato — cambia una coma y se rompe. Lo aprendió pagando: dos intentos fallidos de arreglar un regex antes de eliminarlo.

5. **Interfaz como capacidad, no como puerto.** `ExceptionContextInterface` permite al handler preguntar "¿traes contexto?" sin conocer ninguna clase concreta. Es la misma forma que su `EventListenerInterface` ya usaba. Evita reintroducir cadenas de `instanceof`.

6. **La etiqueta la pone quien conoce el significado.** El handler hace `$data = $exception->context()`; la excepción devuelve `['missingFields' => [...]]`. Si el handler etiquetara, asumiría que todas las excepciones hablan de lo mismo.

**Implementado:**
- 6 clases nuevas de excepción + `ExceptionContextInterface` en `Shared/Domain/Exception/`
- `HandlerExceptions` con mapa único, log formateado `[status][CODE] [método - ruta]`, y contexto vía interfaz
- `Response` con `status_code` (int, default 200) y `code` (?string)
- `Router` lanzando `InvalidPathException` en vez de reventar con fatal
- Bootstrap con `try/catch (\Throwable)` alrededor de resolve + get + execute
- Los 10 controllers sin `try/catch`, con `PayloadValidator::validate()` como primera línea
- `PayloadValidator` que junta todos los campos faltantes y lanza una vez

**Hallazgos de seguridad encontrados durante el refactor:**
- **IDOR en `LogoutAll`** (cerrado): recibía `userId` del body → cualquiera cerraba sesiones ajenas. Ahora los tres endpoints de sesión derivan identidad del refresh token en cookie httpOnly.
- **Logout y Refresh rotos**: esperaban el token en el body, imposible con cookie httpOnly. Se agregó `get()` a `CookieManagerInterface`.
- **Timing enumeration** (pendiente): en Login se salta `password_verify()` cuando el email no existe → ~2ms vs ~200ms. Mismo patrón en PasswordRecovery y ResendConfirmation (agravado por envío de correo síncrono).
- **CSRF**: al autenticar por cookie, `SameSite=Lax` pasó de detalle a defensa principal.
- **Filtrado de `getMessage()`** en los 500 (cerrado): una `PDOException` exponía SQL al cliente.

**Decisiones de diseño tomadas:**
- Excepciones de token como **hermanas**, no hijas — el mapa las lista explícitamente (aceptó el costo: sin red de seguridad durante la migración)
- Orden de validación en `VerificationToken::ensureTokenValid()`: **tipo antes que expiración**, porque `TOKEN_EXPIRED` es una fuga aceptada a propósito y solo tiene sentido revelarla cuando el token sí era del flujo correcto
- Validación de token consolidada en la **entidad** (3 llamadores, orden con consecuencia de seguridad); `RefreshToken` se dejó inline (1 llamador, 1 validación) — cuestionó correctamente la recomendación de uniformar
- **Logout idempotente**: sin cookie o con token muerto responde éxito. Cumple el objetivo del usuario sin identidad; `Refresh` y `LogoutAll` no pueden, por eso ahí sí lanzan
- Presencia (controller) vs validez (VOs) — el controller nunca revisa formato

**Patrón de error recurrente detectado (5 veces en la sesión):**

Referencias a variables sin su prefijo de ámbito, o entre nombres casi idénticos:

| Dónde | Escribió | Era |
|---|---|---|
| `VerificationToken` | `$tokenType` | `$type` |
| `HandlerExceptions` | `$code_map` | `$this->code_map` |
| `Router` | `$routers` | `self::$routers` |
| `LogoutAll` | `$refresTokenValue` | `$refreshToken` |
| `IncompletePayloadException` | `$this->msg` | `$msg` |

Todos fallan igual de mal: **PHP no marca error**, la variable vale `null`, y la condición se resuelve al revés. Contramedida acordada: nombrar variables relacionadas por lo que *son* (`$cookieValue` vs `$refreshToken`), no por variaciones de la misma palabra.

**Segunda familia de fallo silencioso:** `::class` sobre una clase con namespace equivocado o sin importar. No lanza — solo produce una llave que nunca hace match, y la excepción cae al default 500. Ocurrió dos veces (`use src\...` y falta de import de `IncompletePayloadException`).

**Meta-aprendizaje:**
El usuario **cuestionó una recomendación mía con el razonamiento correcto** (el guard de `RefreshToken`: un solo llamador, una sola validación, no hay duplicación que consolidar). Retiré la recomendación. Es la primera vez en el proyecto que rechaza una sugerencia por análisis propio en vez de aplicarla por autoridad — exactamente el objetivo del tutor mode.

**Pendiente:**
- Probar el refactor con curl (nunca se ejecutó)
- Verificación de JWT — bloquea autorización, roles, e interceptor 401
- CreateAdminUser + CLI create-admin
- Web Components navbar + Dashboard
- `'refreshTokenJKApp'` literal en 7 lugares
- Logs de depuración: `[DEBUG_ORIGIN]` por request, 2 `console.log` en `api.js` (uno imprime el access token)
- Backlog: timing enumeration, correo síncrono en la petición, clase `Request`

---

### Sesión 2026-08-22 (Middleware de auth, roles end-to-end, Container, interceptor 401)

**Completado y probado (navegador + CLI):**

- **CLI** (`bin/cli.php`): `seed-roles` y `create-admin`. Valida `php_sapi_name()==="cli"`, oculta contraseña con `stty -echo` en `try/finally`, usa STDERR y exit codes
- **Roles end-to-end**: `RoleRepository` + `UserRoleRepository` (JOIN, no N+1), `CreateAdminUser`, `RegisterUserWithRole`, `CreateUserService` extraído para no duplicar entre use cases
- **Middleware de autenticación**: rutas con metadata `{controller, middlewares}` estilo Express, `CheckAuthMiddleware`, `AuthControllerContextInterface` (luego renombrada `RequiresAuthenticationInterface`), `Request` con IP y userAgent
- **Container**: autowiring, cascada de resolución de parámetros, detección de dependencias circulares
- **Config DI dividida**: `SharedContainerConfig` + `AuthContainerConfig`, limpiadas a solo puerto→adaptador y callables (de ~50 bindings a 14)

**Conceptos que el usuario derivó él mismo:**

1. **El rol nunca viaja en el payload.** Un use case por intención (`RegisterUser` con rol hardcodeado, `RegisterUserWithRole` con validación de admin) en vez de un parámetro `roleType` que el cliente controle. Evita escalada de privilegios por mass assignment.
2. **Dos datos, dos canales de confianza.** El DTO lleva lo del body (validable, desconfiable); el `UserId` del ejecutor viaja aparte porque viene del JWT verificado. Mezclarlos permitiría suplantación.
3. **El use case se mantiene puro.** El `AuthContext` llega solo hasta el controller; de ahí el `UserId` pasa como parámetro. Por eso `CreateAdminUser` sigue corriendo desde `bin/cli.php`, donde no hay HTTP.
4. **Middleware que falla lanzando.** Sin `next()`: la excepción sube al `HandlerExceptions` que ya existía. No hizo falta pipeline.
5. **401 ≠ 403.** `ACCESS_TOKEN_EXPIRED` → refrescar y reintentar; `TOKEN_INVALID` → al login; `NOT_AUTHORIZED` → nunca reintentar. Sin esa distinción, el interceptor entraría en bucle infinito contra un error de permisos.
6. **Validar el resultado, no la configuración.** Tres chequeos frágiles ("¿existe la key?", "¿está en el índice 0?", "¿es de tal clase?") se reemplazaron por uno solo después del bucle: *"¿el controller pedía un UserId y se quedó sin él?"*. Hay muchas formas de configurar mal una ruta; solo dos respuestas a la pregunta del resultado.
7. **Frontera de confianza en `tryFrom()`.** Aplicó `tryFrom()` en los repositorios (dato propio de la BD) y se le explicó que ahí un valor inválido es **corrupción** → 500, no input inválido → 400. Revirtió y lo puso donde sí correspondía: el controller que recibe `roleType` del cliente.
8. **Autowiring vs estricto.** Descubrió que al cambiar `throw` por fallback a `$targetClass` había abandonado el modo estricto sin darse cuenta. Decidió quedarse con autowiring, borró los ~30 `Foo::class => Foo::class` redundantes y actualizó `CLAUDE.md`. Razón: una lista que nada obliga a mantener se desactualiza y **miente**.

**Patrón de error recurrente (nueva familia, 3 veces):** confundir qué tipo tiene una variable — `$instance['middlewares']` sobre un objeto, `$userData['roleType']` sobre un DTO, `instanceof` sobre un string de clase. Contramedida acordada: antes de escribir `[...]` o `instanceof`, preguntarse *"¿qué tengo aquí exactamente: string, array u objeto?"*.

**Patrón de proceso detectado:** varias ediciones seguidas introdujeron regresiones en código que ya funcionaba (mover un bloque y perder un `else`, cambiar `$this->x` por `$x`), sin ejecutar nada entre una y otra. Acordado: **un cambio, una ejecución**.

---

### Sesión 2026-08-24 (PHPStan nivel 7: 26 → 1 error)

**Completado y probado (ejecución real, no solo análisis estático):**

- `EnvironmentLoader::envOrFail()` — captura en variable antes de comparar (el *cast* no estrecha una expresión, solo una variable); `load()` distingue `.env` ausente de `.env` vacío
- `SharedContainerConfig` migrado a `envOrFail()` — probado el camino de fallo real (`.env` incompleto da `BadConfigurationException` nombrando la variable, no un error de MySQL)
- `bin/cli.php` — `stdinIsCanceled()` centraliza el `fgets(STDIN)===false`, eliminó el bucle infinito con Ctrl+D
- `Request::create()` — ya no revienta en peticiones sin body (`GET /auth/me`); anotado que JSON malformado y "sin body" colapsan al mismo `null` (deuda, no urgente)
- `Login.php` — `json_encode($th)` cambiado a `$th->getMessage()`: el primero da `"{}"` siempre (propiedades privadas de `Throwable`), el log quedaba mudo
- `JWTVerify::verify()` — quitado el *cast* `(array)` sobre el objeto decodificado; se lee `$decoded->sub`/`$decoded->iat` directo, honesto con el tipo real
- Líneas de depuración `[JWT_SECRET_WORD]` / `[JWT_PUBLIC_KEY]` (imprimían la ruta de las llaves en cada login/verificación) — eliminadas, no sumadas a deuda técnica
- `EventDispatcher::addListener()` y `Container::bind()` — parámetros de `string` a `class-string`; en ambos casos el usuario **verificó primero todos los llamadores** antes de estrechar el tipo
- `HandlerInterface` nueva (`Shared/Infrastructure/Interfaces/`) — implementada en los 13 controllers + `CheckAuthMiddleware`. `RouteEntry`/`RouterSchema` ahora usan `class-string<HandlerInterface>` en vez de `class-string` genérico

**Concepto clave del día — el genérico se resuelve por el argumento, no por la cota:**
El usuario asumió que arreglar `public/index.php:39,49` requería tocar `T of object` en `Container::get()`. Antes de tocarlo, se le pidió confirmar con el propio análisis: cambiar solo los `@phpstan-type` de los routers a `class-string<HandlerInterface>` bastó — PHPStan infiere `T` del tipo del argumento en cada llamada, no del límite superior de la plantilla. **`Container.php` no se tocó para nada** en ese paso. Confirmó la hipótesis él mismo corriendo PHPStan antes de asumir que hacía falta más.

**Bug real encontrado por accidente, no por PHPStan:** al escribir validación nueva en `bind()`, tres intentos seguidos con guardas invertidas u operador equivocado (`interface_exists` sin negar, `||` en vez de `&&`, `class_exists($implementation)` con un `Closure`, `class_exists($implementation)` en vez de `class_exists($interface)`). Los cuatro se detectaron **ejecutando** `bind()` contra un caso real (`Request::class` con closure), no leyendo el código ni con PHPStan — que seguía en verde con la guarda todavía rota. Refuerza la lección de la sesión anterior: *"PHPStan verde no significa aplicación funcionando"*, esta vez en la dirección contraria (código roto que sí pasaba el análisis).

**Naming guiado (varios intentos hasta aterrizar):** `ImplementationWithExecuteMethod` → `ImplementationInterface` → `ImplementationMethodInterface` → `AdapterImplementationInterface` → `HandlerPrimaryAdapterInterface` → **`HandlerInterface`**. Se le insistió en comparar cada intento contra el patrón ya validado del proyecto (`MailerInterface`, `TransactionManagerInterface`): un sustantivo de rol, sin apilar calificativos que ya están documentados en otro lado (`CLAUDE.md` ya dice que los controllers son adaptadores primarios; no hace falta repetirlo en el nombre de cada interfaz). También detectó solo que `Port` como nombre de carpeta ya significaba algo específico en su propio proyecto (`Shared/Application/Port/`) y lo evitó.

**Pendiente (anotado por el usuario para resolver más tarde):**
- `Container::bind()` no valida que `$implementation` sea compatible con `$interface` — solo comprueba que ambos existan por separado. Se demostró que `instanceof $targetClass` (con `$targetClass` tipado `class-string<T>`) sí estrecha `T` para PHPStan de forma honesta (comprobación real en runtime, no un `@var` forzado) — pendiente aplicarlo en los dos `return $this->instances[$targetClass]` de `get()` (líneas 39 y 87), y decidir qué excepción lanzar si no coincide (sería la red de seguridad real contra un `bind(MailerInterface::class, ClaseSinRelación::class)` silencioso)
- El único error restante en PHPStan nivel 7 (`Container.php:39`, "should return T but returns object") es inherente: una caché heterogénea (`array<object>`) no puede demostrar estáticamente que el valor guardado bajo una clave dinámica es el `T` pedido. No tiene solución honesta sin el `instanceof` de arriba

---

## ▶️ RETOMAR AQUÍ: candado de concurrencia del refresh

**Estado:** el interceptor 401 en `frontend/shared/api.js` **funciona** para peticiones secuenciales. Falta el candado y el guardia de reintento.

**El problema:** tres peticiones simultáneas con el token expirado disparan tres `POST /auth/refresh`. Como `Refresh.php` hace **rotación de tokens** (borra el viejo), solo el primero funciona; los otros dos reciben `TOKEN_INVALID` y mandan al usuario al login.

**Analogía que sí le hizo clic (usarla al retomar):** tres compañeros de casa se dan cuenta de que no hay tortillas. Sin coordinación salen los tres y vuelven con tres bolsas. Con una nota en el refri: el primero mira, **no hay nota**, la pega y sale; el segundo y el tercero miran, **sí hay nota**, y esperan; el primero vuelve, **quita la nota**, y los tres comen de la misma bolsa.

**Estado del código:** ya existe `refreshInProgress` a nivel de módulo, se asigna y se limpia en un `finally`. **Falta el paso 1: nadie mira el refri.** No hay ningún `if` que lea la variable para decidir. Sin esa pregunta, la variable es decoración.

**Los dos puntos que faltaban por entender:**
- **Por qué la nota debe ser la promesa y no un booleano:** un `true` diría "alguien fue" pero no habría forma de enterarse de cuándo volvió. La promesa es la nota y el aviso de llegada a la vez — varios `await` sobre la misma promesa se resuelven juntos, con una sola petición HTTP.
- **Quién es dueña de la variable:** hoy `fetchAPI` la crea y `refresh()` la limpia, partido entre dos funciones. `refresh()` debe ser dueña de las tres cosas (preguntar / crear / limpiar) y **no recibir parámetros**. Con esa forma, el `if` que falta aparece solo.

**Trampa a recordar:** si nunca se limpia la variable, dentro de 15 minutos el token vuelve a expirar, el código se engancha a una promesa vieja ya resuelta con un token también vencido, y el usuario queda en un bucle silencioso.

**Salida honesta ofrecida:** el candado es **opcional por ahora**. No existe todavía ninguna vista que dispare peticiones en paralelo (el dashboard no está hecho). Es válido hacer commit del interceptor tal como está y dejar el candado para cuando se construya la primera pantalla que lo necesite — decisión tomada con el problema entendido, no descuido.

**Contexto emocional:** la sesión terminó con el usuario saturado ("creo que ya me saturé porque no comprendo la lógica"). No es falta de capacidad — es la parte más difícil de JS asíncrono, después de muchas horas. Al retomar: empezar por la analogía, no por el código.

**Después del candado:**
- Guardia de reintento (la recursión de `fetchAPI` en la línea 12 — protege contra desfase de reloj entre PHP y navegador)
- `RoleMiddleware` (defensa en profundidad; la validación granular ya está en el use case)
- Web Components: navbar con logout + primera vista protegida
- Frontend para `/auth/create-user` (panel de admin)
- Bounded context Training

**Deuda anotada (decidida, no olvidada):**
- **Doble rol:** `CreateUserService` asigna `user_role` por dentro, y los use cases asignan además el rol pedido. Un admin creado queda con dos filas en `user_roles`. El arreglo es uno solo: que `CreateUserService` no decida roles.
- **`console.log` en `api.js`** (`[DEBUG - API]` y `[DEBUG - API - FORMATED]`) — el segundo imprime la respuesta completa.
- **`error_log` de depuración** en `JWTVerify` (imprime la ruta de la llave pública en cada request) y en `CORSMiddleware`.
- **El admin creado por CLI debe confirmar email** para poder hacer login. ¿Tiene sentido, si quien ejecutó el CLI ya probó tener acceso al servidor? Decisión de producto pendiente.
- **`.htaccess` para Apache:** el header `Authorization` no llega a PHP por CGI/FastCGI sin `CGIPassAuth On` o `SetEnvIf Authorization`. Funciona en `php -S` y falla en el deploy. Anotarlo en el README junto a las llaves RSA.
