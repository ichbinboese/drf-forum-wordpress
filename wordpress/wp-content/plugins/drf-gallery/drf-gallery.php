<?php
/**
 * Plugin Name: DRF Foto-Alben & Galerien
 * Plugin URI:  https://drf-augenblicke.de
 * Description: Professionelle Fotogalerien mit Drag & Drop Upload, visueller Drag & Drop Bildersortierung im WordPress-Admin, modernem Grid/Masonry/Polaroid Layout, Tastatur-Lightbox und Elementor Widget.
 * Version:     1.0.0
 * Author:      de.rec.fotografie e.V.
 * Text Domain: drf-gallery
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DRF_GALLERY_VERSION', '1.0.0');
define('DRF_GALLERY_PATH', plugin_dir_path(__FILE__));
define('DRF_GALLERY_URL', plugin_dir_url(__FILE__));
define('DRF_GALLERY_FILE', __FILE__);

// Core Includes
require_once DRF_GALLERY_PATH . 'includes/class-gallery-db.php';
require_once DRF_GALLERY_PATH . 'includes/class-gallery-admin.php';
require_once DRF_GALLERY_PATH . 'includes/class-gallery-frontend.php';

/**
 * Plugin Activation
 */
function drf_gallery_activate() {
    DRF_Gallery_DB::create_tables();
}
register_activation_hook(__FILE__, 'drf_gallery_activate');

/**
 * Initialize Plugin Components
 */
function drf_gallery_init() {
    // Database tables verify on init if not yet run
    if (is_admin()) {
        DRF_Gallery_DB::create_tables();
        DRF_Gallery_Admin::get_instance();
    }
    DRF_Gallery_Frontend::get_instance();
}
add_action('plugins_loaded', 'drf_gallery_init');

/**
 * Register Elementor Widget
 */
function drf_gallery_register_elementor_widget($widgets_manager) {
    if (did_action('elementor/loaded')) {
        require_once DRF_GALLERY_PATH . 'includes/class-elementor-gallery-widget.php';
        if (class_exists('DRF_Elementor_Album_Widget')) {
            $widgets_manager->register(new \DRF_Elementor_Album_Widget());
        }
    }
}
add_action('elementor/widgets/register', 'drf_gallery_register_elementor_widget');
