<?php
/**
 * DRF Augenblicke - Theme Option Helpers
 *
 * @package DRF_Augenblicke
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get default theme options array
 */
function drf_get_default_theme_options() {
    $theme_uri = get_template_directory_uri();
    return [
        // 1. Branding & Header
        'logo_text'          => 'AUGENBLICKE',
        'logo_short'         => 'd.r.f',
        'logo_sub'           => 'de.rec.fotografie',
        'logo_image'         => '',
        'accent_color'       => '#f5b301',
        'header_btn_home'    => 'Homepage',
        'header_btn_forum'   => 'Forum – mit Zugang',

        // 2. Hero Section
        'hero_tag'           => 'DAS JÄHRLICHE TREFFEN DER d.r.f.',
        'hero_title'         => 'Willkommen bei Augenblicke.',
        'hero_title_accent'  => 'Augenblicke.',
        'hero_desc'          => 'Wir sind ein eingetragener, gemeinnütziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein – mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.',
        'hero_btn_primary'   => 'Mehr erfahren',
        'hero_btn_forum'     => 'Zum Forum',
        'hero_polaroid_1'    => $theme_uri . '/assets/images/polaroid-dock.jpg',
        'hero_polaroid_2'    => $theme_uri . '/assets/images/polaroid-camera.jpg',
        'hero_badge_image'   => $theme_uri . '/assets/images/badge-treffen.png',

        // 3. Event & Countdown
        'event_badge'        => 'AUGENBLICKE XXVI',
        'event_title'        => '14. – 17. Mai 2027 • Haus Lutherrose, Neuendettelsau',
        'event_sub'          => 'Fronleichnam-Wochenende • Workshops, Fotowalks, Studio & Grillabend',
        'event_target_date'  => '2026-05-28T09:00',
        'event_btn_text'     => 'Zur Anmeldung',
        'event_btn_link'     => '/anmeldung/',

        // 4. Feature Cards
        'card_1_title'       => 'Verein',
        'card_1_desc'        => 'Wer wir sind, wofür wir stehen und was uns als Gemeinschaft ausmacht.',
        'card_1_link'        => '/verein/',
        'card_1_image'       => $theme_uri . '/assets/images/card-verein.jpg',

        'card_2_title'       => 'Workshops',
        'card_2_desc'        => 'Praxisnah, inspirierend und vielfältig – Workshops für verschiedene Fotografie-Bereiche.',
        'card_2_link'        => '/workshops/',
        'card_2_image'       => $theme_uri . '/assets/images/card-workshops.jpg',

        'card_3_title'       => 'Shuttleservice',
        'card_3_desc'        => 'Bequem und unkompliziert – unser Shuttleservice zum Veranstaltungsort.',
        'card_3_link'        => '/shuttleservice/',
        'card_3_image'       => $theme_uri . '/assets/images/card-shuttle.jpg',

        'card_4_title'       => 'Tagungsbüro',
        'card_4_desc'        => 'Dein Ansprechpartner vor Ort – für Anmeldung, Infos und alles rund um das Treffen.',
        'card_4_link'        => '/tagungsbuero/',
        'card_4_image'       => $theme_uri . '/assets/images/card-tagungsbuero.jpg',

        // 5. Flarum Integration
        'flarum_url'         => home_url('/forum/'),
        'flarum_feed_limit'  => 4,
        'flarum_sso_enabled' => 1,
        'flarum_box_title'   => 'Flarum Single Sign-On',
        'flarum_box_desc'    => 'Mit deinem WordPress-Konto hast du automatisch Zugang zum Forum. Keine separate Registrierung erforderlich.',

        // 6. Contact & Footer
        'contact_email'      => 'tagungsbuero2026@d-r-f.de',
        'contact_phone'      => '+49 (0) 9874 12345',
        'contact_address'    => 'de.rec.fotografie e.V., Hauptstraße 1, 91564 Neuendettelsau',
        'footer_copyright'   => 'de.rec.fotografie e.V. – Jahrestreffen »AUGENBLICKE«.',
        'footer_subline'     => 'WordPress & Flarum Community Platform',
    ];
}

/**
 * Retrieve a single theme option with fallback
 *
 * @param string $key Option key
 * @param mixed  $default Optional fallback
 * @return mixed
 */
function drf_get_theme_option($key, $default = null) {
    $options = get_option('drf_theme_options');
    $defaults = drf_get_default_theme_options();

    if ($default === null && isset($defaults[$key])) {
        $default = $defaults[$key];
    }

    if (is_array($options) && isset($options[$key]) && $options[$key] !== '') {
        return $options[$key];
    }

    return $default;
}

