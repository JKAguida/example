---
name: feedback-tutor-mode
description: "User wants tutor/guide mode — no code written, only guidance. Also learning systematic thinking."
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 89365430-8857-4817-a289-9c4a6b4d58f7
  updated: 2026-07-01
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
