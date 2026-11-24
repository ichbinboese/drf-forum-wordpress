<?php
/**
 * Template Name: Tagungsbüro
 * Description: Tagungsbüro & Anmeldung zu den Augenblicke-Treffen
 *
 * @package DRF_Augenblicke
 */

get_header();
?>

<div class="page-hero">
    <div class="container">
        <div class="hero-tag">
            <span class="hero-tag-bar"></span>
            <span>de.rec.fotografie e.V. &bull; Tagungsb&uuml;ro</span>
        </div>
        <h1 class="page-title">Tagungsb&uuml;ro &amp; Anmeldung</h1>
        <p class="page-lead">
            Dein Ansprechpartner vor Ort und online &ndash; f&uuml;r Vorbereitung, Zimmerreservierungen, Anmeldung und den organisatorischen Ablauf der Treffen.
        </p>
    </div>
</div>

<div class="page-content-area">
    <div class="container">
        <!-- 1. Interaktives Anmelde-Plugin Formular -->
        <section class="anmeldung-section">
            <?php
            // Render the DRF Registration Plugin Form
            if (shortcode_exists('drf_anmeldung')) {
                echo do_shortcode('[drf_anmeldung]');
            } else {
                echo '<p>Anmeldeformular wird geladen...</p>';
            }
            ?>
        </section>

        <!-- 2. Tagungsbüro Infoboxen & Mitglieder -->
        <div class="page-card-container" style="margin-top: 40px;">
            <div class="drf-section-header">
                <span class="drf-section-badge">TAGUNGSBÜRO</span>
                <h2 class="drf-section-title">Organisation, Betreuung &amp; Verwaltung durch das Tagungsbüro</h2>
                <p class="drf-section-lead">Ehrenamtlich gewählt aus der Community: Das Tagungsbüro (TB) organisiert den reibungslosen Ablauf, verwaltet die Finanzen treuhänderisch und betreut die Zimmerkontingente im Haus Lutherrose.</p>
            </div>
            <p>
                Das TB ist weder kommerzieller Veranstalter noch gewinnorientierter Anbieter der Workshops, sondern sorgt f&uuml;r den reibungslosen Ablauf im Tagungshaus, verwaltet die Zimmerkontingente und koordiniert den Shuttleservice.
            </p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin: 24px 0;">
                <h3 style="margin-top: 0; color: #141518; font-size: 1.2rem; font-weight: 700;">
                    Mitglieder des Tagungsb&uuml;ros
                </h3>
                <p style="color: #64748b; font-size: 0.95rem;">Gewählt ab August 2025 zur Organisation von AUGENBLICKE XXVI (2026) und Folgejahren:</p>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; margin-top: 14px;">
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Bernd Franzen</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Burkhard Gudd</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Christian Kolinski</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Hans-J&ouml;rg Schlaberg</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Holger Quattelbaum</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Jochen Petry</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Leoni Schlaberg</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Luigi Rotta</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Oliver Jennrich</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Robert M. Klenk</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Robert Steiger</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Sebastian Z&uuml;ndorf</div>
                    <div style="background:#ffffff; padding:10px 14px; border-radius:8px; border:1px solid #cbd5e1; font-weight:600; color:#141518;">Wolfgang Paul</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 28px;">
                <div style="background: #141518; color: #ffffff; padding: 22px; border-radius: 12px; border-left: 4px solid #f5b301;">
                    <h4 style="color: #f5b301; margin-top: 0; font-weight: 700;">Kontakt zum Tagungsb&uuml;ro</h4>
                    <p style="color: #e2e8f0; font-size: 0.95rem; line-height: 1.5;">
                        Allgemeine Fragen zum Ablauf, zu Zimmerw&uuml;nschen oder Vorbereitungen bitte per E-Mail an:
                    </p>
                    <p style="font-size: 1.05rem; font-weight: 700;">
                        <a href="mailto:tagungsbuero2026@d-r-f.de" style="color: #f5b301; text-decoration: none;">tagungsbuero2026@d-r-f.de</a>
                    </p>
                    <small style="color: #94a3b8;">Fragen allgemeiner Natur k&ouml;nnen auch jederzeit im Flarum Forum oder in der Newsgroup gestellt werden.</small>
                </div>

                <div style="background: #ffffff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; border-left: 4px solid #141518;">
                    <h4 style="color: #141518; margin-top: 0; font-weight: 700;">Tagungsort: Haus Lutherrose</h4>
                    <p style="color: #475569; font-size: 0.95rem; line-height: 1.5;">
                        Haus Lutherrose, Wilhelm-L&ouml;he-Str. 20, 91564 Neuendettelsau<br>
                        Mit gro&szlig;en Seminarr&auml;umen, Fotostudios, Gr&uuml;nanlagen f&uuml;r Portr&auml;t- und Naturfotografie sowie Gastronomie vor Ort.
                    </p>
                    <p style="margin-bottom: 0; margin-top: 12px;">
                        <a href="https://haus-lutherrose.de/" target="_blank" rel="noopener" style="color: #141518; font-weight: 700; text-decoration: underline;">&rarr; Zur Website von Haus Lutherrose</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
