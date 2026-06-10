# UI Guidelines - Local Admin Panel

Basado en el diseño del panel de cursos (`courses_list.mustache`), estos son los lineamientos para replicar la interfaz en otras vistas.

## Estructura General
Todo contenedor de vista debe envolverse en un `.ap-card` o `.ap-card-title`.

## Barras de Filtros y Acciones
- Contenedor: `<div class="ap-filter-bar" style="margin-bottom: 20px;">`
- Formulario: Usar `display: flex; gap: 10px; align-items: center; flex-wrap: wrap; width: 100%;`
- Inputs/Selects: Usar clases Bootstrap `.form-control` y `.form-select`.
- Botones de acción principal (Ej: Crear): Botón a la derecha (`margin-left: auto;`) con estilos verdes `#198754` (`background-color: #198754; color: #ffffff !important; border-color: #198754; font-weight: 600;`).

## Tablas y Ordenamiento (Sorting)
- Contenedor: `<div class="ap-table-wrapper">`
- Estilo de tabla: `<table class="ap-table table table-striped table-hover">`
- Cabeceras (Ordenamiento):
  - Usar enlaces sin estilo (`color: inherit; text-decoration: none; display: flex; align-items: center; justify-content: space-between; gap: 5px;`).
  - Lógica: En el backend (PHP) se manejan los parámetros `sort` (nombre de la columna) y `direction` (`asc` o `desc`). Al hacer clic en el encabezado, se recarga la página invirtiendo la dirección actual si coincide la columna, o usando `asc` por defecto.
  - Iconos: Añadir `<i class="fa fa-sort"></i>` para columnas inútiles, o `<i class="fa fa-sort-asc"></i>` / `<i class="fa fa-sort-desc"></i>` para reflejar el estado actual.

## Estados (Badges)
- Visible: `<span class="badge bg-success" style="padding: 3px 8px; border-radius: 10px; font-size: 0.85em;">Visible</span>`
- Oculto: `<span class="badge bg-secondary" style="padding: 3px 8px; border-radius: 10px; font-size: 0.85em;">Oculto</span>`

## Botones de Acción (Fila)
- Estilo general: `btn btn-sm` sin bordes y fondo transparente (`border: none; background: transparent; padding: 2px 6px;`).
- Colores para cada acción:
  - Editar/Visualizar: `.text-secondary`
  - Mostrar: `.text-success` (Icono `t/show, core`)
  - Ocultar: `.text-warning` (Icono `t/hide, core`)
  - Eliminar: `.text-danger` (Icono `t/delete, core`)
- Preferir iconos `{{#pix}}...{{/pix}}` de Moodle core, o FontAwesome si no existe uno adecuado.

## Acciones Masivas (Bulk Actions)
- Interfaz (HTML):
  - Casilla global: `<input type="checkbox" class="form-check-input ap-bulk-select-all">` en el `<thead>`.
  - Casilla por fila: `<input type="checkbox" class="form-check-input ap-bulk-checkbox" value="{{id}}">` en el `<tbody>`.
  - Barra de acciones: `<div class="ap-bulk-actions mt-3 p-3 bg-light border rounded" style="display: none;">` que agrupa los botones masivos.
  - Botones estilo outline (`btn-outline-warning`, `btn-outline-success`, `btn-outline-danger`).
- Lógica (AMD/JS):
  - Escuchar eventos `change` en todos los checkboxes. Si hay 1 o más seleccionados, se muestra la barra `.ap-bulk-actions`. Si no hay ninguno, se oculta.
  - El checkbox `.ap-bulk-select-all` marca o desmarca todos los demás.
  - Al enviar el formulario masivo, se recolectan los `value` de las casillas chequeadas para añadirlos dinámicamente o validar antes de hacer el submit.

## Modales de Confirmación (Core AMD)
- Para confirmaciones destructivas (Borrar, Ocultar), **priorizar la API nativa de Moodle**: `core/notification`.
- Lógica (AMD/JS):
  - Interceptar el clic en el botón (`e.preventDefault()`).
  - Llamar a `Notification.confirm(title, question, saveLabel, noLabel, function() { ... })`.
  - Dentro de la función callback (`done`), realizar el envío del formulario (`form.submit()`) o redirección correspondiente.
- Para modales personalizados (Creación/Edición compleja):
  - Usar la estructura estándar Bootstrap 5: `.modal.fade` > `.modal-dialog` > `.modal-content` > `.modal-header`, `.modal-body`, `.modal-footer`.
  - El botón de cierre utiliza las clases `.close.btn-close` y atributos `data-bs-dismiss="modal" data-dismiss="modal"`.
