<?php
/**
 * DRF Treffen-Anmeldung - Meeting Manager
 *
 * @package DRF_Anmeldung
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Meeting_Manager {

    public function __construct() {
        // Can hook any meeting logic here
    }

    /**
     * Get meeting by ID or slug
     */
    public static function get_meeting($id_or_slug) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_meetings';

        if (is_numeric($id_or_slug)) {
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id_or_slug));
        } else {
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE slug = %s", $id_or_slug));
        }
    }

    /**
     * Get the currently active meeting (e.g. 2026 or 2027)
     */
    public static function get_active_meeting() {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_meetings';
        $active = $wpdb->get_row("SELECT * FROM $table WHERE status = 'active' ORDER BY year ASC LIMIT 1");
        if (!$active) {
            $active = $wpdb->get_row("SELECT * FROM $table ORDER BY year ASC LIMIT 1");
        }
        return $active;
    }

    /**
     * Get all meetings
     */
    public static function get_all_meetings() {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_meetings';
        return $wpdb->get_results("SELECT * FROM $table ORDER BY year DESC");
    }

    /**
     * Save / Update a meeting
     */
    public static function save_meeting($data, $id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_meetings';

        $record = [
            'slug'             => sanitize_title($data['slug'] ?? 'augenblicke-' . ($data['year'] ?? date('Y'))),
            'title'            => sanitize_text_field($data['title']),
            'year'             => absint($data['year']),
            'location'         => sanitize_text_field($data['location']),
            'location_details' => sanitize_textarea_field($data['location_details'] ?? ''),
            'start_date'       => sanitize_text_field($data['start_date']),
            'end_date'         => sanitize_text_field($data['end_date']),
            'price_ez'         => floatval($data['price_ez'] ?? 0),
            'price_dz'         => floatval($data['price_dz'] ?? 0),
            'price_mbz'        => floatval($data['price_mbz'] ?? 0),
            'price_day'        => floatval($data['price_day'] ?? 0),
            'status'           => sanitize_text_field($data['status'] ?? 'active'),
        ];

        if ($id) {
            $wpdb->update($table, $record, ['id' => absint($id)]);
            return $id;
        } else {
            $wpdb->insert($table, $record);
            return $wpdb->insert_id;
        }
    }

    /**
     * Delete a meeting
     */
    public static function delete_meeting($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_meetings';
        return $wpdb->delete($table, ['id' => absint($id)]);
    }

    /**
     * Get registration count for a meeting
     */
    public static function get_registration_count($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'drf_registrations';
        return (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE meeting_id = %d", absint($id)));
    }
}
