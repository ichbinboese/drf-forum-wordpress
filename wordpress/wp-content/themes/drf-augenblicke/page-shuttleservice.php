<?php
/**
 * Template Name: Shuttleservice
 * Description: Shuttleservice für die Augenblicke-Tagungsgäste
 *
 * @package DRF_Augenblicke
 */

get_header();

$shuttle_booked = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drf_shuttle_nonce'])) {
    if (wp_verify_nonce($_POST['drf_shuttle_nonce'], 'drf_shuttle_action')) {
        $shuttle_booked = true;
    }
}
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Shuttleservice &ndash; Bequem zum Treffen</h1>
        <p class="page-lead">
            Bequem und unkompliziert: Unser Shuttleservice bringt dich und deine Fotoausr&uuml;stung zuverl&auml;ssig vom Bahnhof direkt zum Haus Lutherrose.
        </p>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <?php if ($shuttle_booked) : ?>
                <div style="background: #e6f4ea; border-left: 4px solid #137333; padding: 20px 24px; border-radius: 8px; margin-bottom: 28px;">
                    <h3 style="color: #137333; margin-top: 0;">Shuttle-Anfrage erhalten!</h3>
                    <p style="margin-bottom: 0;">
                        Vielen Dank! Unser Shuttle-Koordinator meldet sich kurz vor dem Treffen per E-Mail bei dir mit den genauen Abholzeiten und der Telefonnummer des Fahrers.
                    </p>
                </div>
            <?php endif; ?>

            <h2>Unser Shuttle-Konzept</h2>
            <p>
                Um allen Bahnreisenden eine entspannte Anreise mit schwerem Fotogep&auml;ck, Stativen und Studioequipment zu erm&ouml;glichen, organisieren wir wie in jedem Jahr den ehrenamtlichen <strong>AUGENBLICKE Shuttleservice</strong>.
            </p>

            <h2>Abholstationen &amp; Fahrzeiten f&uuml;r 2026</h2>
            <table class="drf-table">
                <thead>
                    <tr>
                        <th>Bahnhof</th>
                        <th>Strecke / Anbindung</th>
                        <th>Fahrzeit zum Tagungshaus</th>
                        <th>Abholzeiten am Anreisetag (28. Mai)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Bahnhof Wicklesgreuth</strong></td>
                        <td>S-Bahn S4 (N&uuml;rnberg &ndash; Ansbach) &amp; Regionalbahnen</td>
                        <td>ca. 8 Minuten</td>
                        <td>St&uuml;ndlich ab 13:00 bis 19:30 Uhr</td>
                    </tr>
                    <tr>
                        <td><strong>Hauptbahnhof Ansbach</strong></td>
                        <td>IC/ICE &amp; Fernverkehr</td>
                        <td>ca. 20 Minuten</td>
                        <td>Nach vorheriger Vereinbarung (14:30, 16:30, 18:30 Uhr)</td>
                    </tr>
                </tbody>
            </table>

            <h2>Shuttle-Platz vorab reservieren</h2>
            <p>
                Bitte gib uns fr&uuml;hzeitig deine voraussichtliche Ankunftszeit bekannt, damit wir die Fahrten koordinieren k&ouml;nnen:
            </p>

            <form method="post" action="" style="margin-top: 24px; max-width: 680px;">
                <?php wp_nonce_field('drf_shuttle_action', 'drf_shuttle_nonce'); ?>

                <div class="drf-form-group">
                    <label class="drf-label" for="shuttle_name">Name *</label>
                    <input type="text" id="shuttle_name" name="shuttle_name" class="drf-input" required placeholder="Dein Name">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="shuttle_email">E-Mail-Adresse *</label>
                    <input type="email" id="shuttle_email" name="shuttle_email" class="drf-input" required placeholder="name@beispiel.de">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="station">Gew&uuml;nschter Bahnhof *</label>
                    <select id="station" name="station" class="drf-select" required>
                        <option value="wicklesgreuth">Bahnhof Wicklesgreuth (Empfohlen)</option>
                        <option value="ansbach">Hauptbahnhof Ansbach</option>
                    </select>
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="arrival_time">Voraussichtliche Ankunftszeit am 28. Mai 2026 *</label>
                    <input type="text" id="arrival_time" name="arrival_time" class="drf-input" required placeholder="z.B. 15:42 Uhr (S-Bahn S4)">
                </div>

                <div class="drf-form-group">
                    <label class="drf-label" for="luggage_note">Besonderes Gep&auml;ck / Ausr&uuml;stung</label>
                    <input type="text" id="luggage_note" name="luggage_note" class="drf-input" placeholder="z.B. Gro&szlig;formatkoffer, Studioblitzstative, Fotorucksack">
                </div>

                <button type="submit" class="pill-btn pill-btn-home" style="font-size: 1rem; padding: 12px 28px; cursor: pointer;">
                    <span>Shuttle reservieren</span>
                    <span>&rarr;</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?php
get_footer();
