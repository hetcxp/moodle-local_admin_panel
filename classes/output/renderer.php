<?php
namespace local_admin_panel\output;

use plugin_renderer_base;

defined('MOODLE_INTERNAL') || die();

class renderer extends plugin_renderer_base {
    
    public function render_main(main $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/main', $data);
    }

    public function render_dashboard_panel(dashboard_panel $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/dashboard', $data);
    }

    public function render_courses_layout(courses_layout $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/courses_layout', $data);
    }

    public function render_courses_list(courses_list $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/courses_list', $data);
    }

    public function render_categories_list(categories_list $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/categories_list', $data);
    }

    public function render_users_panel(users_panel $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/users', $data);
    }

    public function render_cohorts_panel(cohorts_panel $page) {
        $data = $page->export_for_template($this);
        return $this->render_from_template('local_admin_panel/cohorts', $data);
    }
}
