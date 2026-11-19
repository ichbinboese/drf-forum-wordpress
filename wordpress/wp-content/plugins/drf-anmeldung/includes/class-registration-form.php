<?php
/**
 * DRF Treffen-Anmeldung - Registration Form & Handler
 *
 * @package DRF_Anmeldung
 */

if (!defined('ABSPATH')) {
    exit;
}

class DRF_Registration_Form {

    public function __construct() {
        add_shortcode('drf_anmeldung', [$this, 'render_form_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_form_assets']);
        add_action('wp_ajax_drf_submit_registration', [$this, 'handle_ajax_submission']);
        add_action('wp_ajax_nopriv_drf_submit_registration', [$this, 'handle_ajax_submission']);
    }

    public function enqueue_form_assets() {
        wp_enqueue_style(
            'drf-anmeldung-css',
            DRF_ANMELDUNG_URL . 'assets/css/anmeldung.css',
            [],
            DRF_ANMELDUNG_VERSION
        );

        wp_enqueue_script(
            'drf-anmeldung-js',
            DRF_ANMELDUNG_URL . 'assets/js/anmeldung.js',
            ['jquery'],
            DRF_ANMELDUNG_VERSION,
            true
        );

        wp_localize_script('drf-anmeldung-js', 'drfAnmeldungData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('drf_registration_nonce'),
        ]);
    }

    /**
     * Render Shortcode [drf_anmeldung]
     */
    public function render_form_shortcode($atts) {
        $atts = shortcode_atts([
            'meeting' => '',
            'year'    => '',
        ], $atts, 'drf_anmeldung');

        if (!empty($atts['meeting'])) {
            $meeting = DRF_Meeting_Manager::get_meeting($atts['meeting']);
        } elseif (!empty($atts['year'])) {
            $meeting = DRF_Meeting_Manager::get_meeting('augenblicke-' . $atts['year']);
        } else {
            $meeting = DRF_Meeting_Manager::get_active_meeting();
        }

        if (!$meeting) {
            return '<div class="drf-notice drf-notice-warning">' . esc_html__('Aktuell ist keine Anmeldung geöffnet. Bitte wende dich an das Tagungsbüro.', 'drf-anmeldung') . '</div>';
        }

        $all_meetings = DRF_Meeting_Manager::get_all_meetings();

        ob_start();
        ?>
        <div class="drf-anmeldung-wrapper" id="drfAnmeldungWrapper">
            <!-- Header Infobox -->
            <div class="drf-meeting-banner">
                <div class="drf-banner-header">
                    <span class="drf-meeting-badge"><?php echo esc_html($meeting->title); ?></span>
                    <span class="drf-meeting-year"><?php echo esc_html($meeting->year); ?></span>
                </div>
                <h3 class="drf-meeting-location"><?php echo esc_html($meeting->location); ?></h3>
                <div class="drf-meeting-dates">
                    <span>📅 <strong>Zeitraum:</strong> <?php echo date_i18n('d.m.Y', strtotime($meeting->start_date)); ?> &ndash; <?php echo date_i18n('d.m.Y', strtotime($meeting->end_date)); ?></span>
                    <span>📍 <strong>Veranstalter:</strong> de.rec.fotografie e.V. &bull; Tagungsbüro</span>
                </div>
                <?php if (!empty($meeting->location_details)) : ?>
                    <p class="drf-meeting-details"><?php echo esc_html($meeting->location_details); ?></p>
                <?php endif; ?>

                <?php if (count($all_meetings) > 1) : ?>
                    <div class="drf-meeting-switch">
                        <label for="drfMeetingSelect">Treffen wechseln:</label>
                        <select id="drfMeetingSelect" onchange="if(this.value) window.location.href=this.value;">
                            <?php foreach ($all_meetings as $m) : ?>
                                <option value="<?php echo esc_url(add_query_arg('meeting_id', $m->id)); ?>" <?php selected($m->id, $meeting->id); ?>>
                                    <?php echo esc_html($m->title . ' (' . $m->year . ' in ' . $m->location . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Form Container -->
            <form id="drfRegistrationForm" class="drf-reg-form" method="post">
                <input type="hidden" name="action" value="drf_submit_registration">
                <input type="hidden" name="meeting_id" value="<?php echo esc_attr($meeting->id); ?>">
                <?php wp_nonce_field('drf_registration_nonce', 'drf_reg_nonce'); ?>

                <!-- Step 1: Persönliche Daten -->
                <fieldset class="drf-form-section">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">1</span>
                        <span>Persönliche Angaben</span>
                    </legend>
                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_firstname">Vorname <span class="drf-req">*</span></label>
                            <input type="text" id="reg_firstname" name="firstname" required placeholder="z.B. Frank">
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_lastname">Nachname <span class="drf-req">*</span></label>
                            <input type="text" id="reg_lastname" name="lastname" required placeholder="z.B. Schliffke">
                        </div>
                    </div>

                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_nickname">Nickname / Newsgroup-Handle</label>
                            <input type="text" id="reg_nickname" name="nickname" placeholder="z.B. microbug oder SilberKorn">
                            <small>Für Namensschild vor Ort und Forum-Zuordnung</small>
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_email">E-Mail-Adresse <span class="drf-req">*</span></label>
                            <input type="email" id="reg_email" name="email" required placeholder="name@beispiel.de">
                            <small>Für Anmeldebestätigung und Tagungsbüro-Infos</small>
                        </div>
                    </div>

                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_phone">Telefon / Mobilnummer</label>
                            <input type="tel" id="reg_phone" name="phone" placeholder="z.B. 0170 1234567">
                            <small>Für Erreichbarkeit vor Ort bei Anreise / Shuttle</small>
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_street">Straße &amp; Hausnummer</label>
                            <input type="text" id="reg_street" name="street" placeholder="Musterstraße 12">
                        </div>
                    </div>

                    <div class="drf-form-row">
                        <div class="drf-form-group">
                            <label for="reg_zip_city">PLZ &amp; Wohnort</label>
                            <input type="text" id="reg_zip_city" name="zip_city" placeholder="z.B. 91564 Neuendettelsau">
                        </div>
                    </div>
                </fieldset>

                <!-- Step 2: Teilnahme & Zimmerkategorie -->
                <fieldset class="drf-form-section">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">2</span>
                        <span>Teilnahme &amp; Unterkunft</span>
                    </legend>

                    <div class="drf-form-group">
                        <label>Teilnahme-Dauer:</label>
                        <div class="drf-radio-cards">
                            <label class="drf-radio-card">
                                <input type="radio" name="participation_type" value="full" checked data-factor="1">
                                <div class="drf-card-label">
                                    <strong>Gesamtes Treffen (Donnerstag &ndash; Sonntag)</strong>
                                    <span>Vollzeit &bull; 3 Übernachtungen &bull; 4 Tage Programm</span>
                                </div>
                            </label>
                            <label class="drf-radio-card">
                                <input type="radio" name="participation_type" value="part_fri" data-factor="0.75">
                                <div class="drf-card-label">
                                    <strong>Teilzeit (Freitag &ndash; Sonntag)</strong>
                                    <span>2 Übernachtungen</span>
                                </div>
                            </label>
                            <label class="drf-radio-card">
                                <input type="radio" name="participation_type" value="day" data-factor="0.3">
                                <div class="drf-card-label">
                                    <strong>Tagesgast (ohne Übernachtung im Haus)</strong>
                                    <span>Inkl. Tagespauschale &amp; Verpflegung vor Ort</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="drf-form-group">
                        <label>Gewünschte Zimmerkategorie:</label>
                        <div class="drf-radio-cards">
                            <label class="drf-radio-card">
                                <input type="radio" name="room_category" value="ez" checked data-price="<?php echo esc_attr($meeting->price_ez); ?>">
                                <div class="drf-card-label">
                                    <strong>Einzelzimmer (EZ)</strong>
                                    <span>Eigenes Zimmer mit Bad &bull; ca. <?php echo number_format($meeting->price_ez, 2, ',', '.'); ?> &euro;</span>
                                </div>
                            </label>
                            <label class="drf-radio-card">
                                <input type="radio" name="room_category" value="dz" data-price="<?php echo esc_attr($meeting->price_dz); ?>">
                                <div class="drf-card-label">
                                    <strong>Doppelzimmer (DZ)</strong>
                                    <span>Geteiltes Zimmer &bull; ca. <?php echo number_format($meeting->price_dz, 2, ',', '.'); ?> &euro; pro Person</span>
                                </div>
                            </label>
                            <label class="drf-radio-card">
                                <input type="radio" name="room_category" value="mbz" data-price="<?php echo esc_attr($meeting->price_mbz); ?>">
                                <div class="drf-card-label">
                                    <strong>Mehrbettzimmer / Bettenlager</strong>
                                    <span>Günstige Kategorie &bull; ca. <?php echo number_format($meeting->price_mbz, 2, ',', '.'); ?> &euro; pro Person</span>
                                </div>
                            </label>
                            <label class="drf-radio-card">
                                <input type="radio" name="room_category" value="self" data-price="<?php echo esc_attr($meeting->price_day); ?>">
                                <div class="drf-card-label">
                                    <strong>Selbstzahler / WoMo / Heimschläfer</strong>
                                    <span>Nur Verpflegung &amp; Tagungspauschale &bull; ca. <?php echo number_format($meeting->price_day, 2, ',', '.'); ?> &euro;</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="drf-form-group" id="drfRoomPartnerGroup" style="display:none;">
                        <label for="reg_room_partner">Wunschpartner für Doppelzimmer:</label>
                        <input type="text" id="reg_room_partner" name="room_partner" placeholder="Name oder Nickname deines Zimmerpartners">
                    </div>
                </fieldset>

                <!-- Step 3: Verpflegung & Ernährung -->
                <fieldset class="drf-form-section">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">3</span>
                        <span>Verpflegung &amp; Ernährung</span>
                    </legend>

                    <div class="drf-form-group">
                        <label>Ernährungsform:</label>
                        <div class="drf-pill-select">
                            <label><input type="radio" name="diet" value="omnivore" checked> <span>Normalkost</span></label>
                            <label><input type="radio" name="diet" value="vegetarian"> <span>Vegetarisch</span></label>
                            <label><input type="radio" name="diet" value="vegan"> <span>Vegan</span></label>
                        </div>
                    </div>

                    <div class="drf-form-group">
                        <label for="reg_diet_allergies">Allergien / Lebensmittelunverträglichkeiten:</label>
                        <input type="text" id="reg_diet_allergies" name="diet_allergies" placeholder="z.B. Laktose, Gluten, Nüsse (wird an die Hausküche weitergegeben)">
                    </div>
                </fieldset>

                <!-- Step 4: Workshops & Eigene Angebote -->
                <fieldset class="drf-form-section">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">4</span>
                        <span>Workshops &amp; Sessions</span>
                    </legend>
                    <p class="drf-section-desc">Bei AUGENBLICKE gestalten die Teilnehmer das Programm aktiv mit.</p>

                    <div class="drf-form-group">
                        <label for="reg_workshop_interest">Interessensschwerpunkte:</label>
                        <textarea id="reg_workshop_interest" name="workshop_interest" rows="2" placeholder="z.B. Analogfotografie, Studioblitz-Technik, Altglas-Linsen, Makro, Bildkritik, Astro"></textarea>
                    </div>

                    <div class="drf-form-group">
                        <label for="reg_workshop_offer">Möchtest du selbst einen Workshop anbieten?</label>
                        <input type="text" id="reg_workshop_offer" name="workshop_offer" placeholder="Thema / Titel deines Workshop-Vorschlags (optional)">
                    </div>

                    <div class="drf-form-group">
                        <label for="reg_workshop_details">Raum- / Ausstattungsbedarf für deinen Workshop:</label>
                        <textarea id="reg_workshop_details" name="workshop_offer_details" rows="2" placeholder="z.B. Beamer, Dunkelkammer, Studiofläche, Fotowalk im Freien"></textarea>
                    </div>
                </fieldset>

                <!-- Step 5: Shuttleservice für Bahnreisende -->
                <fieldset class="drf-form-section">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">5</span>
                        <span>Shuttleservice (Bahn-Anreise)</span>
                    </legend>
                    <p class="drf-section-desc">Kommst du mit der Bahn? Unser Shuttleservice holt dich gerne am Bahnhof ab!</p>

                    <div class="drf-form-group">
                        <label class="drf-checkbox-label">
                            <input type="checkbox" id="reg_shuttle_needed" name="shuttle_needed" value="1">
                            <span><strong>Ja, ich benötige Shuttleservice vom/zum Bahnhof</strong></span>
                        </label>
                    </div>

                    <div id="drfShuttleDetails" style="display:none;" class="drf-sub-block">
                        <div class="drf-form-row drf-cols-2">
                            <div class="drf-form-group">
                                <label for="reg_shuttle_station">Gewünschter Bahnhof:</label>
                                <select id="reg_shuttle_station" name="shuttle_station">
                                    <option value="Neuendettelsau">Bahnhof Neuendettelsau</option>
                                    <option value="Ansbach">Bahnhof Ansbach (Fernverkehr / IC / RE)</option>
                                    <option value="Wicklesgreuth">Bahnhof Wicklesgreuth (S-Bahn Nürnberg)</option>
                                </select>
                            </div>
                            <div class="drf-form-group">
                                <label for="reg_shuttle_arrival">Geplante Ankunftszeit:</label>
                                <input type="text" id="reg_shuttle_arrival" name="shuttle_arrival" placeholder="z.B. Do. ca. 15:45 Uhr (Zug RE 58212)">
                            </div>
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_shuttle_luggage">Gepäckgröße / Begleitpersonen:</label>
                            <input type="text" id="reg_shuttle_luggage" name="shuttle_luggage" placeholder="z.B. 1 Koffer + 1 Fotorucksack, 1 Person">
                        </div>
                    </div>
                </fieldset>

                <!-- Step 6: Freiwillige Sozialspende für das Sozialgremium („Sozialsowjet“) -->
                <fieldset class="drf-form-section drf-sozial-highlight">
                    <legend class="drf-section-title">
                        <span class="drf-step-num">6</span>
                        <span>Freiwillige Sozialspende &bull; Sozialgremium</span>
                    </legend>
                    <p class="drf-section-desc">
                        Niemand soll aus finanziellen Gründen dem Treffen fernbleiben müssen. Das <strong>Sozialgremium</strong> unterstützt Teilnehmer diskret und unbürokratisch. Möchtest du deinen Teilnehmerbeitrag freiwillig um eine Spende aufstocken?
                    </p>

                    <div class="drf-form-group">
                        <div class="drf-donation-pills">
                            <label><input type="radio" name="social_donation_select" value="0" checked> <span>Keine Spende</span></label>
                            <label><input type="radio" name="social_donation_select" value="10"> <span>+ 10 &euro;</span></label>
                            <label><input type="radio" name="social_donation_select" value="25"> <span>+ 25 &euro;</span></label>
                            <label><input type="radio" name="social_donation_select" value="50"> <span>+ 50 &euro;</span></label>
                            <label><input type="radio" name="social_donation_select" value="custom"> <span>Anderer Betrag</span></label>
                        </div>
                        <div id="drfCustomDonationGroup" style="display:none; margin-top:10px;">
                            <input type="number" id="reg_social_donation" name="social_donation" min="0" step="5" value="0" placeholder="Betrag in Euro">
                        </div>
                    </div>
                </fieldset>

                <!-- Calculation Summary & Consent -->
                <div class="drf-form-summary">
                    <div class="drf-summary-box">
                        <div class="drf-summary-row">
                            <span>Teilnehmerbeitrag (geschätzt):</span>
                            <span id="drfSummaryBase">0,00 &euro;</span>
                        </div>
                        <div class="drf-summary-row">
                            <span>Freiwillige Sozialspende:</span>
                            <span id="drfSummaryDonation">0,00 &euro;</span>
                        </div>
                        <div class="drf-summary-row drf-summary-total">
                            <strong>Gesamtbetrag (Zahlung per Überweisung):</strong>
                            <strong id="drfSummaryTotal">0,00 &euro;</strong>
                        </div>
                        <p class="drf-summary-note">
                            * Die genauen Kontodaten des de.rec.fotografie e.V. Treffenkontos und dein persönlicher Verwendungszweck werden dir nach dem Absenden per E-Mail zugestellt.
                        </p>
                    </div>

                    <div class="drf-form-group">
                        <label for="reg_notes">Sonstige Anmerkungen / Fragen an das Tagungsbüro:</label>
                        <textarea id="reg_notes" name="notes" rows="3" placeholder="z.B. Fahrgemeinschaft ab München, besondere Zimmerwünsche, etc."></textarea>
                    </div>

                    <div class="drf-form-group drf-consent-group">
                        <label class="drf-checkbox-label">
                            <input type="checkbox" name="privacy_consent" required value="1">
                            <span>Ich habe die <a href="<?php echo esc_url(home_url('/datenschutz/')); ?>" target="_blank">Datenschutzerklärung</a> gelesen und willige ein, dass meine Angaben zur Organisation des Treffens durch das Tagungsbüro verarbeitet werden. <span class="drf-req">*</span></span>
                        </label>
                    </div>

                    <div class="drf-submit-row">
                        <button type="submit" id="drfSubmitBtn" class="btn-drf-submit">
                            <span>Verbindlich für <?php echo esc_html($meeting->title); ?> anmelden</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>

                <div id="drfFormFeedback" class="drf-feedback" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX Form Submission Handler
     */
    public function handle_ajax_submission() {
        check_ajax_referer('drf_registration_nonce', 'drf_reg_nonce');

        $meeting_id = absint($_POST['meeting_id'] ?? 0);
        $meeting = DRF_Meeting_Manager::get_meeting($meeting_id);
        if (!$meeting) {
            wp_send_json_error(['message' => 'Ungültiges Treffen ausgewählt.']);
        }

        $firstname = sanitize_text_field($_POST['firstname'] ?? '');
        $lastname  = sanitize_text_field($_POST['lastname'] ?? '');
        $email     = sanitize_email($_POST['email'] ?? '');

        if (empty($firstname) || empty($lastname) || empty($email)) {
            wp_send_json_error(['message' => 'Bitte alle Pflichtfelder (Vorname, Nachname, E-Mail) ausfüllen.']);
        }

        $nickname       = sanitize_text_field($_POST['nickname'] ?? '');
        $phone          = sanitize_text_field($_POST['phone'] ?? '');
        $street         = sanitize_text_field($_POST['street'] ?? '');
        $zip_city       = sanitize_text_field($_POST['zip_city'] ?? '');
        $part_type      = sanitize_text_field($_POST['participation_type'] ?? 'full');
        $room_cat       = sanitize_text_field($_POST['room_category'] ?? 'ez');
        $room_partner   = sanitize_text_field($_POST['room_partner'] ?? '');
        $diet           = sanitize_text_field($_POST['diet'] ?? 'omnivore');
        $diet_allergies = sanitize_textarea_field($_POST['diet_allergies'] ?? '');
        $ws_interest    = sanitize_textarea_field($_POST['workshop_interest'] ?? '');
        $ws_offer       = sanitize_text_field($_POST['workshop_offer'] ?? '');
        $ws_details     = sanitize_textarea_field($_POST['workshop_offer_details'] ?? '');
        $shuttle        = !empty($_POST['shuttle_needed']) ? 1 : 0;
        $shuttle_st     = sanitize_text_field($_POST['shuttle_station'] ?? '');
        $shuttle_arr    = sanitize_text_field($_POST['shuttle_arrival'] ?? '');
        $shuttle_lug    = sanitize_text_field($_POST['shuttle_luggage'] ?? '');
        $donation       = floatval($_POST['social_donation'] ?? 0);
        $notes          = sanitize_textarea_field($_POST['notes'] ?? '');

        // Calculate base price
        $base_price = $meeting->price_ez;
        if ($room_cat === 'dz') $base_price = $meeting->price_dz;
        elseif ($room_cat === 'mbz') $base_price = $meeting->price_mbz;
        elseif ($room_cat === 'self') $base_price = $meeting->price_day;

        if ($part_type === 'part_fri') $base_price = $base_price * 0.75;
        elseif ($part_type === 'day') $base_price = $meeting->price_day;

        $total_amount = $base_price + $donation;

        global $wpdb;
        $table = $wpdb->prefix . 'drf_registrations';

        $inserted = $wpdb->insert($table, [
            'meeting_id'             => $meeting->id,
            'firstname'              => $firstname,
            'lastname'               => $lastname,
            'nickname'               => $nickname,
            'email'                  => $email,
            'phone'                  => $phone,
            'street'                 => $street,
            'zip_city'               => $zip_city,
            'participation_type'     => $part_type,
            'room_category'          => $room_cat,
            'room_partner'           => $room_partner,
            'diet'                   => $diet,
            'diet_allergies'         => $diet_allergies,
            'workshop_interest'      => $ws_interest,
            'workshop_offer'         => $ws_offer,
            'workshop_offer_details' => $ws_details,
            'shuttle_needed'         => $shuttle,
            'shuttle_station'        => $shuttle_st,
            'shuttle_arrival'        => $shuttle_arr,
            'shuttle_luggage'        => $shuttle_lug,
            'social_donation'        => $donation,
            'total_amount'           => $total_amount,
            'notes'                  => $notes,
            'status'                 => 'new',
        ]);

        if (!$inserted) {
            wp_send_json_error(['message' => 'Datenbankfehler beim Speichern der Anmeldung.']);
        }

        $reg_id = $wpdb->insert_id;

        // Send confirmation email
        $subject = "Anmeldebestätigung: {$meeting->title} (" . ($nickname ? $nickname : $firstname) . ")";
        $body = "Hallo {$firstname},\n\n";
        $body .= "vielen Dank für deine verbindliche Anmeldung zu {$meeting->title}!\n\n";
        $body .= "=== DEINE ANMELDEDATEN ===\n";
        $body .= "Name: {$firstname} {$lastname}\n";
        if ($nickname) $body .= "Nickname: {$nickname}\n";
        $body .= "Zimmerkategorie: " . strtoupper($room_cat) . "\n";
        if ($room_partner) $body .= "Zimmerpartner: {$room_partner}\n";
        $body .= "Ernährung: {$diet}\n";
        if ($shuttle) $body .= "Shuttleservice: Ja (ab {$shuttle_st}, Ankunft: {$shuttle_arr})\n";
        if ($donation > 0) $body .= "Freiwillige Sozialspende: " . number_format($donation, 2, ',', '.') . " EUR\n";
        $body .= "Gesamtbetrag: " . number_format($total_amount, 2, ',', '.') . " EUR\n\n";
        $body .= "=== ÜBERWEISUNG & TAGUNGSBÜRO ===\n";
        $body .= "Bitte überweise den Gesamtbetrag zeitnah auf das Treffen-Konto von de.rec.fotografie e.V.\n";
        $body .= "Verwendungszweck: AUGENBLICKE {$meeting->year} - {$lastname} - #{$reg_id}\n\n";
        $body .= "Bei Rückfragen erreichst du das Tagungsbüro unter tagungsbuero{$meeting->year}@d-r-f.de.\n\n";
        $body .= "Wir freuen uns auf ein großartiges Treffen in Neuendettelsau!\n";
        $body .= "Dein d.r.f-Tagungsbüro\n";

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        @wp_mail($email, $subject, $body, $headers);

        // Also notify Tagungsbüro
        $tb_email = 'tagungsbuero2026@d-r-f.de';
        $tb_subject = "[Neue Anmeldung #{$reg_id}] {$meeting->title}: {$firstname} {$lastname}" . ($nickname ? " ({$nickname})" : '');
        @wp_mail($tb_email, $tb_subject, $body, $headers);

        wp_send_json_success([
            'message' => "Vielen Dank, {$firstname}! Deine Anmeldung für {$meeting->title} wurde erfolgreich erfasst. Eine Bestätigungs-E-Mail wurde an {$email} gesendet.",
            'regId'   => $reg_id,
            'total'   => number_format($total_amount, 2, ',', '.') . ' €',
        ]);
    }
}
