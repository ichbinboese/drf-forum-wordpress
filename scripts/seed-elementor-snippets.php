<?php
/**
 * Seed DRF Reusable Elementor Snippets & Build Elementor Pages
 */

require __DIR__ . '/../wordpress/wp-load.php';

if (!class_exists('\Elementor\Plugin')) {
    die("Elementor is not active!\n");
}

echo "========================================================\n";
echo "  d.r.f Augenblicke - Elementor Snippets & Pages Setup  \n";
echo "========================================================\n";

// Helper: Create an Elementor Section Data Structure (Boxed / Contained, kein Full-Width Bleed)
function drf_make_elementor_section($widget_type, $settings = []) {
    return [
        'id'       => substr(md5(uniqid($widget_type, true)), 0, 7),
        'elType'   => 'section',
        'isInner'  => false,
        'settings' => [
            'layout' => 'boxed',
        ],
        'elements' => [
            [
                'id'       => substr(md5(uniqid('col', true)), 0, 7),
                'elType'   => 'column',
                'isInner'  => false,
                'settings' => [
                    '_column_size' => 100,
                ],
                'elements' => [
                    [
                        'id'         => substr(md5(uniqid('wid', true)), 0, 7),
                        'elType'     => 'widget',
                        'isInner'    => false,
                        'widgetType' => $widget_type,
                        'settings'   => $settings,
                    ],
                ],
            ],
        ],
    ];
}

// 1. CREATE REUSABLE ELEMENTOR SNIPPETS (elementor_library)
echo "1. Creating Reusable Elementor Snippets (Library Templates)...\n";

