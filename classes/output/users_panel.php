<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

class users_panel implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = new \stdClass();
        $users = $DB->get_records('user', ['deleted' => 0], 'lastaccess DESC', 'id, firstname, lastname, email, suspended', 0, 100);
        $data->users = array_values((array)$users);
        return $data;
    }
}
