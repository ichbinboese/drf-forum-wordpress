/**
 * DRF Augenblicke - Shared Flarum Header Navigation
 */
(function () {
  function injectDrfHeader() {
    if (document.getElementById('drfGlobalNav')) return;

    var header = document.createElement('div');
    header.id = 'drfGlobalNav';
    header.className = 'drf-flarum-header-wrap';
    header.innerHTML = `
      <header class="drf-site-topbar">
        <div class="drf-topbar-inner">
          <a href="/" class="drf-brand-link" title="Zurück zur Homepage">
            <span class="drf-brand-icon">
              <img src="/forum/assets/drf-logo.svg" alt="d.r.f – de.rec.fotografie" width="130" height="100">
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
            <a href="/forum/" class="drf-pill-btn drf-pill-forum">
              <span>Forum &bull; Aktiv</span>
            </a>
          </div>

          <button class="drf-mobile-toggle" id="drfMobileToggle" aria-label="Navigation öffnen">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
          </button>

          <div class="drf-mobile-panel" id="drfMobilePanel">
            <a href="/">🏠 Zurück zur Homepage</a>
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
    `;

    var app = document.getElementById('app') || document.body;
    app.parentNode.insertBefore(header, app);

    var toggle = document.getElementById('drfMobileToggle');
    var panel = document.getElementById('drfMobilePanel');
    if (toggle && panel) {
      toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        panel.classList.toggle('is-open');
      });
      document.addEventListener('click', function (e) {
        if (!panel.contains(e.target) && !toggle.contains(e.target)) {
          panel.classList.remove('is-open');
        }
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectDrfHeader);
  } else {
    injectDrfHeader();
  }
  setTimeout(injectDrfHeader, 300);
})();
