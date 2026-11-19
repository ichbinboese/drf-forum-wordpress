<?php
/**
 * DRF Shuttle Route Planner Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Shuttle_Route_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_shuttle_route';
    }

    public function get_title() {
        return __('d.r.f Shuttle Routen-Planer (Bahn & Transfer)', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-navigation-horizontal';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['shuttle', 'route', 'anreise', 'bahn', 'wicklesgreuth', 'ansbach', 'karte'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_shuttle_route',
            [
                'label' => __('Routen Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Bequeme Anreise & Shuttle-Routen zum Haus Lutherrose',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="container drf-elementor-shuttle-route-wrap">
            <div class="drf-shuttle-route-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); margin: 32px 0;">
            <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 16px; margin-bottom: 24px;">
                <span style="background: #f5b301; color: #141518; font-weight: 800; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;">Kostenloser Service</span>
                <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 8px 0 4px 0;"><?php echo esc_html($settings['title']); ?></h3>
                <p style="color: #64748b; font-size: 0.95rem; margin: 0;">Wir holen dich und dein schweres Fotogepäck direkt am Bahnsteig ab.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
                <!-- Route 1: Wicklesgreuth -->
                <div style="background: #f8fafc; border-left: 4px solid #f5b301; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h4 style="margin: 0; font-size: 1.15rem; color: #1e293b;">Option 1: Bahnhof Wicklesgreuth</h4>
                        <span style="background: #e2e8f0; color: #0f172a; font-weight: 700; font-size: 0.75rem; padding: 2px 6px; border-radius: 4px;">Empfohlen</span>
                    </div>
                    <ul style="margin: 10px 0 16px 18px; color: #475569; font-size: 0.92rem; line-height: 1.6;">
                        <li><strong>Anbindung:</strong> S-Bahn S4 ab Nürnberg Hbf (ca. 28 Min. Fahrzeit) oder Regionalbahnen.</li>
                        <li><strong>Shuttle-Fahrzeit:</strong> Nur ca. 8 Minuten bis zum Haus Lutherrose.</li>
                        <li><strong>Taktung:</strong> Stündlich ab 13:00 bis 19:30 Uhr am Anreisetag.</li>
                    </ul>
                    <a href="/shuttleservice/" class="btn btn-primary" style="font-size: 0.88rem; padding: 8px 16px;">
                        <span>Shuttle ab Wicklesgreuth wählen &rarr;</span>
                    </a>
                </div>

                <!-- Route 2: Ansbach -->
                <div style="background: #f8fafc; border-left: 4px solid #0284c7; border-radius: 8px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <h4 style="margin: 0; font-size: 1.15rem; color: #1e293b;">Option 2: Hauptbahnhof Ansbach</h4>
                        <span style="background: #e0f2fe; color: #0369a1; font-weight: 700; font-size: 0.75rem; padding: 2px 6px; border-radius: 4px;">Fernverkehr / ICE</span>
                    </div>
                    <ul style="margin: 10px 0 16px 18px; color: #475569; font-size: 0.92rem; line-height: 1.6;">
                        <li><strong>Anbindung:</strong> IC- und ICE-Fernverkehr (Strecke Stuttgart &ndash; Nürnberg).</li>
                        <li><strong>Shuttle-Fahrzeit:</strong> ca. 20 Minuten bis zum Tagungshaus.</li>
                        <li><strong>Abholungen:</strong> Feste Slots um 14:30, 16:30 und 18:30 Uhr nach Vereinbarung.</li>
                    </ul>
                    <a href="/shuttleservice/" class="btn" style="background: #0284c7; color: #ffffff; font-size: 0.88rem; padding: 8px 16px; border-radius: 6px; font-weight: 700;">
                        <span>Shuttle ab Ansbach anfragen &rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
}
