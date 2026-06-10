# Project Context Map

## Tech Stack
- PHP 8.x
- Moodle 4.x/5.2 API
- JavaScript (AMD / RequireJS via Moodle)
- Mustache (Moodle Templates)
- CSS (Bootstrap / Moodle Boost theme)

## Entry Points & Key Files
- `index.php` - Controlador principal y enrutador de tabs.
- `action.php` - Controlador de acciones locales (visibilidad, borrado, creación de cursos, acciones masivas).
- `styles.css` - Estilos unificados del plugin.
- `version.php` - Metadatos de versión y dependencias.
- `.llm_build/ui_guidelines.md` - Lineamientos de UI.

## Directory Structure & Signatures
### `classes/` (Negocio y Renderizables)
- `course_manager.php`: `class course_manager` (Centraliza lógica de acciones masivas sobre cursos).

### `classes/output/` (Renderizables)
- `renderer.php`: `class renderer` (Maneja `render_courses_list`, `render_dashboard_panel`, etc).
- `courses_list.php`: `class courses_list` (Construye la lista de cursos con paginación y ordenamiento).
- `categories_list.php`: `class categories_list` (Construye la lista de categorías, columnas de estado, cantidad de cursos y acciones).
- `courses_layout.php`: `class courses_layout` (Estructura de subpaneles para gestión de cursos).
- `dashboard_panel.php`: `class dashboard_panel` (Vista principal).

### `amd/src/` (Scripts del lado del cliente)
- `course_actions.js`: Intercepta clicks en `.ap-course-action`, maneja modales `Notification.confirm`.
- `category_actions.js`: Lógica del lado del cliente para las acciones individuales y masivas de categorías, creación y edición.

### `templates/` (Vistas Mustache)
- `courses_list.mustache`: Muestra la tabla de cursos y enlaza `url_hide`, `url_show`.
- `categories_list.mustache`: Muestra la tabla de categorías, con acciones dinámicas en masa.
- `courses_layout.mustache`: Layout con subtabs.
- `main.mustache`: Estructura principal del panel.
