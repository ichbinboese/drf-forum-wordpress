<?php
/**
 * Template Name: Elementor Standard (Contained DRF)
 * Template Post Type: page, post, elementor_library
 * Description: Contained layout template for Elementor builder pages with DRF header and footer.
 *
 * @package DRF_Augenblicke
 */

get_header();

while (have_posts()) :
    the_post();
    $subheading = function_exists('drf_get_page_subheading') ? drf_get_page_subheading() : '';
    $slug = get_post_field('post_name');
    ?>
    <?php if ($slug !== 'startseite') : ?>
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
    <?php endif; ?>

    <main id="primary" class="site-main drf-elementor-page drf-contained-page">
        <?php the_content(); ?>
    </main>
    <?php
endwhile;

get_footer();
