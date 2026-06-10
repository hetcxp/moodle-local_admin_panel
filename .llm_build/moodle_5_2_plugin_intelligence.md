# Moodle 5.2 Plugin Intelligence & Security Guidelines

## 1. Arquitectura y Estructura
- **Separación estricta**: Lógica en PHP (`classes/`), presentación en HTML (`templates/*.mustache`), y comportamiento en JS (`amd/src/*.js`).
- **Plantillas Mustache**: Prohibido usar `echo` con HTML en PHP. Usar clases `renderable` y `templatable` en `classes/output/`.
- **JavaScript AMD**: Todo JS debe estar modularizado (AMD). Prohibido usar etiquetas `<script>`. Llamar a JS vía `$PAGE->requires->js_call_amd()`.
- **Base de Datos**: Agnóstico. Usar la API DML de Moodle (ej. `$DB->get_record()`). No usar consultas específicas de base de datos.
- **Estructura Requerida**: 
  - `classes/`: Lógica de negocio (autoloaded).
  - `db/`: `install.xml`, `access.php` (capacidades), `services.php`.
  - `lang/en/`: Archivos de idioma obligatorios. No hardcodear texto.
  - `templates/`: Archivos `.mustache`.
  - `amd/src/`: JS fuente.
  - `tests/`: Pruebas PHPUnit y Behat.

## 2. Seguridad (Requisito Crítico de Auditoría)
- **Bloqueo de Acceso Directo**: Todo archivo PHP ejecutable debe iniciar con: `defined('MOODLE_INTERNAL') || die();`.
- **Sanitización de Inputs**: NUNCA acceder a superglobales (`$_GET`, `$_POST`). Usar estrictamente `required_param()` u `optional_param()`.
- **Inyección SQL**: Usar placeholders (`?` o `:nombre`) en consultas. Prohibido concatenar variables en strings SQL.
- **Validación de Accesos**: Rutas protegidas por `require_login()`. Comprobar permisos antes de cada acción con `has_capability()` o `require_capability()`.
- **Protección CSRF**: Toda acción de escritura/actualización debe verificar `$sesskey` con `require_sesskey()`.
- **Funciones Peligrosas**: Prohibido el uso de `eval()`, `unserialize()`, `call_user_func()` con datos no confiables.

## 3. Privacy Policy (GDPR Compliance)
- **Privacy API**: Obligatorio implementar la API de Privacidad de Moodle (`classes/privacy/provider.php`) declarando si el plugin almacena datos de usuario, o implementando `\core_privacy\local\metadata\null_provider` si no lo hace.

## 4. Pruebas y Control de Calidad
- **PHPUnit**: Obligatorio para probar la lógica en `classes/`. Cobertura de funciones críticas.
- **Behat**: Obligatorio para pruebas de aceptación y UI (flujos de usuario y JS).
- **Code Linting**: Debe pasar el CodeChecker de Moodle sin advertencias (PHPCS, ESLint, Stylelint).

## 5. Boilerplate y Rendimiento
- **Dependencias**: No forzar uso de Composer a nivel servidor por parte de los administradores. Evitar librerías externas si Moodle core tiene alternativas.
- **Rendimiento**: Evitar consultas SQL en bucles. Usar cachés de Moodle (MUC) donde aplique.
