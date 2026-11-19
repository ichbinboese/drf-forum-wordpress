<?php
/**
 * DRF Foto-Alben & Galerien - Frontend Display & Shortcodes
 *
 * Provides [drf_album] and [drf_gallery] shortcodes with modern responsive
 * Grid, Masonry, and Polaroid layouts plus integrated lightbox.
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Gallery_Frontend {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_shortcode('drf_album', [$this, 'render_album_shortcode']);
        add_shortcode('drf_gallery', [$this, 'render_all_albums_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
    }

    /**
     * Register frontend assets (loaded conditionally or on shortcode)
     */
    public function register_assets() {
        wp_register_style(
            'drf-gallery-frontend-css',
            DRF_GALLERY_URL . 'assets/css/frontend-gallery.css',
            [],
            DRF_GALLERY_VERSION
        );

        wp_register_script(
            'drf-gallery-frontend-js',
            DRF_GALLERY_URL . 'assets/js/frontend-gallery.js',
            ['jquery'],
            DRF_GALLERY_VERSION,
            true
        );
    }

    public function enqueue_assets() {
        wp_enqueue_style('drf-gallery-frontend-css');
        wp_enqueue_script('drf-gallery-frontend-js');
    }

    /**
     * Shortcode: [drf_album id="1" layout="grid" columns="3" show_title="1" show_desc="1" lightbox="1"]
     */
    public function render_album_shortcode($atts) {
        $atts = shortcode_atts([
            'id'         => 0,
            'slug'       => '',
            'layout'     => 'grid',    // 'grid', 'masonry', 'polaroid'
            'columns'    => '3',       // 2, 3, 4
            'show_title' => '1',
            'show_desc'  => '1',
            'lightbox'   => '1',
            'max'        => 0,
        ], $atts, 'drf_album');

        $this->enqueue_assets();

        $album_id = !empty($atts['id']) ? $atts['id'] : $atts['slug'];
        if (empty($album_id)) {
            // Pick first album if none specified
            $all = DRF_Gallery_DB::get_all_albums();
            if (!empty($all)) {
                $album = $all[0];
            } else {
                return '<div class="drf-album-empty"><p>Keine Alben gefunden.</p></div>';
            }
        } else {
            $album = DRF_Gallery_DB::get_album($album_id);
        }

        if (!$album) {
            return '<div class="drf-album-empty"><p>Album nicht gefunden.</p></div>';
        }

        $images = DRF_Gallery_DB::get_album_images($album->id);
        if ($atts['max'] > 0 && count($images) > (int) $atts['max']) {
            $images = array_slice($images, 0, (int) $atts['max']);
        }

        if (empty($images)) {
            return '<div class="drf-album-empty"><p>Dieses Album enthält noch keine Fotos.</p></div>';
        }

        $layout = in_array($atts['layout'], ['grid', 'masonry', 'polaroid']) ? $atts['layout'] : 'grid';
        $columns = in_array((int) $atts['columns'], [2, 3, 4]) ? (int) $atts['columns'] : 3;
        $enable_lightbox = filter_var($atts['lightbox'], FILTER_VALIDATE_BOOLEAN) || $atts['lightbox'] === '1' || $atts['lightbox'] === 'yes';

        ob_start();
        ?>
        <div class="drf-gallery-wrapper drf-gallery-contained" id="drf-album-<?php echo esc_attr($album->id); ?>">
            <?php if ($atts['show_title'] === '1' || $atts['show_desc'] === '1') : ?>
                <div class="drf-album-header">
                    <?php if ($atts['show_title'] === '1' && !empty($album->title)) : ?>
                        <h3 class="drf-album-title"><?php echo esc_html($album->title); ?></h3>
                    <?php endif; ?>
                    <?php if ($atts['show_desc'] === '1' && !empty($album->description)) : ?>
                        <p class="drf-album-description"><?php echo nl2br(esc_html($album->description)); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="drf-gallery-container drf-layout-<?php echo esc_attr($layout); ?> drf-cols-<?php echo esc_attr($columns); ?>"
                 data-gallery-id="album-<?php echo esc_attr($album->id); ?>"
                 data-lightbox="<?php echo $enable_lightbox ? '1' : '0'; ?>">

                <?php foreach ($images as $index => $img) :
                    $full_url = !empty($img->full_url) ? $img->full_url : $img->large_url;
                    $medium_url = !empty($img->large_url) ? $img->large_url : $img->medium_url;
                    $caption = !empty($img->caption) ? $img->caption : $img->title;
                    ?>
                    <figure class="drf-gallery-item"
                            data-index="<?php echo esc_attr($index); ?>"
                            data-full="<?php echo esc_url($full_url); ?>"
                            data-caption="<?php echo esc_attr($caption); ?>">
                        <div class="drf-item-inner">
                            <a href="<?php echo esc_url($full_url); ?>"
                               class="drf-lightbox-trigger"
                               title="<?php echo esc_attr($caption); ?>"
                               <?php echo !$enable_lightbox ? 'onclick="return false;"' : ''; ?>>
                                <img src="<?php echo esc_url($medium_url); ?>"
                                     alt="<?php echo esc_attr($caption); ?>"
                                     loading="lazy"
                                     width="<?php echo esc_attr($img->width); ?>"
                                     height="<?php echo esc_attr($img->height); ?>">
                                
                                <?php if ($layout !== 'polaroid') : ?>
                                    <div class="drf-item-overlay">
                                        <span class="drf-zoom-icon">🔍</span>
                                        <?php if (!empty($caption)) : ?>
                                            <span class="drf-item-overlay-caption"><?php echo esc_html($caption); ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <?php if ($layout === 'polaroid') : ?>
                                <figcaption class="drf-polaroid-caption">
                                    <?php echo esc_html($caption ?: 'DRF Analog'); ?>
                                </figcaption>
                            <?php endif; ?>
                        </div>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: [drf_gallery columns="3" layout="cards"]
     * Renders overview cards for all published albums
     */
    public function render_all_albums_shortcode($atts) {
        $atts = shortcode_atts([
            'columns' => '3',
            'layout'  => 'cards',
        ], $atts, 'drf_gallery');

        $this->enqueue_assets();

        $albums = DRF_Gallery_DB::get_all_albums();
        if (empty($albums)) {
            return '<div class="drf-album-empty"><p>Noch keine Alben angelegt.</p></div>';
        }

        $columns = in_array((int) $atts['columns'], [2, 3, 4]) ? (int) $atts['columns'] : 3;

        ob_start();
        ?>
        <div class="drf-gallery-wrapper drf-gallery-contained">
            <div class="drf-all-albums-grid drf-cols-<?php echo esc_attr($columns); ?>">
                <?php foreach ($albums as $album) :
                    $cover = !empty($album->cover_url) ? $album->cover_url : '';
                    ?>
                    <article class="drf-album-card-front" id="drf-card-<?php echo esc_attr($album->id); ?>">
                        <div class="drf-card-cover-wrap">
                            <?php if ($cover) : ?>
                                <img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr($album->title); ?>" loading="lazy">
                            <?php else : ?>
                                <div class="drf-card-cover-empty">📷</div>
                            <?php endif; ?>
                            <span class="drf-card-count-badge">
                                <?php echo sprintf(_n('%d Foto', '%d Fotos', $album->image_count, 'drf-gallery'), $album->image_count); ?>
                            </span>
                        </div>
                        <div class="drf-card-body">
                            <h4 class="drf-card-title"><?php echo esc_html($album->title); ?></h4>
                            <?php if (!empty($album->description)) : ?>
                                <p class="drf-card-text"><?php echo wp_trim_words(esc_html($album->description), 16); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
