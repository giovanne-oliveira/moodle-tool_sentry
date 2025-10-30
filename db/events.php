<?php
/**
 * Evetns to start sentry sdk and get errors
 *
 * @package   tool_sentry
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$observers = [];

$observers[] = [
    'eventname' => 'core\event\base',
    'callback' => '\tool_sentry\helper::init',
    'internal' => true,
    'priority'    => 9999,
];

$observers[] = [
    'eventname' => 'core\event\base',
    'callback' => '\tool_sentry\helper::geterros',
    'internal' => true,
    'priority'    => 0,
];
