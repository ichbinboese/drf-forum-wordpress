<?php
/**
 * DRF Treffen-Anmeldung - Admin Dashboard
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
            global $wpdb;
            $wpdb->delete($wpdb->prefix . 'drf_meetings', ['id' => $id]);
            wp_redirect(add_query_arg(['page' => 'drf-meetings-manage', 'status' => 'deleted'], admin_url('admin.php')));
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
        // UTF-8 BOM for Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header row
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
                        <p class="drf-admin-subtitle">Verwaltung der Teilnehmer, Zimmerbelegung, Shuttleservice &amp; Sozialspenden</p>
                    </div>
                </div>
                <div class="drf-admin-header-actions">
                    <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_export_csv', 'meeting_id' => $filter_meeting]), 'drf_export_csv_nonce')); ?>" class="button button-primary">
                        <span class="dashicons dashicons-download" style="margin-top:4px;"></span> CSV-Export f&uuml;r Tagungsb&uuml;ro
                    </a>
                </div>
            </header>

            <!-- Stats Bar -->
            <div style="display:flex; gap:16px; margin-bottom:20px;">
                <div style="flex:1; background:#ffffff; padding:16px; border-radius:8px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase;">Anmeldungen</div>
                    <div style="font-size:24px; font-weight:800; color:#111827;"><?php echo $total_count; ?></div>
                </div>
                <div style="flex:1; background:#ffffff; padding:16px; border-radius:8px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase;">Gesamtsumme Beiträge</div>
                    <div style="font-size:24px; font-weight:800; color:#111827;"><?php echo number_format($total_amount_sum, 2, ',', '.'); ?> &euro;</div>
                </div>
                <div style="flex:1; background:#ffffff; padding:16px; border-radius:8px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase;">Sozialspenden („Sozialsowjet“)</div>
                    <div style="font-size:24px; font-weight:800; color:#10b981;"><?php echo number_format($total_donation_sum, 2, ',', '.'); ?> &euro;</div>
                </div>
                <div style="flex:1; background:#ffffff; padding:16px; border-radius:8px; border:1px solid #e5e7eb; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; text-transform:uppercase;">Shuttleservice Anfragen</div>
                    <div style="font-size:24px; font-weight:800; color:#3b82f6;"><?php echo $shuttle_count; ?></div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div style="background:#ffffff; padding:12px 16px; border-radius:8px; border:1px solid #e5e7eb; margin-bottom:16px; display:flex; align-items:center; gap:12px;">
                <form method="get" action="" style="display:flex; align-items:center; gap:12px; width:100%;">
                    <input type="hidden" name="page" value="drf-anmeldungen">

                    <label for="filter_meeting"><strong>Treffen:</strong></label>
                    <select name="filter_meeting" id="filter_meeting" onchange="this.form.submit()">
                        <option value="0">&ndash; Alle Treffen anzeigen &ndash;</option>
                        <?php foreach ($all_meetings as $m) : ?>
                            <option value="<?php echo esc_attr($m->id); ?>" <?php selected($filter_meeting, $m->id); ?>>
                                <?php echo esc_html($m->title . ' (' . $m->year . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="filter_status"><strong>Status:</strong></label>
                    <select name="filter_status" id="filter_status" onchange="this.form.submit()">
                        <option value="">&ndash; Alle Status &ndash;</option>
                        <option value="new" <?php selected($filter_status, 'new'); ?>>Neu</option>
                        <option value="confirmed" <?php selected($filter_status, 'confirmed'); ?>>Bestätigt</option>
                        <option value="paid" <?php selected($filter_status, 'paid'); ?>>Bezahlt</option>
                        <option value="cancelled" <?php selected($filter_status, 'cancelled'); ?>>Storniert</option>
                    </select>

                    <?php if ($filter_meeting || $filter_status) : ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=drf-anmeldungen')); ?>" class="button button-secondary">Filter zurücksetzen</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table -->
            <div style="background:#ffffff; border-radius:8px; border:1px solid #e5e7eb; overflow:hidden;">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="45">#</th>
                            <th>Teilnehmer</th>
                            <th>Treffen</th>
                            <th>Zimmer</th>
                            <th>Verpflegung</th>
                            <th>Shuttle</th>
                            <th>Spende</th>
                            <th>Betrag</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations)) : ?>
                            <tr>
                                <td colspan="10" style="text-align:center; padding:30px; color:#6b7280;">
                                    Keine Anmeldungen für die gewählten Kriterien gefunden.
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($registrations as $r) : ?>
                                <tr>
                                    <td><strong>#<?php echo esc_html($r->id); ?></strong></td>
                                    <td>
                                        <strong><?php echo esc_html($r->firstname . ' ' . $r->lastname); ?></strong>
                                        <?php if (!empty($r->nickname)) : ?>
                                            <span style="color:#d97706; font-weight:600;">(<?php echo esc_html($r->nickname); ?>)</span>
                                        <?php endif; ?>
                                        <br>
                                        <small><a href="mailto:<?php echo esc_attr($r->email); ?>"><?php echo esc_html($r->email); ?></a></small>
                                        <?php if (!empty($r->phone)) : ?>
                                            <small>&bull; <?php echo esc_html($r->phone); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo esc_html($r->meeting_title); ?></strong><br>
                                        <small><?php echo esc_html($r->meeting_location); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background:#e5e7eb; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:700;">
                                            <?php echo strtoupper(esc_html($r->room_category)); ?>
                                        </span>
                                        <?php if (!empty($r->room_partner)) : ?>
                                            <br><small>mit: <?php echo esc_html($r->room_partner); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html(ucfirst($r->diet)); ?>
                                        <?php if (!empty($r->diet_allergies)) : ?>
                                            <br><small style="color:#dc2626;">⚠ <?php echo esc_html($r->diet_allergies); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->shuttle_needed) : ?>
                                            <span style="color:#2563eb; font-weight:700;">Ja (<?php echo esc_html($r->shuttle_station); ?>)</span>
                                            <?php if (!empty($r->shuttle_arrival)) : ?>
                                                <br><small><?php echo esc_html($r->shuttle_arrival); ?></small>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <span style="color:#9ca3af;">Nein</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($r->social_donation > 0) : ?>
                                            <span style="color:#10b981; font-weight:700;">+<?php echo number_format($r->social_donation, 2, ',', '.'); ?> &euro;</span>
                                        <?php else : ?>
                                            <span style="color:#9ca3af;">0 &euro;</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?php echo number_format($r->total_amount, 2, ',', '.'); ?> &euro;</strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status_colors = [
                                            'new'       => '#d97706',
                                            'confirmed' => '#2563eb',
                                            'paid'      => '#16a34a',
                                            'cancelled' => '#dc2626',
                                        ];
                                        $color = $status_colors[$r->status] ?? '#6b7280';
                                        ?>
                                        <span style="background:<?php echo $color; ?>; color:#ffffff; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:700;">
                                            <?php echo strtoupper(esc_html($r->status)); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:4px; flex-wrap:wrap;">
                                            <?php if ($r->status !== 'confirmed') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'confirmed']), 'drf_status_nonce')); ?>" class="button button-small" title="Als bestätigt markieren">Bestätigen</a>
                                            <?php endif; ?>
                                            <?php if ($r->status !== 'paid') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'paid']), 'drf_status_nonce')); ?>" class="button button-small" title="Als bezahlt markieren" style="color:#16a34a;">Bezahlt</a>
                                            <?php endif; ?>
                                            <?php if ($r->status !== 'cancelled') : ?>
                                                <a href="<?php echo esc_url(wp_nonce_url(add_query_arg(['action' => 'drf_change_status', 'reg_id' => $r->id, 'status' => 'cancelled']), 'drf_status_nonce')); ?>" class="button button-small" style="color:#dc2626;" onclick="return confirm('Anmeldung wirklich stornieren?');">Storno</a>
                                            <?php endif; ?>
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
                        <p class="drf-admin-subtitle">Erstellen und konfigurieren Sie anstehende Treffen für de.rec.fotografie e.V.</p>
                    </div>
                </div>
            </header>

            <div style="display:flex; gap:24px; align-items:flex-start;">
                <!-- Left: Form to create or edit meeting -->
                <div style="flex:1; background:#ffffff; padding:24px; border-radius:12px; border:1px solid #e5e7eb; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h2><?php echo $edit_meeting ? 'Treffen bearbeiten' : 'Neues Treffen anlegen'; ?></h2>
                    <form method="post" action="">
                        <?php wp_nonce_field('drf_save_meeting_nonce'); ?>
                        <?php if ($edit_meeting) : ?>
                            <input type="hidden" name="meeting_id" value="<?php echo esc_attr($edit_meeting->id); ?>">
                        <?php endif; ?>

                        <table class="form-table">
                            <tr>
                                <th><label for="meet_title">Titel des Treffens</label></th>
                                <td>
                                    <input type="text" id="meet_title" name="title" required value="<?php echo esc_attr($edit_meeting->title ?? 'AUGENBLICKE XXVII (2027)'); ?>" class="regular-text">
                                    <p class="description">z.B. AUGENBLICKE XXVI (2026) oder AUGENBLICKE XXVII (2027)</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_year">Jahr</label></th>
                                <td>
                                    <input type="number" id="meet_year" name="year" required value="<?php echo esc_attr($edit_meeting->year ?? (date('Y') + 1)); ?>" style="width:100px;">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_location">Tagungsort</label></th>
                                <td>
                                    <input type="text" id="meet_location" name="location" required value="<?php echo esc_attr($edit_meeting->location ?? 'Haus Lutherrose, Neuendettelsau'); ?>" class="large-text">
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_dates">Zeitraum (Start / Ende)</label></th>
                                <td>
                                    <input type="date" name="start_date" required value="<?php echo esc_attr($edit_meeting->start_date ?? '2027-05-27'); ?>">
                                    bis
                                    <input type="date" name="end_date" required value="<?php echo esc_attr($edit_meeting->end_date ?? '2027-05-30'); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th><label>Teilnehmerbeiträge (&euro;)</label></th>
                                <td>
                                    <label>EZ: <input type="number" step="0.5" name="price_ez" value="<?php echo esc_attr($edit_meeting->price_ez ?? 250); ?>" style="width:90px;"></label>
                                    <label style="margin-left:8px;">DZ: <input type="number" step="0.5" name="price_dz" value="<?php echo esc_attr($edit_meeting->price_dz ?? 205); ?>" style="width:90px;"></label>
                                    <label style="margin-left:8px;">MBZ: <input type="number" step="0.5" name="price_mbz" value="<?php echo esc_attr($edit_meeting->price_mbz ?? 170); ?>" style="width:90px;"></label>
                                    <label style="margin-left:8px;">Tagesgast: <input type="number" step="0.5" name="price_day" value="<?php echo esc_attr($edit_meeting->price_day ?? 65); ?>" style="width:90px;"></label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_status">Status</label></th>
                                <td>
                                    <select name="status" id="meet_status">
                                        <option value="active" <?php selected($edit_meeting->status ?? 'active', 'active'); ?>>Aktiv (Anmeldung geöffnet)</option>
                                        <option value="planning" <?php selected($edit_meeting->status ?? '', 'planning'); ?>>In Planung (Vorschau)</option>
                                        <option value="closed" <?php selected($edit_meeting->status ?? '', 'closed'); ?>>Geschlossen (Ausgebucht / Beendet)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="meet_details">Beschreibung / Anreise</label></th>
                                <td>
                                    <textarea name="location_details" id="meet_details" rows="3" class="large-text"><?php echo esc_textarea($edit_meeting->location_details ?? ''); ?></textarea>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" name="drf_save_meeting" class="button button-primary button-large">
                                <?php echo $edit_meeting ? 'Änderungen speichern' : 'Treffen anlegen'; ?>
                            </button>
                            <?php if ($edit_meeting) : ?>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=drf-meetings-manage')); ?>" class="button button-secondary">Abbrechen</a>
                            <?php endif; ?>
                        </p>
                    </form>
                </div>

                <!-- Right: List of Meetings -->
                <div style="flex:1; background:#ffffff; padding:24px; border-radius:12px; border:1px solid #e5e7eb; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
                    <h2>Bestehende Treffen</h2>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Jahr</th>
                                <th>Titel &amp; Ort</th>
                                <th>Zeitraum</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($meetings as $m) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($m->year); ?></strong></td>
                                    <td>
                                        <strong><?php echo esc_html($m->title); ?></strong><br>
                                        <small><?php echo esc_html($m->location); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo date_i18n('d.m.Y', strtotime($m->start_date)); ?> &ndash; <?php echo date_i18n('d.m.Y', strtotime($m->end_date)); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($m->status === 'active') : ?>
                                            <span style="background:#16a34a; color:#ffffff; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700;">Aktiv</span>
                                        <?php elseif ($m->status === 'planning') : ?>
                                            <span style="background:#d97706; color:#ffffff; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700;">In Planung</span>
                                        <?php else : ?>
                                            <span style="background:#6b7280; color:#ffffff; padding:2px 8px; border-radius:10px; font-size:11px; font-weight:700;">Geschlossen</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo esc_url(admin_url('admin.php?page=drf-meetings-manage&edit_id=' . $m->id)); ?>" class="button button-small">Bearbeiten</a>
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
