/**
 * HANZO Stepper - Milestone highlight based on status
 * Add data-hanzo-stepper and data-current="deposit_pending" to container
 * Steps: data-step="deposit_pending" etc.
 */
(function() {
  const ORDER = ['deposit_pending','deposit_paid','in_production','shipped','delivered'];
  function updateStepper(el) {
    const current = (el.getAttribute('data-current') || '').toLowerCase();
    const idx = ORDER.indexOf(current);
    const steps = el.querySelectorAll('[data-step]');
    steps.forEach((s, i) => {
      const stepKey = (s.getAttribute('data-step') || '').toLowerCase();
      const stepIdx = ORDER.indexOf(stepKey);
      s.classList.remove('done', 'active', 'pending');
      if (stepIdx < idx) s.classList.add('done');
      else if (stepIdx === idx) s.classList.add('active');
      else s.classList.add('pending');
    });
  }
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-hanzo-stepper]').forEach(updateStepper);
  });
})();
