<?php
/**
 * DRF Photo Wall & Polaroids Elementor Widget
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
        return ['gallery', 'polaroid', 'fotos', 'impressionen', 'filmstrip', 'augenblicke'];
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

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

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
            [
                'title'   => 'Shuttle & Tagungsbüro',
                'caption' => 'Bequeme Anreise & Zimmer',
                'badge'   => 'Orga',
                'img'     => DRF_THEME_URI . '/assets/images/card-shuttle.jpg',
                'rot'     => '-3deg',
            ],
            [
                'title'   => 'Rezeption Haus Lutherrose',
                'caption' => 'Treffpunkt Tagungsbüro',
                'badge'   => 'Tagungsort',
                'img'     => DRF_THEME_URI . '/assets/images/card-tagungsbuero.jpg',
                'rot'     => '1deg',
            ],
        ];
        ?>
        <div class="container drf-elementor-gallery-wrap">
            <div class="drf-gallery-section" style="margin: 40px 0;">
            <?php if (!empty($settings['title'])) : ?>
                <div class="drf-section-header">
                    <span class="drf-section-badge">IMPRESSIONEN &amp; GALERIE</span>
                    <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                    <?php if (!empty($settings['subtitle'])) : ?>
                        <p class="drf-section-lead"><?php echo esc_html($settings['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
                <?php foreach ($items as $item) : ?>
                    <div class="drf-polaroid-card" style="background: #ffffff; padding: 10px 10px 18px 10px; border-radius: 6px; box-shadow: 0 8px 24px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; transform: rotate(<?php echo esc_attr($item['rot']); ?>); transition: all 0.25s ease;">
                        <div style="width: 100%; height: 190px; overflow: hidden; border-radius: 4px; background: #000000; position: relative;">
                            <img src="<?php echo esc_url($item['img']); ?>" alt="<?php echo esc_attr($item['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
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
