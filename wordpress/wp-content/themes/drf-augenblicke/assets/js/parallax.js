/**
 * DRF Augenblicke - Apple-Style Parallax & Scroll Reveal Engine
 *
 * Features:
 * - High performance IntersectionObserver scroll reveal
 * - Staggered fly-in for grids and cards
 * - 60fps/120fps requestAnimationFrame multi-plane parallax depth on Hero
 * - Full client-side toggle support (UI Pill, ?parallax=0 query param, or localStorage)
 */
(function () {
  'use strict';

  // 1. Check if Parallax is disabled via URL (?parallax=0) or localStorage
  const urlParams = new URLSearchParams(window.location.search);
  const paramVal = urlParams.get('parallax');
  let isEnabled = true;

  if (paramVal === '0') {
    isEnabled = false;
    try { localStorage.setItem('drf_parallax_active', '0'); } catch (e) {}
  } else if (paramVal === '1') {
    isEnabled = true;
    try { localStorage.setItem('drf_parallax_active', '1'); } catch (e) {}
  } else {
    try {
      const stored = localStorage.getItem('drf_parallax_active');
      if (stored === '0') isEnabled = false;
    } catch (e) {}
  }

  // Check prefers-reduced-motion
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    isEnabled = false;
  }

  if (!isEnabled) {
    document.documentElement.classList.add('no-parallax');
    document.addEventListener('DOMContentLoaded', function () {
      document.body.classList.add('no-parallax');
      createToggleSwitch(false);
    });
    return;
  }

  document.addEventListener('DOMContentLoaded', function () {
    // 2. Identify and setup reveal elements
    const elementsToReveal = [
      ...document.querySelectorAll('.drf-section-header'),
      ...document.querySelectorAll('.feature-card'),
      ...document.querySelectorAll('.drf-workshop-card'),
      ...document.querySelectorAll('.drf-polaroid-card'),
      ...document.querySelectorAll('.drf-timeline-item'),
      ...document.querySelectorAll('.drf-countdown-card'),
      ...document.querySelectorAll('.drf-event-banner'),
      ...document.querySelectorAll('.hero-content'),
      ...document.querySelectorAll('.hero-visual')
    ];

    elementsToReveal.forEach(function (el) {
      if (!el.classList.contains('drf-timeline-item')) {
        el.classList.add('drf-reveal');
      }
    });

    // 3. IntersectionObserver for Entrance Animations
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver(
        function (entries, obs) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-revealed');
              obs.unobserve(entry.target);
            }
          });
        },
        {
          root: null,
          rootMargin: '0px 0px -40px 0px',
          threshold: 0.12
        }
      );

      elementsToReveal.forEach(function (el) {
        observer.observe(el);
      });
    } else {
      // Fallback: reveal all immediately
      elementsToReveal.forEach(function (el) {
        el.classList.add('is-revealed');
      });
    }

    // Immediate reveal for hero elements within view
    setTimeout(function () {
      const heroContent = document.querySelector('.hero-content');
      const heroVisual = document.querySelector('.hero-visual');
      if (heroContent) heroContent.classList.add('is-revealed');
      if (heroVisual) heroVisual.classList.add('is-revealed');
    }, 120);

    // 4. Multi-Layer Parallax on Scroll (Apple iPhone Style Depth)
    const heroSection = document.querySelector('.hero-augenblicke, .drf-elementor-hero');
    const polaroidDock = document.querySelector('.polaroid-dock');
    const polaroidCamera = document.querySelector('.polaroid-camera');

    if (heroSection && (polaroidDock || polaroidCamera)) {
      if (polaroidDock) polaroidDock.classList.add('parallax-dock');
      if (polaroidCamera) polaroidCamera.classList.add('parallax-camera');

      let ticking = false;

      function onScrollParallax() {
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;
        const heroHeight = heroSection.offsetHeight;

        // Only calculate while hero is in or near viewport
        if (scrollY < heroHeight + 150) {
          // Front camera polaroid drifts upward faster (-0.14x) with slight tilt
          if (polaroidCamera) {
            const transY = scrollY * -0.14;
            const rot = 4 + scrollY * 0.01;
            polaroidCamera.style.transform = `translate3d(0, ${transY.toFixed(1)}px, 0) rotate(${rot.toFixed(1)}deg)`;
          }

          // Back dock polaroid drifts at different rate (-0.06x) with opposite counter-tilt
          if (polaroidDock) {
            const transY = scrollY * -0.06;
            const rot = -6 - scrollY * 0.008;
            polaroidDock.style.transform = `translate3d(0, ${transY.toFixed(1)}px, 0) rotate(${rot.toFixed(1)}deg)`;
          }
        }

        ticking = false;
      }

      window.addEventListener('scroll', function () {
        if (!ticking) {
          window.requestAnimationFrame(onScrollParallax);
          ticking = true;
        }
      }, { passive: true });
    }

    // 5. Create Live Demo Control Pill
    createToggleSwitch(true);
  });

  /**
   * Floating Parallax Switch (For client presentation and instant toggling)
   */
  function createToggleSwitch(active) {
    if (document.getElementById('drfParallaxSwitch')) return;

    const switchBtn = document.createElement('div');
    switchBtn.id = 'drfParallaxSwitch';
    switchBtn.className = 'drf-parallax-switch' + (active ? '' : ' is-disabled');
    switchBtn.title = 'Klick zum Umschalten der Apple-Parallaxe-Effekte (Kunden-Demo)';
    switchBtn.innerHTML = `
      <span class="switch-dot"></span>
      <span class="switch-label">${active ? '✨ Parallaxe: AN' : 'Parallaxe: AUS'}</span>
    `;

    switchBtn.addEventListener('click', function () {
      const willBeActive = document.body.classList.contains('no-parallax');
      if (willBeActive) {
        document.body.classList.remove('no-parallax');
        document.documentElement.classList.remove('no-parallax');
        switchBtn.classList.remove('is-disabled');
        switchBtn.querySelector('.switch-label').textContent = '✨ Parallaxe: AN';
        try { localStorage.setItem('drf_parallax_active', '1'); } catch (e) {}
      } else {
        document.body.classList.add('no-parallax');
        document.documentElement.classList.add('no-parallax');
        switchBtn.classList.add('is-disabled');
        switchBtn.querySelector('.switch-label').textContent = 'Parallaxe: AUS';
        try { localStorage.setItem('drf_parallax_active', '0'); } catch (e) {}
      }
    });

    document.body.appendChild(switchBtn);
  }
})();
