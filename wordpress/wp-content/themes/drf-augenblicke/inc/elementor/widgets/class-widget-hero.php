<?php
/**
 * DRF Hero Section Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Hero_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_hero';
    }

    public function get_title() {
        return __('d.r.f Hero Section & Polaroid', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-image-hotspot';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['hero', 'augenblicke', 'drf', 'polaroid', 'banner'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Hero Inhalte', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'hero_tag',
            [
                'label'       => __('Tagline / Dachzeile', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'DAS JÄHRLICHE TREFFEN DER d.r.f.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'hero_title',
            [
                'label'       => __('Haupt-Überschrift', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => 'Willkommen bei Augenblicke.',
                'label_block' => true,
            ]
        );

        $this->add_control(
            'hero_desc',
            [
                'label'       => __('Intro Text / Beschreibung', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXTAREA,
                'default'     => 'Wir sind ein eingetragener, gemeinnütziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein – mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.',
                'rows'        => 4,
            ]
        );

        $this->add_control(
            'btn_primary_text',
            [
                'label'   => __('Button 1 Text (Primär)', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Mehr erfahren',
            ]
        );

        $this->add_control(
            'btn_primary_url',
            [
                'label'   => __('Button 1 URL', 'drf-augenblicke'),
                'type'    => Controls_Manager::URL,
                'default' => [
                    'url' => '#drfCardsGrid',
                ],
            ]
        );

        $this->add_control(
            'btn_forum_text',
            [
                'label'   => __('Button 2 Text (Forum)', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Zum Forum',
            ]
        );

        $this->add_control(
            'btn_forum_url',
            [
                'label'   => __('Button 2 URL', 'drf-augenblicke'),
                'type'    => Controls_Manager::URL,
                'default' => [
                    'url' => '/forum/',
                ],
            ]
        );

        $this->end_controls_section();

        // Polaroid Media Section
        $this->start_controls_section(
            'section_polaroids',
            [
                'label' => __('Polaroids & Sticker', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'polaroid_1',
            [
                'label'   => __('Polaroid 1 (Groß)', 'drf-augenblicke'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => DRF_THEME_URI . '/assets/images/polaroid-dock.jpg',
                ],
            ]
        );

        $this->add_control(
            'polaroid_2',
            [
                'label'   => __('Polaroid 2 (Klein / Kamera)', 'drf-augenblicke'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => DRF_THEME_URI . '/assets/images/polaroid-camera.jpg',
                ],
            ]
        );

        $this->add_control(
            'badge_sticker',
            [
                'label'   => __('Badge Sticker', 'drf-augenblicke'),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => DRF_THEME_URI . '/assets/images/badge-treffen.png',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $hero_tag         = $settings['hero_tag'];
        $hero_title       = $settings['hero_title'];
        $hero_desc        = $settings['hero_desc'];
        $btn_primary_text = $settings['btn_primary_text'];
        $btn_primary_url  = !empty($settings['btn_primary_url']['url']) ? $settings['btn_primary_url']['url'] : '#drfCardsGrid';
        $btn_forum_text   = $settings['btn_forum_text'];
        $btn_forum_url    = !empty($settings['btn_forum_url']['url']) ? $settings['btn_forum_url']['url'] : '/forum/';

        $p1 = !empty($settings['polaroid_1']['url']) ? $settings['polaroid_1']['url'] : DRF_THEME_URI . '/assets/images/polaroid-dock.jpg';
        $p2 = !empty($settings['polaroid_2']['url']) ? $settings['polaroid_2']['url'] : DRF_THEME_URI . '/assets/images/polaroid-camera.jpg';
        $badge = !empty($settings['badge_sticker']['url']) ? $settings['badge_sticker']['url'] : DRF_THEME_URI . '/assets/images/badge-treffen.png';
        ?>
        <section class="hero-augenblicke drf-elementor-hero">
            <div class="container hero-grid">
                <div class="hero-content">
                    <div class="hero-tag">
                        <span class="hero-tag-bar"></span>
                        <span><?php echo esc_html($hero_tag); ?></span>
                    </div>

                    <h1 class="hero-headline">
                        <?php
                        $parts = explode('.', $hero_title, 2);
                        if (count($parts) === 2 && !empty(trim($parts[0]))) {
                            echo esc_html(trim($parts[0])) . '<span class="text-accent">.</span>';
                        } else {
                            echo esc_html($hero_title);
                        }
                        ?>
                    </h1>

                    <p class="hero-description">
                        <?php echo nl2br(esc_html($hero_desc)); ?>
                    </p>

                    <div class="hero-actions">
                        <a href="<?php echo esc_url($btn_primary_url); ?>" class="btn btn-primary">
                            <span><?php echo esc_html($btn_primary_text); ?></span>
                            <span class="btn-arrow">&rarr;</span>
                        </a>

                        <a href="<?php echo esc_url($btn_forum_url); ?>" class="btn btn-forum">
                            <span class="btn-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </span>
                            <span><?php echo esc_html($btn_forum_text); ?></span>
                        </a>
                    </div>
                </div>

                <div class="hero-visual" aria-hidden="true">
                    <div class="polaroid-wrapper">
                        <div class="polaroid-item polaroid-back">
                            <div class="polaroid-photo-area">
                                <img src="<?php echo esc_url($p1); ?>" alt="Augenblicke Fotografie Impressionen" loading="eager">
                            </div>
                            <div class="polaroid-caption">Augenblicke &bull; Momente</div>
                        </div>

                        <div class="polaroid-item polaroid-front">
                            <div class="polaroid-photo-area">
                                <img src="<?php echo esc_url($p2); ?>" alt="Kamera und Technik" loading="eager">
                            </div>
                            <div class="polaroid-caption">Licht &amp; Perspektive</div>
                        </div>

                        <div class="badge-sticker">
                            <img src="<?php echo esc_url($badge); ?>" alt="Das jährliche Augenblicke-Treffen">
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
