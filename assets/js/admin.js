(function () {
  'use strict';
  document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('adminSidebar');
    var toggle = document.getElementById('adminSidebarToggle');
    var closeBtn = document.getElementById('adminSidebarClose');
    var backdrop = document.getElementById('adminSidebarBackdrop');
    function closeSidebar() {
      if (sidebar) sidebar.classList.remove('open');
      if (backdrop) backdrop.classList.remove('show');
    }
    if (toggle) toggle.addEventListener('click', function () {
      if (sidebar) sidebar.classList.add('open');
      if (backdrop) backdrop.classList.add('show');
    });
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (backdrop) backdrop.addEventListener('click', closeSidebar);

    document.querySelectorAll('[data-admin-table-search]').forEach(function (input) {
      var tableId = input.getAttribute('data-admin-table-search');
      var table = document.getElementById(tableId);
      if (!table) return;
      input.addEventListener('input', function () {
        var q = input.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(function (tr) {
          tr.style.display = tr.innerText.toLowerCase().indexOf(q) >= 0 ? '' : 'none';
        });
      });
    });

    document.querySelectorAll('th[data-sort]').forEach(function (th) {
      th.style.cursor = 'pointer';
      th.addEventListener('click', function () {
        var table = th.closest('table');
        if (!table) return;
        var tbody = table.querySelector('tbody');
        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
        var idx = Array.prototype.indexOf.call(th.parentElement.children, th);
        var asc = th.getAttribute('data-dir') !== 'asc';
        rows.sort(function (a, b) {
          var ta = (a.children[idx] ? a.children[idx].innerText : '').trim().toLowerCase();
          var tb = (b.children[idx] ? b.children[idx].innerText : '').trim().toLowerCase();
          return asc ? ta.localeCompare(tb, undefined, { numeric: true }) : tb.localeCompare(ta, undefined, { numeric: true });
        });
        rows.forEach(function (r) { tbody.appendChild(r); });
        th.setAttribute('data-dir', asc ? 'asc' : 'desc');
      });
    });

    if (window.Chart) {
      var line = document.getElementById('ordersChart');
      if (line) {
        new Chart(line, {
          type: 'line',
          data: { labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'], datasets: [{ label: 'Orders', data: [24, 32, 29, 41, 38, 46], borderColor: '#0d1b2a', backgroundColor: 'rgba(13,27,42,.1)', tension: .35, fill: true }] },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var category = document.getElementById('categoryChart');
      if (category) {
        new Chart(category, {
          type: 'bar',
          data: { labels: ['Fashion', 'Packaging', 'Consumer', 'Machinery'], datasets: [{ data: [18, 24, 15, 33], backgroundColor: ['#0d1b2a', '#c9a227', '#16a34a', '#f59e0b'] }] },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var pay = document.getElementById('paymentChart');
      if (pay) {
        new Chart(pay, {
          type: 'doughnut',
          data: { labels: ['Pending', 'Verified', 'Rejected'], datasets: [{ data: [14, 43, 3], backgroundColor: ['#f59e0b', '#16a34a', '#ef4444'] }] },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
      var quote = document.getElementById('quoteChart');
      if (quote) {
        new Chart(quote, {
          type: 'bar',
          data: { labels: ['W1', 'W2', 'W3', 'W4'], datasets: [{ label: 'Quotation Value (k USD)', data: [45, 38, 52, 61], backgroundColor: '#1b2838' }] },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
    }
  });
})();

