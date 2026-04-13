document.addEventListener('DOMContentLoaded', () => {
  const header = document.querySelector('.header');
  const logo = document.querySelector('.logo');
  const navHeader = document.querySelector('.nav--header');
  const navSide = document.querySelector('.nav--side');

  const START_HEIGHT = window.innerHeight;
  const MIN_HEIGHT = 120;
  const SCROLL_RANGE = 500;

  function onScroll() {
    const progress = Math.min(window.scrollY / SCROLL_RANGE, 1);

    // Header height: 100vh → MIN_HEIGHT
    header.style.height = (START_HEIGHT + (MIN_HEIGHT - START_HEIGHT) * progress) + 'px';

    // Border-bottom
    const borderOpacity = Math.max(0, Math.min(1, (progress - 0.1) / 0.3));
    header.style.borderBottomColor = `rgba(0,0,0,${borderOpacity.toFixed(3)})`;

    // Logo scale: 3.5 → 1.1
    logo.style.transform = `scale(${(3.5 + (0.6 - 3.5) * progress).toFixed(3)})`;

    // Nav im Header fadet ein bei 30-60%
    if (navHeader) {
      const navIn = Math.max(0, Math.min(1, (progress - 0.8) / 0.3));
      navHeader.style.opacity = navIn.toFixed(3);
    }

    // Linke Nav: blur + fade-out
    if (navSide) {
      if (progress > 0.05) {
        const blurPx = Math.min(6, (progress - 0.05) * 15);
        const sideOpacity = Math.max(0, 0.5 - progress * 0.5);
        navSide.style.filter = `blur(${blurPx.toFixed(1)}px)`;
        navSide.style.opacity = sideOpacity.toFixed(3);
        navSide.style.pointerEvents = 'none';
      } else {
        navSide.style.filter = '';
        navSide.style.opacity = '0.5';
        navSide.style.pointerEvents = 'auto';
      }
    }
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Smooth Scroll für Anker-Links — pro Link eigener Offset
  document.querySelectorAll('.nav a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        const isMobile = window.innerWidth <= 768;
        let offset = 100;  // default
        const href = this.getAttribute('href');
        if (href === '#boutique') offset = isMobile ? +90 : -90;
        else if (href === '#pairing') offset = isMobile ? 130 : 40;
        else if (href === '#about') offset = isMobile ? 140 : 120;
        window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
      }
    });
  });

  // Intersection Observer für hero-text-2 und pairing-hero
  ['.hero-text-2 h1', '.pairing-hero h1'].forEach(sel => {
    const el = document.querySelector(sel);
    if (el) {
      const obs = new IntersectionObserver(entries => {
        entries.forEach(e => el.classList.toggle('is-visible', e.isIntersecting));
      }, { threshold: 0.5 });
      obs.observe(el);
    }
  });

  // Intersection Observer für About-H1 Underline Animation
  const aboutH1 = document.querySelector('.about h1');
  if (aboutH1) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => aboutH1.classList.toggle('is-visible', e.isIntersecting));
    }, { threshold: 0.5 });
    obs.observe(aboutH1);
  }
});
