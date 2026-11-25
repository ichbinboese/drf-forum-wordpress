<?php
/**
 * DRF Augenblicke Theme Functions & Definitions
 *
 * @package DRF_Augenblicke
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('DRF_THEME_VERSION', '1.4.1');
define('DRF_THEME_DIR', get_template_directory());
define('DRF_THEME_URI', get_template_directory_uri());

// Include Theme Helpers & Administration
require_once DRF_THEME_DIR . '/inc/helpers.php';
require_once DRF_THEME_DIR . '/inc/admin-options.php';
require_once DRF_THEME_DIR . '/inc/workshops.php';

// Include Elementor Integration & Custom Widgets
if (file_exists(DRF_THEME_DIR . '/inc/elementor/class-drf-elementor.php')) {
    require_once DRF_THEME_DIR . '/inc/elementor/class-drf-elementor.php';
}

/**
 * 1. Theme Setup
 */
function drf_theme_setup() {
    // Make theme available for translation
    load_theme_textdomain('drf-augenblicke', DRF_THEME_DIR . '/languages');

    // Add default posts and comments RSS feed links to head.
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title.
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    add_theme_support('post-thumbnails');

    // Enable support for Elementor Page Builder
    add_theme_support('elementor');

    // Register primary navigation menu
    register_nav_menus([
        'primary-menu' => __('Hauptnavigation (Desktop & Mobil)', 'drf-augenblicke'),
        'footer-menu'  => __('Footer Navigation', 'drf-augenblicke'),
    ]);

    // HTML5 markup support
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
}
add_action('after_setup_theme', 'drf_theme_setup');

/**
 * 2. Enqueue Styles & Scripts
 */
function drf_enqueue_assets() {
    // Theme stylesheet
    wp_enqueue_style(
        'drf-theme-styles',
        DRF_THEME_URI . '/assets/css/theme.css',
        [],
        DRF_THEME_VERSION
    );

    // Dynamic accent color support
    $accent = drf_get_theme_option('accent_color', '#f5b301');
    if ($accent && $accent !== '#f5b301') {
        $custom_css = ":root { --color-primary: {$accent}; --color-primary-hover: {$accent}; }";
        wp_add_inline_style('drf-theme-styles', $custom_css);
    }

    // Main JavaScript
    wp_enqueue_script(
        'drf-theme-script',
        DRF_THEME_URI . '/assets/js/main.js',
        [],
        DRF_THEME_VERSION,
        true
    );

    // Pass countdown target date to JavaScript
    $target_date = drf_get_theme_option('event_target_date', '2026-05-28T09:00');
    wp_localize_script('drf-theme-script', 'drfThemeData', [
        'targetDate' => $target_date,
        'homeUrl'    => home_url('/'),
        'forumUrl'   => drf_get_forum_url(),
    ]);
}
add_action('wp_enqueue_scripts', 'drf_enqueue_assets');

/**
 * 3. Helper: Get Flarum Forum URL
 */
function drf_get_forum_url() {
    $forum_url = drf_get_theme_option('flarum_url');
    if (!empty($forum_url)) {
        return esc_url($forum_url);
    }
    return home_url('/forum/');
}

/**
 * 4. Helper: Render SVG Icons
 */
function drf_get_icon($name) {
    switch ($name) {
        case 'camera':
            return '<svg viewBox="0 0 48 38" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 6L17 2H31L34 6H42C44.2091 6 46 7.79086 46 10V32C46 34.2091 44.2091 36 42 36H6C3.79086 36 2 34.2091 2 32V10C2 7.79086 3.79086 6 6 6H14Z" />
                <circle cx="24" cy="20" r="8" />
                <circle cx="24" cy="20" r="3" fill="currentColor" />
            </svg>';
        case 'home':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>';
        case 'lock':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>';
        case 'users':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>';
        case 'bus':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="14" rx="2"></rect>
                <path d="M3 10h18"></path>
                <circle cx="7" cy="18" r="2"></circle>
                <circle cx="17" cy="18" r="2"></circle>
            </svg>';
        case 'calendar':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>';
        case 'chevron-right':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>';
        case 'menu':
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>';
        default:
            return '';
    }
}

/**
 * 5. Auto-Create Pages on Activation
 */
