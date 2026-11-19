<?php
/**
 * DRF Foto-Alben & Galerien - Elementor Widget
 *
 * Integrates albums into Elementor with interactive controls:
 * Album picker, Layout (Grid, Masonry, Polaroid), Columns, Lightbox toggle.
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Elementor_Album_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'drf_album_gallery';
    }

    public function get_title() {
        return __('DRF Foto-Album & Galerie', 'drf-gallery');
    }

    public function get_icon() {
        return 'eicon-gallery-grid';
    }

    public function get_categories() {
        return ['drf-widgets', 'general'];
    }

    public function get_keywords() {
        return ['gallery', 'album', 'foto', 'bilder', 'lightbox', 'polaroid', 'masonry', 'drf'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Album Einstellungen', 'drf-gallery'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Fetch albums for dropdown
        $album_options = ['0' => __('— Bitte Album wählen —', 'drf-gallery')];
        if (class_exists('DRF_Gallery_DB')) {
            $albums = DRF_Gallery_DB::get_all_albums();
            foreach ($albums as $alb) {
                $album_options[$alb->id] = $alb->title . ' (' . $alb->image_count . ' Fotos)';
            }
        }

        $this->add_control(
            'album_id',
            [
                'label'       => __('Foto-Album', 'drf-gallery'),
                'type'        => \Elementor\Controls_Manager::SELECT,
                'default'     => !empty($albums[0]) ? $albums[0]->id : '0',
                'options'     => $album_options,
                'description' => __('Wähle das Album aus, das angezeigt werden soll.', 'drf-gallery'),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'   => __('Layout-Stil', 'drf-gallery'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid'     => __('Modernes Fotogitter (Uniform Grid)', 'drf-gallery'),
                    'masonry'  => __('Fließendes Masonry (Originalformate)', 'drf-gallery'),
                    'polaroid' => __('Vintage Polaroid Prints', 'drf-gallery'),
                ],
            ]
        );

        $this->add_control(
            'columns',
            [
                'label'   => __('Spalten', 'drf-gallery'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'options' => [
                    '2' => __('2 Spalten', 'drf-gallery'),
                    '3' => __('3 Spalten (Empfohlen)', 'drf-gallery'),
                    '4' => __('4 Spalten', 'drf-gallery'),
                ],
            ]
        );

        $this->add_control(
            'max_images',
            [
                'label'   => __('Maximale Anzahl Bilder (0 = alle)', 'drf-gallery'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 0,
                'max'     => 100,
                'step'    => 1,
                'default' => 0,
            ]
        );

        $this->add_control(
            'enable_lightbox',
            [
                'label'        => __('Lightbox aktivieren', 'drf-gallery'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Ja', 'drf-gallery'),
                'label_off'    => __('Nein', 'drf-gallery'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'        => __('Album-Titel anzeigen', 'drf-gallery'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Ja', 'drf-gallery'),
                'label_off'    => __('Nein', 'drf-gallery'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->add_control(
            'show_desc',
            [
                'label'        => __('Album-Beschreibung anzeigen', 'drf-gallery'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => __('Ja', 'drf-gallery'),
                'label_off'    => __('Nein', 'drf-gallery'),
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $album_id = absint($settings['album_id'] ?? 0);

        if (!$album_id) {
            echo '<div class="drf-album-empty"><p>' . esc_html__('Bitte wähle in den Elementor-Einstellungen ein Album aus.', 'drf-gallery') . '</p></div>';
            return;
        }

        $shortcode_atts = [
            'id'         => $album_id,
            'layout'     => esc_attr($settings['layout']),
            'columns'    => esc_attr($settings['columns']),
            'lightbox'   => ($settings['enable_lightbox'] === 'yes') ? '1' : '0',
            'show_title' => ($settings['show_title'] === 'yes') ? '1' : '0',
            'show_desc'  => ($settings['show_desc'] === 'yes') ? '1' : '0',
            'max'        => absint($settings['max_images']),
        ];

        echo DRF_Gallery_Frontend::get_instance()->render_album_shortcode($shortcode_atts);
    }
}
