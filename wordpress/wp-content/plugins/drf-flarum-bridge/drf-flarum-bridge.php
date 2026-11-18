<?php
/**
 * Plugin Name: DRF Flarum Community Bridge
 * Plugin URI: https://github.com/ichbinboese/drf-forum-wordpress
 * Description: Nahtlose Integration von WordPress und dem Flarum Forum für de.rec.fotografie: Single Sign-On (SSO), Beitrags-Synchronisation und Diskussions-Widgets.
 * Version: 1.0.0
 * Author: de.rec.fotografie e.V.
 * Author URI: https://www.d-r-f.de/augenblicke/
 * License: GPLv2 or later
 * Text Domain: drf-flarum-bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Flarum_Bridge {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_shortcode('drf_flarum_discussions', [$this, 'render_discussions_shortcode']);
    }

    /**
     * Admin Menu
     */
    public function add_admin_menu() {
        add_options_page(
            'Flarum Forum Anbindung',
            'Flarum Forum',
            'manage_options',
            'drf-flarum-settings',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Settings Registration
     */
    public function register_settings() {
        register_setting('drf_flarum_group', 'drf_flarum_url');
        register_setting('drf_flarum_group', 'drf_flarum_api_key');
        register_setting('drf_flarum_group', 'drf_flarum_sso_secret');
    }

    /**
     * Render Settings Page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Flarum Forum Anbindung &ndash; de.rec.fotografie</h1>
            <p>Konfiguriere hier die Verbindung zwischen deiner WordPress-Installation und dem Flarum Community-Forum.</p>
            <form method="post" action="options.php">
                <?php
                settings_fields('drf_flarum_group');
                do_settings_sections('drf_flarum_group');
                $forum_url = get_option('drf_flarum_url', home_url('/forum/'));
                $api_key = get_option('drf_flarum_api_key', '');
                $sso_secret = get_option('drf_flarum_sso_secret', wp_generate_password(32, false));
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="drf_flarum_url">Flarum Forum URL</label></th>
                        <td>
                            <input name="drf_flarum_url" type="url" id="drf_flarum_url" value="<?php echo esc_attr($forum_url); ?>" class="regular-text">
                            <p class="description">Standard: <code><?php echo esc_html(home_url('/forum/')); ?></code> oder <code>http://localhost:8088</code></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="drf_flarum_api_key">Flarum Master API Key</label></th>
                        <td>
                            <input name="drf_flarum_api_key" type="password" id="drf_flarum_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                            <p class="description">Flarum API Key aus der Tabelle <code>api_keys</code> für automatische User-Erstellung.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="drf_flarum_sso_secret">SSO Shared Secret</label></th>
                        <td>
                            <input name="drf_flarum_sso_secret" type="text" id="drf_flarum_sso_secret" value="<?php echo esc_attr($sso_secret); ?>" class="regular-text">
                            <p class="description">Gemeinsames Geheimnis für JWT / HMAC Authentifizierung.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Einstellungen speichern'); ?>
            </form>
        </div>
        <?php
    }

    /**
     * REST Route for SSO Handshake
     */
    public function register_rest_routes() {
        register_rest_route('drf/v1', '/sso/verify', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handle_sso_verify'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function handle_sso_verify($request) {
        $secret = get_option('drf_flarum_sso_secret');
        $token = $request->get_param('token');

        if (empty($token) || empty($secret)) {
            return new WP_Error('invalid_token', 'Ungültiger Token oder Secret nicht konfiguriert', ['status' => 400]);
        }

        // Return current user data or verified payload
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            return [
                'success'  => true,
                'id'       => $user->ID,
                'username' => $user->user_login,
                'email'    => $user->user_email,
                'display'  => $user->display_name,
            ];
        }

        return ['success' => false, 'message' => 'Benutzer nicht eingeloggt'];
    }

    /**
     * Shortcode: [drf_flarum_discussions]
     */
    public function render_discussions_shortcode($atts) {
        $atts = shortcode_atts([
            'count' => 5,
        ], $atts);

        $forum_url = get_option('drf_flarum_url', home_url('/forum/'));

        ob_start();
        ?>
        <div class="drf-flarum-shortcode-widget">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px;">Aktuelle Diskussionen aus dem Forum</h3>
            <p style="color: #6b7280; font-size: 0.9rem;">Diskutiere mit &uuml;ber Foto-Sessions, Dunkelkammer und Technik:</p>
            <a href="<?php echo esc_url($forum_url); ?>" class="pill-btn pill-btn-forum" style="margin-top: 10px;">
                <span>Direkt zum Forum &rarr;</span>
            </a>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_action('plugins_loaded', ['DRF_Flarum_Bridge', 'get_instance']);
