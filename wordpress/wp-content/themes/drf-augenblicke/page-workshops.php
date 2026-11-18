<?php
/**
 * Template Name: Workshops
 * Description: Workshop-Übersicht für Augenblicke
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<div class="page-hero">
    <div class="container">
        <h1 class="page-title">Workshops &amp; Sessions</h1>
        <p class="page-lead">
            Praxisnah, inspirierend und vielf&auml;ltig: Workshops von Mitgliedern f&uuml;r Mitglieder in entspannter Atmosph&auml;re.
        </p>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <div class="page-card-container">
            <h2>Unser Workshop-Prinzip</h2>
            <p>
                Bei <strong>AUGENBLICKE</strong> gibt es keine bezahlten Vortragenden &ndash; das Programm lebt vom enormen Wissen und der Praxiserfahrung unserer eigenen Teilnehmer. Jeder kann sein Fachwissen in einer Session, einem Vortrag oder einem praktischen Studioaufbau weitergeben.
            </p>

            <h2>Geplante Workshops f&uuml;r 2026 (Neuendettelsau)</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin: 24px 0;">
                <div style="background: #f9fafb; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 20px;">
                    <div style="color: var(--color-primary-text); background: var(--color-primary); font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 8px;">Studio &amp; Licht</div>
                    <h3 style="margin-top: 4px; font-size: 1.15rem;">Studioblitz &amp; Lichtführung</h3>
                    <p style="font-size: 0.9rem; color: #4b5563;">
                        Einführung in Softboxen, Striplights, Beauty Dish und präzise Blitzbelichtungsmessung. Praktische Shootings mit Modellen und wechselnden Licht-Setups.
                    </p>
                </div>

                <div style="background: #f9fafb; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 20px;">
                    <div style="color: #ffffff; background: #374151; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 8px;">Analog &amp; Labor</div>
                    <h3 style="margin-top: 4px; font-size: 1.15rem;">Dunkelkammer: SW-Barytabzüge</h3>
                    <p style="font-size: 0.9rem; color: #4b5563;">
                        Echtes Handwerk: Vergrößerer einstellen, Gradationswandel-Filter, Split-Grade-Belichtung und Archiv-Wässerung für langlebige Barytabzüge.
                    </p>
                </div>

                <div style="background: #f9fafb; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 20px;">
                    <div style="color: var(--color-primary-text); background: var(--color-primary); font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 8px;">Optik &amp; Kameras</div>
                    <h3 style="margin-top: 4px; font-size: 1.15rem;">Altglas-Charakteristik an Spiegellos</h3>
                    <p style="font-size: 0.9rem; color: #4b5563;">
                        Klassische Objektive (M42, Leica M, Contax/Yashica) adaptieren. Seifenblasen-Bokeh, Flaring gezielt nutzen und manuelle Scharfstellung mit Peaking.
                    </p>
                </div>

                <div style="background: #f9fafb; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 20px;">
                    <div style="color: #ffffff; background: #374151; font-weight: 700; font-size: 0.78rem; padding: 3px 8px; border-radius: 4px; display: inline-block; margin-bottom: 8px;">Technik &amp; Natur</div>
                    <h3 style="margin-top: 4px; font-size: 1.15rem;">Makro- und Focus-Stacking</h3>
                    <p style="font-size: 0.9rem; color: #4b5563;">
                        Extreme Schärfentiefe im Nahbereich: Elektronischer Makroschlitten, motorisiertes Focus-Bracketing und Berechnung mit Helicon Focus oder Photoshop.
                    </p>
                </div>
            </div>

            <h2>Eigenen Workshop vorschlagen</h2>
            <p>
                Du hast ein spannendes Thema, das du gerne vorstellen m&ouml;chtest? Schlage deine Session einfach im Flarum Forum im Bereich <em>Workshops &amp; Sessions</em> vor oder wende dich an das Tagungsb&uuml;ro!
            </p>
            <div style="margin-top: 20px;">
                <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-forum">
                    <span>Im Forum Workshop vorschlagen &rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
