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
        'event_title'        => '28. – 31. Mai 2026 • Haus Lutherrose, Neuendettelsau',
        'event_sub'          => 'Fronleichnam-Wochenende • Workshops, Fotowalks, Studio & Grillabend',
        'event_target_date'  => '2026-05-28T09:00',
        'event_btn_text'     => 'Zur Anmeldung',
        'event_btn_link'     => '/tagungsbuero/',

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