function drf_create_default_pages() {
    $pages = [
        'verein' => [
            'title'    => 'Verein',
            'template' => 'page-verein.php',
            'content'  => 'Informationen zum gemeinnützigen Verein de.rec.fotografie e.V.',
        ],
        'augenblicke' => [
            'title'    => 'Augenblicke',
            'template' => 'page-augenblicke.php',
            'content'  => 'Das jährliche Treffen der traditionsreichen Fotografie-Community.',
        ],
        'anmeldung' => [
            'title'    => 'Anmeldung',
            'template' => 'template-elementor-fullwidth.php',
            'content'  => 'Verbindliche Anmeldung und Zimmerbuchung für das Augenblicke-Treffen.',
        ],
        'tagungsbuero' => [
            'title'    => 'Tagungsbüro',
            'template' => 'template-elementor-fullwidth.php',
            'content'  => 'Organisation, Betreuung und Zimmerverwaltung für das Jahrestreffen.',
        ],
        'sozialgremium' => [
            'title'    => 'Sozialgremium',
            'template' => 'page-sozialgremium.php',
            'content'  => 'Vertrauliche Unterstützung und Hilfsfonds der Community.',
        ],
        'shuttleservice' => [
            'title'    => 'Shuttleservice',
            'template' => 'page-shuttleservice.php',
            'content'  => 'Fahrpläne und Shuttleservice vom Bahnhof zum Tagungshaus.',
        ],
        'workshops' => [
            'title'    => 'Workshops',
            'template' => 'page-workshops.php',
            'content'  => 'Workshops, Sessions, Fotowalks und Praxisseminare.',
        ],
        'impressum' => [
            'title'    => 'Impressum',
            'template' => 'page-impressum.php',
            'content'  => 'Gesetzliche Pflichtangaben nach § 5 TMG.',
        ],
        'datenschutz' => [
            'title'    => 'Datenschutz',
            'template' => 'page-datenschutz.php',
            'content'  => 'Datenschutzerklärung gemäß DSGVO.',
        ],
    ];

    foreach ($pages as $slug => $data) {
        $existing = get_page_by_path($slug);
        if (!$existing) {
            $page_id = wp_insert_post([
                'post_title'     => $data['title'],
                'post_name'      => $slug,
                'post_content'   => $data['content'],
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ]);
            if ($page_id && !empty($data['template'])) {
                update_post_meta($page_id, '_wp_page_template', $data['template']);
            }
        }
    }
}
add_action('after_switch_theme', 'drf_create_default_pages');

/**
 * Modern Favicon & App Icon Integration
 */
function drf_add_favicons() {
    $home_url = home_url('/');
    ?>
    <!-- DRF Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="<?php echo esc_url($home_url . 'favicon.svg'); ?>">
    <link rel="alternate icon" type="image/x-icon" href="<?php echo esc_url($home_url . 'favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url($home_url . 'favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url($home_url . 'favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url($home_url . 'apple-touch-icon.png'); ?>">
    <link rel="manifest" href="<?php echo esc_url($home_url . 'site.webmanifest'); ?>">
    <meta name="theme-color" content="#141518">
    <meta name="msapplication-TileColor" content="#141518">
    <?php
}
add_action('wp_head', 'drf_add_favicons', 1);
add_action('admin_head', 'drf_add_favicons', 1);
add_action('login_head', 'drf_add_favicons', 1);

/**
 * Unified Page Subheading Helper (H2 Lead Text)
 */
function drf_get_page_subheading($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    if (!$post_id) return '';

    $custom = get_post_meta($post_id, '_drf_page_subheading', true);
    if (!empty($custom)) {
        return $custom;
    }

    $post = get_post($post_id);
    if (!$post) return '';

    $slug = $post->post_name;
    $map = [
        'startseite'     => 'Das alljährliche Treffen von de.rec.fotografie – Fotografie, Workshops und kollegialer Austausch seit 1999.',
        'anmeldung'      => 'Verbindliche Anmeldung und Zimmerreservierung für das AUGENBLICKE-Jahrestreffen.',
        'tagungsbuero'   => 'Organisation, Betreuung, Zimmerverwaltung und zentrale Anlaufstelle für das Jahrestreffen.',
        'sozialgremium'  => 'Solidarität und Vertraulichkeit: Niemand soll aus finanziellen Gründen auf die Teilnahme verzichten müssen.',
        'augenblicke'    => 'Vier Tage Fotografie, intensive Workshops, Studioaufbauten und legendäre Abende unter Gleichgesinnten.',
        'workshops'      => 'Praxisnah, inspirierend und vielfältig: Workshops von Mitgliedern für Mitglieder in entspannter Atmosphäre.',
        'shuttleservice' => 'Bequem und unkompliziert: Kostenlose Abholung vom Bahnhof direkt zum Haus.',
        'verein'         => 'Wer wir sind, wofür wir stehen und was uns seit über zwei Jahrzehnten als lebendige Fotogemeinschaft ausmacht.',
        'impressum'      => 'Gesetzliche Anbieterkennzeichnung und Angaben gemäß § 5 TMG des de.rec.fotografie e.V.',
        'datenschutz'    => 'Transparente Informationen über die Erhebung und Verarbeitung personenbezogener Daten gemäß DSGVO.',
    ];

    return $map[$slug] ?? '';
}


