<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

class cohorts_panel implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = new \stdClass();
        $cohorts = $DB->get_records('cohort', null, 'name ASC', 'id, name, idnumber', 0, 100);
        $data->cohorts = array_values((array)$cohorts);
        return $data;
    }
}
