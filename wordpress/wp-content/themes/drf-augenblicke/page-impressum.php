<?php
/**
 * Template Name: Impressum
 * Description: Impressum für de.rec.fotografie e.V.
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
            <div class="drf-section-header">
                <span class="drf-section-badge">RECHTLICHE ANGABEN</span>
                <h2 class="drf-section-title">Gesetzliche Anbieterkennzeichnung &amp; Vorstandskontakt</h2>
                <p class="drf-section-lead">Pflichtangaben nach &sect; 5 TMG sowie vertretungsberechtigte Personen des eingetragenen Vereins de.rec.fotografie e.V.</p>
            </div>

            <h2>Herausgeber &amp; Betreiber der Website</h2>
            <p>
                <strong>de.rec.fotografie e.V.</strong><br>
                Vereinsregister: Amtsgericht N&uuml;rnberg &bull; VR 20491<br>
                Gemeinn&uuml;tziger Verein zur F&ouml;rderung von Kunst und Fotografie
            </p>

            <h2>Vertreten durch den Vorstand</h2>
            <p>
                1. Vorsitzender: Frank Schliffke<br>
                2. Vorsitzender: Dr. Michael Breuer<br>
                Schatzmeister: Thomas Weber
            </p>

            <h2>Kontakt</h2>
            <p>
                E-Mail: <a href="mailto:vorstand@d-r-f.de">vorstand@d-r-f.de</a><br>
                Tagungsb&uuml;ro: <a href="mailto:tagungsbuero2026@d-r-f.de">tagungsbuero2026@d-r-f.de</a><br>
                Website: <a href="https://www.d-r-f.de/augenblicke/">www.d-r-f.de/augenblicke/</a>
            </p>

            <h2>Verantwortlich f&uuml;r den Inhalt nach &sect; 55 Abs. 2 RStV</h2>
            <p>
                Frank Schliffke<br>
                c/o de.rec.fotografie e.V.
            </p>

            <h2>Haftungshinweis</h2>
            <p>
                Trotz sorgf&auml;ltiger inhaltlicher Kontrolle &uuml;bernehmen wir keine Haftung f&uuml;r die Inhalte externer Links. F&uuml;r den Inhalt der verlinkten Seiten sind ausschlie&szlig;lich deren Betreiber verantwortlich. F&uuml;r Beitr&auml;ge im Flarum Forum und in der Usenet-Bridge sind die jeweiligen Verfasser verantwortlich.
            </p>
        </div>
    </div>
</div>

<?php
get_footer();
