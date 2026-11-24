<?php
/**
 * DRF Verein Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Verein_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_verein';
    }

    public function get_title() {
        return __('d.r.f Verein & Gemeinschaft', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-info-box';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['verein', 'gemeinschaft', 'satzung', 'vorstand', 'gemeinnuetzig', 'usenet'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_verein',
            [
                'label' => __('Verein Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'de.rec.fotografie e.V. – Der Verein',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="container drf-elementor-verein-wrap">
            <div class="drf-elementor-verein" style="margin: 28px 0;">
            <div class="drf-section-header">
                <span class="drf-section-badge">VEREIN &amp; VORSTAND</span>
                <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                <p class="drf-section-lead">
                    Der Verein <strong>de.rec.fotografie e.V.</strong> entstand aus der traditionsreichen Usenet-Newsgruppe <code>de.rec.fotografie</code> (d.r.f) – wir fördern die Fotografie als Kunst- und Kulturgut und pflegen das kollegiale Miteinander.
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 24px 0;">
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 22px;">
                    <h3 style="color: #1e293b; margin-top: 0;">Gemeinnütziger Zweck</h3>
                    <p style="color: #475569; font-size: 0.95rem; margin: 0;">
                        Zweck des Vereins ist die Förderung der Fotografie als Kunst- und Kulturgut, die Vermittlung fotografischen Wissens und die Pflege des nichtkommerziellen fachlichen Austauschs.
                    </p>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 22px;">
                    <h3 style="color: #1e293b; margin-top: 0;">Das jährliche Großtreffen</h3>
                    <p style="color: #475569; font-size: 0.95rem; margin: 0;">
                        Einmal im Jahr treffen sich Mitglieder und Gäste bei <strong>»Augenblicke«</strong>: Vier Tage geballte Fotopraxis, Studioaufbauten, analoges Handwerk und persönliche Begegnungen.
                    </p>
                </div>
            </div>

            <div style="background: #141518; color: #ffffff; border-radius: 10px; padding: 24px; border-left: 4px solid #f5b301; margin-top: 24px;">
                <h4 style="color: #f5b301; margin-top: 0;">Mitglied werden &amp; Mitmachen</h4>
                <p style="color: #e2e8f0; margin-bottom: 14px; font-size: 0.95rem;">
                    Jeder Fotografiebegeisterte ist herzlich willkommen – egal ob digital, analog, Hobbyist oder Profi.
                </p>
                <a href="mailto:vorstand@d-r-f.de" style="color: #f5b301; font-weight: 700; text-decoration: none;">
                    &rarr; Kontakt zum Vorstand: vorstand@d-r-f.de
                </a>
            </div>
        </div>
    </div>
    <?php
}
}
