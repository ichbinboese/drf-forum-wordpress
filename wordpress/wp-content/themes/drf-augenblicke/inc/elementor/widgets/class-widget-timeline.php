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

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'year',
            [
                'label'   => __('Jahr', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '2026',
            ]
        );

        $repeater->add_control(
            'code',
            [
                'label'   => __('Kürzel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '[LR]',
            ]
        );

        $repeater->add_control(
            'tag',
            [
                'label'   => __('Titel / Tag', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'AUGENBLICKE',
            ]
        );

        $repeater->add_control(
            'venue',
            [
                'label'   => __('Veranstaltungsort', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Haus Lutherrose, Neuendettelsau',
            ]
        );

        $repeater->add_control(
            'desc',
            [
                'label'   => __('Beschreibung', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'badge',
            [
                'label'   => __('Badge Label', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $repeater->add_control(
            'active',
            [
                'label'        => __('Hervorgehoben / Aktiv', 'drf-augenblicke'),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
            ]
        );

        $this->add_control(
            'milestones',
            [
                'label'       => __('Meilensteine / Treffen', 'drf-augenblicke'),
                'type'        => Controls_Manager::REPEATER,
                'fields'      => $repeater->get_controls(),
                'default'     => [
                    [
                        'year'    => '2027',
                        'code'    => '[LR]',
                        'tag'     => 'AUGENBLICKE XXVII',
                        'venue'   => 'Haus Lutherrose, Neuendettelsau',
                        'desc'    => 'Voranmeldung und Zimmerkontingent-Planung für das Christi-Himmelfahrt-Wochenende (06.–09. Mai 2027) bereits geöffnet.',
                        'badge'   => 'In Vorbereitung',
                        'active'  => 'yes',
                    ],
                    [
                        'year'    => '2026',
                        'code'    => '[LR]',
                        'tag'     => 'AUGENBLICKE XXVI',
                        'venue'   => 'Haus Lutherrose, Neuendettelsau',
                        'desc'    => 'Das kommende Jahrestreffen an Fronleichnam (28.–31. Mai 2026) mit Studioblitz-Aufbauten, SW-Dunkelkammerlabor, Altglas-Shootings und Schnellfotowettbewerb.',
                        'badge'   => 'Aktuelle Anmeldung',
                        'active'  => 'yes',
                    ],
                    [
                        'year'    => '2024',
                        'code'    => '[LR]',
                        'tag'     => 'AUGENBLICKE XXV',
                        'venue'   => 'Haus Lutherrose, Neuendettelsau',
                        'desc'    => 'Das große 25. Jubiläumstreffen: Feierliche Bildprojektionen, Available Light Porträts und Wiedersehen vieler Generationen.',
                        'badge'   => 'Jubiläum',
                        'active'  => '',
                    ],
                    [
                        'year'    => '2023',
                        'code'    => '[FB]',
                        'tag'     => 'AUGENBLICKE XXIV',
                        'venue'   => 'Göbel\'s Schlosshotel, Friedewald',
                        'desc'    => 'Architektur, Landschaft und Available Light in historischer Schlosskulisse.',
                        'badge'   => 'Schloss',
                        'active'  => '',
                    ],
                    [
                        'year'    => '2022',
                        'code'    => '[SE]',
                        'tag'     => 'AUGENBLICKE XXIII',
                        'venue'   => 'Hotel Schwarzenshof, Rudolstadt',
                        'desc'    => 'Das ersehnte Post-Corona Wiedersehen mit Fokus auf Makro- und Naturfotografie in Thüringen.',
                        'badge'   => 'Reunion',
                        'active'  => '',
                    ],
                    [
                        'year'    => '2019',
                        'code'    => '[LR]',
                        'tag'     => 'AUGENBLICKE XXII',
                        'venue'   => 'Haus Lutherrose, Neuendettelsau',
                        'desc'    => 'Premieren-Treffen in Neuendettelsau: Großzügige Seminarräume und Einweihung des neuen Studio-Setups.',
                        'badge'   => 'Meilenstein',
                        'active'  => '',
                    ],
                    [
                        'year'    => '2007',
                        'code'    => '[BM]',
                        'tag'     => 'AUGENBLICKE I',
                        'venue'   => 'Kloster Michaelstein, Blankenburg',
                        'desc'    => 'Die offizielle Geburtsstunde der Treffen-Reihe unter dem Namen »AUGENBLICKE«. Vier Tage intensive Fotografie.',
                        'badge'   => 'Geburt AUGENBLICKE',
                        'active'  => '',
                    ],
                    [
                        'year'    => '1999',
                        'code'    => '[OR]',
                        'tag'     => 'd.r.f Urtreffen',
                        'venue'   => 'Burg Rieneck, Spessart',
                        'desc'    => 'Die Teilnehmer der Newsgroup de.rec.fotografie treffen sich erstmals im echten Leben. Beginn einer bis heute lebendigen Gemeinschaft.',
                        'badge'   => 'Ursprung',
                        'active'  => '',
                    ],
                ],
                'title_field' => '{{{ year }}} - {{{ tag }}}',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $milestones = !empty($settings['milestones']) ? $settings['milestones'] : [
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
            <div class="drf-timeline-section">
            <?php if (!empty($settings['title'])) : ?>
                <div class="drf-section-header">
                    <span class="drf-section-badge">CHRONIK &amp; GESCHICHTE</span>
                    <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                    <?php if (!empty($settings['subtitle'])) : ?>
                        <p class="drf-section-lead"><?php echo esc_html($settings['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="drf-timeline-wrap">
                <?php foreach ($milestones as $m) :
                    $dot_color = $m['active'] ? '#f5b301' : '#94a3b8';
                    ?>
                    <div class="drf-timeline-item">
                        <!-- Timeline Dot -->
                        <div class="drf-timeline-dot" style="border-color: <?php echo esc_attr($dot_color); ?>;"></div>

                        <div class="drf-timeline-card">
                            <div class="drf-timeline-card-header">
                                <div>
                                    <span class="drf-timeline-year"><?php echo esc_html($m['year']); ?></span>
                                    <span class="drf-timeline-tag"><?php echo esc_html($m['tag']); ?></span>
                                    <code class="drf-timeline-code"><?php echo esc_html($m['code']); ?></code>
                                </div>
                                <span class="drf-timeline-badge <?php echo $m['active'] ? 'is-active' : ''; ?>">
                                    <?php echo esc_html($m['badge']); ?>
                                </span>
                            </div>

                            <div class="drf-timeline-venue">
                                📍 <?php echo esc_html($m['venue']); ?>
                            </div>

                            <p class="drf-timeline-desc">
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
