<?php
/**
 * The Header for DRF Augenblicke Theme
 *
 * @package DRF_Augenblicke
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- 1. Top Bar (Dark Header) -->
<header class="site-topbar" id="siteTopbar">
    <div class="container topbar-inner">
        <!-- Logo Branding -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link" rel="home">
            <span class="brand-icon brand-logo-svg">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/drf-logo.svg'); ?>" alt="d.r.f – de.rec.fotografie" width="78" height="60" class="drf-header-logo-svg">
            </span>
            <div class="brand-text-group">
                <span class="brand-title"><?php echo esc_html(drf_get_theme_option('logo_text', 'AUGENBLICKE')); ?></span>
                <span class="brand-divider">|</span>
                <span class="brand-short"><?php echo esc_html(drf_get_theme_option('logo_short', 'd.r.f')); ?></span>
                <span class="brand-sub"><?php echo esc_html(drf_get_theme_option('logo_sub', 'de.rec.fotografie')); ?></span>
            </div>
        </a>

        <!-- Desktop Action Buttons: Homepage & Forum -->
        <div class="topbar-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="pill-btn pill-btn-home">
                <?php echo drf_get_icon('home'); ?>
                <span><?php echo esc_html(drf_get_theme_option('header_btn_home', 'Homepage')); ?></span>
            </a>
            <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-forum">
                <?php echo drf_get_icon('lock'); ?>
                <span><?php echo esc_html(drf_get_theme_option('header_btn_forum', 'Forum – mit Zugang')); ?></span>
            </a>
        </div>

        <!-- Mobile Hamburger Button -->
        <button class="mobile-nav-toggle" id="mobileNavToggle" aria-label="<?php esc_attr_e('Menü öffnen', 'drf-augenblicke'); ?>" aria-expanded="false">
            <?php echo drf_get_icon('menu'); ?>
        </button>

        <!-- Mobile Navigation Panel (Dropdown) -->
        <div class="mobile-nav-panel" id="mobileNavPanel">
            <ul class="mobile-nav-list">
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/verein/')); ?>">
                        <?php echo drf_get_icon('users'); ?>
                        <span>Verein</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/augenblicke/')); ?>">
                        <?php echo drf_get_icon('camera'); ?>
                        <span>Augenblicke</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>">
                        <?php echo drf_get_icon('calendar'); ?>
                        <span>Tagungsbüro</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/sozialgremium/')); ?>">
                        <?php echo drf_get_icon('users'); ?>
                        <span>Sozialgremium</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/shuttleservice/')); ?>">
                        <?php echo drf_get_icon('bus'); ?>
                        <span>Shuttleservice</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/workshops/')); ?>">
                        <?php echo drf_get_icon('camera'); ?>
                        <span>Workshops</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/impressum/')); ?>">
                        <?php echo drf_get_icon('lock'); ?>
                        <span>Impressum</span>
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a href="<?php echo esc_url(home_url('/datenschutz/')); ?>">
                        <?php echo drf_get_icon('lock'); ?>
                        <span>Datenschutz</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Mobile Quick Bar (Homepage & Forum Pills for Mobile) -->
<div class="mobile-quick-actions">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="pill-btn pill-btn-home">
        <?php echo drf_get_icon('home'); ?>
        <span>Homepage</span>
    </a>
    <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-forum">
        <?php echo drf_get_icon('lock'); ?>
        <span>Forum &ndash; mit Zugang</span>
    </a>
</div>

<!-- 2. Desktop Sub Navigation Bar (White Submenu) -->
<nav class="site-subnav" aria-label="<?php esc_attr_e('Hauptnavigation', 'drf-augenblicke'); ?>">
    <div class="container">
        <ul class="subnav-menu">
            <li class="subnav-item <?php echo is_page('verein') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/verein/')); ?>" class="subnav-link">Verein</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('augenblicke') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/augenblicke/')); ?>" class="subnav-link">Augenblicke</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('tagungsbuero') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>" class="subnav-link">Tagungsbüro</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('sozialgremium') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/sozialgremium/')); ?>" class="subnav-link">Sozialgremium</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('shuttleservice') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/shuttleservice/')); ?>" class="subnav-link">Shuttleservice</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('workshops') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/workshops/')); ?>" class="subnav-link">Workshops</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('impressum') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/impressum/')); ?>" class="subnav-link">Impressum</a>
            </li>
            <li class="subnav-divider">|</li>
            <li class="subnav-item <?php echo is_page('datenschutz') ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/datenschutz/')); ?>" class="subnav-link">Datenschutz</a>
            </li>
        </ul>
    </div>
</nav>
