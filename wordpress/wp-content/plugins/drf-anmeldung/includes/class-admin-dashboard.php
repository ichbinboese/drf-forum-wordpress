<?php
/**
 * DRF Treffen-Anmeldung - Modern Admin Dashboard
 *
 * @package DRF_Anmeldung
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Admin_Dashboard {

    public function __construct() {
        add_action('admin_menu', [$this, 'register_admin_menus']);
        add_action('admin_init', [$this, 'handle_admin_actions']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'drf-anmeldungen') === false && strpos($hook, 'drf-meetings-manage') === false) {
            return;
        }
        wp_enqueue_style(
            'drf-anmeldung-admin-css',
            DRF_ANMELDUNG_URL . 'assets/css/admin.css',
            [],
            DRF_ANMELDUNG_VERSION
        );
    }

    public function register_admin_menus() {
        add_menu_page(
            __('Treffen Anmeldungen', 'drf-anmeldung'),
            __('Treffen Anmeldungen', 'drf-anmeldung'),
            'manage_options',
            'drf-anmeldungen',
            [$this, 'render_registrations_page'],
            'dashicons-tickets-alt',
            58
        );

        add_submenu_page(
            'drf-anmeldungen',
            __('Alle Anmeldungen', 'drf-anmeldung'),
            __('Alle Anmeldungen', 'drf-anmeldung'),
            'manage_options',
            'drf-anmeldungen',
            [$this, 'render_registrations_page']
        );

        add_submenu_page(
            'drf-anmeldungen',
            __('Treffen verwalten', 'drf-anmeldung'),
            __('Treffen verwalten', 'drf-anmeldung'),
            'manage_options',
            'drf-meetings-manage',
            [$this, 'render_meetings_page']
        );
    }

    /**
     * Handle Admin Actions (CSV Export, Status Changes, Meeting CRUD)
     */
    public function handle_admin_actions() {
        if (!current_user_can('manage_options')) {
            return;
        }

        // 1. CSV Export for Tagungsbüro
        if (isset($_GET['action']) && $_GET['action'] === 'drf_export_csv') {
            check_admin_referer('drf_export_csv_nonce');
            $meeting_id = absint($_GET['meeting_id'] ?? 0);
            $this->export_csv($meeting_id);
            exit;
        }

        // 2. Change Registration Status
        if (isset($_GET['action']) && $_GET['action'] === 'drf_change_status') {
            check_admin_referer('drf_status_nonce');
            $reg_id = absint($_GET['reg_id'] ?? 0);
            $new_status = sanitize_text_field($_GET['status'] ?? 'new');

            global $wpdb;
            $wpdb->update($wpdb->prefix . 'drf_registrations', ['status' => $new_status], ['id' => $reg_id]);

            wp_redirect(remove_query_arg(['action', 'reg_id', 'status', '_wpnonce']));
            exit;
        }

        // 3. Save Meeting (New or Edit)
        if (isset($_POST['drf_save_meeting'])) {
            check_admin_referer('drf_save_meeting_nonce');
            $id = !empty($_POST['meeting_id']) ? absint($_POST['meeting_id']) : null;
            DRF_Meeting_Manager::save_meeting($_POST, $id);
            wp_redirect(add_query_arg(['page' => 'drf-meetings-manage', 'status' => 'saved'], admin_url('admin.php')));
            exit;
        }

        // 4. Delete Meeting
        if (isset($_GET['action']) && $_GET['action'] === 'drf_delete_meeting') {
            check_admin_referer('drf_delete_meeting_nonce');
            $id = absint($_GET['meeting_id'] ?? 0);
            $has_regs = DRF_Meeting_Manager::get_registration_count($id);
            if ($has_regs > 0 && !isset($_GET['force'])) {
                wp_redirect(add_query_arg(['page' => 'drf-meetings-manage', 'status' => 'has_registrations'], admin_url('admin.php')));
                exit;
            }
            DRF_Meeting_Manager::delete_meeting($id);
            wp_redirect(add_query_arg(['page' => 'drf-meetings-manage', 'status' => 'deleted'], admin_url('admin.php')));
            exit;
        }

        // 5. Delete Registration / Participant
        if (isset($_GET['action']) && $_GET['action'] === 'drf_delete_registration') {
            check_admin_referer('drf_delete_reg_nonce');
            $reg_id = absint($_GET['reg_id'] ?? 0);
            global $wpdb;
            $wpdb->delete($wpdb->prefix . 'drf_registrations', ['id' => $reg_id]);
            wp_redirect(add_query_arg(['page' => 'drf-anmeldungen', 'status' => 'reg_deleted'], admin_url('admin.php')));
            exit;
        }

        // 6. Save/Edit Registration / Participant
        if (isset($_POST['drf_save_registration'])) {
            check_admin_referer('drf_save_reg_nonce');
            global $wpdb;
            $reg_id = absint($_POST['reg_id'] ?? 0);

            $data = [
                'meeting_id'             => absint($_POST['meeting_id'] ?? 0),
                'firstname'              => sanitize_text_field($_POST['firstname'] ?? ''),
                'lastname'               => sanitize_text_field($_POST['lastname'] ?? ''),
                'nickname'               => sanitize_text_field($_POST['nickname'] ?? ''),
                'email'                  => sanitize_email($_POST['email'] ?? ''),
                'phone'                  => sanitize_text_field($_POST['phone'] ?? ''),
                'street'                 => sanitize_text_field($_POST['street'] ?? ''),
                'zip_city'               => sanitize_text_field($_POST['zip_city'] ?? ''),
                'participation_type'     => sanitize_text_field($_POST['participation_type'] ?? 'full'),
                'room_category'          => sanitize_text_field($_POST['room_category'] ?? 'ez'),
                'room_partner'           => sanitize_text_field($_POST['room_partner'] ?? ''),
                'diet'                   => sanitize_text_field($_POST['diet'] ?? 'omnivore'),
                'diet_allergies'         => sanitize_textarea_field($_POST['diet_allergies'] ?? ''),
                'shuttle_needed'         => !empty($_POST['shuttle_needed']) ? 1 : 0,
                'shuttle_station'        => sanitize_text_field($_POST['shuttle_station'] ?? ''),
                'shuttle_arrival'        => sanitize_text_field($_POST['shuttle_arrival'] ?? ''),
                'social_donation'        => floatval(str_replace(',', '.', $_POST['social_donation'] ?? 0)),
                'total_amount'           => floatval(str_replace(',', '.', $_POST['total_amount'] ?? 0)),
                'notes'                  => sanitize_textarea_field($_POST['notes'] ?? ''),
                'status'                 => sanitize_text_field($_POST['status'] ?? 'new'),
            ];

            if ($reg_id > 0) {
                $wpdb->update($wpdb->prefix . 'drf_registrations', $data, ['id' => $reg_id]);
                $msg = 'reg_saved';
            } else {
                $wpdb->insert($wpdb->prefix . 'drf_registrations', $data);
                $msg = 'reg_created';
            }
            wp_redirect(add_query_arg(['page' => 'drf-anmeldungen', 'status' => $msg], admin_url('admin.php')));
            exit;
        }
    }

    /**
     * Export Registrations as CSV for Tagungsbüro
     */
    private function export_csv($meeting_id) {
        global $wpdb;
        $table_reg = $wpdb->prefix . 'drf_registrations';
        $table_meet = $wpdb->prefix . 'drf_meetings';

        if ($meeting_id) {
            $registrations = $wpdb->get_results($wpdb->prepare("SELECT r.*, m.title as meeting_title, m.year as meeting_year FROM $table_reg r LEFT JOIN $table_meet m ON r.meeting_id = m.id WHERE r.meeting_id = %d ORDER BY r.id ASC", $meeting_id));
            $filename = 'drf_anmeldungen_meeting_' . $meeting_id . '_' . date('Y-m-d') . '.csv';
        } else {
            $registrations = $wpdb->get_results("SELECT r.*, m.title as meeting_title, m.year as meeting_year FROM $table_reg r LEFT JOIN $table_meet m ON r.meeting_id = m.id ORDER BY r.id ASC");
            $filename = 'drf_anmeldungen_alle_' . date('Y-m-d') . '.csv';
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, [
            'ID', 'Treffen', 'Jahr', 'Vorname', 'Nachname', 'Nickname', 'E-Mail', 'Telefon',
            'Straße', 'PLZ_Ort', 'Dauer', 'Zimmer', 'Zimmerpartner', 'Ernährung', 'Allergien',
            'Workshop_Interesse', 'Workshop_Angebot', 'Workshop_Bedarf', 'Shuttle_Bedarf',
            'Shuttle_Bahnhof', 'Shuttle_Ankunft', 'Shuttle_Gepäck', 'Sozialspende_EUR',
            'Gesamtbetrag_EUR', 'Status', 'Datum', 'Notizen'
        ], ';');

        foreach ($registrations as $r) {
            fputcsv($output, [
                $r->id,
                $r->meeting_title,
                $r->meeting_year,
                $r->firstname,
                $r->lastname,
                $r->nickname,
                $r->email,
                $r->phone,
                $r->street,
                $r->zip_city,
                $r->participation_type,
                $r->room_category,
                $r->room_partner,
                $r->diet,
                $r->diet_allergies,
                $r->workshop_interest,
                $r->workshop_offer,
                $r->workshop_offer_details,
                $r->shuttle_needed ? 'Ja' : 'Nein',
                $r->shuttle_station,
                $r->shuttle_arrival,
                $r->shuttle_luggage,
                number_format($r->social_donation, 2, ',', ''),
                number_format($r->total_amount, 2, ',', ''),
                $r->status,
                $r->created_at,
                $r->notes,
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Render Registrations Management Page
     */
    public function render_registrations_page() {
        global $wpdb;
        $table_reg = $wpdb->prefix . 'drf_registrations';
        $table_meet = $wpdb->prefix . 'drf_meetings';

        $filter_meeting = isset($_GET['filter_meeting']) ? absint($_GET['filter_meeting']) : 0;
        $filter_status  = isset($_GET['filter_status']) ? sanitize_text_field($_GET['filter_status']) : '';

        $where = [];
        $params = [];

        if ($filter_meeting) {
            $where[] = "r.meeting_id = %d";
            $params[] = $filter_meeting;
        }
        if (!empty($filter_status)) {
            $where[] = "r.status = %s";
            $params[] = $filter_status;
        }

        $sql = "SELECT r.*, m.title as meeting_title, m.year as meeting_year, m.location as meeting_location FROM $table_reg r LEFT JOIN $table_meet m ON r.meeting_id = m.id";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
            $query = $wpdb->prepare($sql . " ORDER BY r.id DESC", $params);
        } else {
            $query = $sql . " ORDER BY r.id DESC";
        }

        $registrations = $wpdb->get_results($query);
        $all_meetings = DRF_Meeting_Manager::get_all_meetings();

        // Check if editing or creating a registration
        $edit_reg_id = isset($_GET['edit_reg']) ? sanitize_text_field($_GET['edit_reg']) : null;
        $edit_reg = null;
        if ($edit_reg_id !== null) {
            if ($edit_reg_id === 'new') {
                $edit_reg = (object) [
                    'id'                     => 0,
                    'meeting_id'             => $all_meetings[0]->id ?? 0,
                    'firstname'              => '',
                    'lastname'               => '',
                    'nickname'               => '',
                    'email'                  => '',
                    'phone'                  => '',
                    'street'                 => '',
                    'zip_city'               => '',
                    'participation_type'     => 'full',
                    'room_category'          => 'ez',
                    'room_partner'           => '',
                    'diet'                   => 'omnivore',
                    'diet_allergies'         => '',
                    'shuttle_needed'         => 0,
                    'shuttle_station'        => 'wicklesgreuth',
                    'shuttle_arrival'        => '',
                    'social_donation'        => 0,
                    'total_amount'           => 0,
                    'notes'                  => '',
                    'status'                 => 'new',
                ];
            } else {
                $edit_reg = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_reg WHERE id = %d", absint($edit_reg_id)));
            }
        }

        // Calculate KPI stats
        $total_count = count($registrations);
        $total_amount_sum = 0;
        $total_donation_sum = 0;
        $shuttle_count = 0;

        foreach ($registrations as $r) {
            $total_amount_sum += floatval($r->total_amount);
            $total_donation_sum += floatval($r->social_donation);
            if ($r->shuttle_needed) $shuttle_count++;
        }
        ?>
        <div class="wrap drf-admin-wrap">
            <header class="drf-admin-header">
                <div class="drf-admin-header-brand">
                    <span class="dashicons dashicons-tickets-alt drf-admin-logo-icon"></span>
                    <div>
                        <h1 class="drf-admin-title">Jahrestreffen &bull; Anmeldungen</h1>
                        <p class="drf-admin-subtitle">Teilnehmerverwaltung, Zimmerkontingente, Bahnhofs-Shuttle &amp; Solidarfonds</p>
                    </div>
                </div>
                <div class="drf-admin-header-actions" style="display:flex; align-items:center; gap:10px;">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen&edit_reg=new')); ?>" class="button" style="background:#ffffff; color:#141518; border-color:#e2e8f0; font-weight:700; padding:6px 16px; height:auto; line-height:1.8;">
                        <span class="dashicons dashicons-plus-alt2" style="margin-top:4px;"></span> Neuer Teilnehmer
                    </a>
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_export_csv', 'meeting_id' => $filter_meeting]), 'drf_export_csv_nonce')); ?>" class="button button-primary" style="background:#f5b301; color:#141518; border-color:#f5b301; font-weight:700; padding:6px 16px; height:auto; line-height:1.8;">
                        <span class="dashicons dashicons-download" style="margin-top:4px;"></span> CSV-Export
                    </a>
                </div>
            </header>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'reg_saved') : ?>
                <div class="notice notice-success is-dismissible" style="margin:16px 0;"><p><strong>Teilnehmeranmeldung erfolgreich gespeichert!</strong></p></div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'reg_created') : ?>
                <div class="notice notice-success is-dismissible" style="margin:16px 0;"><p><strong>Neuer Teilnehmer erfolgreich angelegt!</strong></p></div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'reg_deleted') : ?>
                <div class="notice notice-success is-dismissible" style="margin:16px 0;"><p><strong>Teilnehmeranmeldung wurde erfolgreich gelöscht.</strong></p></div>
            <?php endif; ?>

            <?php if ($edit_reg) : ?>
                <!-- Edit / Create Participant Form Card -->
                <div style="background:#ffffff; padding:28px 32px; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 4px 15px rgba(0,0,0,0.06); margin-bottom:28px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:20px;">
                        <h2 style="margin:0; font-size:1.35rem; color:#0f172a; font-weight:800;">
                            <?php echo $edit_reg->id ? ('Teilnehmer bearbeiten: #' . $edit_reg->id . ' – ' . esc_html($edit_reg->firstname . ' ' . $edit_reg->lastname)) : 'Neuen Teilnehmer manuell anlegen'; ?>
                        </h2>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen')); ?>" class="button button-secondary" style="font-size:0.85rem;">✕ Schließen</a>
                    </div>

                    <form method="post" action="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen')); ?>">
                        <?php wp_nonce_field('drf_save_reg_nonce'); ?>
                        <input type="hidden" name="drf_save_registration" value="1">
                        <input type="hidden" name="reg_id" value="<?php echo esc_attr($edit_reg->id ?? 0); ?>">

                        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px;">
                            <!-- Group 1: Persönliche Daten -->
                            <div style="background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #e2e8f0;">
                                <h3 style="margin-top:0; font-size:1.05rem; color:#141518; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">1. Personendaten</h3>
                                <p>
                                    <label><strong>Vorname:</strong></label><br>
                                    <input type="text" name="firstname" value="<?php echo esc_attr($edit_reg->firstname); ?>" class="regular-text" style="width:100%;" required>
                                </p>
                                <p>
                                    <label><strong>Nachname:</strong></label><br>
                                    <input type="text" name="lastname" value="<?php echo esc_attr($edit_reg->lastname); ?>" class="regular-text" style="width:100%;" required>
                                </p>
                                <p>
                                    <label><strong>Usenet / Foren-Nickname:</strong></label><br>
                                    <input type="text" name="nickname" value="<?php echo esc_attr($edit_reg->nickname); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>E-Mail-Adresse:</strong></label><br>
                                    <input type="email" name="email" value="<?php echo esc_attr($edit_reg->email); ?>" class="regular-text" style="width:100%;" required>
                                </p>
                                <p>
                                    <label><strong>Telefon / Mobil:</strong></label><br>
                                    <input type="text" name="phone" value="<?php echo esc_attr($edit_reg->phone); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>Straße &amp; Hausnummer:</strong></label><br>
                                    <input type="text" name="street" value="<?php echo esc_attr($edit_reg->street); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>PLZ &amp; Ort:</strong></label><br>
                                    <input type="text" name="zip_city" value="<?php echo esc_attr($edit_reg->zip_city); ?>" class="regular-text" style="width:100%;">
                                </p>
                            </div>

                            <!-- Group 2: Treffen & Zimmer -->
                            <div style="background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #e2e8f0;">
                                <h3 style="margin-top:0; font-size:1.05rem; color:#141518; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">2. Treffen &amp; Unterkunft</h3>
                                <p>
                                    <label><strong>Treffen:</strong></label><br>
                                    <select name="meeting_id" style="width:100%;">
                                        <?php foreach ($all_meetings as $m) : ?>
                                            <option value="<?php echo esc_attr($m->id); ?>" <?php selected($edit_reg->meeting_id, $m->id); ?>>
                                                <?php echo esc_html($m->title . ' (' . $m->year . ' - ' . $m->location . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </p>
                                <p>
                                    <label><strong>Zimmerkategorie:</strong></label><br>
                                    <select name="room_category" style="width:100%;">
                                        <option value="ez" <?php selected($edit_reg->room_category, 'ez'); ?>>Einzelzimmer (EZ)</option>
                                        <option value="dz" <?php selected($edit_reg->room_category, 'dz'); ?>>Doppelzimmer (DZ)</option>
                                        <option value="mbz" <?php selected($edit_reg->room_category, 'mbz'); ?>>Mehrbettzimmer (MBZ)</option>
                                        <option value="external" <?php selected($edit_reg->room_category, 'external'); ?>>Tagesgast (Externe Unterkunft)</option>
                                    </select>
                                </p>
                                <p>
                                    <label><strong>Zimmerpartner (bei DZ / MBZ):</strong></label><br>
                                    <input type="text" name="room_partner" value="<?php echo esc_attr($edit_reg->room_partner); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>Teilnahme-Art:</strong></label><br>
                                    <select name="participation_type" style="width:100%;">
                                        <option value="full" <?php selected($edit_reg->participation_type, 'full'); ?>>Gesamtes Treffen (Donnerstag bis Sonntag)</option>
                                        <option value="partial" <?php selected($edit_reg->participation_type, 'partial'); ?>>Teilzeit / Einzelne Tage</option>
                                    </select>
                                </p>
                            </div>

                            <!-- Group 3: Verpflegung & Shuttle -->
                            <div style="background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #e2e8f0;">
                                <h3 style="margin-top:0; font-size:1.05rem; color:#141518; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">3. Verpflegung &amp; Shuttle</h3>
                                <p>
                                    <label><strong>Ernährung:</strong></label><br>
                                    <select name="diet" style="width:100%;">
                                        <option value="omnivore" <?php selected($edit_reg->diet, 'omnivore'); ?>>Omnivor (Normale Kost)</option>
                                        <option value="vegetarian" <?php selected($edit_reg->diet, 'vegetarian'); ?>>Vegetarisch</option>
                                        <option value="vegan" <?php selected($edit_reg->diet, 'vegan'); ?>>Vegan</option>
                                    </select>
                                </p>
                                <p>
                                    <label><strong>Allergien &amp; Unverträglichkeiten:</strong></label><br>
                                    <textarea name="diet_allergies" rows="2" style="width:100%;"><?php echo esc_textarea($edit_reg->diet_allergies); ?></textarea>
                                </p>
                                <div style="margin-top:14px; padding-top:10px; border-top:1px dashed #cbd5e1;">
                                    <label style="font-weight:700;">
                                        <input type="checkbox" name="shuttle_needed" value="1" <?php checked($edit_reg->shuttle_needed, 1); ?>>
                                        Bahnhofs-Shuttle gewünscht
                                    </label>
                                </div>
                                <p style="margin-top:10px;">
                                    <label><strong>Shuttle-Bahnhof:</strong></label><br>
                                    <select name="shuttle_station" style="width:100%;">
                                        <option value="wicklesgreuth" <?php selected($edit_reg->shuttle_station, 'wicklesgreuth'); ?>>Wicklesgreuth (S-Bahn ab Nürnberg / Ansbach)</option>
                                        <option value="ansbach" <?php selected($edit_reg->shuttle_station, 'ansbach'); ?>>Ansbach Hbf (Fernverkehr IC/ICE)</option>
                                    </select>
                                </p>
                                <p>
                                    <label><strong>Geplante Ankunftszeit / Zug-Nr:</strong></label><br>
                                    <input type="text" name="shuttle_arrival" value="<?php echo esc_attr($edit_reg->shuttle_arrival); ?>" class="regular-text" style="width:100%;">
                                </p>
                            </div>

                            <!-- Group 4: Status, Beträge & Notizen -->
                            <div style="background:#f8fafc; padding:20px; border-radius:10px; border:1px solid #e2e8f0;">
                                <h3 style="margin-top:0; font-size:1.05rem; color:#141518; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">4. Status &amp; Finanzen</h3>
                                <p>
                                    <label><strong>Anmeldestatus:</strong></label><br>
                                    <select name="status" style="width:100%; font-weight:700;">
                                        <option value="new" <?php selected($edit_reg->status, 'new'); ?>>Neu (Eingegangen)</option>
                                        <option value="confirmed" <?php selected($edit_reg->status, 'confirmed'); ?>>Bestätigt</option>
                                        <option value="paid" <?php selected($edit_reg->status, 'paid'); ?>>Bezahlt</option>
                                        <option value="cancelled" <?php selected($edit_reg->status, 'cancelled'); ?>>Storniert</option>
                                    </select>
                                </p>
                                <p>
                                    <label><strong>Solidarfonds-Spende (&euro;):</strong></label><br>
                                    <input type="text" name="social_donation" value="<?php echo esc_attr(number_format($edit_reg->social_donation, 2, ',', '')); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>Gesamtbetrag (&euro;):</strong></label><br>
                                    <input type="text" name="total_amount" value="<?php echo esc_attr(number_format($edit_reg->total_amount, 2, ',', '')); ?>" class="regular-text" style="width:100%;">
                                </p>
                                <p>
                                    <label><strong>Interne Tagungsbüro-Notizen:</strong></label><br>
                                    <textarea name="notes" rows="3" style="width:100%;"><?php echo esc_textarea($edit_reg->notes); ?></textarea>
                                </p>
                            </div>
                        </div>

                        <div style="margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0; display:flex; gap:12px; align-items:center;">
                            <button type="submit" class="button button-primary button-large" style="background:#f5b301; color:#141518; border-color:#f5b301; font-weight:800; padding:6px 20px;">
                                <?php echo $edit_reg->id ? 'Änderungen speichern' : 'Teilnehmer anlegen'; ?>
                            </button>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen')); ?>" class="button button-secondary button-large" style="padding:6px 16px;">Abbrechen</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Modern KPI Stats Grid -->
            <div class="drf-stats-grid">
                <div class="drf-stat-card card-blue">
                    <div class="drf-stat-header">
                        <span class="drf-stat-label">Anmeldungen</span>
                        <span class="drf-stat-icon">👥</span>
                    </div>
                    <div class="drf-stat-value"><?php echo $total_count; ?></div>
                    <div class="drf-stat-sub">Registrierte Teilnehmer</div>
                </div>

                <div class="drf-stat-card card-gold">
                    <div class="drf-stat-header">
                        <span class="drf-stat-label">Gesamtsumme Beiträge</span>
                        <span class="drf-stat-icon">💶</span>
                    </div>
                    <div class="drf-stat-value"><?php echo number_format($total_amount_sum, 2, ',', '.'); ?> &euro;</div>
                    <div class="drf-stat-sub">Inkl. Übernachtung &amp; Verpflegung</div>
                </div>

                <div class="drf-stat-card card-green">
                    <div class="drf-stat-header">
                        <span class="drf-stat-label">Solidarfonds („Sozialsowjet“)</span>
                        <span class="drf-stat-icon">🤝</span>
                    </div>
                    <div class="drf-stat-value" style="color:#0f172a;"><?php echo number_format($total_donation_sum, 2, ',', '.'); ?> &euro;</div>
                    <div class="drf-stat-sub">Freiwillige Spenden</div>
                </div>

                <div class="drf-stat-card card-purple">
                    <div class="drf-stat-header">
                        <span class="drf-stat-label">Shuttle-Bedarf</span>
                        <span class="drf-stat-icon">🚐</span>
                    </div>
                    <div class="drf-stat-value" style="color:#0f172a;"><?php echo $shuttle_count; ?></div>
                    <div class="drf-stat-sub">Wicklesgreuth &amp; Ansbach</div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="drf-filter-box">
                <form method="get" action="" class="drf-filter-group">
                    <input type="hidden" name="page" value="drf-anmeldungen">

                    <span class="drf-filter-label">Treffen:</span>
                    <select name="filter_meeting" id="filter_meeting" class="drf-select-styled" onchange="this.form.submit()">
                        <option value="0">&ndash; Alle Treffen anzeigen &ndash;</option>
                        <?php foreach ($all_meetings as $m) : ?>
                            <option value="<?php echo esc_attr($m->id); ?>" <?php selected($filter_meeting, $m->id); ?>>
                                <?php echo esc_html($m->title . ' (' . $m->year . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <span class="drf-filter-label" style="margin-left:12px;">Status:</span>
                    <select name="filter_status" id="filter_status" class="drf-select-styled" onchange="this.form.submit()">
                        <option value="">&ndash; Alle Status &ndash;</option>
                        <option value="new" <?php selected($filter_status, 'new'); ?>>Neu</option>
                        <option value="confirmed" <?php selected($filter_status, 'confirmed'); ?>>Bestätigt</option>
                        <option value="paid" <?php selected($filter_status, 'paid'); ?>>Bezahlt</option>
                        <option value="cancelled" <?php selected($filter_status, 'cancelled'); ?>>Storniert</option>
                    </select>

                    <?php if ($filter_meeting || $filter_status) : ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen')); ?>" class="button button-secondary" style="margin-left:10px;">Filter aufheben</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Modern Table -->
            <div class="drf-table-container">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th width="200">Teilnehmer</th>
                            <th width="160">Treffen</th>
                            <th width="120">Zimmer</th>
                            <th width="130">Verpflegung</th>
                            <th width="140">Shuttle</th>
                            <th width="90">Spende</th>
                            <th width="100">Gesamt</th>
                            <th width="110">Status</th>
                            <th width="240">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations)) : ?>
                            <tr>
                                <td colspan="10" style="text-align:center; padding:40px; color:#64748b;">
                                    <div style="font-size:2rem; margin-bottom:8px;">📬</div>
                                    <strong>Keine Anmeldungen für die gewählten Kriterien gefunden.</strong>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($registrations as $r) : ?>
                                <tr>
                                    <td><strong>#<?php echo esc_html($r->id); ?></strong></td>
                                    <td>
                                        <div style="font-weight:700; color:#0f172a;"><?php echo esc_html($r->firstname . ' ' . $r->lastname); ?></div>
                                        <?php if (!empty($r->nickname)) : ?>
                                            <span style="color:#d97706; font-weight:700; font-size:0.85rem;">@<?php echo esc_html($r->nickname); ?></span><br>
                                        <?php endif; ?>
                                        <small><a href="mailto:<?php echo esc_attr($r->email); ?>" style="color:#141518; text-decoration:none; font-weight:600;"><?php echo esc_html($r->email); ?></a></small>
                                        <?php if (!empty($r->phone)) : ?>
                                            <br><small style="color:#64748b;"><?php echo esc_html($r->phone); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; font-size:0.9rem;"><?php echo esc_html($r->meeting_title); ?></div>
                                        <small style="color:#64748b;"><?php echo esc_html($r->meeting_location); ?></small>
                                    </td>
                                    <td>
                                        <span class="drf-badge drf-badge-room">
                                            <?php echo strtoupper(esc_html($r->room_category)); ?>
                                        </span>
                                        <?php if (!empty($r->room_partner)) : ?>
                                            <div style="font-size:0.78rem; color:#475569; margin-top:4px;">mit: <strong><?php echo esc_html($r->room_partner); ?></strong></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html(ucfirst($r->diet)); ?></strong>
                                        <?php if (!empty($r->diet_allergies)) : ?>
                                            <div style="color:#dc2626; font-size:0.78rem; margin-top:2px; font-weight:600;">⚠ <?php echo esc_html($r->diet_allergies); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->shuttle_needed) : ?>
                                            <span style="color:#141518; font-weight:700;">🚐 <?php echo esc_html(ucfirst($r->shuttle_station)); ?></span>
                                            <?php if (!empty($r->shuttle_arrival)) : ?>
                                                <div style="font-size:0.78rem; color:#64748b;"><?php echo esc_html($r->shuttle_arrival); ?></div>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span style="color:#94a3b8;">Eigene Anreise</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->social_donation > 0) : ?>
                                            <span style="color:#141518; font-weight:700;">+<?php echo number_format($r->social_donation, 2, ',', '.'); ?> &euro;</span>
                                        <?php else : ?>
                                            <span style="color:#94a3b8;">0 &euro;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong style="font-size:1rem; color:#0f172a;"><?php echo number_format($r->total_amount, 2, ',', '.'); ?> &euro;</strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status_class = 'drf-badge-status-new';
                                        $status_label = 'Neu';
                                        if ($r->status === 'confirmed') {
                                             $status_class = 'drf-badge-status-confirmed';
                                             $status_label = 'Bestätigt';
                                        } elseif ($r->status === 'paid') {
                                             $status_class = 'drf-badge-status-paid';
                                             $status_label = 'Bezahlt';
                                        } elseif ($r->status === 'cancelled') {
                                             $status_class = 'drf-badge-status-cancelled';
                                             $status_label = 'Storniert';
                                        }
                                        ?>
                                        <span class="drf-badge <?php echo $status_class; ?>">
                                            <?php echo $status_label; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="drf-action-group">
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen&edit_reg=' . $r->id)); ?>" class="drf-btn-action drf-btn-edit" title="Teilnehmerdaten bearbeiten">Bearbeiten</a>
                                            <?php if ($r->status !== 'confirmed') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'confirmed']), 'drf_status_nonce')); ?>" class="drf-btn-action drf-btn-confirm" title="Als bestätigt markieren">Bestätigen</a>
                                            <?php endif; ?>
                                            <?php if ($r->status !== 'paid') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'paid']), 'drf_status_nonce')); ?>" class="drf-btn-action drf-btn-paid" title="Als bezahlt markieren">Bezahlt</a>
                                            <?php endif; ?>
                                            <?php if ($r->status !== 'cancelled') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'cancelled']), 'drf_status_nonce')); ?>" class="drf-btn-action drf-btn-cancel" onclick="return confirm('Anmeldung wirklich stornieren?');">Storno</a>
                                            <?php endif; ?>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=drf-anmeldungen&action=drf_delete_registration&reg_id=' . $r->id), 'drf_delete_reg_nonce')); ?>" class="drf-btn-action drf-btn-delete" title="Teilnehmer löschen" onclick="return confirm('Möchten Sie diese Teilnehmeranmeldung von <?php echo esc_js($r->firstname . ' ' . $r->lastname); ?> wirklich unwiderruflich löschen?');">Löschen</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Render Meetings Management Page (e.g. 2026, 2027 Neuendettelsau)
     */
    public function render_meetings_page() {
        $meetings = DRF_Meeting_Manager::get_all_meetings();
        $edit_id = isset($_GET['edit_id']) ? absint($_GET['edit_id']) : 0;
        $edit_meeting = $edit_id ? DRF_Meeting_Manager::get_meeting($edit_id) : null;
        ?>
        <div class="wrap drf-admin-wrap">
            <header class="drf-admin-header">
                <div class="drf-admin-header-brand">
                    <span class="dashicons dashicons-calendar-alt drf-admin-logo-icon"></span>
                    <div>
                        <h1 class="drf-admin-title">Jahrestreffen verwalten (2026, 2027, ...)</h1>
                        <p class="drf-admin-subtitle">Erstellen und konfigurieren von Augenblicke-Treffen für de.rec.fotografie e.V.</p>
                    </div>
                </div>
            </header>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'saved') : ?>
                <div class="notice notice-success is-dismissible" style="margin:16px 0;"><p><strong>Treffen erfolgreich gespeichert!</strong></p></div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'deleted') : ?>
                <div class="notice notice-success is-dismissible" style="margin:16px 0;"><p><strong>Treffen wurde erfolgreich gelöscht.</strong></p></div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'has_registrations') : ?>
                <div class="notice notice-error is-dismissible" style="margin:16px 0;">
                    <p><strong>Treffen kann nicht gelöscht werden:</strong> Für dieses Treffen liegen bereits Teilnehmer-Anmeldungen vor. Bitte löschen oder verschieben Sie zuerst die Anmeldungen im Tagungsbüro.</p>
                </div>
            <?php endif; ?>

            <div style="display:flex; gap:24px; align-items:flex-start; flex-wrap:wrap;">
                <!-- Left: Form to create or edit meeting -->
                <div style="flex:1.2; min-width:340px; background:#ffffff; padding:28px; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <h2 style="font-size:1.3rem; font-weight:800; color:#0f172a; margin-top:0; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                        <?php echo $edit_meeting ? 'Treffen bearbeiten' : 'Neues Treffen anlegen'; ?>
                    </h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('drf_save_meeting_nonce'); ?>
                        <?php if ($edit_meeting) : ?>
                            <input type="hidden" name="meeting_id" value="<?php echo esc_attr($edit_meeting->id); ?>">
                        <?php endif; ?>

                        <table class="form-table" style="margin-top:10px;">
                            <tr>
                                <th><label for="meet_title"><strong>Titel des Treffens</strong></label></th>
                                <td>
                                    <input type="text" id="meet_title" name="title" required value="<?php echo esc_attr($edit_meeting->title ?? 'AUGENBLICKE XXVII (2027)'); ?>" class="regular-text" style="font-weight:700;">
                                    <p class="description">z.B. AUGENBLICKE XXVI (2026) oder AUGENBLICKE XXVII (2027)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_year"><strong>Jahr</strong></label></th>
                                <td>
                                    <input type="number" id="meet_year" name="year" required value="<?php echo esc_attr($edit_meeting->year ?? (date('Y') + 1)); ?>" style="width:100px; font-weight:700;">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_location"><strong>Tagungsort</strong></label></th>
                                <td>
                                    <input type="text" id="meet_location" name="location" required value="<?php echo esc_attr($edit_meeting->location ?? 'Haus Lutherrose, Neuendettelsau'); ?>" class="large-text">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_dates"><strong>Zeitraum</strong></label></th>
                                <td>
                                    <input type="date" name="start_date" required value="<?php echo esc_attr($edit_meeting->start_date ?? '2027-05-27'); ?>">
                                    <span style="margin:0 6px;">bis</span>
                                    <input type="date" name="end_date" required value="<?php echo esc_attr($edit_meeting->end_date ?? '2027-05-30'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th><label><strong>Teilnehmerbeiträge (&euro;)</strong></label></th>
                                <td>
                                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                        <label>EZ: <input type="number" step="0.5" name="price_ez" value="<?php echo esc_attr($edit_meeting->price_ez ?? 250); ?>" style="width:85px;"></label>
                                        <label>DZ: <input type="number" step="0.5" name="price_dz" value="<?php echo esc_attr($edit_meeting->price_dz ?? 205); ?>" style="width:85px;"></label>
                                        <label>MBZ: <input type="number" step="0.5" name="price_mbz" value="<?php echo esc_attr($edit_meeting->price_mbz ?? 170); ?>" style="width:85px;"></label>
                                        <label>Tagesgast: <input type="number" step="0.5" name="price_day" value="<?php echo esc_attr($edit_meeting->price_day ?? 65); ?>" style="width:85px;"></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_status"><strong>Status</strong></label></th>
                                <td>
                                    <select name="status" id="meet_status" class="drf-select-styled">
                                        <option value="active" <?php selected($edit_meeting->status ?? 'active', 'active'); ?>>Aktiv (Anmeldung geöffnet)</option>
                                        <option value="planning" <?php selected($edit_meeting->status ?? '', 'planning'); ?>>In Planung (Vorschau)</option>
                                        <option value="closed" <?php selected($edit_meeting->status ?? '', 'closed'); ?>>Geschlossen (Ausgebucht / Beendet)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_details"><strong>Beschreibung / Anreise</strong></label></th>
                                <td>
                                    <textarea name="location_details" id="meet_details" rows="3" class="large-text"><?php echo esc_textarea($edit_meeting->location_details ?? ''); ?></textarea>
                                </td>
                            </tr>
                        </table>

                        <p class="submit" style="margin-top:20px; display:flex; gap:10px;">
                            <button type="submit" name="drf_save_meeting" class="button button-primary button-large" style="background:#f5b301; color:#141518; border-color:#f5b301; font-weight:800;">
                                <?php echo $edit_meeting ? 'Änderungen speichern' : 'Treffen speichern'; ?>
                            </button>
                            <?php if ($edit_meeting) : ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=drf-meetings-manage')); ?>" class="button button-secondary">Abbrechen</a>
                            <?php endif; ?>
                        </p>
                    </form>
                </div>

                <!-- Right: List of Meetings -->
                <div style="flex:1; min-width:320px; background:#ffffff; padding:28px; border-radius:14px; border:1px solid #e2e8f0; box-shadow:0 2px 10px rgba(0,0,0,0.04);">
                    <h2 style="font-size:1.3rem; font-weight:800; color:#0f172a; margin-top:0; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
                        Bestehende Treffen
                    </h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th width="60">Jahr</th>
                                <th>Titel &amp; Ort</th>
                                <th>Status</th>
                                <th width="150">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meetings as $m) :
                                $reg_count = DRF_Meeting_Manager::get_registration_count($m->id);
                                $delete_url = wp_nonce_url(admin_url('admin.php?page=drf-meetings-manage&action=drf_delete_meeting&meeting_id=' . $m->id), 'drf_delete_meeting_nonce');
                                ?>
                                <tr>
                                    <td><strong style="font-size:1.05rem;"><?php echo esc_html($m->year); ?></strong></td>
                                    <td>
                                        <strong><?php echo esc_html($m->title); ?></strong><br>
                                        <small style="color:#64748b;"><?php echo esc_html($m->location); ?></small>
                                        <?php if ($reg_count > 0) : ?>
                                            <br><span class="drf-badge" style="background:#e0f2fe; color:#0369a1; font-size:0.75rem; margin-top:4px; display:inline-block;">
                                                <?php echo sprintf(_n('%d Anmeldung', '%d Anmeldungen', $reg_count, 'drf-anmeldung'), $reg_count); ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($m->status === 'active') : ?>
                                            <span class="drf-badge drf-badge-status-paid">Aktiv</span>
                                        <?php elseif ($m->status === 'planning') : ?>
                                            <span class="drf-badge drf-badge-status-new">In Planung</span>
                                        <?php else : ?>
                                            <span class="drf-badge drf-badge-room">Geschlossen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:inline-flex; gap:6px; align-items:center;">
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=drf-meetings-manage&edit_id=' . $m->id)); ?>" class="button button-small" title="Treffen bearbeiten">Bearbeiten</a>
                                            <a href="<?php echo esc_url($delete_url); ?>" class="button button-small" style="color:#b91c1c; border-color:#fca5a5;" title="Treffen löschen" onclick="return confirm('Möchten Sie das Treffen &bdquo;<?php echo esc_js($m->title); ?>&ldquo; wirklich löschen?');">Löschen</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
}
