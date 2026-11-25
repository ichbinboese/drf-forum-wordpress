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
                'default' => 'Der Solidarfonds („Sozialsowjet“) & Vertrauenspersonen',
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
        <div class="container drf-elementor-sozial-wrap">
            <div class="drf-elementor-sozial">
                <div class="drf-section-header">
                    <span class="drf-section-badge">SOLIDARITÄT &amp; HILFE</span>
                    <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                    <p class="drf-section-lead">
                        Seit den Anfängen von <code>de.rec.fotografie</code> und den ersten Treffen 1999 gilt ein unumstößlicher Grundsatz: Wir halten als Gemeinschaft zusammen. Niemand soll aus finanziellen Gründen auf das Jahrestreffen verzichten müssen – diskret, vertraulich und unbürokratisch.
                    </p>
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0;">
                    <h3 style="margin-top: 0; color: #141518; font-size: 1.2rem; font-weight: 700;">
                        Vertrauenspersonen des Sozialgremiums
                    </h3>
                    <p style="color: #64748b; font-size: 0.95rem;">Unabhängig und zur absoluten Verschwiegenheit verpflichtet:</p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 12px; margin-top: 14px;">
                        <div style="background:#ffffff; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1;">
                            <div style="font-weight: 700; color: #141518; font-size: 1.05rem;">Wolfgang Exler</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">Vertrauensperson &bull; Sozialgremium</div>
                        </div>
                        <div style="background:#ffffff; padding:12px 16px; border-radius:8px; border:1px solid #cbd5e1;">
                            <div style="font-weight: 700; color: #141518; font-size: 1.05rem;">Dietmar Belloff</div>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 2px;">Vertrauensperson &bull; Sozialgremium</div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 24px;">
                    <div style="background: #141518; color: #ffffff; padding: 22px; border-radius: 12px; border-left: 4px solid #f5b301;">
                        <h4 style="color: #f5b301; margin-top: 0; font-weight: 700;">Vertraulicher Kontakt</h4>
                        <p style="color: #e2e8f0; font-size: 0.95rem; line-height: 1.5;">
                            Fragen zu unb&uuml;rokratischen Zusch&uuml;ssen f&uuml;r Tagungsbeitrag, Fahrtkosten oder Unterkunft bitte diskret an:
                        </p>
                        <p style="font-size: 1.05rem; font-weight: 700; margin-bottom: 0;">
                            <a href="mailto:<?php echo esc_attr($settings['email']); ?>" style="color: #f5b301; text-decoration: none;">
                                <?php echo esc_html($settings['email']); ?>
                            </a>
                        </p>
                    </div>

                    <div style="background: #ffffff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 4px solid #141518;">
                        <h4 style="color: #141518; margin-top: 0; font-weight: 700;">Absolute Vertraulichkeit garantiert</h4>
                        <p style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 0;">
                            Alle Anfragen werden streng diskret und pers&ouml;nlich behandelt &ndash; weder das Tagungsb&uuml;ro noch der Vorstand erfahren Namen oder Details. Der Solidarfonds wird vollst&auml;ndig aus freiwilligen Spenden der Gemeinschaft getragen.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
