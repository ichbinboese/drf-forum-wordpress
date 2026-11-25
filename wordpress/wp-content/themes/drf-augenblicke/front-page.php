<?php
/**
 * The Front Page template for DRF Augenblicke Theme
 *
 * @package DRF_Augenblicke
 */

// If page is built with Elementor, render Elementor content directly
if (class_exists('\Elementor\Plugin')) {
    $doc = \Elementor\Plugin::$instance->documents->get(get_the_ID());
    if ($doc && $doc->is_built_with_elementor()) {
        get_header();
        while (have_posts()) :
            the_post();
            ?>
            <main id="primary" class="site-main drf-elementor-front-page">
                <?php the_content(); ?>
            </main>
            <?php
        endwhile;
        get_footer();
        return;
    }
}

get_header();

// Fetch options with fallbacks
$hero_tag          = drf_get_theme_option('hero_tag', 'DAS JÄHRLICHE TREFFEN DER d.r.f.');
$hero_title        = drf_get_theme_option('hero_title', 'Willkommen bei Augenblicke.');
$hero_desc         = drf_get_theme_option('hero_desc', 'Wir sind ein eingetragener, gemeinnütziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein – mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.');
$hero_btn_primary  = drf_get_theme_option('hero_btn_primary', 'Mehr erfahren');
$hero_btn_forum    = drf_get_theme_option('hero_btn_forum', 'Zum Forum');
$hero_polaroid_1   = drf_get_theme_option('hero_polaroid_1', DRF_THEME_URI . '/assets/images/polaroid-dock.jpg');
$hero_polaroid_2   = drf_get_theme_option('hero_polaroid_2', DRF_THEME_URI . '/assets/images/polaroid-camera.jpg');
$hero_badge_image  = drf_get_theme_option('hero_badge_image', DRF_THEME_URI . '/assets/images/badge-treffen.png');

$event_badge       = drf_get_theme_option('event_badge', 'AUGENBLICKE XXVI');
$event_title       = drf_get_theme_option('event_title', '14. – 17. Mai 2026 • Haus Lutherrose, Neuendettelsau');
$event_sub         = drf_get_theme_option('event_sub', 'Fronleichnam-Wochenende • Workshops, Fotowalks, Studio & Grillabend');
$event_btn_text    = drf_get_theme_option('event_btn_text', 'Zur Anmeldung');
$event_btn_link    = drf_get_theme_option('event_btn_link', '/tagungsbuero/');

$card_1_title      = drf_get_theme_option('card_1_title', 'Verein');
$card_1_desc       = drf_get_theme_option('card_1_desc', 'Wer wir sind, wofür wir stehen und was uns als Gemeinschaft ausmacht.');
$card_1_link       = drf_get_theme_option('card_1_link', '/verein/');
$card_1_image      = drf_get_theme_option('card_1_image', DRF_THEME_URI . '/assets/images/card-verein.jpg');

$card_2_title      = drf_get_theme_option('card_2_title', 'Workshops');
$card_2_desc       = drf_get_theme_option('card_2_desc', 'Praxisnah, inspirierend und vielfältig – Workshops für verschiedene Fotografie-Bereiche.');
$card_2_link       = drf_get_theme_option('card_2_link', '/workshops/');
$card_2_image      = drf_get_theme_option('card_2_image', DRF_THEME_URI . '/assets/images/card-workshops.jpg');

$card_3_title      = drf_get_theme_option('card_3_title', 'Shuttleservice');
$card_3_desc       = drf_get_theme_option('card_3_desc', 'Bequem und unkompliziert – unser Shuttleservice zum Veranstaltungsort.');
$card_3_link       = drf_get_theme_option('card_3_link', '/shuttleservice/');
$card_3_image      = drf_get_theme_option('card_3_image', DRF_THEME_URI . '/assets/images/card-shuttle.jpg');

$card_4_title      = drf_get_theme_option('card_4_title', 'Tagungsbüro');
$card_4_desc       = drf_get_theme_option('card_4_desc', 'Dein Ansprechpartner vor Ort – für Anmeldung, Infos und alles rund um das Treffen.');
$card_4_link       = drf_get_theme_option('card_4_link', '/tagungsbuero/');
$card_4_image      = drf_get_theme_option('card_4_image', DRF_THEME_URI . '/assets/images/card-tagungsbuero.jpg');

