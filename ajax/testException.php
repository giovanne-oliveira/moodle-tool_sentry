<?php
/**
 * Ajax to test exception.
 *
 * @package   tool_sentry
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * © 2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../config.php');
require_once('../classes/helper.php');
require_once('../vendor/autoload.php');

require_admin();

header('Content-Type: text/plain');

try {
    tool_sentry\helper::init();
    throw new Exception('This is a test exception from Moodle Sentry plugin!');
} catch (Exception $e) {
    $data = Sentry\captureException($e);
    echo "✅ Test exception sent to Sentry.\n";
    echo "Message: " . $e->getMessage() . "\n";
    var_dump($data);
}
