<?php
/**
 * Template Name: Verein
 * Description: Vereinsseite für de.rec.fotografie e.V.
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
            <h2>Unsere Wurzeln &amp; Gemeinschaft</h2>
            <p>
                Der Verein <strong>de.rec.fotografie e.V.</strong> entstand aus der traditionsreichen Usenet-Newsgruppe <em>de.rec.fotografie</em> (d.r.f), die seit den sp&auml;ten 1990er Jahren eine der pr&auml;gendsten deutschsprachigen Plattformen f&uuml;r Fotografie-Begeisterte ist. Ob analoges Mittelformat, Grossformat-Kameras, legend&auml;re Objektive (»Altglas«) oder modernste Digitaltechnik: Bei uns z&auml;hlen Leidenschaft, handwerkliches K&ouml;nnen und gegenseitige Hilfsbereitschaft.
            </p>

            <h2>Gemeinn&uuml;tziger Zweck</h2>
            <p>
                Unser Verein ist als gemeinn&uuml;tzig anerkannt. Satzungsgem&auml;&szlig; f&ouml;rdern wir Kunst und Kultur im Bereich der Fotografie. Wir bieten eine unabh&auml;ngige, werbefreie Plattform f&uuml;r den fachlichen Diskurs, f&ouml;rdern den fotografischen Nachwuchs und erm&ouml;glichen durch unser j&auml;hrliches Treffen »AUGENBLICKE« den pers&ouml;nlichen Wissens- und Erfahrungsaustausch.
            </p>

            <h2>Vorstand &amp; Ansprechpartner</h2>
            <table class="drf-table">
                <thead>
                    <tr>
                        <th>Funktion</th>
                        <th>Name</th>
                        <th>Kontakt</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>1. Vorsitzender</strong></td>
                        <td>Frank Schliffke</td>
                        <td>vorstand@d-r-f.de</td>
                    </tr>
                    <tr>
                        <td><strong>2. Vorsitzender</strong></td>
                        <td>Dr. Michael Breuer</td>
                        <td>vorstand@d-r-f.de</td>
                    </tr>
                    <tr>
                        <td><strong>Schatzmeister</strong></td>
                        <td>Thomas Weber</td>
                        <td>kasse@d-r-f.de</td>
                    </tr>
                    <tr>
                        <td><strong>Tagungsb&uuml;ro 2026</strong></td>
                        <td>Orga-Team Augenblicke</td>
                        <td>tagungsbuero2026@d-r-f.de</td>
                    </tr>
                </tbody>
            </table>

            <h2>Mitglied werden</h2>
            <p>
                Du m&ouml;chtest unsere Arbeit unterst&uuml;tzen und Teil des Vereins werden? Der Jahresbeitrag betr&auml;gt 30 Euro und kommt vollst&auml;ndig der Gemeinschaft, den Servern, dem Forum und der Organisation der Treffen zugute.
            </p>
            <div style="margin-top: 24px;">
                <a href="<?php echo esc_url(drf_get_forum_url()); ?>" class="pill-btn pill-btn-home">
                    <?php echo drf_get_icon('users'); ?>
                    <span>Im Forum austauschen &amp; Mitgliedsantrag stellen</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
