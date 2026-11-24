<?php
/**
 * DRF Tagungsbüro Team & Infobox Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Tagungsbuero_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_tagungsbuero';
    }

    public function get_title() {
        return __('d.r.f Tagungsbüro Team & Venue', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-person';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['tagungsbuero', 'team', 'members', 'lutherrose', 'kontakt', 'mitglieder'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_tb',
            [
                'label' => __('Tagungsbüro Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Organisation, Betreuung & Verwaltung durch das Tagungsbüro',
            ]
        );

        $this->add_control(
            'email',
            [
                'label'   => __('Kontakt E-Mail', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'tagungsbuero2026@d-r-f.de',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $members = [
            'Bernd Franzen',
            'Burkhard Gudd',
            'Christian Kolinski',
            'Hans-Jörg Schlaberg',
            'Holger Quattelbaum',
            'Jochen Petry',
            'Leoni Schlaberg',
            'Luigi Rotta',
            'Oliver Jennrich',
            'Robert M. Klenk',
            'Robert Steiger',
            'Sebastian Zündorf',
            'Wolfgang Paul'
        ];
        ?>
        <div class="container drf-elementor-tagungsbuero-wrap">
            <div class="drf-elementor-tagungsbuero" style="margin: 28px 0;">
            <div class="drf-section-header">
                <span class="drf-section-badge">TAGUNGSBÜRO</span>
                <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                <p class="drf-section-lead">
                    Ehrenamtlich gewählt aus der Community: Das Tagungsbüro (TB) organisiert den reibungslosen Ablauf, verwaltet die Finanzen treuhänderisch und koordiniert die Zimmerkontingente im Haus Lutherrose.
                </p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0;">
                <h3 style="margin-top: 0; color: #141518; font-size: 1.2rem; font-weight: 700;">
                    Mitglieder des Tagungsb&uuml;ros
                </h3>
                <p style="color: #64748b; font-size: 0.95rem;">Gewählt zur Vorbereitung von AUGENBLICKE XXVI (2026) und Folgejahren:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-top: 14px;">
                    <?php foreach ($members as $m) : ?>
                        <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color: #141518;">
                            <?php echo esc_html($m); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 24px;">
                <div style="background: #141518; color: #ffffff; padding: 22px; border-radius: 12px; border-left: 4px solid #f5b301;">
                    <h4 style="color: #f5b301; margin-top: 0; font-weight: 700;">Kontakt zum Tagungsb&uuml;ro</h4>
                    <p style="color: #e2e8f0; font-size: 0.95rem; line-height: 1.5;">
                        Fragen zum Ablauf, zu Zimmerw&uuml;nschen oder Vorbereitungen bitte an:
                    </p>
                    <p style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0;">
                        <a href="mailto:<?php echo esc_attr($settings['email']); ?>" style="color: #f5b301; text-decoration: none;">
                            <?php echo esc_html($settings['email']); ?>
                        </a>
                    </p>
                </div>

                <div style="background: #ffffff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 4px solid #141518;">
                    <h4 style="color: #141518; margin-top: 0; font-weight: 700;">Tagungsort: Haus Lutherrose</h4>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5;">
                        Wilhelm-L&ouml;he-Str. 20, 91564 Neuendettelsau<br>
                        Mit Seminarr&auml;umen, temporärem Fotostudio, weitläufigen Parkanlagen und Tagungsgastronomie.
                    </p>
                    <p style="margin-bottom: 0; margin-top: 12px;">
                        <a href="https://haus-lutherrose.de/" target="_blank" rel="noopener" style="color: #141518; font-weight: 700; text-decoration: underline;">&rarr; Zur Website von Haus Lutherrose</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
}
}
