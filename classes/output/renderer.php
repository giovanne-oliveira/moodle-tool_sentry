<?php
/**
 * Privacy provider.
 *
 * @package   tool_sentry
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_sentry\output;

/**
 * Class renderer to renderer extra components to the plugin
 * @author    Giovanne Oliveira <giovanne@giovanne.dev>
 * @copyright  2025 Giovanne Oliveira
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {
    /**
     * Renderer test button in the settings page.
     */
    public function render_test_buttons() {
        $data = [
            'wwwroot' => $this->page->url->out(false),
        ];
        return $this->render_from_template('tool_sentry/test_buttons', $data);
    }
}
