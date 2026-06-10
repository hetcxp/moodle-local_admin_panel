<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class main implements renderable, templatable {
    protected $tab;
    protected $content;

    public function __construct($tab, $content) {
        $this->tab = $tab;
        $this->content = $content;
    }

    public function export_for_template(renderer_base $output) {
        $data = new \stdClass();
        $data->tab_dashboard_active = ($this->tab === 'dashboard');
        $data->tab_courses_active = ($this->tab === 'courses');
        $data->tab_users_active = ($this->tab === 'users');
        $data->tab_cohorts_active = ($this->tab === 'cohorts');

        $data->url_dashboard = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'dashboard']))->out(false);
        $data->url_courses = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'courses']))->out(false);
        $data->url_users = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'users']))->out(false);
        $data->url_cohorts = (new moodle_url('/local/admin_panel/index.php', ['tab' => 'cohorts']))->out(false);

        $data->content = $this->content;

        return $data;
    }
}
