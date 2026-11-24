<?php
/**
 * DRF Live Countdown Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Countdown_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_countdown';
    }

    public function get_title() {
        return __('d.r.f Live Event Countdown (2026 / 2027)', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-countdown';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['countdown', 'timer', 'neuendettelsau', 'event', 'tage', 'augenblicke'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_countdown',
            [
                'label' => __('Countdown Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'layout_style',
            [
                'label'   => __('Design / Layout', 'drf-augenblicke'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'modern_light',
                'options' => [
                    'modern_light' => __('Alternative: Helle Stat-Karte (Empfohlen)', 'drf-augenblicke'),
                    'dark_card'    => __('Klassisch: Dunkler Kasten', 'drf-augenblicke'),
                ],
            ]
        );

        $this->add_control(
            'badge',
            [
                'label'   => __('Badge / Kennzeichnung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'AUGENBLICKE XXVI (2026)',
            ]
        );

        $this->add_control(
            'target_date',
            [
                'label'       => __('Ziel-Datum (ISO)', 'drf-augenblicke'),
                'type'        => Controls_Manager::TEXT,
                'default'     => '2026-05-28T09:00:00',
                'description' => __('Format: YYYY-MM-DDTHH:MM:SS', 'drf-augenblicke'),
            ]
        );

        $this->add_control(
            'location_title',
            [
                'label'   => __('Tagungsort', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Haus Lutherrose, Neuendettelsau',
            ]
        );

        $this->add_control(
            'dates_text',
            [
                'label'   => __('Zeitraum Text', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '28. Mai bis 31. Mai 2026 (Fronleichnam)',
            ]
        );

        $this->add_control(
            'show_btn',
            [
                'label'        => __('Button anzeigen', 'drf-augenblicke'),
                'type'         => Controls_Manager::SWITCHER,
                'default'      => 'yes',
                'return_value' => 'yes',
            ]
        );

        $this->add_control(
            'btn_text',
            [
                'label'     => __('Button Text', 'drf-augenblicke'),
                'type'      => Controls_Manager::TEXT,
                'default'   => 'Jetzt Zimmer reservieren',
                'condition' => [
                    'show_btn' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'btn_url',
            [
                'label'     => __('Button URL', 'drf-augenblicke'),
                'type'      => Controls_Manager::TEXT,
                'default'   => '/tagungsbuero/',
                'condition' => [
                    'show_btn' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $target = !empty($settings['target_date']) ? $settings['target_date'] : '2026-05-28T09:00:00';
        $style = !empty($settings['layout_style']) ? $settings['layout_style'] : 'modern_light';
        $show_btn = ($settings['show_btn'] === 'yes') && !empty($settings['btn_text']);
        $timer_id = 'countdown_' . substr(md5(uniqid('cd', true)), 0, 8);
        ?>
        <div class="container drf-elementor-countdown-wrap">
            <?php if ($style === 'modern_light') : ?>
                <!-- ALTERNATIVE: Modern Light Stat Ticket (contrasts cleanly with dark registration card) -->
                <div class="drf-countdown-card drf-countdown-light">
                    <div class="drf-cd-info">
                        <span class="drf-cd-pill">
                            <span class="drf-cd-pulse"></span>
                            <?php echo esc_html($settings['badge']); ?>
                        </span>
                        <h3 class="drf-cd-heading">
                            <?php echo esc_html($settings['location_title']); ?>
                        </h3>
                        <p class="drf-cd-sub">
                            <span class="dashicons dashicons-calendar-alt" style="color:#f5b301; font-size:16px; width:16px; height:16px; line-height:16px; margin-right:4px;"></span>
                            <?php echo esc_html($settings['dates_text']); ?>
                        </p>
                    </div>

                    <div id="<?php echo esc_attr($timer_id); ?>" class="drf-countdown-timer">
                        <div class="drf-cd-tile">
                            <span class="drf-cd-val cd-days">--</span>
                            <span class="drf-cd-lbl">Tage</span>
                        </div>
                        <div class="drf-cd-tile">
                            <span class="drf-cd-val cd-hours">--</span>
                            <span class="drf-cd-lbl">Stunden</span>
                        </div>
                        <div class="drf-cd-tile">
                            <span class="drf-cd-val cd-minutes">--</span>
                            <span class="drf-cd-lbl">Minuten</span>
                        </div>
                        <div class="drf-cd-tile">
                            <span class="drf-cd-val cd-seconds">--</span>
                            <span class="drf-cd-lbl">Sekunden</span>
                        </div>
                    </div>

                    <?php if ($show_btn) : ?>
                        <div class="drf-cd-action">
                            <a href="<?php echo esc_url($settings['btn_url']); ?>" class="btn btn-primary" style="margin:0;">
                                <span><?php echo esc_html($settings['btn_text']); ?></span>
                                <span class="btn-arrow">&rarr;</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            <?php else : ?>
                <!-- CLASSIC: Dark Box Variant -->
                <div class="drf-countdown-card drf-countdown-dark" style="background: linear-gradient(135deg, #141518 0%, #1e2025 100%); color: #ffffff; border-radius: 14px; padding: 28px; border: 1px solid rgba(245, 179, 1, 0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.18); position: relative; overflow: hidden;">
                    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; position: relative; z-index: 2;">
                        <div>
                            <span style="background: #f5b301; color: #141518; font-weight: 800; font-size: 0.75rem; padding: 3px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                                <?php echo esc_html($settings['badge']); ?>
                            </span>
                            <h3 style="font-size: 1.5rem; font-weight: 800; color: #ffffff; margin: 8px 0 4px 0;">
                                <?php echo esc_html($settings['location_title']); ?>
                            </h3>
                            <p style="color: #94a3b8; font-size: 0.92rem; margin: 0;">
                                📅 <?php echo esc_html($settings['dates_text']); ?>
                            </p>
                        </div>

                        <div id="<?php echo esc_attr($timer_id); ?>" class="drf-countdown-timer" style="display: flex; gap: 10px; align-items: center;">
                            <div class="drf-cd-box" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 65px;">
                                <span class="drf-cd-val cd-days" style="display: block; font-size: 1.8rem; font-weight: 800; color: #f5b301; line-height: 1.1;">--</span>
                                <span class="drf-cd-lbl" style="font-size: 0.7rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.5px;">Tage</span>
                            </div>
                            <div class="drf-cd-box" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 65px;">
                                <span class="drf-cd-val cd-hours" style="display: block; font-size: 1.8rem; font-weight: 800; color: #ffffff; line-height: 1.1;">--</span>
                                <span class="drf-cd-lbl" style="font-size: 0.7rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.5px;">Stunden</span>
                            </div>
                            <div class="drf-cd-box" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 65px;">
                                <span class="drf-cd-val cd-minutes" style="display: block; font-size: 1.8rem; font-weight: 800; color: #ffffff; line-height: 1.1;">--</span>
                                <span class="drf-cd-lbl" style="font-size: 0.7rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.5px;">Minuten</span>
                            </div>
                            <div class="drf-cd-box" style="background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 8px; padding: 10px 14px; text-align: center; min-width: 65px;">
                                <span class="drf-cd-val cd-seconds" style="display: block; font-size: 1.8rem; font-weight: 800; color: #f5b301; line-height: 1.1;">--</span>
                                <span class="drf-cd-lbl" style="font-size: 0.7rem; text-transform: uppercase; color: #cbd5e1; letter-spacing: 0.5px;">Sekunden</span>
                            </div>
                        </div>

                        <?php if ($show_btn) : ?>
                            <div>
                                <a href="<?php echo esc_url($settings['btn_url']); ?>" class="btn btn-primary" style="padding: 10px 20px; font-size: 0.92rem; font-weight: 700; border-radius: 8px; white-space: nowrap; margin:0;">
                                    <span><?php echo esc_html($settings['btn_text']); ?> &rarr;</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <script>
            (function() {
                var target = new Date("<?php echo esc_js($target); ?>").getTime();
                var wrap = document.getElementById("<?php echo esc_js($timer_id); ?>");
                if (!wrap) return;

                function update() {
                    var now = new Date().getTime();
                    var diff = target - now;
                    if (diff < 0) diff = 0;

                    var d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var s = Math.floor((diff % (1000 * 60)) / 1000);

                    var elD = wrap.querySelector('.cd-days');
                    var elH = wrap.querySelector('.cd-hours');
                    var elM = wrap.querySelector('.cd-minutes');
                    var elS = wrap.querySelector('.cd-seconds');

                    if (elD) elD.textContent = d;
                    if (elH) elH.textContent = h < 10 ? '0' + h : h;
                    if (elM) elM.textContent = m < 10 ? '0' + m : m;
                    if (elS) elS.textContent = s < 10 ? '0' + s : s;
                }
                update();
                setInterval(update, 1000);
            })();
            </script>
        </div>

            <script>
            (function() {
                var target = new Date("<?php echo esc_js($target); ?>").getTime();
                var wrap = document.getElementById("<?php echo esc_js($timer_id); ?>");
                if (!wrap) return;

                function update() {
                    var now = new Date().getTime();
                    var diff = target - now;
                    if (diff < 0) diff = 0;

                    var d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    var s = Math.floor((diff % (1000 * 60)) / 1000);

                    var elD = wrap.querySelector('.cd-days');
                    var elH = wrap.querySelector('.cd-hours');
                    var elM = wrap.querySelector('.cd-minutes');
                    var elS = wrap.querySelector('.cd-seconds');

                    if (elD) elD.textContent = d;
                    if (elH) elH.textContent = h < 10 ? '0' + h : h;
                    if (elM) elM.textContent = m < 10 ? '0' + m : m;
                    if (elS) elS.textContent = s < 10 ? '0' + s : s;
                }
                update();
                setInterval(update, 1000);
            })();
            </script>
        </div>
    </div>
    <?php
}
}
