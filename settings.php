<?php
/**
 * Version information
 *
 * @package    tool_sentry
 * @author     Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG, $ADMIN, $PAGE;
\tool_sentry\helper::init();

if (is_siteadmin()) {
    if (!$ADMIN->locate('tool_sentry')) {
        $page = new admin_settingpage('sentryconfig', get_string('pluginsettings', 'tool_sentry'));
        $page->add(
            new admin_setting_heading(
                'tool_sentry/options',
                get_string('options', 'tool_sentry'),
                get_string('options_desc', 'tool_sentry')
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/activate',
                get_string('activate', 'tool_sentry'),
                get_string('activate_desc', 'tool_sentry'),
                0
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/dsn',
                get_string('dsn', 'tool_sentry'),
                get_string('dsn_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/javascriptloader',
                get_string('javascriptloader', 'tool_sentry'),
                get_string('javascriptloader_desc', 'tool_sentry'),
                ''
            )
        );
        $PAGE->requires->js(new moodle_url('/admin/tool/sentry/js/connectiontest.js'));
        $renderer = $PAGE->get_renderer('tool_sentry');
        $html = $renderer->render_test_buttons();

        $page->add(new admin_setting_heading(
            'integracaosigaa_test_button',
            get_string('test_conn', 'tool_sentry'),
            $html
        ));

        $page->add(
            new admin_setting_heading(
                'tool_sentry/sentry_options',
                get_string('sentry_options', 'tool_sentry'),
                get_string('sentry_options_desc', 'tool_sentry')
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/release',
                get_string('release', 'tool_sentry'),
                get_string('release_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/activate',
                get_string('activate', 'tool_sentry'),
                get_string('activate_desc', 'tool_sentry'),
                1
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/send_default_pii',
                get_string('send_default_pii', 'tool_sentry'),
                get_string('send_default_pii_desc', 'tool_sentry'),
                0
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/sample_rate',
                get_string('sample_rate', 'tool_sentry'),
                get_string('sample_rate_desc', 'tool_sentry'),
                1,
                PARAM_FLOAT
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/profiles_sample_rate',
                get_string('profiles_sample_rate', 'tool_sentry'),
                get_string('profiles_sample_rate_desc', 'tool_sentry'),
                1,
                PARAM_FLOAT
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/enable_tracing',
                get_string('enable_tracing', 'tool_sentry'),
                get_string('enable_tracing_desc', 'tool_sentry'),
                1
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/traces_sample_rate',
                get_string('traces_sample_rate', 'tool_sentry'),
                get_string('traces_sample_rate_desc', 'tool_sentry'),
                0,
                PARAM_FLOAT
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/max_breadcrumbs',
                get_string('max_breadcrumbs', 'tool_sentry'),
                get_string('max_breadcrumbs_desc', 'tool_sentry'),
                100,
                PARAM_INT
            )
        );
        $page->add(
            new admin_setting_configselect(
                'tool_sentry/max_request_body_size',
                get_string('max_request_body_size', 'tool_sentry'),
                get_string('max_request_body_size_desc', 'tool_sentry'),
                'medium',
                [
                    'never'     => get_string('never', 'tool_sentry'),
                    'small'     => get_string('small', 'tool_sentry'),
                    'medium'    => get_string('medium', 'tool_sentry'),
                    'always'    => get_string('always', 'tool_sentry'),
                ]
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/attach_stacktrace',
                get_string('attach_stacktrace', 'tool_sentry'),
                get_string('attach_stacktrace_desc', 'tool_sentry'),
                0
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/log_messages',
                get_string('log_messages', 'tool_sentry'),
                get_string('log_messages_desc', 'tool_sentry'),
                0
            )
        );
        $page->add(
            new admin_setting_configcheckbox(
                'tool_sentry/auto_hook',
                get_string('auto_hook', 'tool_sentry'),
                get_string('auto_hook_desc', 'tool_sentry'),
                1
            )
        );
        $page->add(
            new admin_setting_heading(
                'tool_sentry/replay_heading',
                get_string('replay_heading', 'tool_sentry'),
                get_string('replay_heading_desc', 'tool_sentry')
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/replays_session_sample_rate',
                get_string('replays_session_sample_rate', 'tool_sentry'),
                get_string('replays_session_sample_rate_desc', 'tool_sentry'),
                0,
                PARAM_FLOAT
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/replays_on_error_sample_rate',
                get_string('replays_on_error_sample_rate', 'tool_sentry'),
                get_string('replays_on_error_sample_rate_desc', 'tool_sentry'),
                1,
                PARAM_FLOAT
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/max_value_length',
                get_string('max_value_length', 'tool_sentry'),
                get_string('max_value_length_desc', 'tool_sentry'),
                1024,
                PARAM_INT
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/environment',
                get_string('environment', 'tool_sentry'),
                get_string('environment_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configmultiselect(
                'tool_sentry/error_types',
                get_string('error_types', 'tool_sentry'),
                get_string('error_types_desc', 'tool_sentry'),
                [E_ALL],
                [
                    E_ERROR             => 'E_ERROR',
                    E_WARNING           => 'E_WARNING',
                    E_PARSE             => 'E_PARSE',
                    E_NOTICE            => 'E_NOTICE',
                    E_CORE_ERROR        => 'E_CORE_ERROR',
                    E_CORE_WARNING      => 'E_CORE_WARNING',
                    E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
                    E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
                    E_USER_ERROR        => 'E_USER_ERROR',
                    E_USER_WARNING      => 'E_USER_WARNING',
                    E_USER_NOTICE       => 'E_USER_NOTICE',
                    E_STRICT            => 'E_STRICT',
                    E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
                    E_DEPRECATED        => 'E_DEPRECATED',
                    E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
                    E_ALL               => 'E_ALL',
                ]
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/server_name',
                get_string('server_name', 'tool_sentry'),
                get_string('server_name_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/ignore_exceptions',
                get_string('ignore_exceptions', 'tool_sentry'),
                get_string('ignore_exceptions_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/ignore_transactions',
                get_string('ignore_transactions', 'tool_sentry'),
                get_string('ignore_transactions_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/in_app_include',
                get_string('in_app_include', 'tool_sentry'),
                get_string('in_app_include_desc', 'tool_sentry'),
                ''
            )
        );
        $page->add(
            new admin_setting_configtext(
                'tool_sentry/in_app_exclude',
                get_string('in_app_exclude', 'tool_sentry'),
                get_string('in_app_exclude_desc', 'tool_sentry'),
                ''
            )
        );
        $ADMIN->add('tools', $page);
    }
}
\tool_sentry\helper::geterros();
