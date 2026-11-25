<?php
/**
 * DRF Workshops Grid Elementor Widget
 *
 * Dynamically queries published workshops from the 'drf_workshop' CPT,
 * provides one-click proposal modal and individual workshop booking modals.
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
        return ['workshops', 'sessions', 'studio', 'dunkelkammer', 'altglas', 'makro', 'baryt', 'praxis'];
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
                'default' => 'Bei AUGENBLICKE gibt es keine bezahlten Referenten – unser Programm lebt vom tiefen Erfahrungsschatz, handwerklichen Können und dem kollegialen Austausch unserer eigenen Teilnehmer.',
                'rows'    => 2,
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        // 1. Fetch published workshops from CPT
        $cpt_workshops = get_posts([
            'post_type'      => 'drf_workshop',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order date',
            'order'          => 'ASC',
        ]);

        $workshops_data = [];

        if (!empty($cpt_workshops)) {
            foreach ($cpt_workshops as $p) {
                $cat      = get_post_meta($p->ID, '_drf_workshop_category', true) ?: 'Praxis & Workshop';
                $bg_color = get_post_meta($p->ID, '_drf_workshop_badge_color', true) ?: '#f5b301';
                $speaker  = get_post_meta($p->ID, '_drf_workshop_speaker', true);
                $time     = get_post_meta($p->ID, '_drf_workshop_time', true);
                $room     = get_post_meta($p->ID, '_drf_workshop_room', true);
                $max_p    = (int) get_post_meta($p->ID, '_drf_workshop_max_participants', true) ?: 12;
                $signups  = get_post_meta($p->ID, '_drf_workshop_signups', true);
                $s_count  = is_array($signups) ? count($signups) : 0;

                $text_color = ($bg_color === '#f5b301' || $bg_color === '#ffffff') ? '#141518' : '#ffffff';

                $workshops_data[] = [
                    'id'          => $p->ID,
                    'title'       => $p->post_title,
                    'desc'        => $p->post_content,
                    'cat'         => $cat,
                    'bg'          => $bg_color,
                    'color'       => $text_color,
                    'speaker'     => $speaker,
                    'time'        => $time,
                    'room'        => $room,
                    'max_p'       => $max_p,
                    'signups'     => $s_count,
                    'spots_left'  => max(0, $max_p - $s_count),
                ];
            }
        } else {
            // Fallback defaults
            $workshops_data = [
                [
                    'id'         => 0,
                    'cat'        => 'Studio & Licht',
                    'bg'         => '#f5b301',
                    'color'      => '#141518',
                    'speaker'    => 'Frank Schliffke',
                    'time'       => 'Freitag, 29. Mai 2026, 14:00 – 17:30 Uhr',
                    'room'       => 'Studio 1, Haus Lutherrose',
                    'max_p'      => 12,
                    'signups'    => 0,
                    'spots_left' => 12,
                    'title'      => 'Studioblitz & Lichtführung',
                    'desc'       => 'Einführung in Softboxen, Striplights, Beauty Dish und präzise Blitzbelichtungsmessung. Praktische Shootings mit Modellen und wechselnden Licht-Setups.',
                ],
                [
                    'id'         => 0,
                    'cat'        => 'Analog & Labor',
                    'bg'         => '#374151',
                    'color'      => '#ffffff',
                    'speaker'    => 'Dr. Michael Breuer',
                    'time'       => 'Samstag, 30. Mai 2026, 10:00 – 13:30 Uhr',
                    'room'       => 'Laborraum Keller, Haus Lutherrose',
                    'max_p'      => 8,
                    'signups'    => 0,
                    'spots_left' => 8,
                    'title'      => 'Dunkelkammer: SW-Barytabzüge',
                    'desc'       => 'Echtes Handwerk im Tagungslabor: Vergrößerer einstellen, Gradationswandel-Filter, Split-Grade-Belichtung und Archiv-Wässerung für langlebige Barytprints.',
                ],
            ];
        }
        ?>
        <div class="container drf-elementor-workshops-wrap">
            <div class="drf-elementor-workshops" style="margin: 28px 0;">
                <div class="drf-section-header" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <span class="drf-section-badge">PROGRAMM &amp; SESSIONS</span>
                        <h2 class="drf-section-title"><?php echo esc_html($settings['title']); ?></h2>
                        <p class="drf-section-lead"><?php echo esc_html($settings['desc']); ?></p>
                    </div>
                    <div style="margin-bottom: 8px;">
                        <button type="button" class="btn btn-primary" onclick="drfOpenModal('modalWorkshopPropose')">
                            <span>✨ Eigenen Workshop vorschlagen</span>
                        </button>
                    </div>
                </div>

                <div class="drf-workshops-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px; margin: 32px 0;">
                    <?php foreach ($workshops_data as $ws) : ?>
                        <div class="drf-workshop-card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 4px 14px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; height: 100%; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 12px; flex-wrap: wrap;">
                                    <span style="background: <?php echo esc_attr($ws['bg']); ?>; color: <?php echo esc_attr($ws['color']); ?>; font-weight: 700; font-size: 0.75rem; padding: 3px 9px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
                                        <?php echo esc_html($ws['cat']); ?>
                                    </span>
                                    <?php if (!empty($ws['max_p'])) : ?>
                                        <span style="font-size: 0.78rem; font-weight: 600; color: <?php echo ($ws['spots_left'] > 0) ? '#16a34a' : '#dc2626'; ?>; background: <?php echo ($ws['spots_left'] > 0) ? '#f0fdf4' : '#fef2f2'; ?>; padding: 2px 7px; border-radius: 4px; border: 1px solid <?php echo ($ws['spots_left'] > 0) ? '#bbf7d0' : '#fecaca'; ?>;">
                                            <?php echo ($ws['spots_left'] > 0) ? sprintf(__('%d Plätze frei', 'drf-augenblicke'), $ws['spots_left']) : __('Ausgebucht (Warteliste)', 'drf-augenblicke'); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <h3 style="margin: 0 0 10px 0; font-size: 1.25rem; font-weight: 700; color: #1e293b; line-height: 1.3;"><?php echo esc_html($ws['title']); ?></h3>

                                <?php if (!empty($ws['speaker'])) : ?>
                                    <div style="font-size: 0.88rem; font-weight: 600; color: #b78103; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                        <span>👤 Leitung:</span> <strong><?php echo esc_html($ws['speaker']); ?></strong>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($ws['time']) || !empty($ws['room'])) : ?>
                                    <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 12px; line-height: 1.4;">
                                        <?php if (!empty($ws['time'])) : ?>
                                            <div>🗓️ <?php echo esc_html($ws['time']); ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($ws['room'])) : ?>
                                            <div>📍 <?php echo esc_html($ws['room']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <p style="font-size: 0.92rem; color: #475569; margin: 0 0 16px 0; line-height: 1.55;"><?php echo esc_html($ws['desc']); ?></p>
                            </div>

                            <div style="padding-top: 16px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                <button type="button" class="btn btn-outline btn-sm" onclick="drfOpenWorkshopSignup(<?php echo (int) $ws['id']; ?>, '<?php echo esc_js($ws['title']); ?>', '<?php echo esc_js($ws['speaker'] ?? ''); ?>', '<?php echo esc_js($ws['cat']); ?>', '<?php echo esc_js($ws['bg']); ?>')" style="width: 100%; justify-content: center;">
                                    <span>Für diesen Workshop anmelden &rarr;</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 26px 20px; text-align: center; margin-top: 28px;">
                    <h4 style="margin-top: 0; color: #1e293b; font-size: 1.15rem;">Möchtest du selbst eine Session leiten?</h4>
                    <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 16px; max-width: 600px; margin-left: auto; margin-right: auto;">
                        Egal ob analoge Entwicklung, High-Speed-Blitzen, Available Light oder Makro – unser Treffen lebt von deinem Wissen. Reiche deinen Vorschlag einfach ein!
                    </p>
                    <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                        <button type="button" class="btn btn-primary" onclick="drfOpenModal('modalWorkshopPropose')">
                            <span>✨ Workshop jetzt vorschlagen</span>
                        </button>
                        <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="btn btn-outline">
                            <span>Im Forum diskutieren &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
