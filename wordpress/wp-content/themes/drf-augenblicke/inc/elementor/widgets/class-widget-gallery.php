<?php
/**
 * DRF Photo Wall & Polaroids Elementor Widget
 *
 * Dynamically queries photos from WordPress backend albums (DRF_Gallery_DB)
 * or the WordPress Media Library, with polaroid styling and lightbox support.
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Gallery_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_gallery';
    }

    public function get_title() {
        return __('d.r.f Polaroid & Fotogalerie', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-image-box';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['gallery', 'polaroid', 'fotos', 'impressionen', 'filmstrip', 'augenblicke', 'workshops'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_gallery',
            [
                'label' => __('Galerie Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Impressionen aus vergangenen Treffen',
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label'   => __('Untertitel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Einblicke in Studioshootings, analoge Filmentwicklung, historische Altglas-Objektive und die legendäre Gemeinschaft vor Ort.',
                'rows'    => 2,
            ]
        );

        // Dynamic Album Selector from Backend DB
        $album_options = ['auto' => __('Automatisch (nach Seitenkontext)', 'drf-augenblicke')];
        if (class_exists('DRF_Gallery_DB')) {
            $albums = \DRF_Gallery_DB::get_all_albums();
            if (!empty($albums)) {
                foreach ($albums as $alb) {
                    $album_options[(string) $alb->id] = $alb->title . ' (' . $alb->image_count . ' Fotos)';
                }
            }
        }
        $album_options['all_media'] = __('Alle Medien aus der WordPress-Mediathek', 'drf-augenblicke');

        $this->add_control(
            'album_id',
            [
                'label'       => __('Backend Foto-Album', 'drf-augenblicke'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'auto',
                'options'     => $album_options,
                'description' => __('Wähle ein Backend-Album oder überlasse dem Widget die automatische Zuordnung.', 'drf-augenblicke'),
            ]
        );

        $this->add_control(
            'custom_images',
            [
                'label'       => __('Manuelle Bildauswahl (Optional)', 'drf-augenblicke'),
                'type'        => Controls_Manager::GALLERY,
                'default'     => [],
                'description' => __('Falls gesetzt, überschreiben diese Bilder das Backend-Album.', 'drf-augenblicke'),
            ]
        );

        $this->add_control(
            'limit',
            [
                'label'   => __('Maximale Bildanzahl', 'drf-augenblicke'),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
                'min'     => 1,
                'max'     => 24,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $limit    = !empty($settings['limit']) ? absint($settings['limit']) : 6;

        $items = [];
        $rotations = ['-2deg', '2.5deg', '-1.5deg', '2deg', '-2.5deg', '1deg', '-1.8deg', '2.2deg'];

        // 1. Check custom manual gallery selection
        if (!empty($settings['custom_images']) && is_array($settings['custom_images'])) {
            $rot_idx = 0;
            foreach (array_slice($settings['custom_images'], 0, $limit) as $c_img) {
                $att_id = absint($c_img['id'] ?? 0);
                if (!$att_id) continue;
                $url = wp_get_attachment_image_url($att_id, 'large') ?: ($c_img['url'] ?? '');
                $title = get_the_title($att_id) ?: __('Foto', 'drf-augenblicke');
                $caption = wp_get_attachment_caption($att_id) ?: $title;

                $items[] = [
                    'id'      => $att_id,
                    'title'   => $title,
                    'caption' => $caption,
                    'badge'   => __('Praxis', 'drf-augenblicke'),
                    'img'     => $url,
                    'rot'     => $rotations[$rot_idx % count($rotations)],
                ];
                $rot_idx++;
            }
        }

        // 2. Query Backend Album from DRF_Gallery_DB
        if (empty($items) && class_exists('DRF_Gallery_DB')) {
            $chosen_album = $settings['album_id'] ?? 'auto';

            if ($chosen_album === 'auto') {
                $current_slug = is_page() ? get_post_field('post_name', get_the_ID()) : '';
                $title_text   = strtolower($settings['title'] ?? '');

                // Match workshops
                if ($current_slug === 'workshops' || strpos($title_text, 'workshop') !== false) {
                    $albums = \DRF_Gallery_DB::get_all_albums();
                    foreach ($albums as $a) {
                        if (strpos(strtolower($a->slug), 'workshop') !== false || strpos(strtolower($a->title), 'workshop') !== false) {
                            $chosen_album = (string) $a->id;
                            break;
                        }
                    }
                    if ($chosen_album === 'auto' && !empty($albums[1])) {
                        $chosen_album = (string) $albums[1]->id;
                    }
                } elseif ($current_slug === 'verein' || strpos($title_text, 'verein') !== false) {
                    $chosen_album = '1';
                }
            }

            if ($chosen_album !== 'all_media' && is_numeric($chosen_album)) {
                $album_obj = \DRF_Gallery_DB::get_album((int) $chosen_album);
                $album_images = \DRF_Gallery_DB::get_album_images((int) $chosen_album);
                if (!empty($album_images)) {
                    $rot_idx = 0;
                    foreach (array_slice($album_images, 0, $limit) as $img_row) {
                        $items[] = [
                            'id'      => $img_row->attachment_id,
                            'title'   => !empty($img_row->title) ? $img_row->title : __('Treffen-Impression', 'drf-augenblicke'),
                            'caption' => !empty($img_row->caption) ? $img_row->caption : (!empty($img_row->title) ? $img_row->title : ''),
                            'badge'   => !empty($album_obj->title) ? mb_substr($album_obj->title, 0, 14) : __('Live', 'drf-augenblicke'),
                            'img'     => $img_row->large_url ?: $img_row->full_url,
                            'rot'     => $rotations[$rot_idx % count($rotations)],
                        ];
                        $rot_idx++;
                    }
                }
            }
        }

        // 3. Fallback: Query attachments from Media Library
        if (empty($items)) {
            $attachments = get_posts([
                'post_type'      => 'attachment',
                'post_mime_type' => 'image',
                'post_status'    => 'inherit',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
            ]);

            if (!empty($attachments)) {
                $rot_idx = 0;
                foreach ($attachments as $att) {
                    $img_url = wp_get_attachment_image_url($att->ID, 'large') ?: wp_get_attachment_url($att->ID);
                    if (!$img_url) continue;
                    $items[] = [
                        'id'      => $att->ID,
                        'title'   => $att->post_title ?: __('Foto', 'drf-augenblicke'),
                        'caption' => $att->post_excerpt ?: ($att->post_title ?: __('Impression', 'drf-augenblicke')),
                        'badge'   => __('Archiv', 'drf-augenblicke'),
                        'img'     => $img_url,
                        'rot'     => $rotations[$rot_idx % count($rotations)],
                    ];
                    $rot_idx++;
                }
            }
        }

        // 4. Ultimate fallback to theme assets if Media Library is completely empty
        if (empty($items)) {
            $items = [
                [
                    'title'   => 'Licht & Schatten im Studio',
                    'caption' => 'Studioblitz & Modelsharing',
                    'badge'   => 'Studio',
                    'img'     => DRF_THEME_URI . '/assets/images/polaroid-camera.jpg',
                    'rot'     => '-2deg',
                ],
                [
                    'title'   => 'Dunkelkammer-Magie',
                    'caption' => 'Echte SW-Barytabzüge',
                    'badge'   => 'Labor',
                    'img'     => DRF_THEME_URI . '/assets/images/card-workshops.jpg',
                    'rot'     => '3deg',
                ],
                [
                    'title'   => 'Gemeinschaft & Grillabend',
                    'caption' => 'Samstagabend im Garten',
                    'badge'   => 'Tradition',
                    'img'     => DRF_THEME_URI . '/assets/images/card-verein.jpg',
                    'rot'     => '-1deg',
                ],
                [
                    'title'   => 'Morgenstimmung am Steg',
                    'caption' => 'Fotowalk zur blauen Stunde',
                    'badge'   => 'Natur',
                    'img'     => DRF_THEME_URI . '/assets/images/polaroid-dock.jpg',
                    'rot'     => '2deg',
                ],
            ];
        }
        ?>
        <div class="container drf-elementor-gallery-wrap">
            <div class="drf-gallery-section">
            <?php if (!empty($settings['title'])) : ?>
                <div class="drf-section-header">
                    <span class="drf-section-badge">IMPRESSIONEN &amp; GALERIE</span>
                    <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                    <?php if (!empty($settings['subtitle'])) : ?>
                        <p class="drf-section-lead"><?php echo esc_html($settings['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="drf-polaroid-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
                <?php foreach ($items as $item) : ?>
                    <div class="drf-polaroid-card" style="background: #ffffff; padding: 10px 10px 18px 10px; border-radius: 6px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; transform: rotate(<?php echo esc_attr($item['rot']); ?>); transition: all 0.25s ease;">
                        <div style="width: 100%; height: 190px; overflow: hidden; border-radius: 4px; background: #000000; position: relative;">
                            <img src="<?php echo esc_url($item['img']); ?>" alt="<?php echo esc_attr($item['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;" loading="lazy">
                            <span style="position: absolute; top: 8px; left: 8px; background: rgba(20,21,24,0.85); color: #f5b301; font-weight: 800; font-size: 0.72rem; padding: 2px 7px; border-radius: 3px; text-transform: uppercase;">
                                <?php echo esc_html($item['badge']); ?>
                            </span>
                        </div>
                        <div style="padding-top: 12px; text-align: center;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0 0 3px 0;"><?php echo esc_html($item['title']); ?></h4>
                            <p style="font-size: 0.82rem; color: #64748b; margin: 0; font-style: italic;"><?php echo esc_html($item['caption']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}
}
