<?php
/**
 * The Front Page template for DRF Augenblicke Theme
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<!-- 1. Hero Section -->
<section class="hero-augenblicke">
    <div class="container hero-grid">
        <!-- Hero Text & CTA -->
        <div class="hero-content">
            <div class="hero-tag">
                <span class="hero-tag-bar"></span>
                <span>DAS JÄHRLICHE TREFFEN DER d.r.f.</span>
            </div>

            <h1 class="hero-title">
                Willkommen bei <span class="accent-yellow">Augenblicke.</span>
            </h1>

            <p class="hero-desc">
                Wir sind ein eingetragener, gemeinn&uuml;tziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein &ndash; mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.
            </p>

            <div class="hero-buttons">
                <a href="<?php echo esc_url(home_url('/augenblicke/')); ?>" class="btn-hero-primary">
                    <span>Mehr erfahren</span>
                    <span>&rarr;</span>
                </a>
                <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="btn-hero-forum">
                    <?php echo drf_get_icon('users'); ?>
                    <span>Zum Forum</span>
                </a>
            </div>
        </div>

        <!-- Hero Visual: Polaroids & Badge -->
        <div class="hero-visual">
            <div class="polaroid-wrapper">
                <div class="polaroid-item polaroid-dock">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/polaroid-dock.jpg'); ?>" alt="Steg am See bei Sonnenuntergang" loading="lazy">
                </div>
                <div class="polaroid-item polaroid-camera">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/polaroid-camera.jpg'); ?>" alt="Kamera im Gras" loading="lazy">
                </div>
                <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/badge-treffen.png'); ?>" alt="Das jährliche Augenblicke-Treffen der d.r.f." class="badge-sticker" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- 2. Feature Cards Section (Verein, Workshops, Shuttleservice, Tagungsbüro) -->
<section class="section-cards" id="featureCards">
    <div class="container">
        <div class="cards-grid">
            <!-- Card 1: Verein -->
            <a href="<?php echo esc_url(home_url('/verein/')); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/card-verein.jpg'); ?>" alt="Verein de.rec.fotografie Gemeinschaft" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('users'); ?>
                        </div>
                        <h2 class="card-title">Verein</h2>
                    </div>
                    <p class="card-desc">
                        Wer wir sind, wof&uuml;r wir stehen und was uns als Gemeinschaft ausmacht.
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 2: Workshops -->
            <a href="<?php echo esc_url(home_url('/workshops/')); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/card-workshops.jpg'); ?>" alt="Workshops Fotografie" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('camera'); ?>
                        </div>
                        <h2 class="card-title">Workshops</h2>
                    </div>
                    <p class="card-desc">
                        Praxisnah, inspirierend und vielf&auml;ltig &ndash; Workshops f&uuml;r verschiedene Fotografie-Bereiche.
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 3: Shuttleservice -->
            <a href="<?php echo esc_url(home_url('/shuttleservice/')); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/card-shuttle.jpg'); ?>" alt="Shuttleservice d.r.f Augenblicke" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('bus'); ?>
                        </div>
                        <h2 class="card-title">Shuttleservice</h2>
                    </div>
                    <p class="card-desc">
                        Bequem und unkompliziert &ndash; unser Shuttleservice zum Veranstaltungsort.
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>

            <!-- Card 4: Tagungsbüro -->
            <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>" class="feature-card">
                <div class="card-media">
                    <img src="<?php echo esc_url(DRF_THEME_URI . '/assets/images/card-tagungsbuero.jpg'); ?>" alt="Tagungsbüro d.r.f" loading="lazy">
                </div>
                <div class="card-body">
                    <div class="card-header-row">
                        <div class="card-icon-badge">
                            <?php echo drf_get_icon('calendar'); ?>
                        </div>
                        <h2 class="card-title">Tagungsb&uuml;ro</h2>
                    </div>
                    <p class="card-desc">
                        Dein Ansprechpartner vor Ort &ndash; f&uuml;r Anmeldung, Infos und alles rund um das Treffen.
                    </p>
                    <span class="card-arrow">&rarr;</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- 3. Event Strip: Datum & Tagungsort -->
<section class="section-event-strip" id="treffen-info">
    <div class="container event-strip-inner">
        <div class="event-details">
            <span class="event-badge">AUGENBLICKE XXVI</span>
            <div>
                <h3 class="event-heading">28. &ndash; 31. Mai 2026 &bull; Haus Lutherrose, Neuendettelsau</h3>
                <p class="event-sub" id="meetingCountdown">Fronleichnam-Wochenende &bull; Workshops, Fotowalks, Studio &amp; Grillabend</p>
            </div>
        </div>
        <div class="event-actions">
            <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>" class="btn-hero-primary">
                <span>Zur Anmeldung</span>
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
                    <h4 class="sidebar-title">Flarum Single Sign-On</h4>
                    <p class="sidebar-text">
                        Mit deinem WordPress-Konto hast du automatisch Zugang zum Forum. Keine separate Registrierung erforderlich.
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
                        E-Mail: <a href="mailto:tagungsbuero2026@d-r-f.de" style="color: #b78103; font-weight: 600;">tagungsbuero2026@d-r-f.de</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
