<?php
/**
 * DRF Treffen-Anmeldung Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Anmeldung_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_anmeldung';
    }

    public function get_title() {
        return __('d.r.f Treffen-Anmeldung (Formular)', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['anmeldung', 'registration', 'formular', 'treffen', 'zimmer', '2026', '2027'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_anmeldung',
            [
                'label' => __('Anmelde-Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $options = [
            '' => __('Automatisch (Aktives Treffen)', 'drf-augenblicke'),
        ];

        if (class_exists('DRF_Meeting_Manager')) {
            $meetings = \DRF_Meeting_Manager::get_all_meetings();
            foreach ($meetings as $m) {
                $options[$m->slug] = sprintf('%s (%s)', $m->title, $m->year);
            }
        }

        $this->add_control(
            'meeting_slug',
            [
                'label'       => __('Treffen auswählen', 'drf-augenblicke'),
                'type'        => Controls_Manager::SELECT,
                'default'     => '',
                'options'     => $options,
                'description' => __('Wähle ein bestimmtes Treffen (z.B. 2026 oder 2027) oder lasse die automatische Auswahl aktiv.', 'drf-augenblicke'),
            ]
        );

        $this->add_control(
            'custom_intro',
            [
                'label'       => __('Zusätzlicher Einleitungstext (Optional)', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => '',
                'rows'        => 3,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $slug = $settings['meeting_slug'];

        echo '<div class="container drf-elementor-anmeldung-wrap">';

        if (!empty($settings['custom_intro'])) {
            echo '<div class="drf-anmeldung-intro" style="margin-bottom: 24px; font-size: 1.05rem; color: #475569;">' . wp_kses_post(wpautop($settings['custom_intro'])) . '</div>';
        }

        if (shortcode_exists('drf_anmeldung')) {
            $shortcode = empty($slug) ? '[drf_anmeldung]' : sprintf('[drf_anmeldung meeting="%s"]', esc_attr($slug));
            echo do_shortcode($shortcode);
        } else {
            echo '<div class="drf-notice drf-notice-warning">' . esc_html__('Das Plugin drf-anmeldung ist nicht aktiv.', 'drf-augenblicke') . '</div>';
        }

        echo '</div>';
    }
}