$snippets_def = [
    [
        'title'       => 'Snippet: Hero Section & Polaroid Collage',
        'slug'        => 'snippet-hero',
        'widget_type' => 'drf_hero',
        'settings'    => [
            'hero_tag'         => 'DAS JÄHRLICHE TREFFEN DER d.r.f.',
            'hero_title'       => 'Willkommen bei Augenblicke.',
            'hero_desc'        => 'Wir sind ein eingetragener, gemeinnütziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein – mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.',
            'btn_primary_text' => 'Mehr erfahren',
            'btn_forum_text'   => 'Zum Forum',
        ],
    ],
    [
        'title'       => 'Snippet: Live Event Countdown 2026/2027',
        'slug'        => 'snippet-countdown',
        'widget_type' => 'drf_countdown',
        'settings'    => [
            'badge'          => 'AUGENBLICKE XXVI (2026)',
            'target_date'    => '2026-05-28T09:00:00',
            'location_title' => 'Haus Lutherrose, Neuendettelsau',
            'dates_text'     => '28. Mai bis 31. Mai 2026 (Fronleichnam)',
            'btn_text'       => 'Jetzt Platz sichern',
            'btn_url'        => '/tagungsbuero/',
        ],
    ],
    [
        'title'       => 'Snippet: 4 Feature Cards Grid',
        'slug'        => 'snippet-cards',
        'widget_type' => 'drf_cards',
        'settings'    => [],
    ],
    [
        'title'       => 'Snippet: Event Banner 2026 & 2027 (Neuendettelsau)',
        'slug'        => 'snippet-event-banner',
        'widget_type' => 'drf_event_banner',
        'settings'    => [
            'badge'          => 'AUGENBLICKE XXVI & XXVII',
            'title'          => '28. – 31. Mai 2026 • Haus Lutherrose, Neuendettelsau',
            'subtitle'       => 'Fronleichnam-Wochenende • Workshops, Studio, Dunkelkammer & Grillabend',
            'show_secondary' => 'yes',
            'secondary_text' => '📅 Vorausschau: Auch AUGENBLICKE XXVII (06.–09. Mai 2027) ist bereits für Zimmerkontingente geöffnet!',
            'button_text'    => 'Jetzt anmelden',
            'button_url'     => '/tagungsbuero/',
        ],
    ],
    [
        'title'       => 'Snippet: Interaktive Treffen-Timeline (1999–2027)',
        'slug'        => 'snippet-timeline',
        'widget_type' => 'drf_timeline',
        'settings'    => [
            'title'    => 'Meilensteine & Treffen-Geschichte (1999 – 2027)',
            'subtitle' => 'Über zwei Jahrzehnte lebendige Fotografie-Gemeinschaft: Von der ersten Verabredung im Usenet bis zu den modernen AUGENBLICKE-Großtreffen.',
        ],
    ],
    [
        'title'       => 'Snippet: Polaroid Photo Gallery Wall',
        'slug'        => 'snippet-gallery',
        'widget_type' => 'drf_gallery',
        'settings'    => [
            'title'    => 'Impressionen der Augenblicke-Treffen',
            'subtitle' => 'Echte Momentaufnahmen: Von analoger Laborchemie über Studio-Setups bis zu Porträts in der Abendsonne.',
        ],
    ],
    [
        'title'       => 'Snippet: Shuttle Transfer-Route (Wicklesgreuth & Ansbach)',
        'slug'        => 'snippet-shuttle-route',
        'widget_type' => 'drf_shuttle_route',
        'settings'    => [
            'title'    => 'Kostenlose Abholung ab Wicklesgreuth & Ansbach',
            'subtitle' => 'Bequem mit dem Zug anreisen: Unser ehrenamtliches Shuttle holt dich pünktlich am Bahnsteig ab.',
        ],
    ],
    [
        'title'       => 'Snippet: Interaktive Treffen-Anmeldung (Multi-Step)',
        'slug'        => 'snippet-anmeldung',
        'widget_type' => 'drf_anmeldung',
        'settings'    => [
            'meeting_slug' => '',
            'custom_intro' => 'Melde dich hier verbindlich für die kommenden Treffen in Neuendettelsau an. Alle Zimmerkontingente, Verpflegungsoptionen und der Shuttleservice werden zentral über dieses Formular koordiniert.',
        ],
    ],
    [
        'title'       => 'Snippet: Treffen-Chronik (1999–2027)',
        'slug'        => 'snippet-chronik',
        'widget_type' => 'drf_chronik',
        'settings'    => [
            'title' => 'Chronik aller Treffen (1999 bis 2027)',
            'desc'  => 'Von den ersten Usenet-Treffen auf Burg Rieneck bis zu den modernen AUGENBLICKE-Großtreffen im Haus Lutherrose Neuendettelsau.',
        ],
    ],
    [
        'title'       => 'Snippet: Tagungsbüro Team & Haus Lutherrose',
        'slug'        => 'snippet-tagungsbuero',
        'widget_type' => 'drf_tagungsbuero',
        'settings'    => [
            'title' => 'Über das Tagungsbüro & Tagungsort',
            'email' => 'tagungsbuero2026@d-r-f.de',
        ],
    ],
    [
        'title'       => 'Snippet: Shuttleservice Fahrplan & Reservierung',
        'slug'        => 'snippet-shuttle',
        'widget_type' => 'drf_shuttle',
        'settings'    => [
            'title'     => 'Shuttleservice – Bequem vom Bahnhof zum Tagungshaus',
            'show_form' => 'yes',
        ],
    ],
    [
        'title'       => 'Snippet: Sozialgremium & Solidarfonds',
        'slug'        => 'snippet-sozial',
        'widget_type' => 'drf_sozial',
        'settings'    => [
            'title' => 'Das Sozialgremium: Solidarität & Vertraulichkeit',
            'email' => 'sozialgremium@d-r-f.de',
        ],
    ],
    [
        'title'       => 'Snippet: Workshops & Sessions Grid',
        'slug'        => 'snippet-workshops',
        'widget_type' => 'drf_workshops',
        'settings'    => [
            'title' => 'Workshops & Praxis-Sessions',
        ],
    ],
    [
        'title'       => 'Snippet: Verein & Gemeinschaft',
        'slug'        => 'snippet-verein',
        'widget_type' => 'drf_verein',
        'settings'    => [
            'title' => 'de.rec.fotografie e.V. – Der Verein',
        ],
    ],
    [
        'title'       => 'Snippet: Community Forum & Usenet Tags',
        'slug'        => 'snippet-flarum',
        'widget_type' => 'drf_flarum_feed',
        'settings'    => [
            'title'     => 'de.rec.fotografie Community Forum',
            'desc'      => 'Das moderne Diskussionsforum für Bildkritik [BK], Workshops [WS], Fototechnik und die Augenblicke-Tagungen – nahtlos mit WordPress verbunden.',
            'forum_url' => '/forum/',
        ],
    ],
];

$saved_snippets = [];

