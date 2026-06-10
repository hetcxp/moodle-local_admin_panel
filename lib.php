<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Extend settings navigation to add a link to the admin panel.
 */
function local_admin_panel_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    if (has_capability('moodle/site:config', $context)) {
        $node = $settingsnav->find('root', \navigation_node::TYPE_SITE_ADMIN);
        if ($node) {
            $node->add(
                get_string('pluginname', 'local_admin_panel'),
                new moodle_url('/local/admin_panel/index.php'),
                \navigation_node::TYPE_SETTING,
                null,
                'local_admin_panel'
            );
        }
    }
}
