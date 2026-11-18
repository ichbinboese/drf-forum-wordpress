<?php
/**
 * Single Post Template
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title"><?php the_title(); ?></h1>
        <p class="page-lead">Ver&ouml;ffentlicht am <?php echo get_the_date(); ?> von <?php the_author(); ?></p>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <?php
            while (have_posts()) : the_post();
                if (has_post_thumbnail()) {
                    the_post_thumbnail('large', ['style' => 'width: 100%; border-radius: 12px; margin-bottom: 24px;']);
                }
                the_content();
            endwhile;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
