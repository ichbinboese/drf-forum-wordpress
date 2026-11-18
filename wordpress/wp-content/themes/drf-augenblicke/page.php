<?php
/**
 * Generic Page Template
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title"><?php the_title(); ?></h1>
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
