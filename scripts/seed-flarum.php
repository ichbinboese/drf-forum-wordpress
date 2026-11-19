<?php
/**
 * Seed DRF Flarum with authentic tags, settings, custom styling, and initial discussions
 */

require __DIR__ . '/../flarum/app/vendor/autoload.php';

$site = require __DIR__ . '/../flarum/app/site.php';
$app = $site->bootApp();
$container = $app->getContainer();

$settings = $container->make(\Flarum\Settings\SettingsRepositoryInterface::class);
$formatter = $container->make(\Flarum\Formatter\Formatter::class);
$db = $container->make(\Illuminate\Database\ConnectionInterface::class);

echo "1. Configuring DRF Flarum Settings & Theme...\n";

$custom_less = <<<'LESS'
// DRF Augenblicke Brand Custom Styling for Flarum
@primary-color: #F5B301;
@drf-charcoal: #141518;
@drf-dark-card: #1e2025;

// Header styling
.App-header {
    background-color: @drf-charcoal !important;
    border-bottom: 2px solid @primary-color;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    .Header-title {
        color: #ffffff !important;
        font-weight: 700;
        letter-spacing: -0.02em;

        a {
            color: #ffffff !important;
            &:hover {
                color: @primary-color !important;
            }
        }
    }

    .Button--link, .Button--flat {
        color: #e2e8f0 !important;
        &:hover {
            color: @primary-color !important;
        }
    }
}

// DRF Top Bar (Connecting Flarum to WordPress)
.drf-flarum-topbar {
    background: #0d0e10;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    font-size: 13px;
    padding: 7px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;

    .drf-topbar-left {
        display: flex;
        align-items: center;
        gap: 16px;

        .drf-topbar-brand {
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;

            .badge {
                background: @primary-color;
                color: #141518;
                font-size: 10px;
                font-weight: 800;
                padding: 1px 6px;
                border-radius: 3px;
                text-transform: uppercase;
            }
        }
    }

    .drf-topbar-nav {
        display: flex;
        align-items: center;
        gap: 14px;
        list-style: none;
        margin: 0;
        padding: 0;

        a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
            font-weight: 500;

            &:hover {
                color: #ffffff;
            }

            &.active {
                color: @primary-color;
                font-weight: 700;
            }
        }
    }
}

@media (max-width: 768px) {
    .drf-flarum-topbar .drf-topbar-nav {
        display: none;
    }
}

// Buttons
.Button--primary {
    background-color: @primary-color !important;
    color: #141518 !important;
    font-weight: 700 !important;
    border: none !important;
    border-radius: 6px !important;
    transition: all 0.15s ease-in-out !important;

    &:hover, &:focus, &:active {
        background-color: darken(@primary-color, 8%) !important;
        color: #000000 !important;
        box-shadow: 0 4px 12px rgba(245, 179, 1, 0.35) !important;
    }
}