foreach ($snippets_def as $def) {
    // Check if already exists
    $existing = get_page_by_path($def['slug'], OBJECT, 'elementor_library');
    if ($existing) {
        $post_id = $existing->ID;
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => $def['title'],
        ]);
    } else {
        $post_id = wp_insert_post([
            'post_title'  => $def['title'],
            'post_name'   => $def['slug'],
            'post_type'   => 'elementor_library',
            'post_status' => 'publish',
        ]);
    }

    $section = drf_make_elementor_section($def['widget_type'], $def['settings']);
    $data = [$section];

    update_post_meta($post_id, '_elementor_edit_mode', 'builder');
    update_post_meta($post_id, '_elementor_template_type', 'section');
    update_post_meta($post_id, '_elementor_data', wp_slash(json_encode($data)));

    $saved_snippets[$def['slug']] = $post_id;
    echo "  -> Erstellt: '{$def['title']}' (ID: {$post_id}) | Shortcode: [elementor-template id=\"{$post_id}\"] oder [drf_snippet slug=\"{$def['slug']}\"]\n";
}

echo "\n2. Building Elementor Homepage ('Startseite')...\n";

$home_page = get_page_by_path('startseite');
if (!$home_page) {
    $home_id = wp_insert_post([
        'post_title'   => 'Startseite',
        'post_name'    => 'startseite',
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'page_template'=> 'template-elementor-fullwidth.php',
    ]);
} else {
    $home_id = $home_page->ID;
    update_post_meta($home_id, '_wp_page_template', 'template-elementor-fullwidth.php');
}

// Assemble Homepage with rich Elementor sections:
// 1. Hero with Polaroid Visuals
// 2. Live Event Countdown 2026/2027
// 3. Event Banner 2026/2027
// 4. Feature Cards
// 5. Gallery Impressions
// 6. Community Forum Teaser
$home_sections = [
    drf_make_elementor_section('drf_hero', [
        'hero_tag'         => 'DAS JÄHRLICHE TREFFEN DER d.r.f.',
        'hero_title'       => 'Willkommen bei Augenblicke.',
        'hero_desc'        => 'Wir sind ein eingetragener, gemeinnütziger Verein und laden einmal im Jahr zu unserem besonderen Treffen ein – mit Austausch, Gemeinschaft und spannenden Workshops rund um Fotografie.',
        'btn_primary_text' => 'Mehr erfahren',
        'btn_forum_text'   => 'Zum Forum',
    ]),
    drf_make_elementor_section('drf_countdown', [
        'badge'          => 'AUGENBLICKE XXVI (2026)',
        'target_date'    => '2026-05-28T09:00:00',
        'location_title' => 'Haus Lutherrose, Neuendettelsau',
        'dates_text'     => '28. Mai bis 31. Mai 2026 (Fronleichnam)',
        'btn_text'       => 'Jetzt anmelden & Platz sichern',
        'btn_url'        => '/tagungsbuero/',
    ]),
    drf_make_elementor_section('drf_event_banner', [
        'badge'          => 'AUGENBLICKE XXVI & XXVII',
        'title'          => '28. – 31. Mai 2026 • Haus Lutherrose, Neuendettelsau',
        'subtitle'       => 'Fronleichnam-Wochenende • Workshops, Studio, Dunkelkammer & Grillabend',
        'show_secondary' => 'yes',
        'secondary_text' => '📅 Vorausschau: Auch AUGENBLICKE XXVII (06.–09. Mai 2027) ist bereits für Zimmerkontingente geöffnet!',
        'button_text'    => 'Zur Anmeldung 2026',
        'button_url'     => '/tagungsbuero/',
    ]),
    drf_make_elementor_section('drf_cards', []),
    drf_make_elementor_section('drf_gallery', [
        'title'    => 'Impressionen der Augenblicke-Treffen',
        'subtitle' => 'Echte Momentaufnahmen aus über zwei Jahrzehnten: Studio-Sessions, Schwarzweiß-Dunkelkammer und gesellige Abende.',
    ]),
    drf_make_elementor_section('drf_flarum_feed', [
        'title'     => 'de.rec.fotografie Community Forum',
        'desc'      => 'Tausche dich mit Teilnehmern und Fotografen über Bildkritik [BK], Workshops [WS] und die kommenden Treffen aus.',
        'forum_url' => '/forum/',
    ]),
];

