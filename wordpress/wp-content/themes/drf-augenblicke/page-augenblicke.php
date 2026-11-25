<?php
/**
 * Template Name: Augenblicke
 * Description: Seite über das Jahrestreffen »AUGENBLICKE«
 *
 * @package DRF_Augenblicke
 */

get_header();
$subheading = function_exists('drf_get_page_subheading') ? drf_get_page_subheading() : '';
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
            <h2>Was ist Augenblicke?</h2>
            <p>
                F&uuml;r alle, die neu hinzugekommen sind: <strong>Augenblicke</strong> ist das j&auml;hrliche Gro&szlig;treffen von d.r.f. Hierbei treffen sich zwischen 40 und 60 Fotografie-Enthusiasten f&uuml;r ein langes Wochenende von Christi Himmelfahrt oder Fronleichnam bis Sonntag.
            </p>
            <p>
                Eingeladen sind alle: Vom langj&auml;hrigen Usenet-Veteran bis zum Neumitglied im Flarum-Forum oder in unserer Facebook-Gruppe. Es gibt kein elitäres Gehabe &ndash; egal ob du mit einer Leica, einer Mittelformat-Hasselblad, einer analogen Spiegelreflex oder dem Smartphone fotografierst: Die Freude am Bild und der fachliche Austausch stehen im Mittelpunkt.
            </p>

            <h2>Kommende Treffen</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin: 20px 0;">
                <div style="background: #f9fafb; border-left: 4px solid var(--color-primary); padding: 18px 24px; border-radius: 6px;">
                    <span style="background: var(--color-primary); color: #141518; font-weight: 700; font-size: 0.75rem; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Aktuelle Anmeldung</span>
                    <h3 style="margin-top: 8px;">AUGENBLICKE XXVI &ndash; 2026</h3>
                    <p><strong>Datum:</strong> 28. bis 31. Mai 2026 (Fronleichnam)</p>
                    <p><strong>Ort:</strong> Haus Lutherrose, Neuendettelsau</p>
                    <p style="font-size: 0.92rem; color: #475569;">
                        Vollpension, helle Seminarr&auml;ume, Studioaufbauten, Altglas-Shootings und der traditionelle Samstagabend.
                    </p>
                    <div style="margin-top: 14px;">
                        <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>" class="pill-btn pill-btn-home">
                            <span>Zur Anmeldung 2026 &rarr;</span>
                        </a>
                    </div>
                </div>

                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-left: 4px solid #141518; padding: 18px 24px; border-radius: 8px;">
                    <span style="background: #141518; color: #f5b301; font-weight: 700; font-size: 0.75rem; padding: 3px 8px; border-radius: 4px; text-transform: uppercase;">Vorausschau</span>
                    <h3 style="margin-top: 8px;">AUGENBLICKE XXVII &ndash; 2027</h3>
                    <p><strong>Datum:</strong> 06. bis 09. Mai 2027 (Christi Himmelfahrt)</p>
                    <p><strong>Ort:</strong> Haus Lutherrose, Neuendettelsau</p>
                    <p style="font-size: 0.92rem; color: #475569;">
                        Auch für 2027 steht das Tagungshaus bereits fest! Die Anmeldung und Zimmerkontingent-Planung ist im Tagungsbüro freigeschaltet.
                    </p>
                    <div style="margin-top: 14px;">
                        <a href="<?php echo esc_url(home_url('/tagungsbuero/')); ?>" class="pill-btn pill-btn-forum">
                            <span>Voranmeldung 2027 &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <h2>Chronik aller Treffen</h2>
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
                    <tr>
                        <td><strong>AUGENBLICKE XXVII</strong></td>
                        <td><code>[LR]</code></td>
                        <td>2027</td>
                        <td>Haus Lutherrose, Neuendettelsau</td>
                        <td>Studio, Digital &amp; Analog, Fotowalk Franken</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE XXVI</strong></td>
                        <td><code>[LR]</code></td>
                        <td>2026</td>
                        <td>Haus Lutherrose, Neuendettelsau</td>
                        <td>Studioblitz, Altglas, Großformat, Relaunch</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE XXV</strong></td>
                        <td><code>[LR]</code></td>
                        <td>2024</td>
                        <td>Haus Lutherrose, Neuendettelsau</td>
                        <td>Jubiläumstreffen, Available Light, Porträt</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE XXIV</strong></td>
                        <td><code>[FB]</code></td>
                        <td>2023</td>
                        <td>Göbel's Schlosshotel, Friedewald</td>
                        <td>Architektur &amp; Landschaftsfotografie</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE XXIII</strong></td>
                        <td><code>[SE]</code></td>
                        <td>2022</td>
                        <td>Hotel Schwarzenshof, Rudolstadt</td>
                        <td>Post-Corona Wiedersehen &amp; Makroworkshop</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE XXII</strong></td>
                        <td><code>[LR]</code></td>
                        <td>2019</td>
                        <td>Haus Lutherrose, Neuendettelsau</td>
                        <td>High-Speed-Fotografie, Modelsharing</td>
                    </tr>
                    <tr>
                        <td><strong>AUGENBLICKE I</strong></td>
                        <td><code>[BM]</code></td>
                        <td>2007</td>
                        <td>Kloster Michaelstein, Blankenburg</td>
                        <td>Die Geburtsstunde der »Augenblicke« Reihe</td>
                    </tr>
                    <tr>
                        <td><strong>d.r.f Urtreffen</strong></td>
                        <td><code>[OR]</code></td>
                        <td>1999</td>
                        <td>Burg Rieneck, Spessart</td>
                        <td>Erstes legendäres Usenet-Treffen</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
get_footer();
