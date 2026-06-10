<?php
namespace local_admin_panel\privacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Privacy Subsystem for local_admin_panel
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
