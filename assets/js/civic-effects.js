(() => {
  'use strict';
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const progress = document.createElement('div');
  progress.className = 'civic-scroll-progress';
  progress.setAttribute('aria-hidden', 'true');
  document.body.appendChild(progress);
  const top = document.createElement('button');
  top.type = 'button';
  top.className = 'civic-back-top';
  top.setAttribute('aria-label', 'Back to top');
  top.innerHTML = '<i class="bi bi-arrow-up" aria-hidden="true"></i>';
  top.addEventListener('click', () => window.scrollTo({ top: 0, behavior: reducedMotion ? 'instant' : 'smooth' }));
  document.body.appendChild(top);
  let scheduled = false;
  function onScroll() {
    if (scheduled) return;
    scheduled = true;
    requestAnimationFrame(() => {
      const max = Math.max(1, document.documentElement.scrollHeight - window.innerHeight);
      progress.style.transform = 'scaleX(' + Math.min(1, window.scrollY / max) + ')';
      top.classList.toggle('is-visible', window.scrollY > 480);
      scheduled = false;
    });
  }
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  onScroll();
  if (reducedMotion || !('IntersectionObserver' in window)) return;
  const targets = document.querySelectorAll(
    '.city-stat, .frequent-card, .city-hero-copy, .city-hero-visual, .form-panel, .admin-metric'
  );
  const observer = new IntersectionObserver(entries => {
    for (const entry of entries) {
      if (entry.isIntersecting) {
        entry.target.classList.add('civic-appeared');
        observer.unobserve(entry.target);
      }
    }
  }, { threshold: 0.1 });
  for (const element of targets) {
    element.classList.add('civic-animate');
    observer.observe(element);
  }
})();