<?php
/**
 * Generic Page Template
 *
 * @package DRF_Augenblicke
 */

get_header();

$subheading = function_exists('drf_get_page_subheading') ? drf_get_page_subheading() : '';
?>

<div class="page-hero">
    <div class="container">
        <div class="hero-tag">
            <span class="hero-tag-bar"></span>
            <span>de.rec.fotografie e.V.</span>
        </div>
        <h1 class="page-title"><?php the_title(); ?></h1>
        <?php if (!empty($subheading)) : ?>
            <h2 class="page-lead"><?php echo esc_html($subheading); ?></h2>
        <?php endif; ?>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <?php
            while (have_posts()) :
                the_post();
                the_content();
            endwhile;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
