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
    ?>
    <main id="primary" class="site-main drf-elementor-page drf-contained-page">
        <?php the_content(); ?>
    </main>
    <?php
endwhile;

get_footer();
