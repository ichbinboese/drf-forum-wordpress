<?php
/**
 * DRF Chronik & Historie Elementor Widget
 *
 * @package DRF_Augenblicke
 */

namespace DRF_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Chronik_Widget extends Widget_Base {

    public function get_name() {
        return 'drf_chronik';
    }

    public function get_title() {
        return __('d.r.f Chronik & Treffen-Historie', 'drf-augenblicke');
    }

    public function get_icon() {
        return 'eicon-table';
    }

    public function get_categories() {
        return ['drf-widgets'];
    }

    public function get_keywords() {
        return ['chronik', 'historie', 'tabelle', 'treffen', 'history', 'usenet'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_chronik',
            [
                'label' => __('Chronik Einstellungen', 'drf-augenblicke'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'title',
            [
                'label'   => __('Titel', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXT,
                'default' => 'Chronik aller Treffen (1999 bis heute)',
            ]
        );

        $this->add_control(
            'desc',
            [
                'label'   => __('Einführungstext', 'drf-augenblicke'),
                'type'    => Controls_Manager::TEXTAREA,
                'default' => 'Von den ersten Usenet-Treffen auf Burg Rieneck bis zu den modernen AUGENBLICKE-Großtreffen im Haus Lutherrose Neuendettelsau.',
                'rows'    => 2,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $rows = [
            ['title' => 'AUGENBLICKE XXVII', 'code' => '[LR]', 'year' => '2027', 'venue' => 'Haus Lutherrose, Neuendettelsau', 'focus' => 'Studio, Digital & Analog, Fotowalk Franken'],
            ['title' => 'AUGENBLICKE XXVI', 'code' => '[LR]', 'year' => '2026', 'venue' => 'Haus Lutherrose, Neuendettelsau', 'focus' => 'Studioblitz, Altglas, Großformat, Relaunch'],
            ['title' => 'AUGENBLICKE XXV', 'code' => '[LR]', 'year' => '2024', 'venue' => 'Haus Lutherrose, Neuendettelsau', 'focus' => 'Jubiläumstreffen, Available Light, Porträt'],
            ['title' => 'AUGENBLICKE XXIV', 'code' => '[FB]', 'year' => '2023', 'venue' => 'Göbel\'s Schlosshotel, Friedewald', 'focus' => 'Architektur & Landschaftsfotografie'],
            ['title' => 'AUGENBLICKE XXIII', 'code' => '[SE]', 'year' => '2022', 'venue' => 'Hotel Schwarzenshof, Rudolstadt', 'focus' => 'Post-Corona Wiedersehen & Makroworkshop'],
            ['title' => 'AUGENBLICKE XXII', 'code' => '[LR]', 'year' => '2019', 'venue' => 'Haus Lutherrose, Neuendettelsau', 'focus' => 'High-Speed-Fotografie, Modelsharing'],
            ['title' => 'AUGENBLICKE I', 'code' => '[BM]', 'year' => '2007', 'venue' => 'Kloster Michaelstein, Blankenburg', 'focus' => 'Die Geburtsstunde der »Augenblicke« Reihe'],
            ['title' => 'd.r.f Urtreffen', 'code' => '[OR]', 'year' => '1999', 'venue' => 'Burg Rieneck, Spessart', 'focus' => 'Erstes legendäres Treffen der Newsgroup'],
        ];
        ?>
        <div class="drf-elementor-chronik" style="margin: 28px 0;">
            <?php if (!empty($settings['title'])) : ?>
                <h2 style="margin-bottom: 8px;"><?php echo esc_html($settings['title']); ?></h2>
            <?php endif; ?>
            <?php if (!empty($settings['desc'])) : ?>
                <p style="color: #64748b; margin-bottom: 20px;"><?php echo esc_html($settings['desc']); ?></p>
            <?php endif; ?>

            <div style="overflow-x: auto;">
                <table class="drf-table">
                    <thead>
                        <tr>
                            <th>Treffen</th>
                            <th>K&uuml;rzel</th>
                            <th>Jahr</th>
                            <th>Veranstaltungsort</th>
                            <th>Schwerpunkte</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($row['title']); ?></strong></td>
                                <td><code style="background: #e2e8f0; color: #1e293b; padding: 2px 6px; border-radius: 4px;"><?php echo esc_html($row['code']); ?></code></td>
                                <td><span style="font-weight: 700; color: #b78103;"><?php echo esc_html($row['year']); ?></span></td>
                                <td><?php echo esc_html($row['venue']); ?></td>
                                <td><?php echo esc_html($row['focus']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
}
