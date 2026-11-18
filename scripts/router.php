<?php
/**
 * Standalone Router for DRF Augenblicke (PHP Built-in Server)
 * Enables instant testing without Docker or WordPress installation
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$theme_dir = dirname(__DIR__) . '/wordpress/wp-content/themes/drf-augenblicke';

// 1. Serve static theme assets
if (preg_match('#^/wp-content/themes/drf-augenblicke/(assets/.*)$#', $uri, $matches)) {
    $file = $theme_dir . '/' . $matches[1];
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimes = [
            'css' => 'text/css',
            'js'  => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg'=> 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
        ];
        if (isset($mimes[$ext])) {
            header('Content-Type: ' . $mimes[$ext]);
        }
        readfile($file);
        exit;
    }
}

// Direct assets route
if (preg_match('#^/assets/(.*)$#', $uri, $matches)) {
    $file = $theme_dir . '/assets/' . $matches[1];
    if (file_exists($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $mimes = [
            'css' => 'text/css',
            'js'  => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg'=> 'image/jpeg',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
        ];
        if (isset($mimes[$ext])) {
            header('Content-Type: ' . $mimes[$ext]);
        }
        readfile($file);
        exit;
    }
}

// 2. Mock WordPress core functions for standalone mode
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
    define('DRF_STANDALONE', true);

    function get_template_directory() {
        return dirname(__DIR__) . '/wordpress/wp-content/themes/drf-augenblicke';
    }
    function get_template_directory_uri() {
        return '/wp-content/themes/drf-augenblicke';
    }
    function load_theme_textdomain($domain, $path = false) {}
    function add_theme_support($feature, $args = null) {}
    function register_nav_menus($locations = []) {}
    function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {}
    function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {}
    function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {}

    function language_attributes() { echo 'lang="de"'; }
    function bloginfo($show) { if ($show === 'charset') echo 'UTF-8'; }
    function wp_head() {
        echo '<link rel="stylesheet" href="/wp-content/themes/drf-augenblicke/assets/css/theme.css">' . PHP_EOL;
        echo '<title>d.r.f Tagungsbüro »Augenblicke«</title>' . PHP_EOL;
    }
    function body_class() { echo 'class="drf-body drf-standalone"'; }
    function wp_body_open() {}
    function wp_footer() {
        echo '<script src="/wp-content/themes/drf-augenblicke/assets/js/main.js"></script>' . PHP_EOL;
    }
    function home_url($path = '/') { return $path; }
    function esc_url($url) { return htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); }
    function esc_attr_e($text, $domain = '') { echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
    function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
    function esc_html_e($text, $domain = '') { echo htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
    function wp_nonce_field($action, $name) {
        echo '<input type="hidden" name="' . $name . '" value="valid_nonce">';
    }
    function wp_verify_nonce($nonce, $action) { return true; }
    function get_header() {
        global $theme_dir;
        include $theme_dir . '/header.php';
    }
    function get_footer() {
        global $theme_dir;
        include $theme_dir . '/footer.php';
    }
    function is_page($slug) {
        global $current_slug;
        return $current_slug === $slug;
    }
    function get_option($key, $default = '') { return $default; }
    require_once $theme_dir . '/functions.php';
}

// 3. Route to corresponding page template
$clean_slug = trim($uri, '/');
global $current_slug;
$current_slug = $clean_slug;

if ($clean_slug === '' || $clean_slug === 'index.php') {
    include $theme_dir . '/front-page.php';
    exit;
}

$page_map = [
    'verein'         => 'page-verein.php',
    'augenblicke'    => 'page-augenblicke.php',
    'tagungsbuero'   => 'page-tagungsbuero.php',
    'sozialgremium'  => 'page-sozialgremium.php',
    'shuttleservice' => 'page-shuttleservice.php',
    'workshops'      => 'page-workshops.php',
    'impressum'      => 'page-impressum.php',
    'datenschutz'    => 'page-datenschutz.php',
];

if (isset($page_map[$clean_slug])) {
    include $theme_dir . '/' . $page_map[$clean_slug];
    exit;
}

// 4. Standalone Flarum Forum Preview
if ($clean_slug === 'forum' || str_starts_with($clean_slug, 'forum/')) {
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>de.rec.fotografie &bull; Flarum Community Forum</title>
        <link rel="stylesheet" href="/wp-content/themes/drf-augenblicke/assets/css/theme.css">
        <style>
            body { background: #f3f4f6; margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
            .flarum-mock-nav { background: #141518; height: 60px; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; border-bottom: 1px solid #2a2d34; }
            .flarum-container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
            .flarum-layout { display: grid; grid-template-columns: 240px 1fr; gap: 24px; }
            .flarum-sidebar { display: flex; flex-direction: column; gap: 14px; }
            .btn-start-discussion { background: #f5b301; color: #111417; font-weight: 700; border: none; padding: 12px 18px; border-radius: 8px; text-align: center; cursor: pointer; }
            .tag-nav { list-style: none; padding: 0; margin: 12px 0 0 0; display: flex; flex-direction: column; gap: 4px; }
            .tag-nav a { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 6px; color: #374151; font-size: 0.92rem; text-decoration: none; }
            .tag-nav a.active, .tag-nav a:hover { background: #e5e7eb; font-weight: 600; color: #000; }
            .tag-dot { width: 10px; height: 10px; border-radius: 50%; }
            .discussion-card { background: #ffffff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; }
            .discussion-title { font-weight: 700; font-size: 1.05rem; color: #111827; text-decoration: none; }
            .discussion-title:hover { color: #f5b301; }
            .discussion-sub { font-size: 0.84rem; color: #6b7280; margin-top: 4px; }
            .flarum-top-pill { background: #212328; border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 9999px; padding: 6px 16px; font-size: 0.88rem; text-decoration: none; }
            .flarum-top-pill:hover { background: #f5b301; color: #111; }
        </style>
    </head>
    <body>
        <div class="flarum-mock-nav">
            <div style="display: flex; align-items: center; gap: 14px;">
                <a href="/" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                    <div style="width: 28px; height: 24px; color: #f5b301;">
                        <?php echo drf_get_icon('camera'); ?>
                    </div>
                    <span style="font-weight: 800; color: #fff; font-size: 1.15rem;">AUGENBLICKE</span>
                    <span style="color: #6b7280;">|</span>
                    <span style="color: #f5b301; font-weight: 700;">d.r.f</span>
                    <span style="color: #9ca3af; font-size: 0.82rem;">Forum</span>
                </a>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="/" class="flarum-top-pill">&larr; Zur&uuml;ck zur Website</a>
                <a href="/tagungsbuero/" class="flarum-top-pill">Tagungsb&uuml;ro</a>
            </div>
        </div>

        <div class="flarum-container">
            <div class="flarum-layout">
                <aside class="flarum-sidebar">
                    <button class="btn-start-discussion" onclick="alert('Flarum Forum aktiv! Neue Diskussion starten...');">+ Neue Diskussion</button>
                    <ul class="tag-nav">
                        <li><a href="#" class="active"><span class="tag-dot" style="background:#f5b301;"></span> Alle Diskussionen</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#f5b301;"></span> Augenblicke 2026</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#3b82f6;"></span> Workshops &amp; Sessions</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#10b981;"></span> Analog &amp; SW-Labor</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#8b5cf6;"></span> Kameratechnik &amp; Altglas</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#ec4899;"></span> Bildkritik &amp; Galerien</a></li>
                        <li><a href="#"><span class="tag-dot" style="background:#6b7280;"></span> Usenet Newsgroup Bridge</a></li>
                    </ul>
                </aside>

                <main>
                    <div class="discussion-card">
                        <div>
                            <a href="#" class="discussion-title">Augenblicke XXVI (28.&ndash;31. Mai 2026): Zimmerbuchung &amp; Fahrgemeinschaften</a>
                            <div class="discussion-sub">Erstellt von <strong>Frank_S</strong> &bull; vor 2 Stunden &bull; Tag: <span style="background:#fff8e1; color:#b78103; padding:2px 6px; border-radius:4px; font-weight:600;">Augenblicke 2026</span></div>
                        </div>
                        <div style="font-weight: 700; color: #4b5563;">💬 28</div>
                    </div>

                    <div class="discussion-card">
                        <div>
                            <a href="#" class="discussion-title">Workshop-Vorschlag: Schwarz-Weiß Dunkelkammer &amp; Baryt-Handabzüge</a>
                            <div class="discussion-sub">Erstellt von <strong>SilberKorn</strong> &bull; gestern &bull; Tag: <span style="background:#eff6ff; color:#1d4ed8; padding:2px 6px; border-radius:4px; font-weight:600;">Workshops &amp; Sessions</span></div>
                        </div>
                        <div style="font-weight: 700; color: #4b5563;">💬 14</div>
                    </div>

                    <div class="discussion-card">
                        <div>
                            <a href="#" class="discussion-title">Usenet-Bridge de.rec.fotografie: Synchronisation und Newsreader-Setup</a>
                            <div class="discussion-sub">Erstellt von <strong>Admin_DRF</strong> &bull; vor 3 Tagen &bull; Tag: <span style="background:#f3f4f6; color:#374151; padding:2px 6px; border-radius:4px; font-weight:600;">Usenet Bridge</span></div>
                        </div>
                        <div style="font-weight: 700; color: #4b5563;">💬 42</div>
                    </div>

                    <div class="discussion-card">
                        <div>
                            <a href="#" class="discussion-title">Altglas an Spiegellos: Bokeh-Klassiker Meyer-Optik &amp; Biotar im Vergleich</a>
                            <div class="discussion-sub">Erstellt von <strong>Linsensuppe</strong> &bull; vor 4 Tagen &bull; Tag: <span style="background:#f5f3ff; color:#6d28d9; padding:2px 6px; border-radius:4px; font-weight:600;">Kameratechnik &amp; Altglas</span></div>
                        </div>
                        <div style="font-weight: 700; color: #4b5563;">💬 31</div>
                    </div>
                </main>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 404 fallback
http_response_code(404);
echo "404 - Seite nicht gefunden: " . htmlspecialchars($uri);
