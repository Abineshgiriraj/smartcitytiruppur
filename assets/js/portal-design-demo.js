(() => {
  "use strict";
  const root = document.querySelector(".demo-portal");
  if (!root) return;
  const controls = root.querySelectorAll("[data-home-layout]");
  const photoLink = root.querySelector(".dp-hero-secondary");
  const storageKey = "tiruppur-homepage-layout";
  let saved = "hub";
  try {
    const choice = localStorage.getItem(storageKey);
    if (choice === "hub" || choice === "photo") saved = choice;
  } catch (_) { /* Privacy settings can disable storage. */ }

  function switchTo(layout) {
    const photo = layout === "photo";
    document.body.classList.toggle("demo-photo-mode", photo);
    for (const control of controls) {
      const active = control.dataset.homeLayout === layout;
      control.setAttribute("aria-pressed", String(active));
    }
    if (photoLink) photoLink.hidden = !photo;
    try { localStorage.setItem(storageKey, layout); } catch (_) {}
  }
  for (const control of controls) {
    control.addEventListener("click", () => switchTo(control.dataset.homeLayout));
  }
  switchTo(saved);

  // The demo's fake sample register/track modals are intentionally not included:
  // all actions here navigate to the project's real complaint workflows.
})();