<?php
/**
 * Version information
 *
 * @package    tool_sentry
 * @author     Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_sentry;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/tool/sentry/vendor/autoload.php');

/**
 * Class helper to provide functions to events
 *
 * @package    tool_sentry
 * @author     Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /** @var bool Whether Sentry has already been initialized. */
    private static $initialized = false;
    /** @var callable|null Previous PHP error handler to preserve default behavior. */
    private static $previousErrorHandler = null;

    /**
     * Cleans and converts Sentry config object into array with correct types.
     *
     * @param \stdClass $config Raw plugin config.
     * @return array|null Clean config array or null if invalid.
     */
    private static function get_clean_config($config): ?array {
        if (empty($config->activate) || empty($config->dsn)) {
            return null;
        }

        unset($config->activate);
        unset($config->version);
        unset($config->javascriptloader);
        // Internal flags not part of Sentry SDK options.
        unset($config->log_messages);
        unset($config->auto_hook);
        unset($config->replays_session_sample_rate);
        unset($config->replays_on_error_sample_rate);

        foreach (['ignore_exceptions', 'ignore_transactions', 'in_app_exclude', 'in_app_include'] as $key) {
            if (isset($config->$key) && $config->$key === "") {
                unset($config->$key);
            }
        }

        $config->enable_tracing = !empty($config->enable_tracing ?? '');
        $config->attach_stacktrace = !empty($config->attach_stacktrace ?? '');
        $config->send_default_pii = !empty($config->send_default_pii ?? '');

        $configarray = (array) $config;

        // Normalize error_types to an integer bitmask for Sentry.
        if (isset($configarray['error_types'])) {
            if (is_array($configarray['error_types'])) {
                $mask = 0;
                foreach ($configarray['error_types'] as $v) {
                    $mask |= (int)$v;
                }
                $configarray['error_types'] = $mask;
            } else if (is_string($configarray['error_types'])) {
                // Moodle stores multiselect as comma-separated string (e.g., "1,2,4").
                if (strpos($configarray['error_types'], ',') !== false) {
                    $parts = array_filter(array_map('trim', explode(',', $configarray['error_types'])), 'strlen');
                    $mask = 0;
                    foreach ($parts as $p) {
                        $mask |= (int)$p;
                    }
                    $configarray['error_types'] = $mask;
                } else if (is_numeric($configarray['error_types'])) {
                    $configarray['error_types'] = (int)$configarray['error_types'];
                } else {
                    // Fallback: remove invalid value to let SDK default.
                    unset($configarray['error_types']);
                }
            }
        }

        foreach ($configarray as $name => $value) {
            if (is_numeric($value) && $name !== 'release') {
                if (strpos($value, '.') !== false) {
                    $configarray[$name] = floatval($value);
                } else {
                    $configarray[$name] = intval($value);
                }
            }
        }

        return $configarray;
    }

    /**
     * Initialize sentry.
     *
     * @param \core\event\base|null $event The event.
     * @return void
     */
    public static function init(?\core\event\base $event = null): void {
        $config = get_config('tool_sentry');
        $sentryconfig = self::get_clean_config($config);
        if ($sentryconfig) {
            if (!self::$initialized) {
                self::$initialized = true;
                self::inject_sentry_js();
                \Sentry\init($sentryconfig);

                // Auto wire: forward PHP logs as messages.
                if (!empty($config->auto_hook)) {
                    self::$previousErrorHandler = set_error_handler([self::class, 'sentry_error_handler']);
                }
            }

            // If configured, add Moodle events as breadcrumbs for context.
            if (!empty($config->auto_hook) && $event !== null) {
                \Sentry\addBreadcrumb(new \Sentry\Breadcrumb(
                    \Sentry\Breadcrumb::LEVEL_INFO,
                    'moodle.event',
                    (new \ReflectionClass($event))->getShortName(),
                    [
                        'component' => $event->component ?? null,
                        'eventname' => $event->eventname ?? null,
                        'courseid' => method_exists($event, 'get_courseid') ? $event->get_courseid() : null,
                        'contextid' => ($event->get_context()) ? $event->get_context()->id : null,
                    ]
                ));
            }
        }
    }

    /**
     * Capture last PHP error (if any).
     *
     * @param \core\event\base|null $event The event.
     * @return void
     */
    public static function geterros(?\core\event\base $event = null): void {
        $config = get_config('tool_sentry');
        if (empty($config->activate)) {
            return;
        }
        // Ensure SDK is initialized with cleaned config before capturing.
        self::init($event);
        try {
            \Sentry\captureLastError();
        } catch (\Throwable $e) {
            // Swallow SDK errors to avoid impacting Moodle execution.
        }
    }

    /**
     * Send an application log/message to Sentry with a given severity level.
     *
     * Usage: \tool_sentry\helper::log('info', 'Something happened');
     * Accepted levels: 'debug', 'info', 'warning', 'error', 'fatal'.
     *
     * @param string $level   Severity level
     * @param string $message Message to send
     * @param array $context  Optional structured context added to the scope
     * @return void
     */
    public static function log(string $level, string $message, array $context = []): void {
        $config = get_config('tool_sentry');
        if (empty($config->activate) || empty($config->dsn)) {
            return;
        }

        // Optional guard: allow toggling message capture via setting.
        if (isset($config->log_messages) && (int)$config->log_messages !== 1) {
            return;
        }

        // Ensure Sentry is initialized once per request before logging.
        self::init();

        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($context, $message, $level) {
            if (!empty($context)) {
                foreach ($context as $key => $value) {
                    $scope->setContext((string)$key, ['value' => $value]);
                }
            }
            \Sentry\captureMessage($message, $level);
        });
    }

    /**
     * PHP error handler to forward non-fatal errors/notices to Sentry without
     * interfering with Moodle's own handlers (returns false to continue default handling).
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool false to allow normal handling to continue
     */
    public static function sentry_error_handler(int $errno, string $errstr, string $errfile = '', int $errline = 0): bool {
        // Respect current error_reporting level.
        if (!(error_reporting() & $errno)) {
            return false;
        }

        // Map PHP error number to Sentry level.
        $level = 'error';
        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
            case E_STRICT:
                $level = 'info';
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $level = 'warning';
                break;
            case E_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
                $level = 'error';
                break;
        }

        // Ensure Sentry is initialized.
        self::init();

        \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($level, $errstr, $errfile, $errline, $errno) {
            $scope->setContext('php_error', [
                'errno' => $errno,
                'file' => $errfile,
                'line' => $errline,
            ]);
            \Sentry\captureMessage($errstr, $level);
        });

        // Continue with the normal PHP/Moodle error handling chain.
        return false;
    }

    /**
     * Injects Sentry JS loader and init code into the page.
     *
     * @return void
     */
    private static function inject_sentry_js(): void {
        global $PAGE;
        $config = get_config('tool_sentry');

        if (empty($config->activate) || empty($config->javascriptloader)) {
            return;
        }

        $javascriptloader = $config->javascriptloader;
        $replaySession = isset($config->replays_session_sample_rate) ? (float)$config->replays_session_sample_rate : null;
        $replayOnError = isset($config->replays_on_error_sample_rate) ? (float)$config->replays_on_error_sample_rate : null;
        $config = self::get_clean_config($config);
        if ($config === null) {
            return;
        }

        $configjson = json_encode($config);

        $code = "
        (function() {
              const script = document.createElement('script');
              script.src = '$javascriptloader'; // substitua se não for usar PHP
              script.crossOrigin = 'anonymous';
              script.onload = function() {
                try {
                  var cfg = $configjson;
                  // Attach Replay integration if sampling is configured.
                  var rs = " + ($replaySession !== null ? json_encode($replaySession) : 'null') + ";
                  var re = " + ($replayOnError !== null ? json_encode($replayOnError) : 'null') + ";
                  if (rs !== null || re !== null) {
                    cfg.integrations = (cfg.integrations || []).concat(Sentry.replayIntegration());
                    if (rs !== null) cfg.replaysSessionSampleRate = rs;
                    if (re !== null) cfg.replaysOnErrorSampleRate = re;
                  }
                  Sentry.init(cfg);
                } catch (e) {
                  // swallow client init errors
                }
              };
              document.head.appendChild(script);
            })();";

        $PAGE->requires->js_init_code($code);
    }
}
