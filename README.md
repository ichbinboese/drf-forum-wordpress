# d.r.f (de.rec.fotografie) & Jahrestreffen »AUGENBLICKE«
### Modernes WordPress 6.7 Theme & Flarum Community Forum

Offizielles Repository für den Webauftritt des gemeinnützigen Vereins **de.rec.fotografie e.V.** und des jährlichen Großtreffens **»AUGENBLICKE«** ([Originalseite](https://www.d-r-f.de/augenblicke/)).

Dieses Projekt implementiert das vollständige, pixelgenaue Design aus den Screendesign-Vorgaben (Desktop & Mobile) inklusive individuellem WordPress-Theme, Single Sign-On (SSO) Brücke zum Flarum Forum und einem sofort startbereiten lokalen Server-Stack.

---

## 📸 Design & Features im Überblick

### 1. WordPress Theme `drf-augenblicke`
- **Farbschema:** Tiefschwarz (`#141518`), das legendäre d.r.f.-Warmgelb / Bernstein (`#F5B301`), modernes Kartengrau (`#F3F4F6`) und weiße Inhaltsflächen.
- **Top Bar (Desktop & Mobil):**
  - Minimalistisches Kamera-Logo (SVG) mit Beschriftung `AUGENBLICKE | d.r.f de.rec.fotografie`.
  - Direkte Pill-Buttons: `🏠 Homepage` (Gelb) und `🔒 Forum – mit Zugang` (Dunkelgrau mit Rahmen).
  - Mobiler Burger-Button mit ausklappbarem Drawer-Menü inklusive Icons.
- **Sub-Navigationsleiste (Desktop):**
  - Horizontale Menüleiste auf weißem Grund mit vertikalen Trennern:  
    `Verein | Augenblicke | Tagungsbüro | Sozialgremium | Shuttleservice | Workshops | Impressum | Datenschutz`
- **Hero-Bereich:**
  - Großflächiges Landschafts-/Fotografen-Hintergrundbild im warmen Abendlicht mit optimal abgestimmtem Abdunklungsverlauf für maximale Lesbarkeit.
  - Gelbe Markierung `— DAS JÄHRLICHE TREFFEN DER d.r.f.`.
  - Typografische Headline `Willkommen bei Augenblicke.` mit Gelb-Akzent.
  - Einladender Intro-Text und zwei Call-to-Action-Buttons (`Mehr erfahren →` und `👥 Zum Forum`).
  - Polaroid-Collage rechts mit See-Steg, Kamera und originalgetreuem, leicht gedrehtem Badge-Sticker: *»Das jährliche Augenblicke-Treffen der d.r.f.«*.
- **4 Interaktive Feature-Cards:**
  1. **Verein:** Gemeinschaft, Wurzeln im Usenet seit 1999, Satzung, Vorstand & Gemeinnützigkeit.
  2. **Workshops:** Studioblitz, Dunkelkammer / SW-Barytabzüge, Altglas an spiegellosen Kameras, Makro & Stacking.
  3. **Shuttleservice:** Kostenloser Transfer von Bahnhöfen (Wicklesgreuth & Ansbach) zum Tagungshaus.
  4. **Tagungsbüro:** Online-Anmeldeformular, Zimmerbuchung im Haus Lutherrose, Ansprechpartner.
- **Responsive Umsetzung:**
  - Auf Desktops: 4 elegante Spalten.
  - Auf Tablets: 2 Spalten.
  - Auf Smartphones: Kompakte horizontale Karten mit Thumbnail links und Inhalten rechts, exakt wie im mobilen Mockup!

### 2. Elementor Page Builder & Reusable Snippets
- **Elementor 4.4.1 Native Integration:** Alle Seiten (Startseite, Tagungsbüro, Verein, Augenblicke Chronik, Workshops, Shuttleservice, Sozialgremium) sind als vollwertige Elementor-Seiten aufgebaut und können visuell im Elementor Editor bearbeitet werden.
- **Eigene Elementor Kategorie:** `d.r.f Augenblicke Snippets & Widgets` mit 12 modularen Widgets:
  1. `drf_hero`: Hero Section mit Polaroid-Collage, Badge-Sticker und CTAs.
  2. `drf_cards`: 4 Feature Cards Grid (Verein, Workshops, Shuttle, Tagungsbüro).
  3. `drf_event_banner`: Ankündigungsbanner für Neuendettelsau 2026 & 2027.
  4. `drf_anmeldung`: Interaktives Treffen-Anmeldeformular mit Treffen-Auswahl.
  5. `drf_chronik`: Treffen-Chronik (1999–2027) mit Usenet-Kürzeln.
  6. `drf_tagungsbuero`: Tagungsbüro Team & Haus Lutherrose Venue-Box.
  7. `drf_shuttle`: Shuttleservice Fahrplan & Reservierungsformular.
  8. `drf_sozial`: Sozialgremium & Solidarfonds ("Sozialsowjet").
  9. `drf_workshops`: Praxis-Workshops & Sessions Grid.
  10. `drf_verein`: Usenet-Wurzeln, Vorstand & Gemeinnützigkeit.
  11. `drf_flarum_feed`: Flarum Forum Teaser & Usenet Tags.
  12. `drf_template_embed`: 1-Klick-Einbettung beliebiger Elementor Snippets.
- **Wiederverwendbare Elementor Snippets (Library Templates):**
  Unter `WP-Admin -> Templates -> Saved Templates` sind alle Sektionen als wiederverwendbare Vorlagen gespeichert.
  Einbettbar überall mit:
  - `[elementor-template id="14"]`
  - `[drf_snippet slug="snippet-hero"]`
  - `[drf_snippet slug="snippet-anmeldung"]`

### 3. Flarum Forum Integration
- **Nahtloses Design:** Flarum übernimmt das d.r.f.-Farbschema (Gold/Dunkel) und den gemeinsamen Header mit direkter Verlinkung zurück zur WordPress-Startseite.
- **Themenbereiche (Tags):**
  - 🏷️ `Augenblicke 2026 (Neuendettelsau)`
  - 🏷️ `Workshops & Sessions`
  - 🏷️ `Analog & SW-Labor`
  - 🏷️ `Kameratechnik & Altglas`
  - 🏷️ `Bildkritik & Galerien`
  - 🏷️ `Tagungsbüro & Organisation`
  - 🏷️ `Usenet Newsgroup Bridge`
- **SSO & Bridge:** WordPress-Plugin `drf-flarum-bridge` für Benutzer-Synchronisation und Diskussions-Widgets.

---

## 🚀 Lokalen Server starten

Das Projekt ist für maximale Entwicklungsgeschwindigkeit und **100% kühlen, geräuschlosen Betrieb auf dem Mac** optimiert (ohne ressourcenhungrige Docker-VMs):

### Option A: Nativer Mac PHP Server (Empfohlen – 0% CPU & kein Lüfterlärm)

WordPress und Flarum laufen nativ über den integrierten PHP-Router und SQLite – blitzschnell, leise und sofort einsatzbereit:

```bash
./start.sh
```

Nach dem Start erreichbar unter:
- 🌐 **Hauptseite (WordPress):** [http://localhost:8080/](http://localhost:8080/)
- 💬 **Forum (Flarum):** [http://localhost:8080/forum/](http://localhost:8080/forum/)
- 🏢 **Tagungsbüro & Anmeldung (2026 & 2027):** [http://localhost:8080/tagungsbuero/](http://localhost:8080/tagungsbuero/)
- 👥 **Verein:** [http://localhost:8080/verein/](http://localhost:8080/verein/)
- 📷 **Augenblicke Chronik:** [http://localhost:8080/augenblicke/](http://localhost:8080/augenblicke/)
- 🛠️ **Workshops:** [http://localhost:8080/workshops/](http://localhost:8080/workshops/)
- 🚐 **Shuttleservice:** [http://localhost:8080/shuttleservice/](http://localhost:8080/shuttleservice/)
- 🤝 **Sozialgremium:** [http://localhost:8080/sozialgremium/](http://localhost:8080/sozialgremium/)
- ⚙️ **WordPress Admin:** [http://localhost:8080/wp-admin/](http://localhost:8080/wp-admin/) *(Benutzer: `admin` | Passwort: `password123`)*
- 🔧 **Flarum Admin:** [http://localhost:8080/forum/admin](http://localhost:8080/forum/admin) *(Benutzer: `admin` | Passwort: `password123`)*

Beenden:
```bash
./stop.sh
```

---

### Option B: Docker Compose (Optional für Linux Server)

Für klassische Linux-Server oder CI/CD-Umgebungen steht weiterhin die Docker-Konfiguration zur Verfügung:

```bash
docker compose up -d
```

---

## 📂 Projektstruktur

```
drf-forum-wordpress/
├── docker-compose.yml              # Multi-Container-Setup (WP, Flarum, MariaDB, Nginx)
├── start.sh                        # Start-Skript für Docker Compose
├── start-standalone.sh             # Sofortstart via PHP Built-in Server
├── stop.sh                         # Stoppt alle lokalen Server sauber
├── README.md                       # Diese Dokumentation
│
├── nginx/
│   └── default.conf                # Reverse Proxy (/ -> WordPress, /forum/ -> Flarum)
│
├── docker/
│   └── init-db.sql                 # Initialisiert DBs 'wordpress' & 'flarum'
│
├── flarum/
│   ├── Dockerfile                  # PHP 8.2 Container für Flarum
│   ├── apache.conf                 # Apache VHost Konfiguration
│   ├── docker-entrypoint.sh        # Automatischer Flarum-Bootstrap
│   └── custom-assets/
│       ├── custom.less             # d.r.f Styling & Farben für Flarum
│       ├── custom.js               # Shared Header Injection
│       └── logo-camera.svg         # Vektorlogo
│
├── wordpress/
│   ├── wp-content/
│   │   ├── plugins/
│   │   │   └── drf-flarum-bridge/  # SSO & Forum-Bridge Plugin
│   │   │       └── drf-flarum-bridge.php
│   │   └── themes/
│   │       └── drf-augenblicke/    # Das custom Augenblicke Theme
│   │           ├── style.css       # Theme Header & Basis-Metadaten
│   │           ├── functions.php   # Theme Support, Menüs & Auto-Page Setup
│   │           ├── header.php      # Topbar, Subnav & Mobiles Menü
│   │           ├── footer.php      # Footer & Copyright
│   │           ├── front-page.php  # Startseite mit Hero & 4 Feature-Karten
│   │           ├── page-verein.php # Detailseite Verein
│   │           ├── page-augenblicke.php # Chronik der Treffen
│   │           ├── page-tagungsbuero.php # Anmeldeformular & Orga
│   │           ├── page-sozialgremium.php # Solidaritätsfonds
│   │           ├── page-shuttleservice.php # Shuttle-Fahrpläne
│   │           ├── page-workshops.php # Workshop-Themen
│   │           ├── page-impressum.php # Impressum nach § 5 TMG
│   │           ├── page-datenschutz.php # DSGVO Datenschutzerklärung
│   │           ├── page.php / single.php / index.php / archive.php
│   │           └── assets/
│   │               ├── css/theme.css # Haupt-Stylesheet (Mockup-getreu)
│   │               ├── js/main.js    # Menüsteuerung & Countdown
│   │               └── images/       # Extrahierte Hero-, Polaroid- & Kartengrafiken
│
└── scripts/
    ├── init-wordpress.sh           # Automatische Installation via WP-CLI
    └── router.php                  # Standalone PHP Routing-Engine
```

---

## 📅 Nächstes Treffen

- **Veranstaltung:** AUGENBLICKE XXVI
- **Datum:** 28. – 31. Mai 2026 (Fronleichnam)
- **Ort:** Haus Lutherrose, Neuendettelsau
- **Kontakt Tagungsbüro:** [tagungsbuero2026@d-r-f.de](mailto:tagungsbuero2026@d-r-f.de)

---

## 🐙 Git & GitHub

Repository: [https://github.com/ichbinboese/drf-forum-wordpress](https://github.com/ichbinboese/drf-forum-wordpress)
