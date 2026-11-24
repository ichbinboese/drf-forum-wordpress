<?php
/**
 * DRF Workshops Grid Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Workshops_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_workshops';
    }

    public function get_title() {
        return __('d.r.f Workshops & Sessions Grid', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['workshops', 'sessions', 'studio', 'dunkelkammer', 'altglas', 'makro', 'baryt'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_ws',
            [
                'label' => __('Workshops Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Workshops & Praxis-Sessions',
            ]
        );

        $this->add_control(
            'desc',
            [
                'label'   => __('Einleitung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Bei AUGENBLICKE gibt es keine bezahlten Referenten – unser Programm lebt vom tiefen Erfahrungsschatz und dem handwerklichen Können unserer eigenen Teilnehmer.',
                'rows'    => 2,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $workshops = [
            [
                'cat'   => 'Studio & Licht',
                'bg'    => '#f5b301',
                'color' => '#141518',
                'title' => 'Studioblitz & Lichtführung',
                'desc'  => 'Einführung in Softboxen, Striplights, Beauty Dish und präzise Blitzbelichtungsmessung. Praktische Shootings mit Modellen und wechselnden Licht-Setups.',
            ],
            [
                'cat'   => 'Analog & Labor',
                'bg'    => '#374151',
                'color' => '#ffffff',
                'title' => 'Dunkelkammer: SW-Barytabzüge',
                'desc'  => 'Echtes Handwerk im Tagungslabor: Vergrößerer einstellen, Gradationswandel-Filter, Split-Grade-Belichtung und Archiv-Wässerung für langlebige Barytprints.',
            ],
            [
                'cat'   => 'Optik & Kameras',
                'bg'    => '#f5b301',
                'color' => '#141518',
                'title' => 'Altglas-Charakteristik an Spiegellos',
                'desc'  => 'Klassische Optiken (M42, Leica M, Contax/Yashica) adaptieren. Seifenblasen-Bokeh, Flaring gezielt nutzen und manuelle Scharfstellung mit Peaking & Lupe.',
            ],
            [
                'cat'   => 'Technik & Makro',
                'bg'    => '#374151',
                'color' => '#ffffff',
                'title' => 'Makro- und Focus-Stacking',
                'desc'  => 'Extreme Schärfentiefe im Nahbereich: Elektronischer Makroschlitten, motorisiertes Focus-Bracketing und Verrechnung mit Helicon Focus oder Photoshop.',
            ],
            [
                'cat'   => 'Fine Art Print',
                'bg'    => '#141518',
                'color' => '#f5b301',
                'title' => 'Farbmanagement & Fine-Art-Druck',
                'desc'  => 'Vom kalibrierten Monitor zum Papier: ICC-Profile, Softproof in Lightroom/Photoshop und Papierauswahl (Hahnemühle Photo Rag, Baryta, Matt).',
            ],
            [
                'cat'   => 'Wettbewerb',
                'bg'    => '#1f2937',
                'color' => '#ffffff',
                'title' => 'Schnellfotowettbewerb [SFW]',
                'desc'  => 'Der legendäre Samstagnachmittag: 3 Stunden Zeit, 1 Thema im Umkreis von Neuendettelsau, genau 1 Bild OOC. Projektion und Punktevergabe am Abend!',
            ],
        ];
        ?>
        <div class="container drf-elementor-workshops-wrap">
            <div class="drf-elementor-workshops" style="margin: 28px 0;">
            <h2><?php echo esc_html($settings['title']); ?></h2>
            <p style="color: #64748b; font-size: 1.05rem;"><?php echo esc_html($settings['desc']); ?></p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 24px 0;">
                <?php foreach ($workshops as $ws) : ?>
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                        <span style="background: <?php echo esc_attr($ws['bg']); ?>; color: <?php echo esc_attr($ws['color']); ?>; font-weight: 700; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;">
                            <?php echo esc_html($ws['cat']); ?>
                        </span>
                        <h3 style="margin: 10px 0 6px 0; font-size: 1.2rem; color: #1e293b;"><?php echo esc_html($ws['title']); ?></h3>
                        <p style="font-size: 0.92rem; color: #475569; margin: 0; line-height: 1.5;"><?php echo esc_html($ws['desc']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; margin-top: 24px;">
                <h4 style="margin-top: 0; color: #1e293b;">Eigenen Workshop vorschlagen?</h4>
                <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 14px;">Hast du ein Thema, das du gerne präsentieren möchtest? Reiche deine Session unkompliziert im Flarum Forum ein!</p>
                <a href="/forum/" class="btn btn-primary" style="display: inline-block;">
                    <span>Im Forum vorschlagen &rarr;</span>
                </a>
            </div>
        </div>
    </div>
    <?php
}
}
