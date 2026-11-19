<?php
/**
 * DRF Reusable Template / Snippet Embed Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Template_Embed_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_template_embed';
    }

    public function get_title() {
        return __('d.r.f Elementor Snippet / Template Embed', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-shortcode';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['snippet', 'template', 'embed', 'reusable', 'vorlage', 'shortcode'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_embed',
            [
                'label' => __('Template / Snippet Auswahl', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $templates = [
            '' => __('-- Gespeichertes Snippet wählen --', 'drf-augenblicke'),
        ];

        $posts = get_posts([
            'post_type'      => 'elementor_library',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ]);

        if (!empty($posts)) {
            foreach ($posts as $p) {
                $templates[$p->ID] = sprintf('%s (ID: %d)', $p->post_title, $p->ID);
            }
        }

        $this->add_control(
            'template_id',
            [
                'label'       => __('Elementor Snippet / Template', 'drf-augenblicke'),
                'type'        => Controls_Manager::SELECT,
                'default'     => '',
                'options'     => $templates,
                'description' => __('Wähle ein gespeichertes Snippet aus der Elementor-Bibliothek zur Einbettung.', 'drf-augenblicke'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $template_id = absint($settings['template_id']);

        if (empty($template_id)) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="drf-notice" style="padding: 14px; background: #f1f5f9; border: 1px dashed #94a3b8; border-radius: 6px; text-align: center; color: #64748b;">'
                    . esc_html__('Bitte wähle ein Elementor Snippet / Template in den Einstellungen.', 'drf-augenblicke')
                    . '</div>';
            }
            return;
        }

        echo Plugin::$instance->frontend->get_builder_content_for_display($template_id);
    }
}
