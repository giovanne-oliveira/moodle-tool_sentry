<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Ajax to test exception.
 *
 * @package   tool_sentry
 * @author    Esdras Caleb
 * @copyright 2023 Esdras Caleb
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
