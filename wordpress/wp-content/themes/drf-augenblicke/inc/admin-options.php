<?php
/**
 * DRF Augenblicke - Theme Administration Panel
 *
 * @package DRF_Augenblicke
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Register Theme Options Menu in WP-Admin
 */
function drf_register_admin_menu() {
    add_menu_page(
        __('Augenblicke Optionen', 'drf-augenblicke'),
        __('Augenblicke Theme', 'drf-augenblicke'),
        'manage_options',
        'drf-theme-options',
        'drf_render_admin_options_page',
        'dashicons-camera',
        59
    );
}
add_action('admin_menu', 'drf_register_admin_menu');

/**
 * 2. Add Admin Bar Shortcut
 */
function drf_admin_bar_menu($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    $wp_admin_bar->add_node([
        'id'    => 'drf-theme-options',
        'title' => '<span class="ab-icon dashicons dashicons-camera" style="top:2px;"></span>' . __('Augenblicke Optionen', 'drf-augenblicke'),
        'href'  => admin_url('admin.php?page=drf-theme-options'),
    ]);
}
add_action('admin_bar_menu', 'drf_admin_bar_menu', 100);

/**
 * 3. Enqueue Admin Assets (Media Uploader, Tabs, Styling)
 */
function drf_enqueue_admin_assets($hook) {
    if ($hook !== 'toplevel_page_drf-theme-options') {
        return;
    }

    // WordPress Media Uploader Scripts & Styles
    wp_enqueue_media();

    // Theme Admin CSS
    wp_enqueue_style(
        'drf-admin-options-css',
        DRF_THEME_URI . '/assets/css/admin-options.css',
        [],
        DRF_THEME_VERSION
    );

    // Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');

    // Theme Admin JS
    wp_enqueue_script(
        'drf-admin-options-js',
        DRF_THEME_URI . '/assets/js/admin-options.js',
        ['jquery', 'wp-color-picker'],
        DRF_THEME_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'drf_enqueue_admin_assets');

/**
 * 4. Handle Saving Theme Options
 */
function drf_handle_admin_options_save() {
    if (!isset($_POST['drf_save_theme_options']) && !isset($_POST['drf_reset_theme_options'])) {
        return;
    }

    if (!current_user_can('manage_options')) {
        wp_die(__('Sie haben keine Berechtigung, diese Optionen zu bearbeiten.', 'drf-augenblicke'));
    }

    check_admin_referer('drf_theme_options_nonce', 'drf_theme_options_nonce_field');

    // Handle Reset
    if (isset($_POST['drf_reset_theme_options'])) {
        delete_option('drf_theme_options');
        wp_redirect(add_query_arg(['page' => 'drf-theme-options', 'status' => 'reset'], admin_url('admin.php')));
        exit;
    }

    // Handle Save
    $raw_inputs = isset($_POST['drf_options']) ? (array)$_POST['drf_options'] : [];
    $sanitized = [];

    foreach ($raw_inputs as $key => $val) {
        $key = sanitize_key($key);
        if (in_array($key, ['hero_desc', 'card_1_desc', 'card_2_desc', 'card_3_desc', 'card_4_desc', 'flarum_box_desc', 'footer_copyright', 'footer_subline'], true)) {
            $sanitized[$key] = wp_kses_post($val);
        } elseif (in_array($key, ['hero_polaroid_1', 'hero_polaroid_2', 'hero_badge_image', 'card_1_image', 'card_2_image', 'card_3_image', 'card_4_image', 'flarum_url', 'logo_image'], true)) {
            $sanitized[$key] = esc_url_raw($val);
        } elseif ($key === 'contact_email') {
            $sanitized[$key] = sanitize_email($val);
        } elseif ($key === 'flarum_feed_limit') {
            $sanitized[$key] = absint($val);
        } else {
            $sanitized[$key] = sanitize_text_field($val);
        }
    }

    update_option('drf_theme_options', $sanitized);

    // Also sync Flarum URL if provided
    if (!empty($sanitized['flarum_url'])) {
        update_option('drf_flarum_url', $sanitized['flarum_url']);
    }

    wp_redirect(add_query_arg(['page' => 'drf-theme-options', 'status' => 'saved'], admin_url('admin.php')));
    exit;
}
add_action('admin_init', 'drf_handle_admin_options_save');

/**
 * 5. Render Admin Options Page
 */
function drf_render_admin_options_page() {
    $options = get_option('drf_theme_options', []);
    $defaults = drf_get_default_theme_options();
    $values = wp_parse_args($options, $defaults);
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    ?>
    <div class="wrap drf-admin-wrap">
        <header class="drf-admin-header">
            <div class="drf-admin-header-brand">
                <span class="dashicons dashicons-camera drf-admin-logo-icon"></span>
                <div>
                    <h1 class="drf-admin-title">AUGENBLICKE &bull; Theme Administration</h1>
                    <p class="drf-admin-subtitle">Professionelle Template-Steuerung f&uuml;r de.rec.fotografie e.V. &amp; Flarum Forum</p>
                </div>
            </div>
            <div class="drf-admin-header-actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="button button-secondary">
                    <span class="dashicons dashicons-external" style="margin-top:4px;"></span> Website &ouml;ffnen
                </a>
                <a href="<?php echo esc_url(drf_get_forum_url()); ?>" target="_blank" class="button button-secondary">
                    <span class="dashicons dashicons-format-chat" style="margin-top:4px;"></span> Forum &ouml;ffnen
                </a>
            </div>
        </header>

        <?php if ($status === 'saved') : ?>
            <div class="notice notice-success is-dismissible">
                <p><strong><?php esc_html_e('Erfolg:', 'drf-augenblicke'); ?></strong> <?php esc_html_e('Die Theme-Optionen wurden erfolgreich gespeichert.', 'drf-augenblicke'); ?></p>
            </div>
        <?php elseif ($status === 'reset') : ?>
            <div class="notice notice-info is-dismissible">
                <p><?php esc_html_e('Alle Optionen wurden auf die Standardwerte zurückgesetzt.', 'drf-augenblicke'); ?></p>
            </div>
        <?php endif; ?>

        <form method="post" action="" id="drfThemeOptionsForm">
            <?php wp_nonce_field('drf_theme_options_nonce', 'drf_theme_options_nonce_field'); ?>

            <div class="drf-admin-container">
                <!-- Navigation Tabs -->
                <nav class="drf-admin-nav" role="tablist">
                    <button type="button" class="drf-nav-item active" data-tab="tab-branding">
                        <span class="dashicons dashicons-art"></span> Branding &amp; Header
                    </button>
                    <button type="button" class="drf-nav-item" data-tab="tab-hero">
                        <span class="dashicons dashicons-slides"></span> Hero &amp; Startseite
                    </button>
                    <button type="button" class="drf-nav-item" data-tab="tab-event">
                        <span class="dashicons dashicons-calendar-alt"></span> Treffen &amp; Countdown
                    </button>
                    <button type="button" class="drf-nav-item" data-tab="tab-cards">
                        <span class="dashicons dashicons-grid-view"></span> 4 Feature-Karten
                    </button>
                    <button type="button" class="drf-nav-item" data-tab="tab-flarum">
                        <span class="dashicons dashicons-format-chat"></span> Flarum Forum Bridge
                    </button>
                    <button type="button" class="drf-nav-item" data-tab="tab-footer">
                        <span class="dashicons dashicons-admin-generic"></span> Kontakt &amp; Footer
                    </button>
                </nav>

                <!-- Tab Contents -->
                <div class="drf-admin-content">

                    <!-- TAB 1: BRANDING & HEADER -->
                    <section class="drf-tab-pane active" id="tab-branding">
                        <div class="drf-pane-header">
                            <h2>Branding &amp; Header Navigation</h2>
                            <p>Hier definieren Sie die Logo-Texte, Farben und die Aktionen in der oberen schwarzen Leiste.</p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="logo_text">Haupt-Logo Text</label></th>
                                <td>
                                    <input type="text" id="logo_text" name="drf_options[logo_text]" value="<?php echo esc_attr($values['logo_text']); ?>" class="regular-text">
                                    <p class="description">Standard: AUGENBLICKE</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="logo_short">K&uuml;rzel &bull; Slogan</label></th>
                                <td>
                                    <input type="text" id="logo_short" name="drf_options[logo_short]" value="<?php echo esc_attr($values['logo_short']); ?>" style="width: 100px;">
                                    <input type="text" id="logo_sub" name="drf_options[logo_sub]" value="<?php echo esc_attr($values['logo_sub']); ?>" class="regular-text">
                                    <p class="description">K&uuml;rzel (z.B. d.r.f) und Untertitel (z.B. de.rec.fotografie)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="accent_color">Akzentfarbe</label></th>
                                <td>
                                    <input type="text" id="accent_color" name="drf_options[accent_color]" value="<?php echo esc_attr($values['accent_color']); ?>" class="drf-color-picker">
                                    <p class="description">Standard: #F5B301 (Goldgelb aus dem Original-Mockup)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="header_btn_home">Topbar Button 1 (Home)</label></th>
                                <td>
                                    <input type="text" id="header_btn_home" name="drf_options[header_btn_home]" value="<?php echo esc_attr($values['header_btn_home']); ?>" class="regular-text">
                                    <p class="description">Beschriftung des grauen Homepage-Pill Buttons (Standard: Homepage)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="header_btn_forum">Topbar Button 2 (Forum)</label></th>
                                <td>
                                    <input type="text" id="header_btn_forum" name="drf_options[header_btn_forum]" value="<?php echo esc_attr($values['header_btn_forum']); ?>" class="regular-text">
                                    <p class="description">Beschriftung des gelben Forums-Pill Buttons (Standard: Forum &ndash; mit Zugang)</p>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <!-- TAB 2: HERO & STARTSEITE -->
                    <section class="drf-tab-pane" id="tab-hero">
                        <div class="drf-pane-header">
                            <h2>Hero-Bereich &amp; Polaroid-Kollage</h2>
                            <p>Gestalten Sie den Startbereich der Webseite mit Titeln, Buttons und Bildern.</p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="hero_tag">Kleine Tagline (oben)</label></th>
                                <td>
                                    <input type="text" id="hero_tag" name="drf_options[hero_tag]" value="<?php echo esc_attr($values['hero_tag']); ?>" class="large-text">
                                    <p class="description">z.B. DAS J&Auml;HRLICHE TREFFEN DER d.r.f.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="hero_title">Haupt-&Uuml;berschrift</label></th>
                                <td>
                                    <input type="text" id="hero_title" name="drf_options[hero_title]" value="<?php echo esc_attr($values['hero_title']); ?>" class="large-text">
                                    <p class="description">z.B. Willkommen bei Augenblicke.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="hero_desc">Einleitungstext</label></th>
                                <td>
                                    <textarea id="hero_desc" name="drf_options[hero_desc]" rows="4" class="large-text"><?php echo esc_textarea($values['hero_desc']); ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="hero_btn_primary">Haupt-CTA Button</label></th>
                                <td>
                                    <input type="text" id="hero_btn_primary" name="drf_options[hero_btn_primary]" value="<?php echo esc_attr($values['hero_btn_primary']); ?>" class="regular-text">
                                    <p class="description">Gelber Button (Standard: Mehr erfahren)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="hero_btn_forum">Sekund&auml;rer Button</label></th>
                                <td>
                                    <input type="text" id="hero_btn_forum" name="drf_options[hero_btn_forum]" value="<?php echo esc_attr($values['hero_btn_forum']); ?>" class="regular-text">
                                    <p class="description">Grauer Button (Standard: Zum Forum)</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>Polaroid Bild 1 (Links)</label></th>
                                <td>
                                    <div class="drf-media-control">
                                        <input type="text" id="hero_polaroid_1" name="drf_options[hero_polaroid_1]" value="<?php echo esc_url($values['hero_polaroid_1']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#hero_polaroid_1">Bild ausw&auml;hlen</button>
                                        <div class="drf-media-preview">
                                            <img src="<?php echo esc_url($values['hero_polaroid_1']); ?>" alt="Vorschau" style="max-height: 80px; margin-top: 8px;">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>Polaroid Bild 2 (Rechts)</label></th>
                                <td>
                                    <div class="drf-media-control">
                                        <input type="text" id="hero_polaroid_2" name="drf_options[hero_polaroid_2]" value="<?php echo esc_url($values['hero_polaroid_2']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#hero_polaroid_2">Bild ausw&auml;hlen</button>
                                        <div class="drf-media-preview">
                                            <img src="<?php echo esc_url($values['hero_polaroid_2']); ?>" alt="Vorschau" style="max-height: 80px; margin-top: 8px;">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label>Badge-Sticker (Rund)</label></th>
                                <td>
                                    <div class="drf-media-control">
                                        <input type="text" id="hero_badge_image" name="drf_options[hero_badge_image]" value="<?php echo esc_url($values['hero_badge_image']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#hero_badge_image">Bild ausw&auml;hlen</button>
                                        <div class="drf-media-preview">
                                            <img src="<?php echo esc_url($values['hero_badge_image']); ?>" alt="Vorschau" style="max-height: 80px; margin-top: 8px;">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <!-- TAB 3: TREFFEN & COUNTDOWN -->
                    <section class="drf-tab-pane" id="tab-event">
                        <div class="drf-pane-header">
                            <h2>Treffen &amp; Live-Countdown</h2>
                            <p>Stellen Sie das anstehende Jahrestreffen ein. Der Countdown auf der Startseite z&auml;hlt automatisch bis zu diesem Datum herunter.</p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="event_badge">Treffen-K&uuml;rzel / Badge</label></th>
                                <td>
                                    <input type="text" id="event_badge" name="drf_options[event_badge]" value="<?php echo esc_attr($values['event_badge']); ?>" class="regular-text">
                                    <p class="description">z.B. AUGENBLICKE XXVI</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="event_title">Datum &amp; Ort</label></th>
                                <td>
                                    <input type="text" id="event_title" name="drf_options[event_title]" value="<?php echo esc_attr($values['event_title']); ?>" class="large-text">
                                    <p class="description">z.B. 28. &ndash; 31. Mai 2026 &bull; Haus Lutherrose, Neuendettelsau</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="event_sub">Zusatzinformation</label></th>
                                <td>
                                    <input type="text" id="event_sub" name="drf_options[event_sub]" value="<?php echo esc_attr($values['event_sub']); ?>" class="large-text">
                                    <p class="description">z.B. Fronleichnam-Wochenende &bull; Workshops, Fotowalks, Studio &amp; Grillabend</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="event_target_date">Live-Countdown Zieldatum</label></th>
                                <td>
                                    <input type="datetime-local" id="event_target_date" name="drf_options[event_target_date]" value="<?php echo esc_attr($values['event_target_date']); ?>" class="regular-text">
                                    <p class="description">Startzeitpunkt f&uuml;r den interaktiven Countdown-Timer (Tage, Stunden, Minuten, Sekunden).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="event_btn_text">Button Beschriftung &amp; Link</label></th>
                                <td>
                                    <input type="text" id="event_btn_text" name="drf_options[event_btn_text]" value="<?php echo esc_attr($values['event_btn_text']); ?>" style="width: 180px;">
                                    <input type="text" id="event_btn_link" name="drf_options[event_btn_link]" value="<?php echo esc_attr($values['event_btn_link']); ?>" class="regular-text">
                                    <p class="description">Button Text (z.B. Zur Anmeldung) und Ziel-URL (z.B. /tagungsbuero/)</p>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <!-- TAB 4: 4 FEATURE KARTEN -->
                    <section class="drf-tab-pane" id="tab-cards">
                        <div class="drf-pane-header">
                            <h2>Die 4 Haupt-Bereiche (Startseiten-Karten)</h2>
                            <p>Passen Sie die vier Kacheln f&uuml;r Verein, Workshops, Shuttleservice und Tagungsb&uuml;ro an.</p>
                        </div>

                        <!-- Card 1 -->
                        <div class="drf-card-edit-block">
                            <h3>Karte 1: Verein</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="card_1_title">Titel &amp; Link</label></th>
                                    <td>
                                        <input type="text" id="card_1_title" name="drf_options[card_1_title]" value="<?php echo esc_attr($values['card_1_title']); ?>" style="width: 200px;">
                                        <input type="text" name="drf_options[card_1_link]" value="<?php echo esc_attr($values['card_1_link']); ?>" class="regular-text" placeholder="URL z.B. /verein/">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="card_1_desc">Beschreibung</label></th>
                                    <td>
                                        <textarea id="card_1_desc" name="drf_options[card_1_desc]" rows="2" class="large-text"><?php echo esc_textarea($values['card_1_desc']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>Hintergrundbild</label></th>
                                    <td>
                                        <input type="text" id="card_1_image" name="drf_options[card_1_image]" value="<?php echo esc_url($values['card_1_image']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#card_1_image">Bild w&auml;hlen</button>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Card 2 -->
                        <div class="drf-card-edit-block">
                            <h3>Karte 2: Workshops</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="card_2_title">Titel &amp; Link</label></th>
                                    <td>
                                        <input type="text" id="card_2_title" name="drf_options[card_2_title]" value="<?php echo esc_attr($values['card_2_title']); ?>" style="width: 200px;">
                                        <input type="text" name="drf_options[card_2_link]" value="<?php echo esc_attr($values['card_2_link']); ?>" class="regular-text" placeholder="URL z.B. /workshops/">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="card_2_desc">Beschreibung</label></th>
                                    <td>
                                        <textarea id="card_2_desc" name="drf_options[card_2_desc]" rows="2" class="large-text"><?php echo esc_textarea($values['card_2_desc']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>Hintergrundbild</label></th>
                                    <td>
                                        <input type="text" id="card_2_image" name="drf_options[card_2_image]" value="<?php echo esc_url($values['card_2_image']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#card_2_image">Bild w&auml;hlen</button>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Card 3 -->
                        <div class="drf-card-edit-block">
                            <h3>Karte 3: Shuttleservice</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="card_3_title">Titel &amp; Link</label></th>
                                    <td>
                                        <input type="text" id="card_3_title" name="drf_options[card_3_title]" value="<?php echo esc_attr($values['card_3_title']); ?>" style="width: 200px;">
                                        <input type="text" name="drf_options[card_3_link]" value="<?php echo esc_attr($values['card_3_link']); ?>" class="regular-text" placeholder="URL z.B. /shuttleservice/">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="card_3_desc">Beschreibung</label></th>
                                    <td>
                                        <textarea id="card_3_desc" name="drf_options[card_3_desc]" rows="2" class="large-text"><?php echo esc_textarea($values['card_3_desc']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>Hintergrundbild</label></th>
                                    <td>
                                        <input type="text" id="card_3_image" name="drf_options[card_3_image]" value="<?php echo esc_url($values['card_3_image']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#card_3_image">Bild w&auml;hlen</button>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Card 4 -->
                        <div class="drf-card-edit-block">
                            <h3>Karte 4: Tagungsb&uuml;ro</h3>
                            <table class="form-table">
                                <tr>
                                    <th><label for="card_4_title">Titel &amp; Link</label></th>
                                    <td>
                                        <input type="text" id="card_4_title" name="drf_options[card_4_title]" value="<?php echo esc_attr($values['card_4_title']); ?>" style="width: 200px;">
                                        <input type="text" name="drf_options[card_4_link]" value="<?php echo esc_attr($values['card_4_link']); ?>" class="regular-text" placeholder="URL z.B. /tagungsbuero/">
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="card_4_desc">Beschreibung</label></th>
                                    <td>
                                        <textarea id="card_4_desc" name="drf_options[card_4_desc]" rows="2" class="large-text"><?php echo esc_textarea($values['card_4_desc']); ?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label>Hintergrundbild</label></th>
                                    <td>
                                        <input type="text" id="card_4_image" name="drf_options[card_4_image]" value="<?php echo esc_url($values['card_4_image']); ?>" class="large-text drf-media-url">
                                        <button type="button" class="button drf-media-upload-btn" data-target="#card_4_image">Bild w&auml;hlen</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </section>

                    <!-- TAB 5: FLARUM FORUM BRIDGE -->
                    <section class="drf-tab-pane" id="tab-flarum">
                        <div class="drf-pane-header">
                            <h2>Flarum Community Forum Anbindung</h2>
                            <p>Konfigurieren Sie die Flarum 2.0 Integration und den Diskussions-Feed auf der Startseite.</p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="flarum_url">Flarum Forum URL</label></th>
                                <td>
                                    <input type="url" id="flarum_url" name="drf_options[flarum_url]" value="<?php echo esc_url($values['flarum_url']); ?>" class="large-text">
                                    <p class="description">Standard: <?php echo esc_url(home_url('/forum/')); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flarum_feed_limit">Anzahl angezeigter Diskussionen</label></th>
                                <td>
                                    <input type="number" id="flarum_feed_limit" name="drf_options[flarum_feed_limit]" value="<?php echo esc_attr($values['flarum_feed_limit']); ?>" min="1" max="12" style="width: 80px;">
                                    <p class="description">Wie viele Live-Themen aus dem Forum im Startseiten-Widget gezeigt werden.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flarum_box_title">SSO Info-Box Titel</label></th>
                                <td>
                                    <input type="text" id="flarum_box_title" name="drf_options[flarum_box_title]" value="<?php echo esc_attr($values['flarum_box_title']); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="flarum_box_desc">SSO Info-Box Text</label></th>
                                <td>
                                    <textarea id="flarum_box_desc" name="drf_options[flarum_box_desc]" rows="3" class="large-text"><?php echo esc_textarea($values['flarum_box_desc']); ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </section>

                    <!-- TAB 6: KONTAKT & FOOTER -->
                    <section class="drf-tab-pane" id="tab-footer">
                        <div class="drf-pane-header">
                            <h2>Kontakt &amp; Footer-Informationen</h2>
                            <p>Hinterlegen Sie die offiziellen Kontaktdaten und rechtlichen Angaben des Vereins.</p>
                        </div>

                        <table class="form-table">
                            <tr>
                                <th scope="row"><label for="contact_email">Offizielle E-Mail Adresse</label></th>
                                <td>
                                    <input type="email" id="contact_email" name="drf_options[contact_email]" value="<?php echo esc_attr($values['contact_email']); ?>" class="regular-text">
                                    <p class="description">z.B. tagungsbuero2026@d-r-f.de</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="contact_phone">Telefonnummer / Hotline</label></th>
                                <td>
                                    <input type="text" id="contact_phone" name="drf_options[contact_phone]" value="<?php echo esc_attr($values['contact_phone']); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="contact_address">Vereinsanschrift</label></th>
                                <td>
                                    <input type="text" id="contact_address" name="drf_options[contact_address]" value="<?php echo esc_attr($values['contact_address']); ?>" class="large-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="footer_copyright">Copyright-Zeile</label></th>
                                <td>
                                    <input type="text" id="footer_copyright" name="drf_options[footer_copyright]" value="<?php echo esc_attr($values['footer_copyright']); ?>" class="large-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="footer_subline">Footer Untertitel</label></th>
                                <td>
                                    <input type="text" id="footer_subline" name="drf_options[footer_subline]" value="<?php echo esc_attr($values['footer_subline']); ?>" class="large-text">
                                </td>
                            </tr>
                        </table>
                    </section>

                </div>
            </div>

            <!-- Sticky Admin Save Bar -->
            <div class="drf-admin-footer">
                <button type="submit" name="drf_save_theme_options" class="button button-primary button-hero">
                    <span class="dashicons dashicons-saved" style="margin-top: 5px;"></span> &Auml;nderungen speichern
                </button>
                <button type="submit" name="drf_reset_theme_options" class="button button-link-delete" onclick="return confirm('Möchten Sie wirklich alle Theme-Optionen auf die Standardwerte zurücksetzen?');">
                    Auf Standardwerte zur&uuml;cksetzen
                </button>
            </div>
        </form>
    </div>
    <?php
}
