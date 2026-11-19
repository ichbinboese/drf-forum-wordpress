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

        if (!empty($_GET['meeting'])) {
            $meeting = DRF_Meeting_Manager::get_meeting(sanitize_text_field($_GET['meeting']));
        } elseif (!empty($_GET['meeting_id'])) {
            $meeting = DRF_Meeting_Manager::get_meeting(absint($_GET['meeting_id']));
        } elseif (!empty($atts['meeting'])) {
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
            <!-- 1. Meeting Switcher Tabs -->
            <?php if (count($all_meetings) > 1) : ?>
                <div class="drf-meeting-switcher-tabs">
                    <span class="drf-switcher-label">Treffen wählen:</span>
                    <div class="drf-switcher-pills">
                        <?php foreach ($all_meetings as $m) :
                            $is_current = ($m->id == $meeting->id);
                            $url = add_query_arg('meeting_id', $m->id);
                            ?>
                            <a href="<?php echo esc_url($url); ?>#drfAnmeldungWrapper" class="drf-pill-tab <?php echo $is_current ? 'active' : ''; ?>">
                                <span class="tab-year"><?php echo esc_html($m->year); ?></span>
                                <span class="tab-title"><?php echo esc_html($m->title); ?></span>
                                <?php if ($m->status === 'active') : ?>
                                    <span class="tab-badge">Offen</span>
                                <?php else : ?>
                                    <span class="tab-badge secondary">Voranmeldung</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 2. Meeting Header Card -->
            <div class="drf-meeting-banner">
                <div class="drf-banner-header">
                    <span class="drf-meeting-badge"><?php echo esc_html($meeting->title); ?></span>
                    <span class="drf-meeting-year"><?php echo esc_html($meeting->year); ?></span>
                </div>
                <h3 class="drf-meeting-location"><?php echo esc_html($meeting->location); ?></h3>
                <div class="drf-meeting-dates">
                    <span>📅 <strong>Zeitraum:</strong> <?php echo esc_html(date_i18n('d.m.Y', strtotime($meeting->start_date))); ?> &ndash; <?php echo esc_html(date_i18n('d.m.Y', strtotime($meeting->end_date))); ?></span>
                    <span>📍 <strong>Veranstalter:</strong> de.rec.fotografie e.V. &bull; Tagungsbüro</span>
                </div>
                <?php if (!empty($meeting->location_details)) : ?>
                    <p class="drf-meeting-details"><?php echo esc_html($meeting->location_details); ?></p>
                <?php endif; ?>
            </div>

            <!-- 3. Multi-Step Progress Bar -->
            <div class="drf-steps-progress">
                <div class="drf-step-item active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Persönliches</div>
                </div>
                <div class="step-line"></div>
                <div class="drf-step-item" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Zimmer &amp; Essen</div>
                </div>
                <div class="step-line"></div>
                <div class="drf-step-item" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Shuttle &amp; Spende</div>
                </div>
                <div class="step-line"></div>
                <div class="drf-step-item" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Übersicht &amp; Abschluss</div>
                </div>
            </div>

            <!-- 4. Form Container -->
            <form id="drfRegistrationForm" class="drf-reg-form" method="post">
                <input type="hidden" name="action" value="drf_submit_registration">
                <input type="hidden" name="meeting_id" value="<?php echo esc_attr($meeting->id); ?>">
                <?php wp_nonce_field('drf_registration_nonce', 'drf_reg_nonce'); ?>

                <!-- STEP 1: Persönliche Angaben -->
                <div class="drf-form-step-content active" data-step-content="1">
                    <div class="drf-step-header">
                        <h3>1. Persönliche Angaben</h3>
                        <p>Bitte gib deine Kontaktdaten ein, damit wir deine Buchung erfassen können.</p>
                    </div>

                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_firstname">Vorname <span class="drf-req">*</span></label>
                            <input type="text" id="reg_firstname" name="firstname" required placeholder="z.B. Matthias">
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_lastname">Nachname <span class="drf-req">*</span></label>
                            <input type="text" id="reg_lastname" name="lastname" required placeholder="z.B. Weber">
                        </div>
                    </div>

                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_nickname">Nickname / Newsgroup-Handle</label>
                            <input type="text" id="reg_nickname" name="nickname" placeholder="z.B. matthias_w oder AltglasFan">
                            <small>Wird auf deinem Namensschild beim Treffen aufgedruckt.</small>
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_email">E-Mail-Adresse <span class="drf-req">*</span></label>
                            <input type="email" id="reg_email" name="email" required placeholder="name@beispiel.de">
                            <small>Für deine Anmeldebestätigung und wichtige Infos vorab.</small>
                        </div>
                    </div>

                    <div class="drf-form-row drf-cols-2">
                        <div class="drf-form-group">
                            <label for="reg_phone">Telefon / Mobilnummer</label>
                            <input type="tel" id="reg_phone" name="phone" placeholder="z.B. 0172 1234567">
                            <small>Wichtig für die Shuttle-Fahrer am Anreisetag.</small>
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_street">Straße &amp; Hausnummer</label>
                            <input type="text" id="reg_street" name="street" placeholder="Objektivstraße 12">
                        </div>
                    </div>

                    <div class="drf-form-row">
                        <div class="drf-form-group">
                            <label for="reg_zip_city">PLZ &amp; Wohnort</label>
                            <input type="text" id="reg_zip_city" name="zip_city" placeholder="z.B. 91564 Neuendettelsau">
                        </div>
                    </div>

                    <div class="drf-step-nav">
                        <div></div>
                        <button type="button" class="btn btn-primary drf-btn-next" data-target="2">
                            <span>Weiter zu Unterkunft &rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Teilnahme & Unterkunft -->
                <div class="drf-form-step-content" data-step-content="2">
                    <div class="drf-step-header">
                        <h3>2. Unterkunft &amp; Verpflegung</h3>
                        <p>Wähle deine bevorzugte Zimmerkategorie und deine Ernährungswünsche im Tagungshaus.</p>
                    </div>

                    <!-- Dauer -->
                    <div class="drf-form-group">
                        <label class="drf-label-strong">Dauer deiner Teilnahme:</label>
                        <div class="drf-duration-options">
                            <label class="drf-duration-pill">
                                <input type="radio" name="participation_type" value="full" checked data-factor="1">
                                <span>Vollzeit (Donnerstag &ndash; Sonntag) &bull; 4 Tage &amp; 3 Nächte</span>
                            </label>
                            <label class="drf-duration-pill">
                                <input type="radio" name="participation_type" value="part_fri" data-factor="0.75">
                                <span>Teilzeit (Freitag &ndash; Sonntag) &bull; 2 Nächte</span>
                            </label>
                            <label class="drf-duration-pill">
                                <input type="radio" name="participation_type" value="day" data-factor="0.3">
                                <span>Tagesgast (ohne Übernachtung im Tagungshaus)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Zimmerkarten -->
                    <div class="drf-form-group">
                        <label class="drf-label-strong">Gewünschte Zimmerkategorie:</label>
                        <div class="drf-room-cards-grid">
                            <!-- EZ -->
                            <label class="drf-room-card selected">
                                <input type="radio" name="room_category" value="ez" checked data-price="<?php echo esc_attr($meeting->price_ez); ?>" data-title="Einzelzimmer (EZ)">
                                <div class="room-card-inner">
                                    <div class="room-icon">🛏️</div>
                                    <div class="room-info">
                                        <div class="room-title">Einzelzimmer (EZ)</div>
                                        <div class="room-details">Eigenes Zimmer mit eigenem Bad/WC</div>
                                    </div>
                                    <div class="room-price">
                                        <span>ca. <?php echo number_format($meeting->price_ez, 2, ',', '.'); ?> &euro;</span>
                                        <small>Vollpension</small>
                                    </div>
                                </div>
                            </label>

                            <!-- DZ -->
                            <label class="drf-room-card">
                                <input type="radio" name="room_category" value="dz" data-price="<?php echo esc_attr($meeting->price_dz); ?>" data-title="Doppelzimmer (DZ)">
                                <div class="room-card-inner">
                                    <div class="room-icon">👥</div>
                                    <div class="room-info">
                                        <div class="room-title">Doppelzimmer (DZ)</div>
                                        <div class="room-details">Zweibettzimmer mit Bad &bull; Preis pro Person</div>
                                    </div>
                                    <div class="room-price">
                                        <span>ca. <?php echo number_format($meeting->price_dz, 2, ',', '.'); ?> &euro;</span>
                                        <small>pro Person</small>
                                    </div>
                                </div>
                            </label>

                            <!-- MBZ -->
                            <label class="drf-room-card">
                                <input type="radio" name="room_category" value="mbz" data-price="<?php echo esc_attr($meeting->price_mbz); ?>" data-title="Mehrbettzimmer (MBZ)">
                                <div class="room-card-inner">
                                    <div class="room-icon">🧳</div>
                                    <div class="room-info">
                                        <div class="room-title">Mehrbettzimmer (MBZ)</div>
                                        <div class="room-details">Ideal für Studenten &amp; Sparfüchse</div>
                                    </div>
                                    <div class="room-price">
                                        <span>ca. <?php echo number_format($meeting->price_mbz, 2, ',', '.'); ?> &euro;</span>
                                        <small>pro Person</small>
                                    </div>
                                </div>
                            </label>

                            <!-- Tagesgast -->
                            <label class="drf-room-card">
                                <input type="radio" name="room_category" value="self" data-price="<?php echo esc_attr($meeting->price_day); ?>" data-title="Heimschläfer / Tagesgast">
                                <div class="room-card-inner">
                                    <div class="room-icon">☀️</div>
                                    <div class="room-info">
                                        <div class="room-title">Tagesgast / WoMo</div>
                                        <div class="room-details">Inkl. Tagungsgebühr &amp; Mahlzeiten im Haus</div>
                                    </div>
                                    <div class="room-price">
                                        <span>ca. <?php echo number_format($meeting->price_day, 2, ',', '.'); ?> &euro;</span>
                                        <small>Pauschale</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Room partner group for DZ -->
                    <div class="drf-form-group" id="drfRoomPartnerGroup" style="display:none; background:#f8fafc; border:1px solid #cbd5e1; border-radius:8px; padding:16px;">
                        <label for="reg_room_partner" style="font-weight:700; color:#1e293b;">Wunsch-Zimmerpartner für Doppelzimmer:</label>
                        <input type="text" id="reg_room_partner" name="room_partner" placeholder="Name oder Nickname deines Partners (oder 'bitte zuteilen')">
                        <small>Wenn du niemanden angibst, teilt das Tagungsbüro dich einem netten Mitreisenden zu.</small>
                    </div>

                    <!-- Verpflegung -->
                    <div class="drf-form-group" style="margin-top:24px;">
                        <label class="drf-label-strong">Ernährungsform:</label>
                        <div class="drf-diet-chips">
                            <label class="drf-diet-chip active">
                                <input type="radio" name="diet" value="omnivore" checked>
                                <span>🥩 Normalkost / Allesesser</span>
                            </label>
                            <label class="drf-diet-chip">
                                <input type="radio" name="diet" value="vegetarian">
                                <span>🥗 Vegetarisch</span>
                            </label>
                            <label class="drf-diet-chip">
                                <input type="radio" name="diet" value="vegan">
                                <span>🌱 Vegan</span>
                            </label>
                        </div>
                    </div>

                    <div class="drf-form-group">
                        <label for="reg_diet_allergies">Lebensmittelunverträglichkeiten / Allergien:</label>
                        <input type="text" id="reg_diet_allergies" name="diet_allergies" placeholder="z.B. Gluten, Laktose, Nussallergie (wird an die Küche übermittelt)">
                    </div>

                    <div class="drf-step-nav">
                        <button type="button" class="btn btn-secondary drf-btn-prev" data-target="1">
                            <span>&larr; Zurück</span>
                        </button>
                        <button type="button" class="btn btn-primary drf-btn-next" data-target="3">
                            <span>Weiter zu Shuttle &amp; Spende &rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Shuttle & Solidarfonds -->
                <div class="drf-form-step-content" data-step-content="3">
                    <div class="drf-step-header">
                        <h3>3. Shuttleservice &amp; Solidarfonds</h3>
                        <p>Kostenloser Bahn-Transfer und freiwillige Unterstützung des »Sozialsowjets«.</p>
                    </div>

                    <!-- Shuttle Service Box -->
                    <div class="drf-shuttle-box">
                        <div class="shuttle-header-toggle">
                            <label class="drf-toggle-switch">
                                <input type="checkbox" id="reg_shuttle_needed" name="shuttle_needed" value="1">
                                <span class="toggle-slider"></span>
                            </label>
                            <div>
                                <strong style="font-size:1.05rem; color:#1e293b;">Ich reise mit der Bahn an und brauche den Shuttle-Service</strong>
                                <p style="font-size:0.88rem; color:#64748b; margin:2px 0 0 0;">Kostenlose Abholung ab Wicklesgreuth (S4) oder Ansbach (IC/ICE).</p>
                            </div>
                        </div>

                        <div id="drfShuttleDetails" style="display:none; margin-top:16px; padding-top:16px; border-top:1px solid #e2e8f0;">
                            <div class="drf-form-row drf-cols-2">
                                <div class="drf-form-group">
                                    <label for="reg_shuttle_station">Gewünschter Bahnhof:</label>
                                    <select id="reg_shuttle_station" name="shuttle_station" class="drf-select">
                                        <option value="wicklesgreuth">Bahnhof Wicklesgreuth (S-Bahn S4 ab Nürnberg Hbf - 8 Min)</option>
                                        <option value="ansbach">Hauptbahnhof Ansbach (IC/ICE Fernverkehr - 20 Min)</option>
                                    </select>
                                </div>
                                <div class="drf-form-group">
                                    <label for="reg_shuttle_arrival">Voraussichtliche Ankunftszeit:</label>
                                    <input type="text" id="reg_shuttle_arrival" name="shuttle_arrival" placeholder="z.B. 14:45 Uhr">
                                </div>
                            </div>
                            <div class="drf-form-group">
                                <label for="reg_shuttle_luggage">Gepäckhinweis:</label>
                                <input type="text" id="reg_shuttle_luggage" name="shuttle_luggage" placeholder="z.B. 1 Koffer, 1 Fotorucksack, 1 sperriges Stativ">
                            </div>
                        </div>
                    </div>

                    <!-- Workshop Angebote -->
                    <div class="drf-form-group" style="margin-top:24px;">
                        <label class="drf-label-strong">Workshop-Themen (Interesse &amp; Eigene Angebote):</label>
                        <div class="drf-form-group">
                            <label for="reg_workshop_interest">Themen, die dich besonders interessieren:</label>
                            <input type="text" id="reg_workshop_interest" name="workshop_interest" placeholder="z.B. Studioblitz, Barytabzüge, Altglas an spiegellos, Makro-Stacking">
                        </div>
                        <div class="drf-form-group">
                            <label for="reg_workshop_offer">Möchtest du selbst eine Session oder einen Vortrag halten?</label>
                            <input type="text" id="reg_workshop_offer" name="workshop_offer" placeholder="Thema deines Vortrags oder Workshops (optional)">
                        </div>
                    </div>

                    <!-- Solidarfonds ("Sozialsowjet") -->
                    <div class="drf-solidar-box" style="background:#fff8e1; border:1px solid #fde047; border-left:5px solid #f5b301; border-radius:10px; padding:20px; margin-top:24px;">
                        <h4 style="color:#b45309; margin:0 0 6px 0; font-size:1.1rem; display:flex; align-items:center; gap:8px;">
                            <span>🤝</span> Freiwilliger Solidarbeitrag ("Sozialsowjet")
                        </h4>
                        <p style="font-size:0.9rem; color:#78350f; margin:0 0 14px 0; line-height:1.5;">
                            Seit 1999 sorgt der Solidarfonds dafür, dass Teilnehmer in vorübergehenden finanziellen Engpässen (z.B. Studenten, Auszubildende) diskret bezuschusst werden können.
                        </p>
                        <div class="drf-donation-pills">
                            <label class="drf-don-pill active"><input type="radio" name="social_donation_select" value="0" checked> <span>Keine Spende</span></label>
                            <label class="drf-don-pill"><input type="radio" name="social_donation_select" value="15"> <span>+ 15 &euro;</span></label>
                            <label class="drf-don-pill"><input type="radio" name="social_donation_select" value="25"> <span>+ 25 &euro;</span></label>
                            <label class="drf-don-pill"><input type="radio" name="social_donation_select" value="50"> <span>+ 50 &euro;</span></label>
                            <label class="drf-don-pill"><input type="radio" name="social_donation_select" value="custom"> <span>Anderer Betrag</span></label>
                        </div>
                        <div id="drfCustomDonationGroup" style="display:none; margin-top:12px;">
                            <input type="number" id="reg_social_donation" name="social_donation" min="0" step="5" value="0" style="max-width:180px;" placeholder="Betrag in €">
                        </div>
                    </div>

                    <div class="drf-step-nav">
                        <button type="button" class="btn btn-secondary drf-btn-prev" data-target="2">
                            <span>&larr; Zurück</span>
                        </button>
                        <button type="button" class="btn btn-primary drf-btn-next" data-target="4">
                            <span>Zur Zusammenfassung &rarr;</span>
                        </button>
                    </div>
                </div>

                <!-- STEP 4: Übersicht & Verbindliche Anmeldung -->
                <div class="drf-form-step-content" data-step-content="4">
                    <div class="drf-step-header">
                        <h3>4. Zusammenfassung &amp; Verbindliche Buchung</h3>
                        <p>Bitte überprüfe deine Angaben sorgfältig vor dem Absenden.</p>
                    </div>

                    <!-- Live Summary Review Card -->
                    <div class="drf-review-summary-card">
                        <div class="review-grid">
                            <div class="review-item">
                                <span class="lbl">Veranstaltung:</span>
                                <strong id="revMeeting"><?php echo esc_html($meeting->title); ?> (<?php echo esc_html($meeting->year); ?>)</strong>
                            </div>
                            <div class="review-item">
                                <span class="lbl">Teilnehmer:</span>
                                <strong id="revName">--</strong> (<span id="revNick">--</span>)
                            </div>
                            <div class="review-item">
                                <span class="lbl">E-Mail &amp; Tel:</span>
                                <span id="revContact">--</span>
                            </div>
                            <div class="review-item">
                                <span class="lbl">Unterkunft:</span>
                                <strong id="revRoom">Einzelzimmer (EZ)</strong>
                            </div>
                            <div class="review-item">
                                <span class="lbl">Verpflegung:</span>
                                <span id="revDiet">Normalkost</span>
                            </div>
                            <div class="review-item">
                                <span class="lbl">Shuttleservice:</span>
                                <span id="revShuttle">Nicht benötigt</span>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="review-price-box">
                            <div class="price-row">
                                <span>Unterkunft &amp; Tagungspauschale:</span>
                                <strong id="drfSummaryBase">0,00 &euro;</strong>
                            </div>
                            <div class="price-row">
                                <span>Freiwilliger Solidarfonds ("Sozialsowjet"):</span>
                                <strong id="drfSummaryDonation">0,00 &euro;</strong>
                            </div>
                            <div class="price-row total-row">
                                <span>Gesamtbetrag (Richtwert):</span>
                                <strong id="drfSummaryTotal" class="total-highlight">0,00 &euro;</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Notice & Checkboxes -->
                    <div class="drf-form-agreements">
                        <label class="drf-checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span>Ich melde mich verbindlich zum Treffen an. Die Zahlung des Tagungsbeitrags erfolgt nach Bestätigung durch das Tagungsbüro per Überweisung.</span>
                        </label>
                        <label class="drf-checkbox-label">
                            <input type="checkbox" name="agree_privacy" required>
                            <span>Ich willige ein, dass meine Kontaktdaten zur Organisation des Treffens durch das gewählte Tagungsbüro verarbeitet werden.</span>
                        </label>
                    </div>

                    <!-- Form Feedback Message Container -->
                    <div id="drfFormFeedback" class="drf-feedback-box" style="display:none;"></div>

                    <div class="drf-step-nav">
                        <button type="button" class="btn btn-secondary drf-btn-prev" data-target="3">
                            <span>&larr; Zurück</span>
                        </button>
                        <button type="submit" id="drfSubmitBtn" class="btn btn-primary btn-submit-final">
                            <span>Verbindlich anmelden &amp; Platz sichern &rarr;</span>
                        </button>
                    </div>
                </div>
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
        if ($room_cat === 'mbz') $base_price = $meeting->price_mbz;
        if ($room_cat === 'self') $base_price = $meeting->price_day;

        $factor = 1.0;
        if ($part_type === 'part_fri') $factor = 0.75;
        if ($part_type === 'day') $factor = 0.3;

        $total_amount = ($base_price * $factor) + $donation;

        global $wpdb;
        $table = $wpdb->prefix . 'drf_registrations';

        $insert_data = [
            'meeting_id'             => $meeting_id,
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
            'status'                 => 'new',
            'notes'                  => $notes,
        ];

        $current_user_id = get_current_user_id();
        if ($current_user_id) {
            $insert_data['user_id'] = $current_user_id;
        }

        $inserted = $wpdb->insert($table, $insert_data);

        if (!$inserted) {
            wp_send_json_error(['message' => 'Datenbankfehler: Die Anmeldung konnte nicht gespeichert werden. ' . $wpdb->last_error]);
        }

        $reg_id = $wpdb->insert_id;

        // Send Email Confirmation
        $subject = sprintf('[d.r.f] Anmeldebestätigung #%d - %s', $reg_id, $meeting->title);
        $message = "Hallo {$firstname},\n\nvielen Dank für deine verbindliche Anmeldung zu {$meeting->title} im {$meeting->location}!\n\n";
        $message .= "Zusammenfassung deiner Anmeldung:\n";
        $message .= "- Buchungsnummer: #{$reg_id}\n";
        $message .= "- Treffen: {$meeting->title} ({$meeting->year})\n";
        $message .= "- Zeitraum: " . date_i18n('d.m.Y', strtotime($meeting->start_date)) . " bis " . date_i18n('d.m.Y', strtotime($meeting->end_date)) . "\n";
        $message .= "- Zimmerkategorie: " . strtoupper($room_cat) . "\n";
        if ($room_partner) $message .= "- Wunschpartner: {$room_partner}\n";
        $message .= "- Verpflegung: {$diet}\n";
        if ($shuttle) $message .= "- Shuttleservice: Abholung ab {$shuttle_st} ({$shuttle_arr})\n";
        if ($donation > 0) $message .= "- Freiwilliger Solidarfonds: " . number_format($donation, 2, ',', '.') . " €\n";
        $message .= "- Gesamtbetrag: " . number_format($total_amount, 2, ',', '.') . " €\n\n";
        $message .= "Das Tagungsbüro prüft nun die Zimmerkontingente und meldet sich zeitnah mit den genauen Überweisungsdaten.\n\n";
        $message .= "Herzliche Grüße,\nDein d.r.f Tagungsbüro";

        @wp_mail($email, $subject, $message);

        wp_send_json_success([
            'message' => sprintf(
                'Vielen Dank, %s! Deine Anmeldung für %s wurde erfolgreich erfasst. Eine Bestätigungs-E-Mail wurde an %s gesendet.',
                esc_html($firstname),
                esc_html($meeting->title),
                esc_html($email)
            ),
            'regId'   => $reg_id,
            'total'   => number_format($total_amount, 2, ',', '.') . ' €',
        ]);
    }
}
