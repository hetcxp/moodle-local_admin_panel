<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_externalpage(
        'local_admin_panel',
        get_string('pluginname', 'local_admin_panel'),
        new moodle_url('/local/admin_panel/index.php')
    ));
}
