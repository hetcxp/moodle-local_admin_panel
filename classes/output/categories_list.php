<?php
namespace local_admin_panel\output;

use renderable;
use templatable;
use renderer_base;

defined('MOODLE_INTERNAL') || die();

class categories_list implements renderable, templatable {
    public function export_for_template(renderer_base $output) {
        global $DB;
        $data = new \stdClass();
        
        $sort = optional_param('sort', 'sortorder', PARAM_ALPHA);
        $dir = optional_param('dir', 'ASC', PARAM_ALPHA);
        $search = optional_param('search', '', PARAM_TEXT);
        $visibility = optional_param('visibility', -1, PARAM_INT);
        
        $valid_sorts = ['name', 'parentname', 'description', 'coursecount', 'visible', 'sortorder'];
        if (!in_array($sort, $valid_sorts)) {
            $sort = 'sortorder';
        }
        $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
        
        $sort_sql = "c.$sort";
        if ($sort === 'parentname') {
            $sort_sql = "p.name";
        }
        
        $where_clauses = [];
        $params = [];
        
        if ($search !== '') {
            $where_clauses[] = "(c.name LIKE :search1 OR c.description LIKE :search2)";
            $params['search1'] = '%' . $DB->sql_like_escape($search) . '%';
            $params['search2'] = '%' . $DB->sql_like_escape($search) . '%';
        }
        
        if ($visibility != -1) {
            $where_clauses[] = "c.visible = :visibility";
            $params['visibility'] = $visibility;
        }
        
        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = "WHERE " . implode(' AND ', $where_clauses);
        }
        
        $sql = "SELECT c.id, c.name, c.description, c.visible, c.coursecount, c.parent, c.sortorder,
                       p.name AS parentname
                FROM {course_categories} c
                LEFT JOIN {course_categories} p ON c.parent = p.id
                $where_sql
                ORDER BY $sort_sql $dir";
                
        $categories = $DB->get_records_sql($sql, $params, 0, 100);
        
        $formatted_categories = [];
        $all_categories = [];
        
        if ($categories) {
            foreach ($categories as $cat) {
                $formatted = new \stdClass();
                $formatted->id = $cat->id;
                $formatted->name = $cat->name;
                $formatted->description = $cat->description;
                $formatted->visible = $cat->visible;
                $formatted->isvisible = (bool)$cat->visible;
                $formatted->coursecount = $cat->coursecount;
                $formatted->parent = $cat->parent;
                $formatted->parentname = $cat->parent ? $cat->parentname : 'Top';
                $formatted->candelete = ($cat->coursecount == 0);
                
                $formatted_categories[] = $formatted;
                $all_categories[] = ['id' => $cat->id, 'name' => $cat->name];
            }
        }
        
        $data->categories = $formatted_categories;
        $data->all_categories = $all_categories;
        $data->sesskey = sesskey();
        
        $data->search = $search;
        $data->visibility_all = ($visibility == -1);
        $data->visibility_visible = ($visibility == 1);
        $data->visibility_hidden = ($visibility == 0);
        
        // Sorting headers
        $headers = [
            'name' => 'Nombre',
            'parentname' => 'Categoría Padre',
            'description' => 'Descripción',
            'coursecount' => 'Cursos Vinculados',
            'visible' => 'Estado'
        ];
        
        $data->headers = [];
        foreach ($headers as $key => $label) {
            $is_current_sort = ($sort === $key);
            $new_dir = ($is_current_sort && $dir === 'ASC') ? 'DESC' : 'ASC';
            $url_params = [
                'tab' => 'courses',
                'subtab' => 'categories',
                'sort' => $key,
                'dir' => $new_dir
            ];
            if ($search !== '') {
                $url_params['search'] = $search;
            }
            if ($visibility != -1) {
                $url_params['visibility'] = $visibility;
            }
            $url = new \moodle_url('/local/admin_panel/index.php', $url_params);
            
            $header = new \stdClass();
            $header->label = $label;
            $header->url = $url->out(false);
            $header->issorted = $is_current_sort;
            $header->sortdir = $is_current_sort ? strtolower($dir) : '';
            $data->headers[] = $header;
        }
        
        return $data;
    }
}
