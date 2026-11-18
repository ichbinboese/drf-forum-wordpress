/**
 * DRF Augenblicke - Main JavaScript
 */
document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle
  const toggleBtn = document.getElementById('mobileNavToggle');
  const navPanel = document.getElementById('mobileNavPanel');

  if (toggleBtn && navPanel) {
    toggleBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      const isOpen = navPanel.classList.toggle('is-open');
      toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    // Close when clicking outside
    document.addEventListener('click', function (e) {
      if (!navPanel.contains(e.target) && !toggleBtn.contains(e.target)) {
        navPanel.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && navPanel.classList.contains('is-open')) {
        navPanel.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // 2. Smooth scrolling for internal anchor links
  document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(function (anchor) {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // 3. Simple countdown / date highlight
  const countdownEl = document.getElementById('meetingCountdown');
  if (countdownEl) {
    const targetDate = new Date('2026-05-28T09:00:00');
    const updateCountdown = () => {
      const now = new Date();
      const diff = targetDate - now;
      if (diff > 0) {
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        countdownEl.textContent = `Noch ${days} Tage bis AUGENBLICKE XXVI`;
      } else {
        countdownEl.textContent = 'AUGENBLICKE XXVI findet jetzt statt!';
      }
    };
    updateCountdown();
  }
});
