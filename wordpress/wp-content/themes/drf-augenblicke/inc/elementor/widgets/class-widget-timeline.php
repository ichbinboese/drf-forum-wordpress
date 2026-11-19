<?php
/**
 * DRF Interactive Timeline Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Timeline_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_timeline';
    }

    public function get_title() {
        return __('d.r.f Interaktive Treffen-Timeline (1999–2027)', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-time-line';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['timeline', 'chronik', 'historie', 'meilensteine', 'treffen', '1999', '2026', '2027'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_timeline',
            [
                'label' => __('Timeline Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Meilensteine & Treffen-Geschichte (1999 – 2027)',
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label'   => __('Untertitel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Über zwei Jahrzehnte lebendige Fotografie-Gemeinschaft: Von der ersten Verabredung im Usenet bis zu den modernen AUGENBLICKE-Großtreffen.',
                'rows'    => 2,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $milestones = [
            [
                'year'    => '2027',
                'code'    => '[LR]',
                'tag'     => 'AUGENBLICKE XXVII',
                'venue'   => 'Haus Lutherrose, Neuendettelsau',
                'desc'    => 'Voranmeldung und Zimmerkontingent-Planung für das Christi-Himmelfahrt-Wochenende (06.–09. Mai 2027) bereits geöffnet.',
                'badge'   => 'In Vorbereitung',
                'active'  => true,
            ],
            [
                'year'    => '2026',
                'code'    => '[LR]',
                'tag'     => 'AUGENBLICKE XXVI',
                'venue'   => 'Haus Lutherrose, Neuendettelsau',
                'desc'    => 'Das kommende Jahrestreffen an Fronleichnam (28.–31. Mai 2026) mit Studioblitz-Aufbauten, SW-Dunkelkammerlabor, Altglas-Shootings und Schnellfotowettbewerb.',
                'badge'   => 'Aktuelle Anmeldung',
                'active'  => true,
            ],
            [
                'year'    => '2024',
                'code'    => '[LR]',
                'tag'     => 'AUGENBLICKE XXV',
                'venue'   => 'Haus Lutherrose, Neuendettelsau',
                'desc'    => 'Das große 25. Jubiläumstreffen: Feierliche Bildprojektionen, Available Light Porträts und Wiedersehen vieler Generationen.',
                'badge'   => 'Jubiläum',
                'active'  => false,
            ],
            [
                'year'    => '2023',
                'code'    => '[FB]',
                'tag'     => 'AUGENBLICKE XXIV',
                'venue'   => 'Göbel\'s Schlosshotel, Friedewald',
                'desc'    => 'Architektur, Landschaft und Available Light in historischer Schlosskulisse.',
                'badge'   => 'Schloss',
                'active'  => false,
            ],
            [
                'year'    => '2022',
                'code'    => '[SE]',
                'tag'     => 'AUGENBLICKE XXIII',
                'venue'   => 'Hotel Schwarzenshof, Rudolstadt',
                'desc'    => 'Das ersehnte Post-Corona Wiedersehen mit Fokus auf Makro- und Naturfotografie in Thüringen.',
                'badge'   => 'Reunion',
                'active'  => false,
            ],
            [
                'year'    => '2019',
                'code'    => '[LR]',
                'tag'     => 'AUGENBLICKE XXII',
                'venue'   => 'Haus Lutherrose, Neuendettelsau',
                'desc'    => 'Premieren-Treffen in Neuendettelsau: Großzügige Seminarräume und Einweihung des neuen Studio-Setups.',
                'badge'   => 'Meilenstein',
                'active'  => false,
            ],
            [
                'year'    => '2007',
                'code'    => '[BM]',
                'tag'     => 'AUGENBLICKE I',
                'venue'   => 'Kloster Michaelstein, Blankenburg',
                'desc'    => 'Die offizielle Geburtsstunde der Treffen-Reihe unter dem Namen »AUGENBLICKE«. Vier Tage intensive Fotografie.',
                'badge'   => 'Geburt AUGENBLICKE',
                'active'  => false,
            ],
            [
                'year'    => '1999',
                'code'    => '[OR]',
                'tag'     => 'd.r.f Urtreffen',
                'venue'   => 'Burg Rieneck, Spessart',
                'desc'    => 'Die Teilnehmer der Newsgroup de.rec.fotografie treffen sich erstmals im echten Leben. Beginn einer bis heute lebendigen Gemeinschaft.',
                'badge'   => 'Ursprung',
                'active'  => false,
            ],
        ];
        ?>
        <div class="container drf-elementor-timeline-wrap">
            <div class="drf-timeline-section" style="margin: 40px 0;">
            <?php if (!empty($settings['title'])) : ?>
                <h2 style="font-size: 1.8rem; font-weight: 800; color: #1e293b; margin-bottom: 6px;"><?php echo esc_html($settings['title']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($settings['subtitle'])) : ?>
                <p style="color: #64748b; font-size: 1rem; margin-bottom: 30px;"><?php echo esc_html($settings['subtitle']); ?></p>
            <?php endif; ?>

            <div class="drf-timeline-wrap" style="position: relative; padding-left: 36px; border-left: 3px solid #e2e8f0; margin-left: 14px;">
                <?php foreach ($milestones as $m) :
                    $dot_color = $m['active'] ? '#f5b301' : '#94a3b8';
                    ?>
                    <div class="drf-timeline-item" style="position: relative; margin-bottom: 32px;">
                        <!-- Timeline Dot -->
                        <div style="position: absolute; left: -46px; top: 4px; width: 22px; height: 22px; border-radius: 50%; background: #ffffff; border: 4px solid <?php echo esc_attr($dot_color); ?>; box-shadow: 0 0 0 4px rgba(245, 179, 1, 0.15);"></div>

                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 20px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: transform 0.2s ease;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">
                                <div>
                                    <span style="font-size: 1.3rem; font-weight: 800; color: #1e293b;"><?php echo esc_html($m['year']); ?></span>
                                    <span style="background: #141518; color: #ffffff; font-weight: 700; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; margin-left: 8px;"><?php echo esc_html($m['tag']); ?></span>
                                    <code style="background: #f1f5f9; color: #0f172a; padding: 2px 6px; border-radius: 4px; font-size: 0.82rem; margin-left: 6px;"><?php echo esc_html($m['code']); ?></code>
                                </div>
                                <span style="background: <?php echo $m['active'] ? '#f5b301' : '#f1f5f9'; ?>; color: <?php echo $m['active'] ? '#141518' : '#64748b'; ?>; font-weight: 700; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">
                                    <?php echo esc_html($m['badge']); ?>
                                </span>
                            </div>

                            <div style="font-weight: 600; color: #b78103; font-size: 0.95rem; margin-bottom: 6px;">
                                📍 <?php echo esc_html($m['venue']); ?>
                            </div>

                            <p style="color: #475569; font-size: 0.92rem; margin: 0; line-height: 1.5;">
                                <?php echo esc_html($m['desc']); ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
}
}