$flarum_box_title  = drf_get_theme_option('flarum_box_title', 'Flarum Single Sign-On');
$flarum_box_desc   = drf_get_theme_option('flarum_box_desc', 'Mit deinem WordPress-Konto hast du automatisch Zugang zum Forum. Keine separate Registrierung erforderlich.');
$contact_email     = drf_get_theme_option('contact_email', 'tagungsbuero2026@d-r-f.de');
?>

<!-- 1. Hero Section -->
<section class="hero-augenblicke">
    <div class="container hero-grid">
        <!-- Hero Text & CTA -->
        <div class="hero-content">
            <div class="hero-tag">
                <span class="hero-tag-bar"></span>
                <span><?php echo esc_html($hero_tag); ?></span>
            </div>

            <h1 class="hero-title">
                <?php
                // Emphasize the last word or accent if applicable
                echo wp_kses_post(str_replace('Augenblicke.', '<span class="accent-yellow">Augenblicke.</span>', esc_html($hero_title)));
                ?>
            </h1>

            <p class="hero-desc">
                <?php echo esc_html($hero_desc); ?>
            </p>

            <div class="hero-buttons">
                <a href="<?php echo esc_url(home_url('/augenblicke/')); ?>" class="btn-hero-primary">
                    <span><?php echo esc_html($hero_btn_primary); ?></span>
                    <span>&rarr;</span>
                </a>
                <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="btn-hero-forum">
                    <?php echo drf_get_icon('users'); ?>
                    <span><?php echo esc_html($hero_btn_forum); ?></span>
                </a>
            </div>
        </div>

        <!-- Hero Visual: Polaroids (Large) -->
        <div class="hero-visual">
            <div class="polaroid-wrapper">
                <div class="polaroid-item polaroid-dock">
                    <img src="<?php echo esc_url($hero_polaroid_1); ?>" alt="Steg am See bei Sonnenuntergang" loading="lazy">
                </div>
                <div class="polaroid-item polaroid-camera">
                    <img src="<?php echo esc_url($hero_polaroid_2); ?>" alt="Kamera und Technik" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. Feature Cards Section (Verein, Workshops, Shuttleservice, Tagungsbüro) -->
<section class="section-cards" id="featureCards">
    <div class="container">
        <div class="cards-grid">
            <!-- Card 1: Verein -->
            <a href="<?php echo esc_url(home_url($card_1_link)); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url($card_1_image); ?>" alt="<?php echo esc_attr($card_1_title); ?>" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('users'); ?>
                        </div>
                        <h2 class="card-title"><?php echo esc_html($card_1_title); ?></h2>
                    </div>
                    <p class="card-desc">
                        <?php echo esc_html($card_1_desc); ?>
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 2: Workshops -->
            <a href="<?php echo esc_url(home_url($card_2_link)); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url($card_2_image); ?>" alt="<?php echo esc_attr($card_2_title); ?>" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('camera'); ?>
                        </div>
                        <h2 class="card-title"><?php echo esc_html($card_2_title); ?></h2>
                    </div>
                    <p class="card-desc">
                        <?php echo esc_html($card_2_desc); ?>
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 3: Shuttleservice -->
            <a href="<?php echo esc_url(home_url($card_3_link)); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url($card_3_image); ?>" alt="<?php echo esc_attr($card_3_title); ?>" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('bus'); ?>
                        </div>
                        <h2 class="card-title"><?php echo esc_html($card_3_title); ?></h2>
                    </div>
                    <p class="card-desc">
                        <?php echo esc_html($card_3_desc); ?>
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 4: Tagungsbüro -->
            <a href="<?php echo esc_url(home_url($card_4_link)); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url($card_4_image); ?>" alt="<?php echo esc_attr($card_4_title); ?>" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('calendar'); ?>
                        </div>
                        <h2 class="card-title"><?php echo esc_html($card_4_title); ?></h2>
                    </div>
                    <p class="card-desc">
                        <?php echo esc_html($card_4_desc); ?>
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- 3. Event Strip: Datum, Ort & Live Countdown Timer -->
<section class="section-event-strip" id="treffen-info">
    <div class="container event-strip-inner">
        <div class="event-details">
            <span class="event-badge"><?php echo esc_html($event_badge); ?></span>
            <div>
                <h3 class="event-heading"><?php echo esc_html($event_title); ?></h3>
                <p class="event-sub"><?php echo esc_html($event_sub); ?></p>
            </div>
        </div>

        <!-- Live Countdown Widget -->
        <div class="event-countdown-container">
            <span class="countdown-caption">COUNTDOWN ZUM TREFFEN:</span>
            <div class="countdown-clock" id="drfCountdownClock">
                <div class="countdown-unit">
                    <span class="countdown-num" id="cdDays">--</span>
                    <span class="countdown-label">Tage</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-num" id="cdHours">--</span>
                    <span class="countdown-label">Std.</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-num" id="cdMins">--</span>
                    <span class="countdown-label">Min.</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-num" id="cdSecs">--</span>
                    <span class="countdown-label">Sek.</span>
                </div>
            </div>
        </div>

        <div class="event-actions">
            <a href="<?php echo esc_url(home_url($event_btn_link)); ?>" class="btn-hero-primary">
                <span><?php echo esc_html($event_btn_text); ?></span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>
