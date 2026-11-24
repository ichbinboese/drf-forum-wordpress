<?php

/**
 * DRF Augenblicke - Shared Header Navigation for Flarum Forum
 */

use Flarum\Extend;
use Flarum\Frontend\Document;

return [
    (new Extend\Frontend('forum'))
        ->content(function (Document $document) {
            $document->head[] = '<link rel="icon" type="image/svg+xml" href="/favicon.svg">';
            $document->head[] = '<link rel="alternate icon" type="image/x-icon" href="/favicon.ico">';
            $document->head[] = '<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">';
            $document->head[] = '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">';
            $document->head[] = '<link rel="manifest" href="/site.webmanifest">';
            $document->head[] = '<meta name="theme-color" content="#141518">';

            $css = <<<CSS
/* DRF Shared Navigation Header */
.drf-flarum-header-wrap {
  position: sticky;
  top: 0;
  z-index: 1100;
  width: 100%;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
}

/* 1. Dark Topbar */
.drf-site-topbar {
  background-color: #141518;
  color: #ffffff;
  border-bottom: 1px solid #2a2d34;
}

.drf-topbar-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 18px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-sizing: border-box;
}

.drf-brand-link {
  display: flex;
  align-items: center;
  gap: 16px;
  text-decoration: none;
  color: #ffffff;
}

.drf-brand-icon {
  width: auto;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.drf-brand-icon img,
.drf-brand-icon svg {
  height: 60px;
  width: auto;
  display: block;
  border-radius: 3px;
}

.drf-brand-text {
  display: flex;
  align-items: baseline;
  gap: 8px;
  white-space: nowrap;
}

.drf-brand-title {
  font-size: 1.2rem;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.drf-brand-sep {
  color: #6b7280;
  font-size: 1.1rem;
  font-weight: 300;
}

.drf-brand-short {
  font-size: 1.05rem;
  font-weight: 700;
  color: #f5b301;
}

.drf-brand-sub {
  font-size: 0.82rem;
  color: #9ca3af;
}

.drf-topbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.drf-pill-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.88rem;
  font-weight: 600;
  border-radius: 9999px;
  padding: 7px 16px;
  text-decoration: none;
  transition: all 0.18s ease;
  white-space: nowrap;
  border: 1px solid transparent;
}

.drf-pill-home {
  background-color: #f5b301;
  color: #111417;
  font-weight: 700;
}

.drf-pill-home:hover {
  background-color: #e0a300;
  transform: translateY(-1px);
  color: #111417;
  text-decoration: none;
}

.drf-pill-forum {
  background-color: #212328;
  border-color: #f5b301;
  color: #f5b301;
}

.drf-mobile-toggle {
  display: none;
  background: #212328;
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 8px;
  color: #ffffff;
  padding: 6px 10px;
  cursor: pointer;
  align-items: center;
  justify-content: center;
}

.drf-mobile-panel {
  display: none;
  position: absolute;
  top: 60px;
  right: 16px;
  width: 240px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  padding: 8px 0;
  z-index: 1200;
}

.drf-mobile-panel.is-open {
  display: block;
}

.drf-mobile-panel a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 18px;
  font-size: 0.92rem;
  font-weight: 600;
  color: #1f2937;
  text-decoration: none;
  border-bottom: 1px solid #f3f4f6;
}

.drf-mobile-panel a:last-child {
  border-bottom: none;
}

.drf-mobile-panel a:hover {
  background-color: #fffdf5;
  color: #b45309;
}

/* 2. White Subnav Bar */
.drf-site-subnav {
  background-color: #ffffff;
  border-bottom: 1px solid #e5e7eb;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.drf-subnav-menu {
  display: flex;
  align-items: center;
  justify-content: center;
  list-style: none;
  flex-wrap: wrap;
  padding: 0;
  margin: 0;
  max-width: 1200px;
  margin: 0 auto;
}

.drf-subnav-item {
  display: flex;
  align-items: center;
}

.drf-subnav-link {
  display: inline-block;
  padding: 10px 16px;
  font-size: 0.92rem;
  font-weight: 600;
  color: #4b5563;
  text-decoration: none;
  transition: color 0.15s ease;
}

.drf-subnav-link:hover {
  color: #000000;
  text-decoration: none;
}

.drf-subnav-sep {
  color: #d1d5db;
  font-size: 0.85rem;
  user-select: none;
}

/* Push down Flarum app so it flows seamlessly below the shared nav */
.App {
  margin-top: 0 !important;
}

.App-header {
  position: static !important;
  background: #1e2229 !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

@media (max-width: 768px) {
  .drf-site-subnav {
    display: none;
  }
  .drf-topbar-actions {
    display: none;
  }
  .drf-brand-sub {
    display: none;
  }
  .drf-mobile-toggle {
    display: flex;
  }
}
CSS;

            $document->head[] = '<style>' . $css . '</style>';

            $html = <<<HTML
<div class="drf-flarum-header-wrap" id="drfGlobalNav">
  <!-- Tier 1: Dark Topbar -->
  <header class="drf-site-topbar">
    <div class="drf-topbar-inner">
      <a href="/" class="drf-brand-link" title="Zurück zur Homepage">
        <span class="drf-brand-icon">
          <img src="/forum/assets/drf-logo.svg" alt="d.r.f – de.rec.fotografie" width="78" height="60">
        </span>
        <div class="drf-brand-text">
          <span class="drf-brand-title">AUGENBLICKE</span>
          <span class="drf-brand-sep">|</span>
          <span class="drf-brand-short">d.r.f</span>
          <span class="drf-brand-sub">de.rec.fotografie</span>
        </div>
      </a>

      <div class="drf-topbar-actions">
        <a href="/" class="drf-pill-btn drf-pill-home">
          <span>&larr; Zurück zur Homepage</span>
        </a>
        <a href="/dateien/" class="drf-pill-btn drf-pill-home" title="Interner Dateibereich (SharePoint-Alternative)">
          <span>📁 Dateiablage</span>
        </a>
        <a href="/forum/" class="drf-pill-btn drf-pill-forum">
          <span>Forum &bull; Aktiv</span>
        </a>
      </div>

      <button class="drf-mobile-toggle" id="drfMobileToggle" aria-label="Navigation öffnen">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
      </button>

      <div class="drf-mobile-panel" id="drfMobilePanel">
        <a href="/">🏠 Zurück zur Homepage</a>
        <a href="/dateien/">📁 Interne Dateiablage</a>
        <a href="/verein/">👥 Verein</a>
        <a href="/augenblicke/">📷 Augenblicke Treffen</a>
        <a href="/tagungsbuero/">📅 Tagungsbüro &amp; Anmeldung</a>
        <a href="/sozialgremium/">🤝 Sozialgremium</a>
        <a href="/shuttleservice/">🚐 Shuttleservice</a>
        <a href="/workshops/">💡 Workshops</a>
        <a href="/impressum/">📄 Impressum</a>
        <a href="/datenschutz/">🔒 Datenschutz</a>
      </div>
    </div>
  </header>

  <!-- Tier 2: White Subnav Bar -->
  <nav class="drf-site-subnav" aria-label="Hauptnavigation">
    <ul class="drf-subnav-menu">
      <li class="drf-subnav-item"><a href="/" class="drf-subnav-link">Startseite</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/verein/" class="drf-subnav-link">Verein</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/augenblicke/" class="drf-subnav-link">Augenblicke</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/tagungsbuero/" class="drf-subnav-link">Tagungsbüro</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/sozialgremium/" class="drf-subnav-link">Sozialgremium</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/shuttleservice/" class="drf-subnav-link">Shuttleservice</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/workshops/" class="drf-subnav-link">Workshops</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/impressum/" class="drf-subnav-link">Impressum</a></li>
      <li class="drf-subnav-sep">|</li>
      <li class="drf-subnav-item"><a href="/datenschutz/" class="drf-subnav-link">Datenschutz</a></li>
    </ul>
  </nav>
</div>
HTML;

            $jsonHtml = json_encode($html);

            $document->foot[] = <<<JS
<script>
(function() {
  function injectDrfNav() {
    if (document.getElementById('drfGlobalNav')) return;
    var app = document.getElementById('app') || document.body;
    var wrap = document.createElement('div');
    wrap.innerHTML = {$jsonHtml};
    var navEl = wrap.firstElementChild;
    app.parentNode.insertBefore(navEl, app);

    var toggle = document.getElementById('drfMobileToggle');
    var panel = document.getElementById('drfMobilePanel');
    if (toggle && panel) {
      toggle.addEventListener('click', function(e) {
        e.stopPropagation();
        panel.classList.toggle('is-open');
      });
      document.addEventListener('click', function(e) {
        if (!panel.contains(e.target) && !toggle.contains(e.target)) {
          panel.classList.remove('is-open');
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectDrfNav);
  } else {
    injectDrfNav();
  }
  setTimeout(injectDrfNav, 300);
})();
</script>
JS;
        }),
];
