<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

class dashboard_panel implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = new \stdClass();
        $data->courses_total = $DB->count_records('course');
        $data->courses_active = $DB->count_records('course', ['visible' => 1]);
        $data->courses_inactive = $DB->count_records('course', ['visible' => 0]);

        $data->users_total = $DB->count_records('user', ['deleted' => 0]);
        $data->users_active = $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]);
        $data->users_inactive = $DB->count_records('user', ['deleted' => 0, 'suspended' => 1]);

        $data->cohorts_total = $DB->count_records('cohort');
        $data->cohorts_users_total = $DB->count_records('cohort_members');
        
        return $data;
    }
}
