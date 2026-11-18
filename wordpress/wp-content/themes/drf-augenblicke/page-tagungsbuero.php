<?php
/**
 * Template Name: Tagungsbüro
 * Description: Tagungsbüro & Anmeldung zu den Augenblicke-Treffen
 *
 * @package DRF_Augenblicke
 */

get_header();

$submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drf_anmeldung_nonce'])) {
    if (wp_verify_nonce($_POST['drf_anmeldung_nonce'], 'drf_anmeldung_action')) {
        $submitted = true;
    }
}
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Tagungsb&uuml;ro</h1>
        <p class="page-lead">
            Dein Ansprechpartner vor Ort und online &ndash; f&uuml;r Fragen, Zimmerreservierungen, Anmeldung und organisatorischen Ablauf.
        </p>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <?php if ($submitted) : ?>
                <div style="background: #e6f4ea; border-left: 4px solid #137333; padding: 20px 24px; border-radius: 8px; margin-bottom: 28px;">
                    <h3 style="color: #137333; margin-top: 0;">Vielen Dank für deine Anmeldung!</h3>
                    <p style="margin-bottom: 0;">
                        Deine Anmeldung für AUGENBLICKE XXVI ist erfolgreich beim Tagungsbüro eingegangen. Wir prüfen die Zimmerkontingente im Haus Lutherrose und senden dir in Kürze eine Bestätigung an <strong><?php echo esc_html($_POST['email'] ?? ''); ?></strong>.
                    </p>
                </div>
            <?php endif; ?>

            <h2>Anmeldung zu AUGENBLICKE XXVI (28. &ndash; 31. Mai 2026)</h2>
            <p>
                Bitte f&uuml;lle das folgende Formular aus, um dir deinen Platz und dein Zimmer im Tagungshaus Lutherrose (Neuendettelsau) zu sichern. Das Kontingent ist auf 55 Pl&auml;tze begrenzt.
            </p>

            <form method="post" action="" style="margin-top: 24px; max-width: 680px;">
                <?php wp_nonce_field('drf_anmeldung_action', 'drf_anmeldung_nonce'); ?>

                <div class="drf-form-group">
                    <label class="drf-label" for="full_name">Vor- und Nachname *</label>
                    <input type="text" id="full_name" name="full_name" class="drf-input" required placeholder="z.B. Max Mustermann">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="nickname">Spitzname / Nick im Usenet oder Forum</label>
                    <input type="text" id="nickname" name="nickname" class="drf-input" placeholder="z.B. Frank_S oder SilberKorn">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="email">E-Mail-Adresse *</label>
                    <input type="email" id="email" name="email" class="drf-input" required placeholder="name@beispiel.de">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="room_type">Unterkunftswunsch (Vollpension) *</label>
                    <select id="room_type" name="room_type" class="drf-select" required>
                        <option value="ez">Einzelzimmer mit Dusche/WC</option>
                        <option value="dz">Doppelzimmer (Partner/Freund bitte im Kommentar angeben)</option>
                        <option value="tagesgast">Tagesgast (inkl. Mahlzeiten, ohne &Uuml;bernachtung)</option>
                    </select>
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="shuttle_needed">Shuttleservice ben&ouml;tigt?</label>
                    <select id="shuttle_needed" name="shuttle_needed" class="drf-select">
                        <option value="nein">Nein, reise mit dem eigenen PKW an</option>
                        <option value="wicklesgreuth">Ja, Abholung Bahnhof Wicklesgreuth</option>
                        <option value="ansbach">Ja, Abholung Bahnhof Ansbach</option>
                    </select>
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="message">Anmerkungen, Di&auml;tw&uuml;nsche (vegetarisch/vegan) oder Workshop-Ideen</label>
                    <textarea id="message" name="message" class="drf-textarea" rows="4" placeholder="Deine Nachricht an das Tagungsbüro..."></textarea>
                </div>

                <button type="submit" class="pill-btn pill-btn-home" style="font-size: 1rem; padding: 12px 28px; cursor: pointer;">
                    <span>Verbindlich anmelden</span>
                    <span>&rarr;</span>
                </button>
            </form>

            <hr style="margin: 40px 0; border: none; border-top: 1px solid var(--color-border-subtle);">

            <h2>Direkter Kontakt zum Tagungsb&uuml;ro</h2>
            <p>
                Du hast Fragen vorab oder m&ouml;chtest eine bestehende Buchung anpassen? Schreib unserem Orga-Team jederzeit direkt:
            </p>
            <p style="font-size: 1.1rem; font-weight: 600;">
                E-Mail: <a href="mailto:tagungsbuero2026@d-r-f.de" style="color: #b78103;">tagungsbuero2026@d-r-f.de</a>
            </p>
        </div>
    </div>
</div>

<?php
get_footer();
