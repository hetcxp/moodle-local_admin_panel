<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/course/lib.php');
require_once(__DIR__ . '/classes/course_manager.php');

$action = required_param('action', PARAM_ALPHA);
$courseid = optional_param('courseid', 0, PARAM_INT);

require_login();
require_sesskey();
require_capability('moodle/site:config', context_system::instance());

if ($courseid) {
    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
}

switch ($action) {
    case 'createcourse':
        $fullname = required_param('fullname', PARAM_TEXT);
        $shortname = required_param('shortname', PARAM_TEXT);
        $summary = optional_param('summary', '', PARAM_RAW);
        $categoryid = required_param('categoryid', PARAM_INT);
        $visible = optional_param('visible', 0, PARAM_INT);
        
        $data = new stdClass();
        $data->fullname = $fullname;
        $data->shortname = $shortname;
        $data->summary = $summary;
        $data->category = $categoryid;
        $data->visible = $visible;
        
        create_course($data);
        break;
    case 'hidecourse':
        $courseids = optional_param_array('courseids', [$courseid], PARAM_INT);
        \local_admin_panel\course_manager::bulk_hide($courseids);
        break;
    case 'showcourse':
        $courseids = optional_param_array('courseids', [$courseid], PARAM_INT);
        \local_admin_panel\course_manager::bulk_show($courseids);
        break;
    case 'deletecourse':
        $courseids = optional_param_array('courseids', [$courseid], PARAM_INT);
        \local_admin_panel\course_manager::bulk_delete($courseids);
        break;
    case 'movecourse':
        $newcategoryid = required_param('newcategoryid', PARAM_INT);
        require_capability('moodle/category:manage', context_system::instance());
        $courseids = optional_param_array('courseids', [$courseid], PARAM_INT);
        \local_admin_panel\course_manager::bulk_move($courseids, $newcategoryid);
        break;
    case 'createcategory':
        $name = required_param('name', PARAM_TEXT);
        $parent = required_param('parent', PARAM_INT);
        $description = optional_param('description', '', PARAM_RAW);
        
        $data = new stdClass();
        $data->name = $name;
        $data->parent = $parent;
        $data->description = $description;
        $data->descriptionformat = FORMAT_HTML;
        
        \core_course_category::create($data);
        break;
    case 'editcategory':
        $catid = required_param('categoryid', PARAM_INT);
        $name = required_param('name', PARAM_TEXT);
        $parent = required_param('parent', PARAM_INT);
        $description = optional_param('description', '', PARAM_RAW);
        
        $category = \core_course_category::get($catid);
        $data = new stdClass();
        $data->id = $catid;
        $data->name = $name;
        $data->parent = $parent;
        $data->description = $description;
        $data->descriptionformat = FORMAT_HTML;
        $category->update($data);
        break;
    case 'hidecategory':
        $catid = optional_param('categoryid', 0, PARAM_INT);
        $catids = optional_param_array('categoryids', $catid ? [$catid] : [], PARAM_INT);
        foreach ($catids as $cid) {
            $category = \core_course_category::get($cid);
            $category->hide();
        }
        break;
    case 'showcategory':
        $catid = optional_param('categoryid', 0, PARAM_INT);
        $catids = optional_param_array('categoryids', $catid ? [$catid] : [], PARAM_INT);
        foreach ($catids as $cid) {
            $category = \core_course_category::get($cid);
            $category->show();
        }
        break;
    case 'deletecategory':
        $catid = optional_param('categoryid', 0, PARAM_INT);
        $catids = optional_param_array('categoryids', $catid ? [$catid] : [], PARAM_INT);
        foreach ($catids as $cid) {
            $category = \core_course_category::get($cid);
            if ($category->coursecount == 0) {
                $category->delete_full(false);
            }
        }
        break;
    default:
        throw new \moodle_exception('invalidaction');
}

$tab = optional_param('tab', 'courses', PARAM_ALPHA);
$subtab = optional_param('subtab', 'courses', PARAM_ALPHA);

redirect(new moodle_url('/local/admin_panel/index.php', ['tab' => $tab, 'subtab' => $subtab]));
