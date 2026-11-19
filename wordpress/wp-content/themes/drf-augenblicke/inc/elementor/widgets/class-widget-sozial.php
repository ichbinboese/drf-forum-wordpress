<?php
/**
 * DRF Sozialgremium Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Sozial_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_sozial';
    }

    public function get_title() {
        return __('d.r.f Sozialgremium & Solidarfonds', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-heart';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['sozialgremium', 'solidarfonds', 'sozialsowjet', 'hilfe', 'spenden', 'exler', 'belloff'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_sozial',
            [
                'label' => __('Sozialgremium Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Das Sozialgremium: Solidarität & Vertraulichkeit',
            ]
        );

        $this->add_control(
            'email',
            [
                'label'   => __('Kontakt E-Mail', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'sozialgremium@d-r-f.de',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="drf-elementor-sozial" style="margin: 28px 0;">
            <h2><?php echo esc_html($settings['title']); ?></h2>
            <p>
                Seit den Anf&auml;ngen von de.rec.fotografie und den ersten Treffen 1999 gilt ein fester Grundsatz: <strong>Wir halten als Gemeinschaft zusammen.</strong> Niemand soll aus finanziellen Gr&uuml;nden auf die Teilnahme an &raquo;Augenblicke&laquo; verzichten m&uuml;ssen.
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin: 18px 0 24px 0;">
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 18px;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 1.05rem;">Wolfgang Exler</div>
                    <div style="font-size: 0.85rem; color: #64748b;">Vertrauensperson &bull; Sozialgremium</div>
                </div>
                <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 14px 18px;">
                    <div style="font-weight: 700; color: #1e293b; font-size: 1.05rem;">Dietmar Belloff</div>
                    <div style="font-size: 0.85rem; color: #64748b;">Vertrauensperson &bull; Sozialgremium</div>
                </div>
            </div>

            <div style="background: #fff8e1; border-left: 4px solid #f5b301; padding: 18px 24px; border-radius: 6px;">
                <p style="margin: 0; font-weight: 600; font-size: 1rem; color: #1e293b;">
                    Vertrauliche Anfrage per E-Mail:
                    <a href="mailto:<?php echo esc_attr($settings['email']); ?>" style="color: #b78103; font-weight: 700;">
                        <?php echo esc_html($settings['email']); ?>
                    </a>
                </p>
                <small style="color: #64748b;">Alle Anfragen werden streng vertraulich behandelt &ndash; weder Tagungsbüro noch Vorstand erfahren Namen.</small>
            </div>
        </div>
        <?php
    }
}
