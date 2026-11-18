/**
 * DRF Augenblicke - Main Theme JavaScript
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

  // 3. Live 4-Segment Countdown Timer (Tage, Stunden, Minuten, Sekunden)
  const cdDays = document.getElementById('cdDays');
  const cdHours = document.getElementById('cdHours');
  const cdMins = document.getElementById('cdMins');
  const cdSecs = document.getElementById('cdSecs');

  if (cdDays && cdHours && cdMins && cdSecs) {
    // Read target date from localized script object or default to May 28, 2026
    const rawTarget = (window.drfThemeData && window.drfThemeData.targetDate) 
      ? window.drfThemeData.targetDate 
      : '2026-05-28T09:00:00';

    const targetDate = new Date(rawTarget).getTime();

    function padZero(num) {
      return num < 10 ? '0' + num : num;
    }

    function updateCountdown() {
      const now = new Date().getTime();
      const distance = targetDate - now;

      if (distance > 0) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        cdDays.textContent = padZero(days);
        cdHours.textContent = padZero(hours);
        cdMins.textContent = padZero(minutes);
        cdSecs.textContent = padZero(seconds);
      } else {
        cdDays.textContent = '00';
        cdHours.textContent = '00';
        cdMins.textContent = '00';
        cdSecs.textContent = '00';
        const caption = document.querySelector('.countdown-caption');
        if (caption) {
          caption.textContent = 'AUGENBLICKE FINDET JETZT STATT!';
        }
      }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
  }

  // 4. Subtle 3D Tilt on Polaroid Images
  const polaroidWrapper = document.querySelector('.polaroid-wrapper');
  if (polaroidWrapper && window.innerWidth > 768) {
    polaroidWrapper.addEventListener('mousemove', function (e) {
      const rect = polaroidWrapper.getBoundingClientRect();
      const x = e.clientX - rect.left - rect.width / 2;
      const y = e.clientY - rect.top - rect.height / 2;
      const rotateX = (-y / rect.height) * 8;
      const rotateY = (x / rect.width) * 8;
      polaroidWrapper.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
    });

    polaroidWrapper.addEventListener('mouseleave', function () {
      polaroidWrapper.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg)';
      polaroidWrapper.style.transition = 'transform 0.4s ease';
    });

    polaroidWrapper.addEventListener('mouseenter', function () {
      polaroidWrapper.style.transition = 'transform 0.1s ease';
    });
  }
});
