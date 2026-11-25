<?php
/**
 * DRF Event Announcement Banner Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Event_Banner_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_event_banner';
    }

    public function get_title() {
        return __('d.r.f Event Banner (2026 / 2027)', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['event', 'banner', 'neuendettelsau', 'meeting', 'augenblicke', 'countdown'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_banner',
            [
                'label' => __('Event Banner Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'badge',
            [
                'label'   => __('Event Badge / Kennung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'AUGENBLICKE XXVI',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'       => __('Event Titel / Zeitraum & Ort', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '14. – 17. Mai 2027 • Haus Lutherrose, Neuendettelsau',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label'       => __('Untertitel / Beschreibung', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Fronleichnam-Wochenende • Workshops, Fotowalks, Studio & Grillabend',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'   => __('Button Beschriftung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Zur Anmeldung 2026',
            ]
        );

        $this->add_control(
            'button_url',
            [
                'label'   => __('Button Verlinkung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '/tagungsbuero/',
            ]
        );

        $this->add_control(
            'show_secondary',
            [
                'label'   => __('Zweit-Hinweis anzeigen (z.B. 2027)', 'drf-augenblicke'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'secondary_text',
            [
                'label'       => __('Zweit-Hinweis Text', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '📅 Auch AUGENBLICKE XXVII (06.–09. Mai 2027) ist bereits für Zimmerkontingente freigeschaltet!',
                'label_block' => true,
                'condition'   => [
                    'show_secondary' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="container drf-elementor-event-banner">
            <div class="event-banner">
                <div class="event-banner-content">
                    <?php if (!empty($settings['badge'])) : ?>
                        <span class="event-badge"><?php echo esc_html($settings['badge']); ?></span>
                    <?php endif; ?>
                    <h2 class="event-title"><?php echo esc_html($settings['title']); ?></h2>
                    <?php if (!empty($settings['subtitle'])) : ?>
                        <p class="event-sub"><?php echo esc_html($settings['subtitle']); ?></p>
                    <?php endif; ?>

                    <?php if ($settings['show_secondary'] === 'yes' && !empty($settings['secondary_text'])) : ?>
                        <p style="margin: 8px 0 0 0; font-size: 0.88rem; color: #f5b301; font-weight: 600;">
                            <?php echo esc_html($settings['secondary_text']); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($settings['button_text'])) : ?>
                    <div class="event-banner-action">
                        <a href="<?php echo esc_url($settings['button_url'] ?? '#'); ?>" class="btn btn-primary" style="margin:0;">
                            <span><?php echo esc_html($settings['button_text']); ?></span>
                            <span class="btn-arrow">&rarr;</span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
