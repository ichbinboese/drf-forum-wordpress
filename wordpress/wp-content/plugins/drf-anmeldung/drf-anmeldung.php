<?php
/**
 * Plugin Name: DRF Treffen-Anmeldung
 * Plugin URI: https://github.com/ichbinboese/drf-forum-wordpress
 * Description: Professionelles Anmelde- und Verwaltungssystem für die Jahrestreffen von de.rec.fotografie (z.B. Augenblicke 2026, 2027 in Neuendettelsau). Mit Zimmerkategorien, Verpflegung, Workshops, Shuttleservice, freiwilliger Sozialspende („Sozialsowjet“) und CSV-Export für das Tagungsbüro.
 * Version: 1.0.0
 * Author: de.rec.fotografie e.V. / Benjamin Böse
 * Author URI: https://www.d-r-f.de/augenblicke/
 * Text Domain: drf-anmeldung
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DRF_ANMELDUNG_VERSION', '1.0.0');
define('DRF_ANMELDUNG_PATH', plugin_dir_path(__FILE__));
define('DRF_ANMELDUNG_URL', plugin_dir_url(__FILE__));

// Require plugin modules
require_once DRF_ANMELDUNG_PATH . 'includes/class-meeting-manager.php';
require_once DRF_ANMELDUNG_PATH . 'includes/class-registration-form.php';
require_once DRF_ANMELDUNG_PATH . 'includes/class-admin-dashboard.php';

/**
 * Plugin Activation: Create database tables & seed initial meetings
 */
function drf_anmeldung_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // 1. Table: Meetings (z.B. 2026, 2027 in Neuendettelsau)
    $table_meetings = $wpdb->prefix . 'drf_meetings';
    $sql_meetings = "CREATE TABLE $table_meetings (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        slug varchar(64) NOT NULL,
        title varchar(255) NOT NULL,
        year int(4) NOT NULL,
        location varchar(255) NOT NULL,
        location_details text NULL,
        start_date date NOT NULL,
        end_date date NOT NULL,
        price_ez decimal(8,2) DEFAULT '0.00' NOT NULL,
        price_dz decimal(8,2) DEFAULT '0.00' NOT NULL,
        price_mbz decimal(8,2) DEFAULT '0.00' NOT NULL,
        price_day decimal(8,2) DEFAULT '0.00' NOT NULL,
        status varchar(32) DEFAULT 'active' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY slug (slug)
    ) $charset_collate;";
    dbDelta($sql_meetings);

    // 2. Table: Registrations
    $table_registrations = $wpdb->prefix . 'drf_registrations';
    $sql_registrations = "CREATE TABLE $table_registrations (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        meeting_id mediumint(9) NOT NULL,
        user_id mediumint(9) NULL,
        firstname varchar(100) NOT NULL,
        lastname varchar(100) NOT NULL,
        nickname varchar(100) DEFAULT '' NOT NULL,
        email varchar(191) NOT NULL,
        phone varchar(100) DEFAULT '' NOT NULL,
        street varchar(255) DEFAULT '' NOT NULL,
        zip_city varchar(255) DEFAULT '' NOT NULL,
        participation_type varchar(50) DEFAULT 'full' NOT NULL,
        room_category varchar(50) DEFAULT 'ez' NOT NULL,
        room_partner varchar(255) DEFAULT '' NOT NULL,
        diet varchar(50) DEFAULT 'omnivore' NOT NULL,
        diet_allergies text NULL,
        workshop_interest text NULL,
        workshop_offer varchar(255) DEFAULT '' NOT NULL,
        workshop_offer_details text NULL,
        shuttle_needed tinyint(1) DEFAULT 0 NOT NULL,
        shuttle_station varchar(100) DEFAULT '' NOT NULL,
        shuttle_arrival varchar(100) DEFAULT '' NOT NULL,
        shuttle_luggage varchar(100) DEFAULT '' NOT NULL,
        social_donation decimal(8,2) DEFAULT '0.00' NOT NULL,
        total_amount decimal(8,2) DEFAULT '0.00' NOT NULL,
        notes text NULL,
        status varchar(32) DEFAULT 'new' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        KEY meeting_id (meeting_id),
        KEY email (email)
    ) $charset_collate;";
    dbDelta($sql_registrations);

    // 3. Seed default meetings (2026 & 2027 Neuendettelsau)
    $existing_2026 = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_meetings WHERE slug = %s", 'augenblicke-2026'));
    if (!$existing_2026) {
        $wpdb->insert($table_meetings, [
            'slug'             => 'augenblicke-2026',
            'title'            => 'AUGENBLICKE XXVI (2026)',
            'year'             => 2026,
            'location'         => 'Haus Lutherrose, Neuendettelsau',
            'location_details' => 'Tagungshaus mit Studio-Räumen, Dunkelkammer-Möglichkeit, Grillplatz und herrlicher fränkischer Umgebung.',
            'start_date'       => '2026-05-28',
            'end_date'         => '2026-05-31',
            'price_ez'         => 240.00,
            'price_dz'         => 195.00,
            'price_mbz'        => 160.00,
            'price_day'        => 60.00,
            'status'           => 'active',
        ]);
    }

    $existing_2027 = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_meetings WHERE slug = %s", 'augenblicke-2027'));
    if (!$existing_2027) {
        $wpdb->insert($table_meetings, [
            'slug'             => 'augenblicke-2027',
            'title'            => 'AUGENBLICKE XXVII (2027)',
            'year'             => 2027,
            'location'         => 'Haus Lutherrose, Neuendettelsau',
            'location_details' => 'Auch 2027 lädt de.rec.fotografie e.V. wieder zum traditionellen Jahrestreffen nach Neuendettelsau ein.',
            'start_date'       => '2027-05-27',
            'end_date'         => '2027-05-30',
            'price_ez'         => 250.00,
            'price_dz'         => 205.00,
            'price_mbz'        => 170.00,
            'price_day'        => 65.00,
            'status'           => 'planning',
        ]);
    }
}
register_activation_hook(__FILE__, 'drf_anmeldung_activate');

/**
 * Initialize Plugin Components
 */
function drf_anmeldung_init() {
    new DRF_Meeting_Manager();
    new DRF_Registration_Form();
    if (is_admin()) {
        new DRF_Admin_Dashboard();
    }
}
add_action('plugins_loaded', 'drf_anmeldung_init');
