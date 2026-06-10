<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class courses_layout implements renderable, templatable {
    protected $subtab;
    protected $content;

    public function __construct($subtab, $content) {
        $this->subtab = $subtab;
        $this->content = $content;
    }

    public function export_for_template(renderer_base $output) {
        $data = new \stdClass();
        $data->subtab_courses_active = ($this->subtab === 'courses');
        $data->subtab_categories_active = ($this->subtab === 'categories');
        $data->url_courses_sub_courses = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'courses', 'subtab' => 'courses']))->out(false);
        $data->url_courses_sub_categories = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'courses', 'subtab' => 'categories']))->out(false);
        $data->content = $this->content;
        return $data;
    }
}
