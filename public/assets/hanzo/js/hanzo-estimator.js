/**
 * HANZO Estimation Widget
 * Fetches estimate from API and renders result panel
 * Form: #hanzo-estimate-form or [data-hanzo-estimator]
 * Result: #hanzo-estimate-result or [data-hanzo-estimate-result]
 */
(function() {
  function init() {
    const form = document.querySelector('#hanzo-estimate-form, [data-hanzo-estimator]');
    const resultEl = document.querySelector('#hanzo-estimate-result, [data-hanzo-estimate-result]');
    const detailsEl = document.querySelector('#estimate-details, [data-hanzo-estimate-details]');
    const totalEl = document.querySelector('#estimate-total, [data-hanzo-estimate-total]');
    if (!form || !resultEl) return;
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const qs = new URLSearchParams(new FormData(form)).toString();
      fetch('/api/estimate?' + qs)
        .then(r => r.json())
        .then(data => {
          if (detailsEl) {
            const method = form.querySelector('[name="method"]')?.value === 'sea' ? 'Sea' : 'Air';
            detailsEl.innerHTML = '<li>Shipping (' + method + '): $' + (data.min || 0) + ' - $' + (data.max || 0) + '</li>';
            if (data.message) detailsEl.innerHTML += '<li class="text-muted">' + data.message + '</li>';
          }
          if (totalEl) totalEl.textContent = (data.min || data.max) ? '$' + (data.min || 0) + ' - $' + (data.max || 0) : 'Contact HANZO';
          resultEl.classList.remove('d-none');
        })
        .catch(() => {
          if (detailsEl) detailsEl.innerHTML = '<li class="text-muted">Unable to fetch estimate.</li>';
          if (totalEl) totalEl.textContent = '—';
          resultEl.classList.remove('d-none');
        });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
