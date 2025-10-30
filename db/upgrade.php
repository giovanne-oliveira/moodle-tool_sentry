<?php
/**
 * Version information
 *
 * @package    tool_sentry
 * @author     Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Function to upgrade the plugin
 *
 * @param int $oldversion - old version of plugin
 * @return bool - if success
 */
function xmldb_tool_sentry_upgrade(int $oldversion): bool {
    global $DB;

    if ($oldversion < 2024071200) {
        $DB->delete_records("config_plugins", [ 'plugin' => 'tool_sentry', 'name' => 'dns']);
        upgrade_plugin_savepoint(true, 2024071200, 'tool', 'sentry');
    }

    return true;
}
