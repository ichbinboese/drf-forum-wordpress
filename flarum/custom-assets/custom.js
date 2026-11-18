/**
 * DRF Augenblicke - Shared Flarum Header Injection
 */
(function () {
  function injectDrfHeader() {
    if (document.getElementById('drfSharedTopbar')) return;

    var header = document.createElement('div');
    header.id = 'drfSharedTopbar';
    header.className = 'drf-flarum-topbar';
    header.innerHTML = `
      <a href="/" class="drf-brand">
        <svg class="drf-logo-icon" viewBox="0 0 48 38" fill="none" stroke="#f5b301" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 6L17 2H31L34 6H42C44.2091 6 46 7.79086 46 10V32C46 34.2091 44.2091 36 42 36H6C3.79086 36 2 34.2091 2 32V10C2 7.79086 3.79086 6 6 6H14Z" />
          <circle cx="24" cy="20" r="8" />
          <circle cx="24" cy="20" r="3" fill="#f5b301" />
        </svg>
        <span class="drf-title">AUGENBLICKE</span>
        <span class="drf-sep">|</span>
        <span class="drf-short">d.r.f</span>
        <span class="drf-tagline">de.rec.fotografie</span>
      </a>

      <div class="drf-nav-links">
        <a href="/" class="drf-btn-back">&larr; Zur&uuml;ck zur Homepage</a>
        <a href="/verein/" class="drf-btn-subnav">Verein</a>
        <a href="/workshops/" class="drf-btn-subnav">Workshops</a>
        <a href="/tagungsbuero/" class="drf-btn-subnav">Tagungsb&uuml;ro</a>
        <a href="/shuttleservice/" class="drf-btn-subnav">Shuttle</a>
      </div>
    `;

    document.body.insertBefore(header, document.body.firstChild);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectDrfHeader);
  } else {
    injectDrfHeader();
  }
})();
