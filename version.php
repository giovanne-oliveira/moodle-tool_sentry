<?php
/**
 * Provides conection with sentry.io to track errors in your moodle site using sentry
 *
 * @package    tool_sentry
 * @copyright  2025 Giovanne Oliveira
 * @author     Giovanne Oliveira <giovanne@giovanne.dev>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin version.
$plugin->version = 2025102400;

// Required Moodle version (3.11).
$plugin->requires = 2021051700;

// Full name of the plugin.
$plugin->component = 'tool_sentry';

// Software maturity level.
$plugin->maturity = MATURITY_STABLE;

// User-friendly version number.
$plugin->release = '1.0.0';