</section>

<!-- 4. Community & Flarum Forum Teaser -->
<section class="section-community">
    <div class="container">
        <div class="section-head">
            <h2 class="section-title">Diskussionen &amp; Community im Flarum Forum</h2>
            <p class="section-subtitle">Tausche dich mit Fotografinnen und Fotografen aus &ndash; von Altglas-Objektiven bis zu den Sessions beim n&auml;chsten Treffen.</p>
        </div>

        <div class="community-grid">
            <!-- Left: Thread Preview -->
            <div class="flarum-preview-card">
                <div class="preview-header">
                    <div class="preview-title">
                        <span>Aktuelle Themen</span>
                        <span class="live-pill">Live Forum</span>
                    </div>
                    <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-forum">
                        <span>Alle Themen ansehen &rarr;</span>
                    </a>
                </div>

                <ul class="forum-thread-list">
                    <li class="forum-thread-item">
                        <div class="thread-main">
                            <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="thread-title">
                                Augenblicke XXVI (2026): Vorbereitung &amp; Fahrgemeinschaften nach Neuendettelsau
                            </a>
                            <div class="thread-meta">
                                <span class="tag-badge accent">Augenblicke 2026</span>
                                <span>vor 2 Stunden von <strong>Frank_S</strong></span>
                            </div>
                        </div>
                        <div class="thread-replies">💬 28</div>
                    </li>

                    <li class="forum-thread-item">
                        <div class="thread-main">
                            <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="thread-title">
                                Workshop-Vorschlag: Schwarz-Weiß Dunkelkammer &amp; Baryt-Handabzüge
                            </a>
                            <div class="thread-meta">
                                <span class="tag-badge">Workshops</span>
                                <span>gestern von <strong>SilberKorn</strong></span>
                            </div>
                        </div>
                        <div class="thread-replies">💬 14</div>
                    </li>

                    <li class="forum-thread-item">
                        <div class="thread-main">
                            <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="thread-title">
                                Usenet-Bridge de.rec.fotografie: Synchronisation und Newsreader-Setup
                            </a>
                            <div class="thread-meta">
                                <span class="tag-badge">Technik &amp; NNTP</span>
                                <span>vor 3 Tagen von <strong>Admin_DRF</strong></span>
                            </div>
                        </div>
                        <div class="thread-replies">💬 42</div>
                    </li>

                    <li class="forum-thread-item">
                        <div class="thread-main">
                            <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="thread-title">
                                Altglas an Spiegellos: Bokeh-Klassiker Meyer-Optik &amp; Biotar im Vergleich
                            </a>
                            <div class="thread-meta">
                                <span class="tag-badge">Optik &amp; Kameras</span>
                                <span>vor 4 Tagen von <strong>Linsensuppe</strong></span>
                            </div>
                        </div>
                        <div class="thread-replies">💬 31</div>
                    </li>
                </ul>
            </div>

            <!-- Right Sidebar: Flarum Quick Login / Usenet Bridge -->
            <div class="community-sidebar">
                <div class="sidebar-box sidebar-box-dark">
                    <h4 class="sidebar-title"><?php echo esc_html($flarum_box_title); ?></h4>
                    <p class="sidebar-text">
                        <?php echo esc_html($flarum_box_desc); ?>
                    </p>
                    <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-home" style="width: 100%;">
                        <?php echo drf_get_icon('lock'); ?>
                        <span>Jetzt im Forum einloggen</span>
                    </a>
                </div>

                <div class="sidebar-box">
                    <h4 class="sidebar-title">Das Tagungsb&uuml;ro hilft</h4>
                    <p class="sidebar-text">
                        Fragen zum Ablauf, zu Zimmerbuchungen oder Shuttleservice?
                    </p>
                    <p style="font-size: 0.88rem; color: #374151;">
                        E-Mail: <a href="mailto:<?php echo esc_attr($contact_email); ?>" style="color: #b78103; font-weight: 600;"><?php echo esc_html($contact_email); ?></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
