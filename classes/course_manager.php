<?php
namespace local_admin_panel;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/lib.php');

class course_manager {

    /**
     * Mueve un conjunto de cursos a una nueva categoría.
     *
     * @param array $courseids IDs de los cursos.
     * @param int $categoryid ID de la categoría destino.
     * @return bool
     */
    public static function bulk_move(array $courseids, int $categoryid): bool {
        global $DB;
        
        if (empty($courseids)) {
            return false;
        }

        if (!\core_course_category::get($categoryid, IGNORE_MISSING)) {
            return false;
        }

        // Moodle core function expects an array of course IDs and category ID
        return move_courses($courseids, $categoryid);
    }

    /**
     * Cambia la visibilidad de un conjunto de cursos.
     *
     * @param array $courseids IDs de los cursos.
     * @param bool $visible true para mostrar, false para ocultar.
     * @return void
     */
    public static function bulk_change_visibility(array $courseids, bool $visible): void {
        foreach ($courseids as $courseid) {
            course_change_visibility($courseid, $visible);
        }
    }

    /**
     * Oculta un conjunto de cursos.
     *
     * @param array $courseids IDs de los cursos.
     * @return void
     */
    public static function bulk_hide(array $courseids): void {
        self::bulk_change_visibility($courseids, false);
    }

    /**
     * Muestra un conjunto de cursos.
     *
     * @param array $courseids IDs de los cursos.
     * @return void
     */
    public static function bulk_show(array $courseids): void {
        self::bulk_change_visibility($courseids, true);
    }

    /**
     * Elimina un conjunto de cursos.
     *
     * @param array $courseids IDs de los cursos.
     * @return void
     */
    public static function bulk_delete(array $courseids): void {
        global $DB;
        
        foreach ($courseids as $courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);
            if ($course) {
                delete_course($course, false);
            }
        }
    }
}
