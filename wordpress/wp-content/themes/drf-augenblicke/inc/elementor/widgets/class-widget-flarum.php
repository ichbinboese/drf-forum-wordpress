<?php
/**
 * DRF Flarum Community Forum Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Flarum_Feed_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_flarum_feed';
    }

    public function get_title() {
        return __('d.r.f Flarum Forum Teaser & Bridge', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-comments';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['flarum', 'forum', 'community', 'usenet', 'diskussionen', 'sso'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_flarum',
            [
                'label' => __('Forum Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'de.rec.fotografie Community Forum',
            ]
        );

        $this->add_control(
            'desc',
            [
                'label'   => __('Beschreibung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Das moderne Webforum für Bildkritik [BK], Workshops [WS], Fototechnik und die Augenblicke-Tagungen – nahtlos mit WordPress verbunden.',
                'rows'    => 2,
            ]
        );

        $this->add_control(
            'forum_url',
            [
                'label'   => __('Forum URL', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '/forum/',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $tags = [
            ['name' => 'Augenblicke Treffen [TR]', 'color' => '#F5B301'],
            ['name' => 'Tagungsbüro [TB]', 'color' => '#E67E22'],
            ['name' => 'Workshops & Sessions [WS]', 'color' => '#3498DB'],
            ['name' => 'Bildkritik & Portfolio [BK]', 'color' => '#9B59B6'],
            ['name' => 'Schnellfotowettbewerb [SFW]', 'color' => '#E74C3C'],
            ['name' => 'Altglas & Manuelle Linsen', 'color' => '#D35400'],
        ];
        ?>
        <div class="container drf-elementor-flarum-wrap">
            <div class="drf-elementor-flarum" style="background: #141518; color: #ffffff; border-radius: 12px; padding: 32px; border-left: 5px solid #f5b301; margin: 32px 0;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px;">
                <div style="max-width: 650px;">
                    <span style="background: #f5b301; color: #141518; font-weight: 800; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">d.r.f Community</span>
                    <h3 style="color: #ffffff; margin: 8px 0; font-size: 1.5rem;"><?php echo esc_html($settings['title']); ?></h3>
                    <p style="color: #cbd5e1; margin-bottom: 16px; font-size: 0.95rem;"><?php echo esc_html($settings['desc']); ?></p>

                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <?php foreach ($tags as $t) : ?>
                            <span style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); border-left: 3px solid <?php echo esc_attr($t['color']); ?>; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; color: #e2e8f0; font-weight: 600;">
                                <?php echo esc_html($t['name']); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <a href="<?php echo esc_url($settings['forum_url']); ?>" class="btn btn-primary" style="padding: 12px 24px; font-size: 1rem;">
                        <span>Direkt zum Forum &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
}
