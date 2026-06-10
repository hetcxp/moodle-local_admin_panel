# Project Specific Rules (Agent Instructions)

Al interactuar con este workspace (`local_admin_panel`), debes cumplir incondicionalmente las siguientes reglas operativas:

1. **Actualización de Contexto (`.llm_build/context_map.md`)**:
   Cada vez que crees, elimines o renombres archivos (clases, plantillas, scripts, etc.), o alteres significativamente la estructura del proyecto, actualiza el `context_map.md` de inmediato.

2. **Ajustes de Frontend y UI (`.llm_build/ui_guidelines.md`)**:
   Consulta los `ui_guidelines.md` antes de construir interfaces. Si diseñas un nuevo patrón, componente o comportamiento de UI que no esté documentado, actualiza este archivo.

3. **Lineamientos de Moodle (`.llm_build/moodle_5_2_plugin_intelligence.md`)**:
   Considera siempre las reglas de arquitectura y buenas prácticas definidas en la inteligencia del plugin (uso de Hooks, AMD, migraciones) antes de implementar lógica de backend o frontend.

4. **Compilación de JavaScript (`.llm_build/compile_amd.py`)**:
   Cada vez que apliques ajustes o crees archivos JS en el directorio `amd/src/`, es obligatorio ejecutar el script de compilación usando:
   `python3 .llm_build/compile_amd.py`

5. **Purgado de Caché (`purge_caches.php`)**:
   Tras modificar JS (compilado), plantillas `.mustache` o clases centrales de PHP, ejecuta automáticamente el script de purgado de caché de Moodle con la ruta exacta:
   `php /Users/hectorteran/Dev/moodle-dev/public/admin/cli/purge_caches.php`
