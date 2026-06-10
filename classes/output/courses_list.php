<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

class courses_list implements renderable, templatable {
    
    protected $sort;
    protected $dir;
    protected $page;
    protected $search;
    protected $category;
    protected $visibility;
    protected $perpage = 20;

    public function __construct($sort = 'timecreated', $dir = 'DESC', $page = 0, $search = '', $category = 0, $visibility = -1) {
        $this->sort = $sort;
        $this->dir = $dir;
        $this->page = $page;
        $this->search = $search;
        $this->category = $category;
        $this->visibility = $visibility;
    }

    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = new \stdClass();
        $data->sesskey = sesskey();

        $sortablecolumns = ['fullname', 'categoryname', 'visible', 'enrolledcount', 'completedcount', 'cohortscount', 'progress', 'timecreated'];
        $sort = in_array($this->sort, $sortablecolumns) ? $this->sort : 'timecreated';
        $dir = ($this->dir === 'ASC') ? 'ASC' : 'DESC';

        $params = [];
        $where = "c.id != 1"; // Exclude site course

        if (!empty($this->search)) {
            $where .= " AND " . $DB->sql_like('c.fullname', ':search', false, false);
            $params['search'] = '%' . $this->search . '%';
        }

        if (!empty($this->category)) {
            $where .= " AND c.category = :category";
            $params['category'] = $this->category;
        }

        if ($this->visibility !== -1) {
            $where .= " AND c.visible = :visibility";
            $params['visibility'] = $this->visibility;
        }

        $sql_select = "
            SELECT c.id, c.fullname, c.shortname, c.visible, c.timecreated, c.category,
                   cc.name AS categoryname,
                   (SELECT COUNT(DISTINCT ue.userid) 
                      FROM {user_enrolments} ue 
                      JOIN {enrol} e ON ue.enrolid = e.id 
                     WHERE e.courseid = c.id AND ue.status = 0) AS enrolledcount,
                   (SELECT COUNT(DISTINCT ccmp.userid) 
                      FROM {course_completions} ccmp 
                      JOIN {user_enrolments} ue ON ccmp.userid = ue.userid
                      JOIN {enrol} e ON ue.enrolid = e.id
                     WHERE ccmp.course = c.id 
                       AND ccmp.timecompleted IS NOT NULL
                       AND e.courseid = c.id 
                       AND ue.status = 0) AS completedcount,
                   (SELECT COUNT(DISTINCT e.customint1) 
                      FROM {enrol} e 
                     WHERE e.courseid = c.id AND e.enrol = 'cohort') AS cohortscount
              FROM {course} c
         LEFT JOIN {course_categories} cc ON c.category = cc.id
             WHERE $where
        ";

        $sql_count = "SELECT COUNT(c.id) FROM {course} c WHERE $where";
        $totalcount = $DB->count_records_sql($sql_count, $params);

        $order_by = "$sort $dir";
        if ($sort === 'progress') {
            $order_by = "CASE WHEN enrolledcount > 0 THEN (completedcount * 1.0 / enrolledcount) ELSE 0 END $dir";
        }

        $sql = $sql_select . " ORDER BY $order_by";
        $limitfrom = $this->page * $this->perpage;
        
        $courses = $DB->get_records_sql($sql, $params, $limitfrom, $this->perpage);

        // Process courses data
        $data->courses = [];
        foreach ($courses as $course) {
            $percent = 0;
            if ($course->enrolledcount > 0) {
                $percent = round(($course->completedcount / $course->enrolledcount) * 100);
            }
            $course->progress_percent = $percent;
            $course->isvisible = ($course->visible == 1);
            
            // URLs para las acciones
            $courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
            $course->url_view = $courseurl->out(false);
            
            $editurl = new moodle_url('/course/edit.php', ['id' => $course->id]); // Podría ser edit, pero la visibilidad a veces se hace con management
            $course->url_edit = $editurl->out(false);

            // Usamos nuestro propio script para evitar que redireccione al management de Moodle
            $actionurl = new moodle_url('/local/admin_panel/action.php', ['courseid' => $course->id, 'sesskey' => sesskey()]);
            $course->url_delete = (new moodle_url($actionurl, ['action' => 'deletecourse']))->out(false);
            $course->url_hide = (new moodle_url($actionurl, ['action' => 'hidecourse']))->out(false);
            $course->url_show = (new moodle_url($actionurl, ['action' => 'showcourse']))->out(false);
            
            // Usamos action.php para gestionar el movimiento del curso también
            $course->url_move = (new moodle_url($actionurl, ['action' => 'movecourse']))->out(false);

            $data->courses[] = $course;
        }

        // Pagination
        $data->has_pagination = $totalcount > $this->perpage;
        $data->pages = [];
        $totalpages = ceil($totalcount / $this->perpage);
        
        // Show limited page numbers if there are too many (simple version for now)
        for ($i = 0; $i < $totalpages; $i++) {
            $data->pages[] = [
                'page' => $i,
                'pagenum' => $i + 1,
                'active' => ($i == $this->page),
                'url' => (new moodle_url('/local/admin_panel/index.php', [
                    'tab' => 'courses',
                    'subtab' => 'courses',
                    'sort' => $this->sort,
                    'dir' => $this->dir,
                    'page' => $i,
                    'search' => $this->search,
                    'category' => $this->category,
                    'visibility' => $this->visibility
                ]))->out(false)
            ];
        }

        // Categories for filter
        $categories = $DB->get_records('course_categories', null, 'name ASC', 'id, name');
        $data->categories = [];
        foreach ($categories as $cat) {
            $data->categories[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'selected' => ($cat->id == $this->category)
            ];
        }

        $data->search = $this->search;
        $data->visibility_all = ($this->visibility === -1);
        $data->visibility_visible = ($this->visibility === 1);
        $data->visibility_hidden = ($this->visibility === 0);

        // Sorting URLs
        $baseurl = new moodle_url('/local/admin_panel/index.php', [
            'tab' => 'courses',
            'subtab' => 'courses',
            'search' => $this->search,
            'category' => $this->category,
            'visibility' => $this->visibility
        ]);

        foreach ($sortablecolumns as $col) {
            $newdir = ($this->sort === $col && $this->dir === 'ASC') ? 'DESC' : 'ASC';
            $url = new moodle_url($baseurl, ['sort' => $col, 'dir' => $newdir]);
            $data->{"url_sort_$col"} = $url->out(false);
            
            // Helpful for template to show sort icons
            if ($this->sort === $col) {
                $data->{"sort_{$col}_active"} = true;
                $data->{"sort_{$col}_is_asc"} = ($this->dir === 'ASC');
            }
        }

        return $data;
    }
}
