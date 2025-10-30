<?php
/**
 * Privacy provider.
 *
 * @package   tool_sentry
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_sentry\privacy;
/**
 * Class provider to provide info of data shared
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    // This plugin does not store any personal user data.
    \core_privacy\local\metadata\null_provider {
    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return  string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
