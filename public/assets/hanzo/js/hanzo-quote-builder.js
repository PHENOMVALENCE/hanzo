/**
 * HANZO Quote Builder - Live total calculation
 * Attach to forms with .quote-field inputs
 */
(function() {
  function calcTotal(container) {
    const fields = container.querySelectorAll('.quote-field');
    let sum = 0;
    fields.forEach(f => { sum += parseFloat(f.value) || 0; });
    const display = container.querySelector('#total-display, [data-hanzo-total]');
    if (display) display.textContent = sum.toFixed(2);
  }
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#quote-form, [data-hanzo-quote-form]').forEach(form => {
      form.querySelectorAll('.quote-field').forEach(el => {
        el.addEventListener('input', () => calcTotal(form));
      });
      calcTotal(form);
    });
  });
})();
