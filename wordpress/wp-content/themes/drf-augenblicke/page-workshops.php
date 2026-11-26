<?php
/**
 * Template Name: Workshops
 * Description: Workshop-Übersicht für Augenblicke
 *
 * @package DRF_Augenblicke
 */

get_header();
$subheading = function_exists('drf_get_page_subheading') ? drf_get_page_subheading() : '';

// Query published workshops from backend
$workshops = get_posts([
    'post_type'      => 'drf_workshop',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC',
]);
?>

<div class="page-hero">
    <div class="container">
        <div class="hero-tag">
            <span class="hero-tag-bar"></span>
            <span>de.rec.fotografie e.V.</span>
        </div>
        <h1 class="page-title"><?php the_title(); ?></h1>
        <?php if (!empty($subheading)) : ?>
            <h2 class="page-lead"><?php echo esc_html($subheading); ?></h2>
        <?php endif; ?>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                <div>
                    <h2 style="margin: 0 0 8px 0;">Unser Workshop-Prinzip</h2>
                    <p style="margin: 0; max-width: 720px;">
                        Bei <strong>AUGENBLICKE</strong> gibt es keine bezahlten Vortragenden &ndash; das Programm lebt vom enormen Wissen und der Praxiserfahrung unserer eigenen Teilnehmer. Jeder kann sein Fachwissen in einer Session, einem Vortrag oder einem praktischen Studioaufbau weitergeben.
                    </p>
                </div>
                <button type="button" class="btn btn-primary" onclick="drfOpenModal('modalWorkshopPropose')">
                    <span>✨ Eigenen Workshop vorschlagen</span>
                </button>
            </div>

            <h2>Geplante Workshops f&uuml;r 2026 (Neuendettelsau)</h2>
            
            <div class="drf-workshops-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 24px 0;">
                <?php if (!empty($workshops)) : ?>
                    <?php foreach ($workshops as $p) :
                        $cat     = get_post_meta($p->ID, '_drf_workshop_category', true) ?: 'Studio & Licht';
                        $color   = get_post_meta($p->ID, '_drf_workshop_badge_color', true) ?: '#f5b301';
                        $tc      = ($color === '#f5b301' || $color === '#ffffff') ? '#141518' : '#ffffff';
                        $speaker = get_post_meta($p->ID, '_drf_workshop_speaker', true);
                        $time    = get_post_meta($p->ID, '_drf_workshop_time', true);
                        $room    = get_post_meta($p->ID, '_drf_workshop_room', true);
                        $max_p   = (int) get_post_meta($p->ID, '_drf_workshop_max_participants', true) ?: 12;
                        $signups = get_post_meta($p->ID, '_drf_workshop_signups', true);
                        $s_count = is_array($signups) ? count($signups) : 0;
                        $spots   = max(0, $max_p - $s_count);
                        ?>
                        <div class="drf-workshop-card" style="background: #ffffff; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 22px; display: flex; flex-direction: column; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                            <div>
                                <div style="display: flex; justify-content: flex-start; align-items: center; margin-bottom: 8px;">
                                    <div style="color: <?php echo esc_attr($tc); ?>; background: <?php echo esc_attr($color); ?>; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 4px; display: inline-block;">
                                        <?php echo esc_html($cat); ?>
                                    </div>
                                </div>
                                <h3 style="margin-top: 4px; font-size: 1.15rem; color: #1e293b;"><?php echo esc_html($p->post_title); ?></h3>
                                <?php if ($speaker) : ?>
                                    <div style="font-size: 0.88rem; font-weight: 600; color: #b78103; margin-bottom: 6px;">👤 <?php echo esc_html($speaker); ?></div>
                                <?php endif; ?>
                                <?php if ($time || $room) : ?>
                                    <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">
                                        <?php if ($time) echo '<div>🗓️ ' . esc_html($time) . '</div>'; ?>
                                        <?php if ($room) echo '<div>📍 ' . esc_html($room) . '</div>'; ?>
                                    </div>
                                <?php endif; ?>
                                <p style="font-size: 0.9rem; color: #4b5563; line-height: 1.5; margin: 0;">
                                    <?php echo esc_html($p->post_content); ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 24px; text-align: center; margin-top: 28px;">
                <h3 style="margin-top: 0; color: #1e293b;">Eigenen Workshop vorschlagen</h3>
                <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 16px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    Du hast ein spannendes Thema, das du gerne vorstellen m&ouml;chtest? Reiche deine Session direkt hier ein oder tausche dich im Flarum Forum mit anderen aus!
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
</div>

<?php
get_footer();
