<?php
/**
 * The Footer for DRF Augenblicke Theme
 *
 * @package DRF_Augenblicke
 */
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <!-- Footer Branding -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="brand-link" rel="home">
                <span class="brand-icon">
                    <?php echo drf_get_icon('camera'); ?>
                </span>
                <div class="brand-text-group">
                    <span class="brand-title"><?php echo esc_html(drf_get_theme_option('logo_text', 'AUGENBLICKE')); ?></span>
                    <span class="brand-divider">|</span>
                    <span class="brand-short"><?php echo esc_html(drf_get_theme_option('logo_short', 'd.r.f')); ?></span>
                    <span class="brand-sub"><?php echo esc_html(drf_get_theme_option('logo_sub', 'de.rec.fotografie')); ?></span>
                </div>
            </a>

            <!-- Footer Links -->
            <ul class="footer-links">
                <li><a href="<?php echo esc_url(home_url('/impressum/')); ?>">Impressum</a></li>
                <li class="subnav-divider">|</li>
                <li><a href="<?php echo esc_url(home_url('/datenschutz/')); ?>">Datenschutz</a></li>
                <li class="subnav-divider">|</li>
                <li><a href="<?php echo esc_url(drf_get_forum_url()); ?>">Forum</a></li>
                <li class="subnav-divider">|</li>
                <li><a href="<?php echo esc_url(home_url('/verein/')); ?>">Verein</a></li>
            </ul>
        </div>

        <div class="footer-bottom">
            <div>&copy; <?php echo date('Y'); ?> <?php echo esc_html(drf_get_theme_option('footer_copyright', 'de.rec.fotografie e.V. – Jahrestreffen »AUGENBLICKE«')); ?></div>
            <div><?php echo esc_html(drf_get_theme_option('footer_subline', 'WordPress & Flarum Community Platform')); ?></div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