// Hero banner
.Hero {
    background: linear-gradient(135deg, #141518 0%, #1e2025 100%) !important;
    border-bottom: 3px solid @primary-color !important;
    color: #ffffff !important;

    h1, h2 {
        color: #ffffff !important;
    }

    .Hero-subtitle {
        color: #cbd5e1 !important;
    }
}

// Tags & Badges
.TagLabel {
    font-weight: 600;
    border-radius: 4px;
}
LESS;

$settings->set('theme_primary_color', '#F5B301');
$settings->set('theme_secondary_color', '#141518');
$settings->set('theme_colored_header', '1');
$settings->set('forum_title', 'de.rec.fotografie Community Forum');
$settings->set('forum_description', 'Das Forum von de.rec.fotografie und dem Jahrestreffen »Augenblicke«');
$settings->set('welcome_title', 'Willkommen im de.rec.fotografie Forum!');
$settings->set('welcome_message', 'Der Treffpunkt für Bildkritik [BK], Workshops [WS], Fototechnik und die jährlichen Tagungen »Augenblicke« im Haus Lutherrose Neuendettelsau (2026 & 2027).');
$settings->set('custom_less', $custom_less);

echo "Settings updated successfully.\n";

echo "2. Seeding DRF Tags...\n";

// Remove existing test tags
$db->table('tags')->delete();
$db->table('discussion_tag')->delete();
$db->table('discussions')->delete();
$db->table('posts')->delete();

$tags = [
    [
        'id' => 1,
        'name' => 'Augenblicke Treffen [TR]',
        'slug' => 'augenblicke-treffen',
        'color' => '#F5B301',
        'icon' => 'fas fa-camera-retro',
        'description' => 'Alles zu den Jahrestreffen AUGENBLICKE 2026 & 2027 in Neuendettelsau (Haus Lutherrose), Fahrgemeinschaften und Rückblicke.',
        'position' => 1,
        'is_primary' => 1,
    ],
    [
        'id' => 2,
        'name' => 'Tagungsbüro [TB]',
        'slug' => 'tagungsbuero',
        'color' => '#E67E22',
        'icon' => 'fas fa-clipboard-list',
        'description' => 'Offizielle Mitteilungen des Tagungsbüros, Buchungsablauf, Zimmerkontingente und Shuttleservice.',
        'position' => 2,
        'is_primary' => 1,
    ],
    [
        'id' => 3,
        'name' => 'Workshops & Sessions [WS]',
        'slug' => 'workshops',
        'color' => '#3498DB',
        'icon' => 'fas fa-chalkboard-teacher',
        'description' => 'Themenvorschläge, Studio-Setups, analoge Dunkelkammer, Altglas und Vorträge.',
        'position' => 3,
        'is_primary' => 1,
    ],
    [
        'id' => 4,
        'name' => 'Bildkritik & Portfolio [BK]',
        'slug' => 'bildkritik',
        'color' => '#9B59B6',
        'icon' => 'fas fa-images',
        'description' => 'Konstruktive Bildkritik nach bewährter de.rec.fotografie Usenet-Tradition: Bildaufbau, Licht und Technik.',
        'position' => 4,
        'is_primary' => 1,
    ],
    [
        'id' => 5,
        'name' => 'Schnellfotowettbewerb [SFW]',
        'slug' => 'sfw',
        'color' => '#E74C3C',
        'icon' => 'fas fa-stopwatch',
        'description' => 'Der legendäre Samstags-Wettbewerb bei Augenblicke: 3 Stunden Zeit, ein Thema, das beste Bild gewinnt!',
        'position' => 5,
        'is_primary' => 1,
    ],
    [
        'id' => 6,
        'name' => 'Nur mal zeigen [NMZ]',
        'slug' => 'nur-mal-zeigen',
        'color' => '#1ABC9C',
        'icon' => 'fas fa-eye',
        'description' => 'Bilder einfach zum Zeigen und Genießen – ohne formale Kritikpflicht.',
        'position' => 6,
        'is_primary' => 1,
    ],
    [
        'id' => 7,
        'name' => 'Fototechnik & Kameras',
        'slug' => 'fototechnik',
        'color' => '#34495E',
        'icon' => 'fas fa-camera',
        'description' => 'Digitale & analoge Kamerasysteme, Objektive, Sensoren und Zubehör.',
        'position' => 7,
        'is_primary' => 1,
    ],
    [
        'id' => 8,
        'name' => 'Altglas & Manuelle Linsen',
        'slug' => 'altglas',
        'color' => '#D35400',
        'icon' => 'fas fa-circle-notch',
        'description' => 'Klassische Optiken (M42, Leica M, Contax) an modernen Kameras adaptieren – Charakter, Flares und Bokeh.',
        'position' => 8,
        'is_primary' => 1,
    ],
    [
        'id' => 9,
        'name' => 'Analoges Labor & SW',
        'slug' => 'analog-labor',
        'color' => '#2C3E50',
        'icon' => 'fas fa-flask',
        'description' => 'Filmentwicklung, Baryt-Prints, Chemie und analoge Dunkelkammerpraxis.',
        'position' => 9,
        'is_primary' => 1,
    ],
    [
        'id' => 10,
        'name' => 'EBV & Bildbearbeitung',
        'slug' => 'ebv',
        'color' => '#2980B9',
        'icon' => 'fas fa-sliders-h',
        'description' => 'Raw-Konverter (Lightroom, Capture One, Darktable), Bildbearbeitung und Farbmanagement.',
        'position' => 10,
        'is_primary' => 1,
    ],
    [
        'id' => 11,
        'name' => 'Recht & Organisation',
        'slug' => 'fotorecht',
        'color' => '#7F8C8D',
        'icon' => 'fas fa-balance-scale',
        'description' => 'Urheberrecht, DSGVO, Panoramafreiheit, Verträge und Vereinsangelegenheiten.',
        'position' => 11,
        'is_primary' => 1,
    ]
];

foreach ($tags as $tagData) {
    $db->table('tags')->insert(array_merge($tagData, [
        'is_restricted' => 0,
        'is_hidden' => 0,
        'discussion_count' => 0,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]));
}
echo "Seeded " . count($tags) . " tags.\n";

echo "3. Seeding Authentic DRF Discussions...\n";

$admin = \Flarum\User\User::find(1);

$discussionsData = [
    [
        'title' => '[TR][LR] AUGENBLICKE XXVI (2026) und XXVII (2027) im Haus Lutherrose, Neuendettelsau',
        'tag_slug' => 'augenblicke-treffen',
        'is_sticky' => 1,
        'posts' => [
            "Liebe d.r.f-Gemeinde,\n\nwir freuen uns riesig, die Planungen für unsere kommenden Jahrestreffen bekanntzugeben!\n\n**AUGENBLICKE XXVI (2026)** findet vom **28. Mai bis 31. Mai 2026** (Fronleichnam-Wochenende) im vertrauten und hervorragend ausgestatteten **Haus Lutherrose in Neuendettelsau** statt.\n\nUnd das Beste: Auch der Termin für **AUGENBLICKE XXVII (2027)** steht bereits fest: **06. Mai bis 09. Mai 2027** (Christi Himmelfahrt) am selben Veranstaltungsort!\n\n### Highlights vor Ort:\n- Mehrere Tagungsräume für parallele Workshops & Fotostudio\n- Eigene Dunkelkammer / SW-Laborstation\n- Wunderschöne Parks und die historische Franken-Region für Fotowalks\n- Der legendäre Grillabend am Samstag\n\nDie Anmeldung ist ab sofort online über unsere Webseite freigeschaltet: [Zur Anmeldung im Tagungsbüro](http://localhost:8080/tagungsbuero/).\n\nWir freuen uns auf alte Bekannte und neue Gesichter!",
            "Klasse, der Termin für 2026 und 2027 ist im Kalender dick rot angestrichen! Haus Lutherrose war schon 2019 und 2024 eine fantastische Location mit viel Platz für Studioblitze und Lichtformer. Ich bringe auf jeden Fall wieder meine Großformatkamera mit!"
        ]
    ],
    [
        'title' => '[TB] Anmeldeverfahren, Zimmerkontingente & Shuttleservice für Neuendettelsau',
        'tag_slug' => 'tagungsbuero',
        'is_sticky' => 1,
        'posts' => [
            "Hallo zusammen,\n\nhier eine kurze Übersicht zum organisatorischen Ablauf:\n\n1. **Zimmerkategorien:**\n   - Einzelzimmer mit DU/WC\n   - Doppelzimmer mit DU/WC\n   - Mehrbettzimmer (ideal für Studenten / Sparfüchse)\n   - Tagesgäste ohne Übernachtung (inkl. Mahlzeiten & Tagungsbeitrag)\n\n2. **Shuttleservice:**\n   Für alle, die mit der Bahn anreisen, bieten wir am Donnerstag wie gewohnt einen Shuttle-Dienst ab **Bahnhof Wicklesgreuth (S4)** und **Hauptbahnhof Ansbach (IC/ICE)** an. Bitte bei der Anmeldung oder über das Shuttle-Formular angeben!\n\n3. **Solidarbeitrag / Sozialsowjet:**\n   Niemand soll aus finanziellen Gründen zuhause bleiben müssen. Das Sozialgremium (Wolfgang Exler & Dietmar Belloff) steht bei Fragen diskret bereit.\n\nFragen zur Buchung könnt ihr direkt hier im Thread stellen!",
            "Super, danke an das gesamte Tagungsbüro für den Einsatz! Ich werde wieder mit der Bahn anreisen und melde mich zeitnah für den Shuttle ab Wicklesgreuth."
        ]
    ],
    [
        'title' => '[WS] Workshop-Ideensammlung 2026: Dunkelkammer, Studioblitz & Altglas',
        'tag_slug' => 'workshops',
        'is_sticky' => 0,
        'posts' => [
            "Moin in die Runde!\n\nWie immer lebt das Programm von euren Vorschlägen und eurem Fachwissen. Folgende Themen sind bereits in Vorbereitung:\n\n1. **Studioblitz & Lichtführung** – Striplights, Beauty Dish und Belichtungsmessung.\n2. **Dunkelkammer: SW-Barytabzüge** – Vergrößerung, Split-Grade und Archivwässerung.\n3. **Altglas-Charakteristik an modernen Spiegellosen** – M42, Contax, Leica M mit praktischem Bokeh-Vergleich.\n4. **Makro & Focus-Stacking** – Rechnen mit Helicon Focus und motorisiertem Schlitten.\n\nWelche Sessions möchtet ihr anbieten oder woran hättet ihr Interesse? Schreibt eure Ideen gerne hier rein!",
            "Ich würde gerne eine Session zu Farbmanagement und Softproof für Fine-Art-Printing anbieten – inklusive ICC-Profilierung und Papierauswahl (Hahnemühle Photo Rag vs. Baryta). Besteht daran Interesse?"
        ]
    ],
    [
        'title' => '[BK] Leitfaden für konstruktive Bildkritik nach d.r.f-Tradition',
        'tag_slug' => 'bildkritik',
        'is_sticky' => 0,
        'posts' => [
            "In unserer Usenet-Newsgroup `de.rec.fotografie` hatte die Bildkritik schon immer einen ganz besonderen Stellenwert.\n\n### Die Grundregeln für gute Bildkritik:\n- **Echtes Interesse:** Nicht nur 'gefällt mir' oder 'gefällt mir nicht', sondern *warum* wirkt das Bild so?\n- **Bildaufbau & Bildwirkung:** Führung des Auges, Kontraste, Schärfeebene und Beschnitt.\n- **Konstruktive Tipps:** Was hätte man vor Ort anders machen können (Perspektive, Brennweite, Zeitpunkt)?\n- **Respektvoller Umgang:** Wir kritisieren das Bild, niemals den Fotografen!\n\nStellt eure Bilder gerne jederzeit mit `[BK]` im Betreff hier ein!"
        ]
    ],
    [
        'title' => '[SFW] Der Schnellfotowettbewerb: 3 Stunden, 1 Thema, 1 Bild!',
        'tag_slug' => 'sfw',
        'is_sticky' => 0,
        'posts' => [
            "Auch bei AUGENBLICKE XXVI in Neuendettelsau wird der Samstag-Nachmittag wieder dem legendären **Schnellfotowettbewerb (SFW)** gehören!\n\n### Der Ablauf:\n1. Am Samstagnachmittag um 14:00 Uhr wird das geheime Thema im Tagungssaal verkündet.\n2. Alle Teilnehmer haben exakt 3 Stunden Zeit, im Umkreis von Neuendettelsau ein passendes Foto zu schießen.\n3. Jeder reicht genau **ein einziges unbeschnittenes Originalbild (Out of Camera / OOC)** ein.\n4. Am Abend erfolgt die gemeinsame Projektion und anonyme Punktevergabe durch alle Tagungsgäste!\n\nDer Wanderpokal wartet auf seinen nächsten Besitzer! 🏆"
        ]
    ]
];

$postCounter = 1;
$discCounter = 1;

foreach ($discussionsData as $dData) {
    $tag = $db->table('tags')->where('slug', $dData['tag_slug'])->first();

    $now = date('Y-m-d H:i:s', time() - (5 - $discCounter) * 3600);

    $discId = $db->table('discussions')->insertGetId([
        'title' => $dData['title'],
        'slug' => \Illuminate\Support\Str::slug($dData['title']),
        'comment_count' => count($dData['posts']),
        'participant_count' => 1,
        'user_id' => $admin->id,
        'first_post_id' => null,
        'last_post_id' => null,
        'last_post_number' => count($dData['posts']),
        'last_posted_at' => $now,
        'last_posted_user_id' => $admin->id,
        'created_at' => $now,
        'is_approved' => 1,
        'is_sticky' => $dData['is_sticky'],
        'is_locked' => 0,
    ]);

    // Attach Tag
    if ($tag) {
        $db->table('discussion_tag')->insert([
            'discussion_id' => $discId,
            'tag_id' => $tag->id,
            'created_at' => $now,
        ]);
        $db->table('tags')->where('id', $tag->id)->increment('discussion_count');
    }

    $firstPostId = null;
    $lastPostId = null;

    foreach ($dData['posts'] as $idx => $content) {
        $postNow = date('Y-m-d H:i:s', strtotime($now) + ($idx * 900));
        $xmlContent = $formatter->parse($content);

        $postId = $db->table('posts')->insertGetId([
            'discussion_id' => $discId,
            'number' => $idx + 1,
            'created_at' => $postNow,
            'user_id' => $admin->id,
            'type' => 'comment',
            'content' => $xmlContent,
            'is_approved' => 1,
            'is_private' => 0,
        ]);

        if ($idx === 0) {
            $firstPostId = $postId;
        }
        $lastPostId = $postId;
    }

    $db->table('discussions')->where('id', $discId)->update([
        'first_post_id' => $firstPostId,
        'last_post_id' => $lastPostId,
    ]);

    $discCounter++;
}

echo "Seeded " . count($discussionsData) . " discussions and posts.\n";

echo "4. Rebuilding Flarum Cache and Assets...\n";
passthru('/usr/local/bin/php -d memory_limit=512M /Users/benjaminbose/drf-forum-wordpress/flarum/app/flarum cache:clear');

echo "DONE! DRF Flarum fully customized and seeded.\n";
