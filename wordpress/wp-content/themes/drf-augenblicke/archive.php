<?php
/**
 * Archive Template
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title"><?php the_archive_title(); ?></h1>
        <?php the_archive_description('<p class="page-lead">', '</p>'); ?>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('feature-card'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="card-media">
                            <?php the_post_thumbnail('medium_large'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="card-header-row">
                            <h2 class="card-title" style="font-size: 1.2rem;">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                        </div>
                        <div class="card-desc">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="card-arrow">&rarr;</a>
                    </div>
                </article>
            <?php endwhile; else : ?>
                <div class="page-card-container">
                    <p><?php esc_html_e('Keine Einträge im Archiv gefunden.', 'drf-augenblicke'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();