update_post_meta($home_id, '_elementor_edit_mode', 'builder');
update_post_meta($home_id, '_elementor_template_type', 'wp-page');
update_post_meta($home_id, '_elementor_data', wp_slash(json_encode($home_sections)));

// Set static front page in WordPress
update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);

echo "  -> Startseite konfiguriert (ID: {$home_id}) und als statische Startseite aktiviert.\n";

echo "\n3. Upgrading Sub-Pages with Elementor...\n";

// Tagungsbüro Page with Elementor
$tb_page = get_page_by_path('tagungsbuero');
if ($tb_page) {
    $tb_sections = [
        drf_make_elementor_section('drf_countdown', [
            'badge'          => 'TAGUNGSBÜRO 2026 & 2027',
            'target_date'    => '2026-05-28T09:00:00',
            'location_title' => 'Haus Lutherrose, Neuendettelsau',
            'dates_text'     => '28. Mai bis 31. Mai 2026 (Fronleichnam)',
            'btn_text'       => 'Direkt zum Anmeldeformular',
            'btn_url'        => '#drfAnmeldungWrapper',
        ]),
        drf_make_elementor_section('drf_event_banner', [
            'badge'          => 'ANMELDUNG 2026 & 2027',
            'title'          => 'Tagungsbüro • Haus Lutherrose, Neuendettelsau',
            'subtitle'       => 'Verbindliche Zimmerbuchungen, Mahlzeiten & Shuttle-Koordination',
            'show_secondary' => 'no',
            'button_text'    => 'Nach unten zum Formular',
            'button_url'     => '#drfAnmeldungWrapper',
        ]),
        drf_make_elementor_section('drf_anmeldung', [
            'meeting_slug' => '',
            'custom_intro' => 'Wähle im Formular das gewünschte Treffen (AUGENBLICKE XXVI für 2026 oder AUGENBLICKE XXVII für 2027). Dein Zimmerkontingent und Mahlzeiten werden direkt für das Tagungsbüro reserviert.',
        ]),
        drf_make_elementor_section('drf_tagungsbuero', [
            'title' => 'Über das Tagungsbüro & Tagungsort',
            'email' => 'tagungsbuero2026@d-r-f.de',
        ]),
    ];
    update_post_meta($tb_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($tb_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($tb_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($tb_page->ID, '_elementor_data', wp_slash(json_encode($tb_sections)));
    echo "  -> Tagungsbüro auf Elementor aktualisiert (ID: {$tb_page->ID})\n";
}

// Augenblicke Chronik Page with Elementor
$ab_page = get_page_by_path('augenblicke');
if ($ab_page) {
    $ab_sections = [
        drf_make_elementor_section('drf_event_banner', [
            'badge'          => 'AUGENBLICKE 2026 & 2027',
            'title'          => 'Das Jahrestreffen von de.rec.fotografie',
            'subtitle'       => 'Vier Tage intensive Workshops, Dunkelkammer, Studio und legendäre Abende.',
            'show_secondary' => 'yes',
            'secondary_text' => 'Neuendettelsau ist bereit für 2026 und 2027!',
            'button_text'    => 'Jetzt anmelden',
            'button_url'     => '/tagungsbuero/',
        ]),
        drf_make_elementor_section('drf_timeline', [
            'title'    => 'Meilensteine & Treffen-Geschichte (1999 – 2027)',
            'subtitle' => 'Über zwei Jahrzehnte lebendige Fotografie-Gemeinschaft: Von der ersten Verabredung im Usenet bis zu den modernen AUGENBLICKE-Großtreffen.',
        ]),
        drf_make_elementor_section('drf_gallery', [
            'title'    => 'Foto-Chronik & Impressionen',
            'subtitle' => 'Ein Rückblick auf vergangene Treffen und die Vorfreude auf Neuendettelsau.',
        ]),
        drf_make_elementor_section('drf_chronik', [
            'title' => 'Vollständige Chronik aller Treffen (1999–2027)',
            'desc'  => 'Von den ersten Treffen auf Burg Rieneck bis zum Haus Lutherrose.',
        ]),
        drf_make_elementor_section('drf_cards', []),
    ];
    update_post_meta($ab_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($ab_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($ab_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($ab_page->ID, '_elementor_data', wp_slash(json_encode($ab_sections)));
    echo "  -> Augenblicke auf Elementor aktualisiert (ID: {$ab_page->ID})\n";
}

// Shuttleservice Page with Elementor
$shuttle_page = get_page_by_path('shuttleservice');
if ($shuttle_page) {
    $shuttle_sections = [
        drf_make_elementor_section('drf_shuttle_route', [
            'title'    => 'Kostenlose Abholung ab Wicklesgreuth & Ansbach',
            'subtitle' => 'Reise entspannt mit dem Zug an – unser Shuttle bringt dich direkt zum Tagungshaus.',
        ]),
        drf_make_elementor_section('drf_shuttle', [
            'title'     => 'Shuttleservice & Anreise für Bahnreisende',
            'show_form' => 'yes',
        ]),
    ];
    update_post_meta($shuttle_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($shuttle_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($shuttle_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($shuttle_page->ID, '_elementor_data', wp_slash(json_encode($shuttle_sections)));
    echo "  -> Shuttleservice auf Elementor aktualisiert (ID: {$shuttle_page->ID})\n";
}

// Sozialgremium Page with Elementor
$sozial_page = get_page_by_path('sozialgremium');
if ($sozial_page) {
    $sozial_sections = [
        drf_make_elementor_section('drf_sozial', [
            'title' => 'Das Sozialgremium & Solidarfonds ("Sozialsowjet")',
            'email' => 'sozialgremium@d-r-f.de',
        ]),
    ];
    update_post_meta($sozial_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($sozial_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($sozial_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($sozial_page->ID, '_elementor_data', wp_slash(json_encode($sozial_sections)));
    echo "  -> Sozialgremium auf Elementor aktualisiert (ID: {$sozial_page->ID})\n";
}

// Workshops Page with Elementor
$ws_page = get_page_by_path('workshops');
if ($ws_page) {
    $ws_sections = [
        drf_make_elementor_section('drf_event_banner', [
            'badge'          => 'AUGENBLICKE 2026 WORKSHOPS',
            'title'          => 'Workshop-Programm in Neuendettelsau',
            'subtitle'       => 'Praxis pur: Großes Studio, Schwarzweiß-Dunkelkammer, Altglas und Makro',
            'show_secondary' => 'no',
            'button_text'    => 'Zur Anmeldung',
            'button_url'     => '/tagungsbuero/',
        ]),
        drf_make_elementor_section('drf_workshops', [
            'title' => 'Geplante Workshops & Sessions',
        ]),
        drf_make_elementor_section('drf_gallery', [
            'title'    => 'Impressionen aus vergangenen Praxis-Workshops',
            'subtitle' => 'Blitzen, Available Light, Makro und analoge Entwicklungsverfahren in Aktion.',
        ]),
    ];
    update_post_meta($ws_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($ws_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($ws_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($ws_page->ID, '_elementor_data', wp_slash(json_encode($ws_sections)));
    echo "  -> Workshops auf Elementor aktualisiert (ID: {$ws_page->ID})\n";
}

// Verein Page with Elementor
$verein_page = get_page_by_path('verein');
if ($verein_page) {
    $verein_sections = [
        drf_make_elementor_section('drf_verein', [
            'title' => 'de.rec.fotografie e.V. – Der Verein',
        ]),
        drf_make_elementor_section('drf_timeline', [
            'title'    => 'Stationen unserer Vereins- und Tagungsgeschichte',
            'subtitle' => 'Wie aus einer Usenet-Newsgroup ein aktiver Verein und eine bundesweite Gemeinschaft wurde.',
        ]),
        drf_make_elementor_section('drf_cards', []),
    ];
    update_post_meta($verein_page->ID, '_wp_page_template', 'template-elementor-fullwidth.php');
    update_post_meta($verein_page->ID, '_elementor_edit_mode', 'builder');
    update_post_meta($verein_page->ID, '_elementor_template_type', 'wp-page');
    update_post_meta($verein_page->ID, '_elementor_data', wp_slash(json_encode($verein_sections)));
    echo "  -> Verein auf Elementor aktualisiert (ID: {$verein_page->ID})\n";
}

// Clear Elementor CSS cache
\Elementor\Plugin::$instance->files_manager->clear_cache();

echo "\n========================================================\n";
echo "  ERFOLG! Elementor Snippets & Seiten aufgebaut! 🚀     \n";
echo "========================================================\n";
