<?php
/**
 * DRF Workshops Custom Post Type, Admin Approval & Registration System
 *
 * Handles:
 * - CPT 'drf_workshop' with categories, speakers, times, rooms, and participants
 * - Admin approval workflow (pending -> publish) with one-click row action
 * - Auto-seeding of initial core workshops
 * - Frontend AJAX submission for proposing workshops (status: pending)
 * - Frontend AJAX signup for individual workshops (with participant roster)
 * - Email notifications sent from admin-configured email with strict email validation
 *
 * @package DRF_Augenblicke
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Workshops_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);
        add_action('save_post_drf_workshop', [$this, 'save_meta_boxes'], 10, 2);

        // Admin Columns
        add_filter('manage_drf_workshop_posts_columns', [$this, 'filter_columns']);
        add_action('manage_drf_workshop_posts_custom_column', [$this, 'render_column_content'], 10, 2);
        add_filter('post_row_actions', [$this, 'add_approval_row_action'], 10, 2);
        add_action('admin_action_drf_approve_workshop', [$this, 'handle_admin_approval']);

        // Frontend AJAX Handlers
        add_action('wp_ajax_drf_propose_workshop', [$this, 'ajax_propose_workshop']);
        add_action('wp_ajax_nopriv_drf_propose_workshop', [$this, 'ajax_propose_workshop']);

        add_action('wp_ajax_drf_signup_workshop', [$this, 'ajax_signup_workshop']);
        add_action('wp_ajax_nopriv_drf_signup_workshop', [$this, 'ajax_signup_workshop']);

        // Render Modals in wp_footer
        add_action('wp_footer', [$this, 'render_workshop_modals']);

        // Enqueue Assets
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Auto-seed initial workshops
        add_action('admin_init', [$this, 'maybe_seed_workshops']);
    }

    /**
     * Register Custom Post Type 'drf_workshop'
     */
    public function register_cpt() {
        $labels = [
            'name'               => __('Workshops', 'drf-augenblicke'),
            'singular_name'      => __('Workshop', 'drf-augenblicke'),
            'menu_name'          => __('Workshops', 'drf-augenblicke'),
            'name_admin_bar'     => __('Workshop', 'drf-augenblicke'),
            'add_new'            => __('Neuer Workshop', 'drf-augenblicke'),
            'add_new_item'       => __('Neuen Workshop anlegen', 'drf-augenblicke'),
            'new_item'           => __('Neuer Workshop', 'drf-augenblicke'),
            'edit_item'          => __('Workshop bearbeiten', 'drf-augenblicke'),
            'view_item'          => __('Workshop ansehen', 'drf-augenblicke'),
            'all_items'          => __('Alle Workshops', 'drf-augenblicke'),
            'search_items'       => __('Workshops durchsuchen', 'drf-augenblicke'),
            'not_found'          => __('Keine Workshops gefunden.', 'drf-augenblicke'),
            'not_found_in_trash' => __('Keine Workshops im Papierkorb.', 'drf-augenblicke'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'workshop'],
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 24,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
        ];

        register_post_type('drf_workshop', $args);
    }

    /**
     * Register Meta Boxes
     */
    public function register_meta_boxes() {
        add_meta_box(
            'drf_workshop_details',
            __('Workshop Details & Referent', 'drf-augenblicke'),
            [$this, 'render_meta_box_details'],
            'drf_workshop',
            'normal',
            'high'
        );

        add_meta_box(
            'drf_workshop_participants',
            __('Teilnehmerliste & Anmeldungen', 'drf-augenblicke'),
            [$this, 'render_meta_box_participants'],
            'drf_workshop',
            'normal',
            'default'
        );
    }

    /**
     * Render Details Meta Box
     */
    public function render_meta_box_details($post) {
        wp_nonce_field('drf_workshop_meta_save', 'drf_workshop_meta_nonce');

        $speaker       = get_post_meta($post->ID, '_drf_workshop_speaker', true);
        $speaker_email = get_post_meta($post->ID, '_drf_workshop_speaker_email', true);
        $category      = get_post_meta($post->ID, '_drf_workshop_category', true) ?: 'Studio & Licht';
        $badge_color   = get_post_meta($post->ID, '_drf_workshop_badge_color', true) ?: '#f5b301';
        $time          = get_post_meta($post->ID, '_drf_workshop_time', true);
        $room          = get_post_meta($post->ID, '_drf_workshop_room', true);
        $max_p         = get_post_meta($post->ID, '_drf_workshop_max_participants', true) ?: '12';
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="drf_ws_speaker">Referent / Leitung *</label></th>
                <td>
                    <input type="text" id="drf_ws_speaker" name="drf_ws_speaker" value="<?php echo esc_attr($speaker); ?>" class="regular-text" placeholder="z.B. Frank Schliffke">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_speaker_email">E-Mail des Referenten *</label></th>
                <td>
                    <input type="email" id="drf_ws_speaker_email" name="drf_ws_speaker_email" value="<?php echo esc_attr($speaker_email); ?>" class="regular-text" placeholder="name@beispiel.de" required>
                    <p class="description">Pflichtfeld: Für Benachrichtigungen bei neuen Workshop-Anmeldungen.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_category">Kategorie / Themenbereich</label></th>
                <td>
                    <select id="drf_ws_category" name="drf_ws_category" class="regular-text">
                        <?php
                        $cats = ['Studio & Licht', 'Analog & Labor', 'Optik & Kameras', 'Technik & Makro', 'Fine Art Print', 'Wettbewerb', 'Bildgestaltung', 'Sonstiges'];
                        foreach ($cats as $cat) {
                            echo '<option value="' . esc_attr($cat) . '" ' . selected($category, $cat, false) . '>' . esc_html($cat) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_badge_color">Badge Akzentfarbe</label></th>
                <td>
                    <input type="text" id="drf_ws_badge_color" name="drf_ws_badge_color" value="<?php echo esc_attr($badge_color); ?>" class="regular-text" style="width: 120px;" placeholder="#f5b301">
                    <span class="description">Hexadezimalcode (z.B. #f5b301 für Gold, #374151 für Dunkelgrau)</span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_time">Termin & Uhrzeit</label></th>
                <td>
                    <input type="text" id="drf_ws_time" name="drf_ws_time" value="<?php echo esc_attr($time); ?>" class="regular-text" placeholder="z.B. Freitag, 29. Mai 2026, 14:00 – 17:00 Uhr">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_room">Raum / Veranstaltungsort</label></th>
                <td>
                    <input type="text" id="drf_ws_room" name="drf_ws_room" value="<?php echo esc_attr($room); ?>" class="regular-text" placeholder="z.B. Studio 1 / Haus Lutherrose">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="drf_ws_max_participants">Max. Teilnehmerzahl</label></th>
                <td>
                    <input type="number" id="drf_ws_max_participants" name="drf_ws_max_participants" value="<?php echo esc_attr($max_p); ?>" class="small-text" min="1" max="100">
                    <span class="description">Plätze insgesamt</span>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render Participants Meta Box
     */
    public function render_meta_box_participants($post) {
        $signups = get_post_meta($post->ID, '_drf_workshop_signups', true);
        if (!is_array($signups)) {
            $signups = [];
        }
        $max_p = (int) get_post_meta($post->ID, '_drf_workshop_max_participants', true) ?: 12;
        $count = count($signups);

        echo '<div style="margin-bottom: 12px; font-weight: 600;">';
        echo sprintf(__('Aktuell angemeldete Teilnehmer: %d von max. %d Plätzen', 'drf-augenblicke'), $count, $max_p);
        echo '</div>';

        if (empty($signups)) {
            echo '<p style="color: #64748b;"><em>Bislang liegen noch keine Einzelanmeldungen für diesen Workshop vor.</em></p>';
            return;
        }

        echo '<table class="widefat striped" style="margin-top: 8px;">';
        echo '<thead><tr><th>#</th><th>Name</th><th>E-Mail</th><th>Hauptanmeldung</th><th>Notiz</th><th>Datum</th></tr></thead>';
        echo '<tbody>';
        $i = 1;
        foreach ($signups as $s) {
            echo '<tr>';
            echo '<td>' . $i++ . '</td>';
            echo '<td><strong>' . esc_html($s['name'] ?? '—') . '</strong></td>';
            echo '<td><a href="mailto:' . esc_attr($s['email'] ?? '') . '">' . esc_html($s['email'] ?? '—') . '</a></td>';
            echo '<td>' . (!empty($s['registered']) ? '<span style="color:#16a34a; font-weight:700;">Ja</span>' : '<span style="color:#64748b;">Nein</span>') . '</td>';
            echo '<td>' . esc_html($s['notes'] ?? '—') . '</td>';
            echo '<td>' . esc_html($s['time'] ?? '—') . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    }

    /**
     * Save Meta Boxes
     */
    public function save_meta_boxes($post_id, $post) {
        if (!isset($_POST['drf_workshop_meta_nonce']) || !wp_verify_nonce($_POST['drf_workshop_meta_nonce'], 'drf_workshop_meta_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['drf_ws_speaker'])) {
            update_post_meta($post_id, '_drf_workshop_speaker', sanitize_text_field($_POST['drf_ws_speaker']));
        }
        if (isset($_POST['drf_ws_speaker_email'])) {
            update_post_meta($post_id, '_drf_workshop_speaker_email', sanitize_email($_POST['drf_ws_speaker_email']));
        }
        if (isset($_POST['drf_ws_category'])) {
            update_post_meta($post_id, '_drf_workshop_category', sanitize_text_field($_POST['drf_ws_category']));
        }
        if (isset($_POST['drf_ws_badge_color'])) {
            update_post_meta($post_id, '_drf_workshop_badge_color', sanitize_hex_color($_POST['drf_ws_badge_color']) ?: '#f5b301');
        }
        if (isset($_POST['drf_ws_time'])) {
            update_post_meta($post_id, '_drf_workshop_time', sanitize_text_field($_POST['drf_ws_time']));
        }
        if (isset($_POST['drf_ws_room'])) {
            update_post_meta($post_id, '_drf_workshop_room', sanitize_text_field($_POST['drf_ws_room']));
        }
        if (isset($_POST['drf_ws_max_participants'])) {
            update_post_meta($post_id, '_drf_workshop_max_participants', absint($_POST['drf_ws_max_participants']));
        }
    }

    /**
     * Custom Columns in Admin List
     */
    public function filter_columns($columns) {
        $new_cols = [];
        $new_cols['cb']           = $columns['cb'];
        $new_cols['title']        = __('Workshop', 'drf-augenblicke');
        $new_cols['category']     = __('Kategorie', 'drf-augenblicke');
        $new_cols['speaker']      = __('Referent & E-Mail', 'drf-augenblicke');
        $new_cols['participants'] = __('Anmeldungen', 'drf-augenblicke');
        $new_cols['status']       = __('Status / Freigabe', 'drf-augenblicke');
        $new_cols['date']         = $columns['date'];
        return $new_cols;
    }

    public function render_column_content($column, $post_id) {
        switch ($column) {
            case 'category':
                $cat   = get_post_meta($post_id, '_drf_workshop_category', true) ?: 'Studio & Licht';
                $color = get_post_meta($post_id, '_drf_workshop_badge_color', true) ?: '#f5b301';
                $tc    = ($color === '#f5b301' || $color === '#ffffff') ? '#141518' : '#ffffff';
                echo '<span style="background:' . esc_attr($color) . '; color:' . esc_attr($tc) . '; font-weight:700; font-size:0.75rem; padding:3px 8px; border-radius:4px; text-transform:uppercase;">' . esc_html($cat) . '</span>';
                break;

            case 'speaker':
                $spk   = get_post_meta($post_id, '_drf_workshop_speaker', true);
                $email = get_post_meta($post_id, '_drf_workshop_speaker_email', true);
                echo '<strong>' . esc_html($spk ?: '—') . '</strong>';
                if ($email) {
                    echo '<br><small><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></small>';
                }
                break;

            case 'participants':
                $signups = get_post_meta($post_id, '_drf_workshop_signups', true);
                $count   = is_array($signups) ? count($signups) : 0;
                $max_p   = (int) get_post_meta($post_id, '_drf_workshop_max_participants', true) ?: 12;
                $bar_pct = min(100, round(($count / max(1, $max_p)) * 100));
                echo '<strong>' . $count . ' / ' . $max_p . '</strong>';
                echo '<div style="background:#e2e8f0; height:6px; border-radius:3px; overflow:hidden; margin-top:4px; width:80px;"><div style="background:#f5b301; height:100%; width:' . $bar_pct . '%;"></div></div>';
                break;

            case 'status':
                $st = get_post_status($post_id);
                if ($st === 'publish') {
                    echo '<span style="color:#16a34a; font-weight:700; background:#dcfce7; padding:3px 8px; border-radius:4px;">Aktiv & Freigeschaltet</span>';
                } elseif ($st === 'pending') {
                    $approve_url = wp_nonce_url(admin_url('admin.php?action=drf_approve_workshop&post=' . $post_id), 'drf_approve_ws_' . $post_id);
                    echo '<span style="color:#b45309; font-weight:700; background:#fef3c7; padding:3px 8px; border-radius:4px;">Ausstehend (Vorschlag)</span>';
                    echo '<div style="margin-top:5px;"><a href="' . esc_url($approve_url) . '" class="button button-small button-primary" style="background:#16a34a; border-color:#15803d;">✅ Jetzt freischalten</a></div>';
                } else {
                    echo '<span style="color:#64748b;">' . esc_html(get_post_status_object($st)->label ?? $st) . '</span>';
                }
                break;
        }
    }

    /**
     * Add Quick Approve action in row actions
     */
    public function add_approval_row_action($actions, $post) {
        if ($post->post_type === 'drf_workshop' && $post->post_status === 'pending') {
            $approve_url = wp_nonce_url(admin_url('admin.php?action=drf_approve_workshop&post=' . $post->ID), 'drf_approve_ws_' . $post->ID);
            $actions['drf_approve'] = '<a href="' . esc_url($approve_url) . '" style="color:#16a34a; font-weight:bold;">✅ Freischalten</a>';
        }
        return $actions;
    }

    /**
     * Handle Admin Approval Action
     */
    public function handle_admin_approval() {
        if (!current_user_can('edit_posts')) {
            wp_die(__('Keine Berechtigung.', 'drf-augenblicke'));
        }

        $post_id = absint($_GET['post'] ?? 0);
        check_admin_referer('drf_approve_ws_' . $post_id);

        wp_update_post([
            'ID'          => $post_id,
            'post_status' => 'publish',
        ]);

        // Notify Speaker
        $speaker_email = get_post_meta($post_id, '_drf_workshop_speaker_email', true);
        $speaker_name  = get_post_meta($post_id, '_drf_workshop_speaker', true);
        $ws_title      = get_the_title($post_id);

        if (!empty($speaker_email) && is_email($speaker_email)) {
            $admin_email = function_exists('drf_get_theme_option') ? drf_get_theme_option('contact_email', get_option('admin_email')) : get_option('admin_email');
            $subject     = sprintf('[d.r.f] Dein Workshop »%s« wurde freigeschaltet!', $ws_title);
            $message     = "Hallo {$speaker_name},\n\ngute Nachrichten: Dein Workshop-Vorschlag »{$ws_title}« wurde von den Administratoren geprüft und soeben offiziell für das Programm von AUGENBLICKE freigeschaltet!\n\nEr ist ab sofort auf der Workshop-Seite für alle Teilnehmer sichtbar und buchbar.\n\nHerzliche Grüße,\nDein d.r.f Orga-Team\nhttps://drf-augenblicke.de/workshops/";
            $headers     = ['From: de.rec.fotografie e.V. <' . $admin_email . '>'];
            @wp_mail($speaker_email, $subject, $message, $headers);
        }

        wp_safe_redirect(admin_url('edit.php?post_type=drf_workshop&drf_approved=1'));
        exit;
    }

    /**
     * AJAX: Propose Workshop (Status: pending)
     */
    public function ajax_propose_workshop() {
        check_ajax_referer('drf_workshop_nonce', 'security');

        $title   = sanitize_text_field($_POST['ws_title'] ?? '');
        $cat     = sanitize_text_field($_POST['ws_category'] ?? 'Studio & Licht');
        $speaker = sanitize_text_field($_POST['ws_speaker'] ?? '');
        $email   = sanitize_email($_POST['ws_email'] ?? '');
        $desc    = sanitize_textarea_field($_POST['ws_desc'] ?? '');
        $room    = sanitize_text_field($_POST['ws_room'] ?? '');
        $max_p   = absint($_POST['ws_max_participants'] ?? 12);

        if (empty($title) || empty($speaker) || empty($email) || empty($desc)) {
            wp_send_json_error(['message' => __('Bitte fülle alle Pflichtfelder (Titel, Referent, E-Mail-Adresse und Beschreibung) aus.', 'drf-augenblicke')]);
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Bitte gib eine gültige E-Mail-Adresse an.', 'drf-augenblicke')]);
        }

        // Color mapping based on category
        $color = '#f5b301';
        if (strpos($cat, 'Analog') !== false || strpos($cat, 'Labor') !== false) {
            $color = '#374151';
        } elseif (strpos($cat, 'Fine Art') !== false) {
            $color = '#141518';
        } elseif (strpos($cat, 'Wettbewerb') !== false) {
            $color = '#1f293b';
        }

        $post_id = wp_insert_post([
            'post_title'   => $title,
            'post_content' => $desc,
            'post_status'  => 'pending',
            'post_type'    => 'drf_workshop',
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error(['message' => __('Fehler beim Speichern: ', 'drf-augenblicke') . $post_id->get_error_message()]);
        }

        update_post_meta($post_id, '_drf_workshop_speaker', $speaker);
        update_post_meta($post_id, '_drf_workshop_speaker_email', $email);
        update_post_meta($post_id, '_drf_workshop_category', $cat);
        update_post_meta($post_id, '_drf_workshop_badge_color', $color);
        update_post_meta($post_id, '_drf_workshop_room', $room);
        update_post_meta($post_id, '_drf_workshop_max_participants', $max_p ?: 12);
        update_post_meta($post_id, '_drf_workshop_signups', []);

        // Send Email Notification to Admin
        $admin_email = function_exists('drf_get_theme_option') ? drf_get_theme_option('contact_email', get_option('admin_email')) : get_option('admin_email');
        $admin_edit_url = admin_url('post.php?post=' . $post_id . '&action=edit');
        $headers = ['From: de.rec.fotografie e.V. <' . $admin_email . '>'];

        $admin_subject = sprintf('[d.r.f] Neuer Workshop-Vorschlag eingereicht: »%s«', $title);
        $admin_msg     = "Hallo Admin,\n\nes wurde soeben ein neuer Workshop-Vorschlag eingereicht und wartet auf deine Freigabe:\n\n";
        $admin_msg    .= "- Titel: {$title}\n";
        $admin_msg    .= "- Kategorie: {$cat}\n";
        $admin_msg    .= "- Referent: {$speaker} ({$email})\n";
        $admin_msg    .= "- Beschreibung:\n{$desc}\n\n";
        $admin_msg    .= "Jetzt im Admin prüfen & freischalten:\n{$admin_edit_url}\n\n";
        $admin_msg    .= "d.r.f Platform Automation";

        @wp_mail($admin_email, $admin_subject, $admin_msg, $headers);

        // Confirmation to proposer
        $proposer_subject = sprintf('[d.r.f] Eingang deines Workshop-Vorschlags: »%s«', $title);
        $proposer_msg     = "Hallo {$speaker},\n\nvielen Dank für deinen Vorschlag für die AUGENBLICKE-Tagung!\n\nWir haben deine Einreichung »{$title}« erhalten. Ein Administrator prüft die Raum- und Zeitplanung und schaltet den Workshop in Kürze frei. Du erhältst eine Benachrichtigung, sobald er aktiv ist.\n\nHerzliche Grüße,\nDein d.r.f Tagungsbüro";

        @wp_mail($email, $proposer_subject, $proposer_msg, $headers);

        wp_send_json_success([
            'message' => sprintf(
                __('Vielen Dank, %s! Dein Workshop-Vorschlag »%s« wurde erfolgreich eingereicht. Ein Administrator prüft ihn und schaltet ihn zeitnah frei.', 'drf-augenblicke'),
                esc_html($speaker),
                esc_html($title)
            ),
        ]);
    }

    /**
     * AJAX: Individual Workshop Signup
     */
    public function ajax_signup_workshop() {
        check_ajax_referer('drf_workshop_nonce', 'security');

        $ws_id      = absint($_POST['workshop_id'] ?? 0);
        $name       = sanitize_text_field($_POST['signup_name'] ?? '');
        $email      = sanitize_email($_POST['signup_email'] ?? '');
        $registered = !empty($_POST['signup_registered']) ? true : false;
        $notes      = sanitize_textarea_field($_POST['signup_notes'] ?? '');

        if (!$ws_id || empty($name) || empty($email)) {
            wp_send_json_error(['message' => __('Bitte gib deinen Namen und eine gültige E-Mail-Adresse an.', 'drf-augenblicke')]);
        }

        if (!is_email($email)) {
            wp_send_json_error(['message' => __('Bitte gib eine gültige E-Mail-Adresse ein.', 'drf-augenblicke')]);
        }

        $post = get_post($ws_id);
        if (!$post || $post->post_type !== 'drf_workshop') {
            wp_send_json_error(['message' => __('Workshop nicht gefunden.', 'drf-augenblicke')]);
        }

        $signups = get_post_meta($ws_id, '_drf_workshop_signups', true);
        if (!is_array($signups)) {
            $signups = [];
        }

        // Check if already registered
        foreach ($signups as $s) {
            if (strtolower($s['email']) === strtolower($email)) {
                wp_send_json_error(['message' => __('Du bist bereits mit dieser E-Mail-Adresse für diesen Workshop eingetragen.', 'drf-augenblicke')]);
            }
        }

        $max_p = (int) get_post_meta($ws_id, '_drf_workshop_max_participants', true) ?: 12;
        if (count($signups) >= $max_p) {
            // Waitlist note
            $waitlisted = true;
        } else {
            $waitlisted = false;
        }

        $signups[] = [
            'name'       => $name,
            'email'      => $email,
            'registered' => $registered,
            'notes'      => $notes,
            'time'       => current_time('mysql'),
            'waitlisted' => $waitlisted,
        ];

        update_post_meta($ws_id, '_drf_workshop_signups', $signups);

        // Send Email Confirmation
        $admin_email   = function_exists('drf_get_theme_option') ? drf_get_theme_option('contact_email', get_option('admin_email')) : get_option('admin_email');
        $speaker_email = get_post_meta($ws_id, '_drf_workshop_speaker_email', true);
        $headers       = ['From: de.rec.fotografie e.V. <' . $admin_email . '>'];

        $subject = sprintf('[d.r.f] Anmeldebestätigung: %s »%s«', $waitlisted ? 'Warteliste' : 'Workshop', $post->post_title);
        $msg     = "Hallo {$name},\n\ndu bist verbindlich für folgenden Praxis-Workshop bei AUGENBLICKE 2026 eingetragen:\n\n";
        $msg    .= "- Workshop: {$post->post_title}\n";
        $msg    .= "- Status: " . ($waitlisted ? 'Warteliste (Max. Teilnehmerzahl erreicht)' : 'Fester Teilnehmerplatz bestätigt') . "\n";
        $msg    .= "- Termin/Ort: " . (get_post_meta($ws_id, '_drf_workshop_time', true) ?: 'Fronleichnam 2026 / Haus Lutherrose') . "\n";
        if (!empty($notes)) $msg .= "- Deine Anmerkung: {$notes}\n";
        $msg    .= "\nWir freuen uns auf deine Teilnahme und spannende praktische Ergebnisse!\n\nHerzliche Grüße,\nDein d.r.f Tagungsbüro";

        @wp_mail($email, $subject, $msg, $headers);

        // Notify Speaker
        if (!empty($speaker_email) && is_email($speaker_email)) {
            $spk_subj = sprintf('[d.r.f] Neue Teilnehmeranmeldung für deinen Workshop »%s«', $post->post_title);
            $spk_msg  = "Hallo,\n\nes gibt eine neue Anmeldung für deinen Workshop »{$post->post_title}«:\n\n";
            $spk_msg .= "- Teilnehmer: {$name} ({$email})\n";
            $spk_msg .= "- Bereits für Tagung angemeldet: " . ($registered ? 'Ja' : 'Noch offen') . "\n";
            if (!empty($notes)) $spk_msg .= "- Notiz / Vorkenntnisse: {$notes}\n";
            $spk_msg .= "\nAktuelle Teilnehmerzahl: " . count($signups) . " von max. {$max_p}\n\nDein d.r.f System";
            @wp_mail($speaker_email, $spk_subj, $spk_msg, $headers);
        }

        wp_send_json_success([
            'message' => sprintf(
                __('Vielen Dank, %s! Du wurdest erfolgreich für den Workshop »%s« angemeldet. Eine Bestätigung wurde an %s gesendet.', 'drf-augenblicke'),
                esc_html($name),
                esc_html($post->post_title),
                esc_html($email)
            ),
            'currentCount' => count($signups),
            'maxCount'     => $max_p,
        ]);
    }

    /**
     * Enqueue JS and CSS for Workshop Modals
     */
    public function enqueue_assets() {
        wp_localize_script('drf-theme-script', 'drf_workshops_data', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('drf_workshop_nonce'),
        ]);
    }

    /**
     * Render Modals in Footer
     */
    public function render_workshop_modals() {
        ?>
        <!-- MODAL 1: Eigenen Workshop vorschlagen -->
        <div id="modalWorkshopPropose" class="drf-modal" role="dialog" aria-modal="true" aria-labelledby="modalProposeTitle" style="display: none;">
            <div class="drf-modal-backdrop" onclick="drfCloseModal('modalWorkshopPropose')"></div>
            <div class="drf-modal-dialog">
                <div class="drf-modal-content">
                    <div class="drf-modal-header">
                        <div class="drf-modal-tag">AUGENBLICKE PROGRAMM</div>
                        <h3 id="modalProposeTitle" class="drf-modal-title">Eigenen Workshop vorschlagen</h3>
                        <button type="button" class="drf-modal-close" onclick="drfCloseModal('modalWorkshopPropose')" aria-label="Schließen">&times;</button>
                    </div>
                    <div class="drf-modal-body">
                        <p class="drf-modal-lead">Unser Programm lebt vom Praxiseinsatz unserer Mitglieder. Reiche deine Session oder deinen Fotowalk ein – wir unterstützen dich bei Räumen und Material!</p>
                        
                        <div id="drfProposeAlert" class="drf-alert" style="display: none;"></div>

                        <form id="drfWorkshopProposeForm" onsubmit="drfSubmitWorkshopPropose(event)">
                            <div class="drf-form-group">
                                <label class="drf-label" for="ws_title">Workshop-Titel *</label>
                                <input type="text" id="ws_title" name="ws_title" class="drf-input" required placeholder="z.B. Großformat 4x5: Mattscheibe & Tilt-Shift Praxis">
                            </div>

                            <div class="drf-form-row drf-modal-cols-2">
                                <div class="drf-form-group">
                                    <label class="drf-label" for="ws_category">Kategorie / Bereich *</label>
                                    <select id="ws_category" name="ws_category" class="drf-select" required>
                                        <option value="Studio & Licht">Studio & Licht</option>
                                        <option value="Analog & Labor">Analog & Labor</option>
                                        <option value="Optik & Kameras">Optik & Kameras</option>
                                        <option value="Technik & Makro">Technik & Makro</option>
                                        <option value="Fine Art Print">Fine Art Print</option>
                                        <option value="Wettbewerb">Wettbewerb & Fotowalk</option>
                                        <option value="Bildgestaltung">Bildgestaltung & Portfolio</option>
                                    </select>
                                </div>
                                <div class="drf-form-group">
                                    <label class="drf-label" for="ws_max_participants">Max. Teilnehmerzahl</label>
                                    <input type="number" id="ws_max_participants" name="ws_max_participants" class="drf-input" value="12" min="1" max="100">
                                </div>
                            </div>

                            <div class="drf-form-row drf-modal-cols-2">
                                <div class="drf-form-group">
                                    <label class="drf-label" for="ws_speaker">Dein Name / Referent *</label>
                                    <input type="text" id="ws_speaker" name="ws_speaker" class="drf-input" required placeholder="Vor- und Nachname">
                                </div>
                                <div class="drf-form-group">
                                    <label class="drf-label" for="ws_email">Deine E-Mail-Adresse *</label>
                                    <input type="email" id="ws_email" name="ws_email" class="drf-input" required placeholder="name@beispiel.de">
                                </div>
                            </div>

                            <div class="drf-form-group">
                                <label class="drf-label" for="ws_desc">Ausführliche Beschreibung & Ablauf *</label>
                                <textarea id="ws_desc" name="ws_desc" class="drf-textarea" rows="4" required placeholder="Was lernen die Teilnehmer? Welche Ausrüstung wird gebraucht? Wie läuft die Session ab?"></textarea>
                            </div>

                            <div class="drf-form-group">
                                <label class="drf-label" for="ws_room">Raumwünsche / benötigte Ausstattung (Optional)</label>
                                <input type="text" id="ws_room" name="ws_room" class="drf-input" placeholder="z.B. Abdunkelbarer Raum, Beamer, Modellhintergrund">
                            </div>

                            <div class="drf-modal-actions">
                                <button type="button" class="btn btn-outline" onclick="drfCloseModal('modalWorkshopPropose')">Abbrechen</button>
                                <button type="submit" id="btnSubmitPropose" class="btn btn-primary">
                                    <span>Vorschlag verbindlich einreichen &rarr;</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL 2: Für Einzelworkshop anmelden -->
        <div id="modalWorkshopSignup" class="drf-modal" role="dialog" aria-modal="true" aria-labelledby="modalSignupTitle" style="display: none;">
            <div class="drf-modal-backdrop" onclick="drfCloseModal('modalWorkshopSignup')"></div>
            <div class="drf-modal-dialog">
                <div class="drf-modal-content">
                    <div class="drf-modal-header">
                        <div class="drf-modal-tag">TEILNAHME-ANMELDUNG</div>
                        <h3 id="modalSignupTitle" class="drf-modal-title">Für Workshop anmelden</h3>
                        <button type="button" class="drf-modal-close" onclick="drfCloseModal('modalWorkshopSignup')" aria-label="Schließen">&times;</button>
                    </div>
                    <div class="drf-modal-body">
                        <div class="drf-selected-workshop-box">
                            <span id="signupWsBadge" class="drf-ws-badge">STUDIO</span>
                            <h4 id="signupWsTitle" class="drf-selected-title">Studioblitz &amp; Lichtführung</h4>
                            <p id="signupWsMeta" class="drf-selected-meta">Referent: Frank Schliffke</p>
                        </div>

                        <div id="drfSignupAlert" class="drf-alert" style="display: none;"></div>

                        <form id="drfWorkshopSignupForm" onsubmit="drfSubmitWorkshopSignup(event)">
                            <input type="hidden" id="signup_ws_id" name="workshop_id" value="0">

                            <div class="drf-form-row drf-modal-cols-2">
                                <div class="drf-form-group">
                                    <label class="drf-label" for="signup_name">Dein Name *</label>
                                    <input type="text" id="signup_name" name="signup_name" class="drf-input" required placeholder="Vor- und Nachname">
                                </div>
                                <div class="drf-form-group">
                                    <label class="drf-label" for="signup_email">E-Mail-Adresse *</label>
                                    <input type="email" id="signup_email" name="signup_email" class="drf-input" required placeholder="name@beispiel.de">
                                </div>
                            </div>

                            <div class="drf-form-group" style="margin: 16px 0;">
                                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.92rem; color: #374151;">
                                    <input type="checkbox" id="signup_registered" name="signup_registered" value="1" checked style="margin-top: 3px;">
                                    <span>Ich habe mich bereits für das Haupttreffen AUGENBLICKE 2026 angemeldet (bzw. plane dies in Kürze).</span>
                                </label>
                            </div>

                            <div class="drf-form-group">
                                <label class="drf-label" for="signup_notes">Anmerkung / Vorkenntnisse / Equipment (Optional)</label>
                                <textarea id="signup_notes" name="signup_notes" class="drf-textarea" rows="2" placeholder="z.B. Eigene analoge Mittelformatkamera vorhanden, keine Vorkenntnisse"></textarea>
                            </div>

                            <div class="drf-modal-actions">
                                <button type="button" class="btn btn-outline" onclick="drfCloseModal('modalWorkshopSignup')">Abbrechen</button>
                                <button type="submit" id="btnSubmitSignup" class="btn btn-primary">
                                    <span>Platz verbindlich sichern &rarr;</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function drfOpenModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.classList.add('drf-modal-open');
            }
        }

        function drfCloseModal(modalId) {
            var modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'none';
                document.body.classList.remove('drf-modal-open');
            }
        }

        function drfOpenWorkshopSignup(id, title, speaker, category, color) {
            document.getElementById('signup_ws_id').value = id;
            document.getElementById('signupWsTitle').textContent = title;
            document.getElementById('signupWsMeta').textContent = speaker ? ('Referent: ' + speaker) : '';
            var badge = document.getElementById('signupWsBadge');
            badge.textContent = category || 'Workshop';
            if (color) {
                badge.style.background = color;
                badge.style.color = (color === '#f5b301') ? '#141518' : '#ffffff';
            }
            document.getElementById('drfSignupAlert').style.display = 'none';
            drfOpenModal('modalWorkshopSignup');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                drfCloseModal('modalWorkshopPropose');
                drfCloseModal('modalWorkshopSignup');
            }
        });

        function drfSubmitWorkshopPropose(e) {
            e.preventDefault();
            var btn = document.getElementById('btnSubmitPropose');
            var alertBox = document.getElementById('drfProposeAlert');
            btn.disabled = true;
            btn.innerHTML = '<span>Wird übermittelt...</span>';

            var formData = new FormData(document.getElementById('drfWorkshopProposeForm'));
            formData.append('action', 'drf_propose_workshop');
            formData.append('security', '<?php echo wp_create_nonce('drf_workshop_nonce'); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = '<span>Vorschlag verbindlich einreichen &rarr;</span>';
                alertBox.style.display = 'block';
                if (data.success) {
                    alertBox.className = 'drf-alert drf-alert-success';
                    alertBox.textContent = data.data.message;
                    document.getElementById('drfWorkshopProposeForm').reset();
                    setTimeout(function() { drfCloseModal('modalWorkshopPropose'); }, 3500);
                } else {
                    alertBox.className = 'drf-alert drf-alert-error';
                    alertBox.textContent = data.data.message || 'Ein Fehler ist aufgetreten.';
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = '<span>Vorschlag verbindlich einreichen &rarr;</span>';
                alertBox.style.display = 'block';
                alertBox.className = 'drf-alert drf-alert-error';
                alertBox.textContent = 'Netzwerkfehler beim Absenden.';
            });
        }

        function drfSubmitWorkshopSignup(e) {
            e.preventDefault();
            var btn = document.getElementById('btnSubmitSignup');
            var alertBox = document.getElementById('drfSignupAlert');
            btn.disabled = true;
            btn.innerHTML = '<span>Wird gespeichert...</span>';

            var formData = new FormData(document.getElementById('drfWorkshopSignupForm'));
            formData.append('action', 'drf_signup_workshop');
            formData.append('security', '<?php echo wp_create_nonce('drf_workshop_nonce'); ?>');

            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                btn.disabled = false;
                btn.innerHTML = '<span>Platz verbindlich sichern &rarr;</span>';
                alertBox.style.display = 'block';
                if (data.success) {
                    alertBox.className = 'drf-alert drf-alert-success';
                    alertBox.textContent = data.data.message;
                    document.getElementById('drfWorkshopSignupForm').reset();
                    setTimeout(function() { drfCloseModal('modalWorkshopSignup'); }, 3000);
                } else {
                    alertBox.className = 'drf-alert drf-alert-error';
                    alertBox.textContent = data.data.message || 'Ein Fehler ist aufgetreten.';
                }
            })
            .catch(function(err) {
                btn.disabled = false;
                btn.innerHTML = '<span>Platz verbindlich sichern &rarr;</span>';
                alertBox.style.display = 'block';
                alertBox.className = 'drf-alert drf-alert-error';
                alertBox.textContent = 'Netzwerkfehler beim Absenden.';
            });
        }
        </script>
        <?php
    }

    /**
     * Auto-seed initial workshops if empty
     */
    public function maybe_seed_workshops() {
        $count = wp_count_posts('drf_workshop');
        $total = ($count->publish ?? 0) + ($count->pending ?? 0) + ($count->draft ?? 0);
        if ($total > 0) {
            return;
        }

        $seeds = [
            [
                'title'    => 'Studioblitz & Lichtführung',
                'cat'      => 'Studio & Licht',
                'speaker'  => 'Frank Schliffke',
                'email'    => 'vorstand@d-r-f.de',
                'color'    => '#f5b301',
                'room'     => 'Studio 1, Haus Lutherrose',
                'time'     => 'Freitag, 29. Mai 2026, 14:00 – 17:30 Uhr',
                'max_p'    => 12,
                'content'  => 'Einführung in Softboxen, Striplights, Beauty Dish und präzise Blitzbelichtungsmessung. Praktische Shootings mit Modellen und wechselnden Licht-Setups in Kleingruppen.',
            ],
            [
                'title'    => 'Dunkelkammer: SW-Barytabzüge',
                'cat'      => 'Analog & Labor',
                'speaker'  => 'Dr. Michael Breuer',
                'email'    => 'vorstand@d-r-f.de',
                'color'    => '#374151',
                'room'     => 'Laborraum Keller, Haus Lutherrose',
                'time'     => 'Samstag, 30. Mai 2026, 10:00 – 13:30 Uhr',
                'max_p'    => 8,
                'content'  => 'Echtes Handwerk im Tagungslabor: Vergrößerer einstellen, Gradationswandel-Filter, Split-Grade-Belichtung und Archiv-Wässerung für langlebige Barytprints.',
            ],
            [
                'title'    => 'Altglas-Charakteristik an Spiegellos',
                'cat'      => 'Optik & Kameras',
                'speaker'  => 'Thomas Weber',
                'email'    => 'kasse@d-r-f.de',
                'color'    => '#f5b301',
                'room'     => 'Seminarraum 2 & Schlosspark',
                'time'     => 'Freitag, 29. Mai 2026, 10:00 – 12:30 Uhr',
                'max_p'    => 15,
                'content'  => 'Klassische Optiken (M42, Leica M, Contax/Yashica) adaptieren. Seifenblasen-Bokeh, Flaring gezielt nutzen und manuelle Scharfstellung mit Peaking & Lupe.',
            ],
            [
                'title'    => 'Makro- und Focus-Stacking',
                'cat'      => 'Technik & Makro',
                'speaker'  => 'Helicon-Team',
                'email'    => 'tagungsbuero2026@d-r-f.de',
                'color'    => '#374151',
                'room'     => 'Schulungsraum 1',
                'time'     => 'Samstag, 30. Mai 2026, 14:30 – 17:00 Uhr',
                'max_p'    => 10,
                'content'  => 'Extreme Schärfentiefe im Nahbereich: Elektronischer Makroschlitten, motorisiertes Focus-Bracketing und Verrechnung mit Helicon Focus oder Photoshop.',
            ],
            [
                'title'    => 'Farbmanagement & Fine-Art-Druck',
                'cat'      => 'Fine Art Print',
                'speaker'  => 'Druck-Experten',
                'email'    => 'tagungsbuero2026@d-r-f.de',
                'color'    => '#141518',
                'room'     => 'Tagungsbüro Lounge',
                'time'     => 'Donnerstag, 28. Mai 2026, 16:00 – 18:30 Uhr',
                'max_p'    => 12,
                'content'  => 'Vom kalibrierten Monitor zum Papier: ICC-Profile, Softproof in Lightroom/Photoshop und Papierauswahl (Hahnemühle Photo Rag, Baryta, Matt).',
            ],
            [
                'title'    => 'Schnellfotowettbewerb [SFW]',
                'cat'      => 'Wettbewerb',
                'speaker'  => 'Orga-Team Augenblicke',
                'email'    => 'tagungsbuero2026@d-r-f.de',
                'color'    => '#1f293b',
                'room'     => 'Großer Saal & Umkreis Neuendettelsau',
                'time'     => 'Samstagnachmittag (14:00 – 17:00 Uhr)',
                'max_p'    => 60,
                'content'  => 'Der legendäre Samstagnachmittag: 3 Stunden Zeit, 1 Thema im Umkreis von Neuendettelsau, genau 1 Bild OOC. Feierliche Projektion und Punktevergabe am Abend!',
            ],
        ];

        foreach ($seeds as $s) {
            $pid = wp_insert_post([
                'post_title'   => $s['title'],
                'post_content' => $s['content'],
                'post_status'  => 'publish',
                'post_type'    => 'drf_workshop',
            ]);
            if ($pid && !is_wp_error($pid)) {
                update_post_meta($pid, '_drf_workshop_speaker', $s['speaker']);
                update_post_meta($pid, '_drf_workshop_speaker_email', $s['email']);
                update_post_meta($pid, '_drf_workshop_category', $s['cat']);
                update_post_meta($pid, '_drf_workshop_badge_color', $s['color']);
                update_post_meta($pid, '_drf_workshop_room', $s['room']);
                update_post_meta($pid, '_drf_workshop_time', $s['time']);
                update_post_meta($pid, '_drf_workshop_max_participants', $s['max_p']);
                update_post_meta($pid, '_drf_workshop_signups', []);
            }
        }
    }
}

// Initialize Singleton
DRF_Workshops_Manager::get_instance();
