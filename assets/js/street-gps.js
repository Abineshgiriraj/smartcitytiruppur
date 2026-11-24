(() => {
  'use strict';
  const use = document.getElementById('use-complaint-location');
  const status = document.getElementById('complaint-location-status');
  const choices = document.getElementById('gps-locality-choices');
  const street = document.getElementById('street_name');
  const streetResults = document.getElementById('street_results');
  const form = street?.form;
  if (!use || !status || !choices || !street || !form) return;
  const ids = ['street_id', 'ward_id', 'zone_id', 'ward_display', 'zone_display'];
  const fields = ids.map(id => document.getElementById(id));
  if (fields.some(el => !el)) return;
  let requestId = 0;
  const messages = {
    inaccurate: 'Your location accuracy is too low. Try outdoors, or choose the locality manually.',
    outside: 'GPS appears outside the Tiruppur search area. Please choose the locality manually.',
    no_match: 'No mapped locality is close enough. Please search for your street manually.',
    manual: 'GPS lookup is temporarily unavailable. Search your street manually.',
    invalid: 'Your device provided invalid location coordinates.'
  };
  const announce = (message, kind = 'info') => {
    status.textContent = message;
    status.dataset.state = kind;
  };
  const clearChoices = () => { requestId++; choices.replaceChildren(); };
  use.addEventListener('click', () => {
    clearChoices();
    if (!navigator.geolocation || !window.isSecureContext) {
      announce('Allow location on a secure site (localhost or HTTPS), or search your locality manually.', 'warning');
      return;
    }
    const id = requestId;
    use.disabled = true;
    announce('Checking location accuracy and nearby mapped streets…');
    navigator.geolocation.getCurrentPosition(async ({ coords }) => {
      if (id !== requestId) { use.disabled = false; return; }
      try {
        const data = new URLSearchParams({
          latitude: String(coords.latitude),
          longitude: String(coords.longitude),
          accuracy: String(coords.accuracy),
          csrf_token: document.querySelector('meta[name="csrf-token"]')?.content || ''
        });
        const response = await fetch(use.dataset.endpoint, { method: 'POST', body: data, credentials: 'same-origin', cache: 'no-store' });
        const payload = await response.json();
        if (id !== requestId) return;
        if (!response.ok || payload.result !== 'choices') {
          announce(messages[payload.result] || 'Unable to look up nearby streets. Choose one manually.', 'warning');
          return;
        }
        announce('Choose the correct locality below to confirm its Ward and Zone. GPS suggestions are approximate.', 'success');
        for (const place of payload.streets) {
          const button = document.createElement('button');
          button.type = 'button';
          button.className = 'gps-locality-choice';
          const text = document.createElement('span');
          text.textContent = place.street_name;
          const subtitle = document.createElement('small');
          subtitle.textContent = 'Ward ' + place.ward_no + ' · Zone ' + place.zone_no + ' · ~' + place.distance_m + ' m away';
          button.append(text, subtitle);
          button.addEventListener('click', () => {
            street.value = place.street_name;
            [place.id, place.ward_id, place.zone_id, place.ward_no, place.zone_no].forEach((value, i) => { fields[i].value = value; });
            streetResults?.replaceChildren();
            streetResults?.classList.add('d-none');
            clearChoices();
            announce('Confirmed: ' + place.street_name + ' · Ward ' + place.ward_no + ' · Zone ' + place.zone_no, 'success');
          });
          choices.appendChild(button);
        }
      } catch (e) {
        if (id === requestId) announce('GPS lookup could not complete. Select the locality manually.', 'warning');
      } finally { use.disabled = false; }
    }, error => {
      if (id === requestId) {
        announce(error.code === 1 ? 'Location permission denied. Use the locality search instead.' : 'Location unavailable. Try again or search manually.', 'warning');
        use.disabled = false;
      }
    }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 });
  });
  // If citizen types a new locality, stale selections are not valid anymore.
  street.addEventListener('input', () => {
    clearChoices();
    fields.forEach(el => { el.value = ''; });
    announce('Choose a locality suggestion to confirm Ward and Zone.');
  });
  form.addEventListener('reset', () => { clearChoices(); announce(''); });
})();