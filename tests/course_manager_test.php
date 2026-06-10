<?php
namespace local_admin_panel;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/admin_panel/classes/course_manager.php');

class course_manager_test extends \advanced_testcase {

    protected function setUp(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
    }

    public function test_bulk_hide_and_show() {
        global $DB;
        $generator = $this->getDataGenerator();

        $cat = $generator->create_category();
        $course1 = $generator->create_course(['category' => $cat->id, 'visible' => 1]);
        $course2 = $generator->create_course(['category' => $cat->id, 'visible' => 1]);

        $courseids = [$course1->id, $course2->id];

        // Ocultar
        \local_admin_panel\course_manager::bulk_hide($courseids);

        $c1 = $DB->get_record('course', ['id' => $course1->id]);
        $c2 = $DB->get_record('course', ['id' => $course2->id]);
        
        $this->assertEquals(0, $c1->visible);
        $this->assertEquals(0, $c2->visible);

        // Mostrar
        \local_admin_panel\course_manager::bulk_show($courseids);

        $c1 = $DB->get_record('course', ['id' => $course1->id]);
        $c2 = $DB->get_record('course', ['id' => $course2->id]);
        
        $this->assertEquals(1, $c1->visible);
        $this->assertEquals(1, $c2->visible);
    }

    public function test_bulk_move() {
        global $DB;
        $generator = $this->getDataGenerator();

        $cat1 = $generator->create_category();
        $cat2 = $generator->create_category();
        
        $course1 = $generator->create_course(['category' => $cat1->id]);
        $course2 = $generator->create_course(['category' => $cat1->id]);

        $courseids = [$course1->id, $course2->id];

        // Mover a cat2
        $result = \local_admin_panel\course_manager::bulk_move($courseids, $cat2->id);

        $this->assertTrue($result);

        $c1 = $DB->get_record('course', ['id' => $course1->id]);
        $c2 = $DB->get_record('course', ['id' => $course2->id]);
        
        $this->assertEquals($cat2->id, $c1->category);
        $this->assertEquals($cat2->id, $c2->category);
    }

    public function test_bulk_delete() {
        global $DB;
        $generator = $this->getDataGenerator();

        $cat = $generator->create_category();
        $course1 = $generator->create_course(['category' => $cat->id]);
        $course2 = $generator->create_course(['category' => $cat->id]);

        $courseids = [$course1->id, $course2->id];

        // Eliminar
        \local_admin_panel\course_manager::bulk_delete($courseids);

        $this->assertFalse($DB->record_exists('course', ['id' => $course1->id]));
        $this->assertFalse($DB->record_exists('course', ['id' => $course2->id]));
    }
}
