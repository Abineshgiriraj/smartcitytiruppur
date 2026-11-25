/* Neo-civic interactions: accessible motion, scroll reveal, count-up and depth. */
(() => {
  'use strict';
  const home = document.querySelector('.neo-home');
  if (!home) return;
  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  // Subtle depth for the photo-led hero. Respects reduced motion and touch devices.
  const photoHero = home.querySelector('.neo-photo-hero');
  const heroPhoto = photoHero?.querySelector('.neo-photo-hero-backdrop img');
  if (photoHero && heroPhoto && !reduceMotion &&
      window.matchMedia('(pointer:fine)').matches) {
    let frame = 0;
    let pointerX = 0;
    let pointerY = 0;
    photoHero.addEventListener('pointermove', (event) => {
      const bounds = photoHero.getBoundingClientRect();
      pointerX = (event.clientX - bounds.left) / bounds.width - .5;
      pointerY = (event.clientY - bounds.top) / bounds.height - .5;
      if (frame) return;
      frame = requestAnimationFrame(() => {
        frame = 0;
        heroPhoto.style.setProperty('--photo-x', (pointerX * -11).toFixed(2) + 'px');
        heroPhoto.style.setProperty('--photo-y', (pointerY * -8).toFixed(2) + 'px');
      });
    }, { passive: true });
    photoHero.addEventListener('pointerleave', () => {
      if (frame) cancelAnimationFrame(frame);
      frame = 0;
      heroPhoto.style.removeProperty('--photo-x');
      heroPhoto.style.removeProperty('--photo-y');
    });
  }

  const revealItems = home.querySelectorAll(
    '.neo-section-heading,.neo-impact-card,.neo-journey-intro,.neo-step,.neo-service,.neo-last-cta-copy'
  );
  if (!reduceMotion && 'IntersectionObserver' in window) {
    const reveal = new IntersectionObserver((entries, observer) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        entry.target.classList.add('neo-inview');
        observer.unobserve(entry.target);
      }
    }, {threshold:0.08, rootMargin:'0px 0px 25px 0px'});
    for (const item of revealItems) {
      item.classList.add('neo-await-reveal');
      reveal.observe(item);
    }

    const counters = home.querySelectorAll('[data-civic-counter]');
    const countObserver = new IntersectionObserver((entries, observer) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue;
        const el = entry.target;
        observer.unobserve(el);
        const value = Number(el.dataset.civicCounter || 0);
        if (!Number.isSafeInteger(value) || value < 0) continue;
        const target = el.querySelector('.neo-impact-value');
        if (!target) continue;
        let started;
        const duration = 1050;
        const formatter = new Intl.NumberFormat('en-IN');
        const tick = (time) => {
          if (started === undefined) started = time;
          const t = Math.min(1, (time-started)/duration);
          const eased = 1 - Math.pow(1-t,4);
          target.textContent = formatter.format(Math.round(value*eased));
          if (t < 1) requestAnimationFrame(tick);
          else target.textContent = formatter.format(value);
        };
        requestAnimationFrame(tick);
      }
    }, {threshold:0.3});
    for (const card of counters) countObserver.observe(card);

    const heroVisual = home.querySelector('.neo-hero-visual');
    const photo = home.querySelector('.neo-photo-frame');
    if (heroVisual && photo && window.matchMedia('(pointer:fine)').matches) {
      heroVisual.addEventListener('pointermove', e => {
        const rect = heroVisual.getBoundingClientRect();
        const x = (e.clientX-rect.left)/rect.width - .5;
        const y = (e.clientY-rect.top)/rect.height - .5;
        photo.style.setProperty('--neo-tilt-x',(-y*4)+'deg');
        photo.style.setProperty('--neo-tilt-y',(x*4)+'deg');
      },{passive:true});
      heroVisual.addEventListener('pointerleave',()=>{
        photo.style.removeProperty('--neo-tilt-x');
        photo.style.removeProperty('--neo-tilt-y');
      });
    }
  }
})();