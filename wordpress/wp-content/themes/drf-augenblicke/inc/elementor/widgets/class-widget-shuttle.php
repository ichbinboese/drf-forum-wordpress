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
        <div class="container drf-elementor-shuttle-wrap" id="shuttleForm">
            <div class="drf-elementor-shuttle">
            <?php if ($settings['show_form'] === 'yes') : ?>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 28px; box-shadow: 0 4px 16px rgba(0,0,0,0.05); max-width: 680px; margin: 0 auto;">
                    <?php if (isset($_GET['shuttle_status']) && $_GET['shuttle_status'] === 'success') : ?>
                        <div style="background: #dcfce7; border-left: 4px solid #16a34a; color: #166534; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem;">
                            <strong>✅ Shuttle-Anfrage erhalten!</strong><br>
                            Vielen Dank! Deine Reservierung wurde erfasst und eine Bestätigungs-E-Mail wurde an deine Adresse gesendet.
                        </div>
                    <?php elseif (isset($_GET['shuttle_status']) && $_GET['shuttle_status'] === 'error') : ?>
                        <div style="background: #fef2f2; border-left: 4px solid #dc2626; color: #991b1b; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.95rem;">
                            <strong>⚠️ Bitte überprüfe deine Angaben!</strong><br>
                            Name und eine gültige E-Mail-Adresse sind Pflichtfelder.
                        </div>
                    <?php endif; ?>

                    <div class="drf-section-header" style="margin-bottom: 20px;">
                        <span class="drf-section-badge">RESERVIERUNG</span>
                        <h3 class="drf-section-title" style="margin: 0 0 6px 0; font-size: 1.45rem !important;">Shuttle-Platz vorab reservieren</h3>
                        <p class="drf-section-lead" style="font-size: 0.95rem;">Gib uns deine voraussichtliche Ankunftszeit bekannt, damit wir deinen Transfer verlässlich einplanen können.</p>
                    </div>

                    <form method="post" action="/shuttleservice/#shuttleForm">
                        <?php wp_nonce_field('drf_shuttle_action', 'drf_shuttle_nonce'); ?>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">Name *</label>
                            <input type="text" name="shuttle_name" class="drf-input" required placeholder="Vor- und Nachname" style="width: 100%;">
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">E-Mail-Adresse *</label>
                            <input type="email" name="shuttle_email" class="drf-input" required placeholder="name@beispiel.de" style="width: 100%;">
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">Bahnhof *</label>
                            <select name="station" class="drf-select" style="width: 100%;">
                                <option value="wicklesgreuth">Bahnhof Wicklesgreuth (Empfohlen – 8 Min.)</option>
                                <option value="ansbach">Hauptbahnhof Ansbach (ca. 20 Min.)</option>
                            </select>
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 12px;">
                            <label class="drf-label">Voraussichtliche Ankunftszeit *</label>
                            <input type="text" name="arrival_time" class="drf-input" required placeholder="z.B. 14:45 Uhr (S-Bahn S4)" style="width: 100%;">
                        </div>
                        <div class="drf-form-group" style="margin-bottom: 16px;">
                            <label class="drf-label">Besonderes Gepäck / Stative (Optional)</label>
                            <input type="text" name="luggage_note" class="drf-input" placeholder="z.B. Großformatkoffer, Lichtstative" style="width: 100%;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
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