/**
 * Ensure all WordPress emails originate from the configured Admin email address
 */
add_filter('wp_mail_from', function($original_email) {
    $admin_email = drf_get_theme_option('contact_email');
    if (!empty($admin_email) && is_email($admin_email)) {
        return $admin_email;
    }
    $wp_admin = get_option('admin_email');
    if (!empty($wp_admin) && is_email($wp_admin)) {
        return $wp_admin;
    }
    return $original_email;
}, 999);

add_filter('wp_mail_from_name', function($original_name) {
    $sender_name = drf_get_theme_option('logo_sub', 'de.rec.fotografie e.V.');
    return !empty($sender_name) ? $sender_name : $original_name;
}, 999);

/**
 * Handle Shuttle Service Reservation Form Submission
 */
function drf_handle_shuttle_submission() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drf_shuttle_nonce'])) {
        if (!wp_verify_nonce($_POST['drf_shuttle_nonce'], 'drf_shuttle_action')) {
            wp_die(__('Sicherheitsprüfung fehlgeschlagen.', 'drf-augenblicke'));
        }

        $name    = sanitize_text_field($_POST['shuttle_name'] ?? '');
        $email   = sanitize_email($_POST['shuttle_email'] ?? '');
        $station = sanitize_text_field($_POST['station'] ?? 'wicklesgreuth');
        $time    = sanitize_text_field($_POST['arrival_time'] ?? '');
        $luggage = sanitize_text_field($_POST['luggage_note'] ?? '');

        $redirect_base = wp_get_referer() ?: home_url('/shuttleservice/');

        if (empty($name) || empty($email) || !is_email($email)) {
            wp_safe_redirect(add_query_arg('shuttle_status', 'error', $redirect_base));
            exit;
        }

        // Save booking to option
        $bookings = get_option('drf_shuttle_bookings', []);
        if (!is_array($bookings)) {
            $bookings = [];
        }
        $station_label = ($station === 'ansbach') ? 'Hauptbahnhof Ansbach' : 'Bahnhof Wicklesgreuth';
        $bookings[] = [
            'name'       => $name,
            'email'      => $email,
            'station'    => $station_label,
            'time'       => $time,
            'luggage'    => $luggage,
            'date'       => current_time('mysql'),
        ];
        update_option('drf_shuttle_bookings', $bookings);

        // Send Email Confirmation to User
        $admin_email   = function_exists('drf_get_theme_option') ? drf_get_theme_option('contact_email', get_option('admin_email')) : get_option('admin_email');
        $headers       = ['From: de.rec.fotografie e.V. <' . $admin_email . '>'];

        $user_subject  = '[d.r.f] Bestätigung deiner Shuttle-Anfrage (AUGENBLICKE 2026)';
        $user_msg      = "Hallo {$name},\n\nvielen Dank für deine Shuttle-Reservierung für das Jahrestreffen AUGENBLICKE 2026!\n\n";
        $user_msg     .= "Deine Angaben:\n";
        $user_msg     .= "- Name: {$name}\n";
        $user_msg     .= "- E-Mail: {$email}\n";
        $user_msg     .= "- Abholstation: {$station_label}\n";
        if (!empty($time)) $user_msg .= "- Geplante Ankunftszeit: {$time}\n";
        if (!empty($luggage)) $user_msg .= "- Besonderes Gepäck: {$luggage}\n";
        $user_msg     .= "\nUnser ehrenamtlicher Shuttle-Fahrer meldet sich wenige Tage vor dem Treffen mit der Telefonnummer des Fahrers und den exakten Details.\n\nHerzliche Grüße,\nDein d.r.f Tagungsbüro";

        @wp_mail($email, $user_subject, $user_msg, $headers);

        // Admin notification
        $admin_subj = sprintf('[d.r.f] Neue Shuttle-Reservierung: %s (%s)', $name, $station_label);
        $admin_msg  = "Hallo Admin,\n\nes ist eine neue Shuttle-Reservierung eingegangen:\n\n";
        $admin_msg .= "- Name: {$name} ({$email})\n";
        $admin_msg .= "- Bahnhof: {$station_label}\n";
        if (!empty($time)) $admin_msg .= "- Zeit: {$time}\n";
        if (!empty($luggage)) $admin_msg .= "- Gepäck: {$luggage}\n";
        $admin_msg .= "\nGesamte Shuttle-Reservierungen: " . count($bookings);

        @wp_mail($admin_email, $admin_subj, $admin_msg, $headers);

        wp_safe_redirect(add_query_arg('shuttle_status', 'success', $redirect_base));
        exit;
    }
}
add_action('template_redirect', 'drf_handle_shuttle_submission');
