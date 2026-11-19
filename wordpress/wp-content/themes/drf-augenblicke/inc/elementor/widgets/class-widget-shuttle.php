<?php
/**
 * DRF Shuttleservice Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Shuttle_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_shuttle';
    }

    public function get_title() {
        return __('d.r.f Shuttleservice & Fahrplan', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-map-pin';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['shuttle', 'bahn', 'wicklesgreuth', 'ansbach', 'anreise', 'transfer'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_shuttle',
            [
                'label' => __('Shuttleservice Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Shuttleservice – Bequem vom Bahnhof zum Tagungshaus',
            ]
        );

        $this->add_control(
            'show_form',
            [
                'label'   => __('Reservierungsformular anzeigen', 'drf-augenblicke'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="container drf-elementor-shuttle-wrap">
            <div class="drf-elementor-shuttle" style="margin: 28px 0;">
            <h2><?php echo esc_html($settings['title']); ?></h2>
            <p>
                Damit alle Bahnreisenden auch mit schwerem Fotogepäck, Stativen und Studioequipment entspannt im <strong>Haus Lutherrose</strong> ankommen, bieten wir einen kostenlosen ehrenamtlichen Shuttleservice an.
            </p>

            <table class="drf-table" style="margin: 20px 0;">
                <thead>
                    <tr>
                        <th>Bahnhof</th>
                        <th>Strecke / Anbindung</th>
                        <th>Fahrzeit zum Tagungshaus</th>
                        <th>Abholzeiten am Anreisetag</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Bahnhof Wicklesgreuth</strong></td>
                        <td>S-Bahn S4 (Nürnberg – Ansbach) &amp; Regionalzüge</td>
                        <td>ca. 8 Minuten</td>
                        <td>Stündlich von 13:00 bis 19:30 Uhr</td>
                    </tr>
                    <tr>
                        <td><strong>Hauptbahnhof Ansbach</strong></td>
                        <td>IC / ICE &amp; Fernverkehr</td>
                        <td>ca. 20 Minuten</td>
                        <td>Nach Vereinbarung (14:30, 16:30, 18:30 Uhr)</td>
                    </tr>
                </tbody>
            </table>

            <?php if ($settings['show_form'] === 'yes') : ?>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-top: 24px; max-width: 600px;">
                    <h3 style="margin-top: 0; color: #1e293b;">Shuttle-Platz vorab reservieren</h3>
                    <form method="post" action="/shuttleservice/">
                        <?php wp_nonce_field('drf_shuttle_action', 'drf_shuttle_nonce'); ?>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">Name *</label>
                            <input type="text" name="shuttle_name" class="drf-input" required placeholder="Dein Name" style="width: 100%;">
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">E-Mail-Adresse *</label>
                            <input type="email" name="shuttle_email" class="drf-input" required placeholder="name@beispiel.de" style="width: 100%;">
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">Bahnhof *</label>
                            <select name="station" class="drf-select" style="width: 100%;">
                                <option value="wicklesgreuth">Bahnhof Wicklesgreuth (Empfohlen)</option>
                                <option value="ansbach">Hauptbahnhof Ansbach</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 10px;">
                            <span>Shuttleplatz anfragen &rarr;</span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
}
