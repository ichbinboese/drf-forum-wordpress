<?php
/**
 * Main Elementor Integration Class for DRF Augenblicke Theme
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Elementor_Bootstrap {

    private static $instance = null;

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {
        add_action('elementor/elements/categories_registered', [$this, 'register_categories']);
        add_action('elementor/widgets/register', [$this, 'register_widgets']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_assets']);

        // Register Elementor Template / Snippet Shortcodes
        $this->register_snippet_shortcodes();
    }

    /**
     * Register [elementor-template id="..."] and [drf_snippet id="..." name="..."]
     */
    public function register_snippet_shortcodes() {
        if (!shortcode_exists('elementor-template')) {
            add_shortcode('elementor-template', function ($atts) {
                $atts = shortcode_atts(['id' => ''], $atts, 'elementor-template');
                if (empty($atts['id']) || !class_exists('\Elementor\Plugin')) {
                    return '';
                }
                return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display(absint($atts['id']));
            });
        }

        if (!shortcode_exists('drf_snippet')) {
            add_shortcode('drf_snippet', function ($atts) {
                $atts = shortcode_atts([
                    'id'   => '',
                    'name' => '',
                    'slug' => '',
                ], $atts, 'drf_snippet');

                $post_id = 0;
                if (!empty($atts['id'])) {
                    $post_id = absint($atts['id']);
                } elseif (!empty($atts['slug'])) {
                    $post = get_page_by_path($atts['slug'], OBJECT, 'elementor_library');
                    if ($post) $post_id = $post->ID;
                } elseif (!empty($atts['name'])) {
                    $post = get_page_by_title($atts['name'], OBJECT, 'elementor_library');
                    if ($post) $post_id = $post->ID;
                }

                if (!$post_id || !class_exists('\Elementor\Plugin')) {
                    return '';
                }

                return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display($post_id);
            });
        }
    }

    /**
     * Register Custom Elementor Category: drf-widgets
     */
    public function register_categories($elements_manager) {
        $elements_manager->add_category(
            'drf-widgets',
            [
                'title' => __('d.r.f Augenblicke Snippets & Widgets', 'drf-augenblicke'),
                'icon'  => 'fa fa-camera-retro',
            ]
        );
    }

    /**
     * Register All Custom DRF Widgets
     */
    public function register_widgets($widgets_manager) {
        $widgets_dir = __DIR__ . '/widgets/';

        $widgets = [
            'class-widget-hero.php'           => 'DRF_Elementor\Widgets\DRF_Hero_Widget',
            'class-widget-cards.php'          => 'DRF_Elementor\Widgets\DRF_Feature_Cards_Widget',
            'class-widget-event-banner.php'   => 'DRF_Elementor\Widgets\DRF_Event_Banner_Widget',
            'class-widget-countdown.php'      => 'DRF_Elementor\Widgets\DRF_Countdown_Widget',
            'class-widget-timeline.php'       => 'DRF_Elementor\Widgets\DRF_Timeline_Widget',
            'class-widget-gallery.php'        => 'DRF_Elementor\Widgets\DRF_Gallery_Widget',
            'class-widget-anmeldung.php'      => 'DRF_Elementor\Widgets\DRF_Anmeldung_Widget',
            'class-widget-chronik.php'        => 'DRF_Elementor\Widgets\DRF_Chronik_Widget',
            'class-widget-tagungsbuero.php'   => 'DRF_Elementor\Widgets\DRF_Tagungsbuero_Widget',
            'class-widget-shuttle.php'        => 'DRF_Elementor\Widgets\DRF_Shuttle_Widget',
            'class-widget-shuttle-route.php'  => 'DRF_Elementor\Widgets\DRF_Shuttle_Route_Widget',
            'class-widget-sozial.php'         => 'DRF_Elementor\Widgets\DRF_Sozial_Widget',
            'class-widget-workshops.php'      => 'DRF_Elementor\Widgets\DRF_Workshops_Widget',
            'class-widget-verein.php'         => 'DRF_Elementor\Widgets\DRF_Verein_Widget',
            'class-widget-flarum.php'         => 'DRF_Elementor\Widgets\DRF_Flarum_Feed_Widget',
            'class-widget-template-embed.php' => 'DRF_Elementor\Widgets\DRF_Template_Embed_Widget',
        ];

        foreach ($widgets as $file => $class) {
            if (file_exists($widgets_dir . $file)) {
                require_once $widgets_dir . $file;
                if (class_exists($class)) {
                    $widgets_manager->register(new $class());
                }
            }
        }
    }

    /**
     * Enqueue Editor Styles
     */
    public function enqueue_editor_assets() {
        wp_enqueue_style(
            'drf-elementor-editor',
            DRF_THEME_URI . '/assets/css/theme.css',
            [],
            DRF_THEME_VERSION
        );
    }
}

// Initialize immediately or on after_setup_theme
add_action('after_setup_theme', function () {
    if (did_action('elementor/loaded')) {
        DRF_Elementor_Bootstrap::instance();
    }
});

// Also initialize if after_setup_theme already passed
if (did_action('after_setup_theme') && did_action('elementor/loaded')) {
    DRF_Elementor_Bootstrap::instance();
}
