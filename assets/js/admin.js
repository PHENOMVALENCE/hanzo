(function () {
  'use strict';

  function initHanzoCharts() {
    if (!window.Chart) {
      return;
    }

    if (window.HANZO_ADMIN_CHARTS) {
      var d = window.HANZO_ADMIN_CHARTS;
      var line = document.getElementById('ordersChart');
      if (line && d.ordersMonthly) {
        new Chart(line, {
          type: 'line',
          data: {
            labels: d.ordersMonthly.labels,
            datasets: [{
              label: 'Orders',
              data: d.ordersMonthly.values,
              borderColor: '#0d1b2a',
              backgroundColor: 'rgba(13,27,42,.1)',
              tension: 0.35,
              fill: true
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var category = document.getElementById('categoryChart');
      if (category && d.categoryDemand) {
        new Chart(category, {
          type: 'bar',
          data: {
            labels: d.categoryDemand.labels,
            datasets: [{
              data: d.categoryDemand.values,
              backgroundColor: ['#0d1b2a', '#c9a227', '#16a34a', '#f59e0b', '#3b82f6', '#8b5cf6', '#64748b', '#ea580c']
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var pay = document.getElementById('paymentChart');
      if (pay && d.payments) {
        new Chart(pay, {
          type: 'doughnut',
          data: {
            labels: d.payments.labels,
            datasets: [{
              data: d.payments.values,
              backgroundColor: ['#f59e0b', '#16a34a', '#ef4444', '#64748b']
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
      var quote = document.getElementById('quoteChart');
      if (quote && d.quotesMonthly) {
        new Chart(quote, {
          type: 'bar',
          data: {
            labels: d.quotesMonthly.labels,
            datasets: [{
              label: 'Quotation value (USD)',
              data: d.quotesMonthly.values,
              backgroundColor: '#1b2838'
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
    }

    if (window.HANZO_REPORT_CHARTS) {
      var r = window.HANZO_REPORT_CHARTS;
      var ro = document.getElementById('reportOrdersChart');
      if (ro && r.ordersMonthly) {
        new Chart(ro, {
          type: 'line',
          data: {
            labels: r.ordersMonthly.labels,
            datasets: [{
              label: 'Orders',
              data: r.ordersMonthly.values,
              borderColor: '#0d1b2a',
              backgroundColor: 'rgba(13,27,42,.08)',
              tension: 0.35,
              fill: true
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var rq = document.getElementById('reportQuotesChart');
      if (rq && r.quotesMonthly) {
        new Chart(rq, {
          type: 'bar',
          data: {
            labels: r.quotesMonthly.labels,
            datasets: [{
              label: 'Landed cost (USD)',
              data: r.quotesMonthly.values,
              backgroundColor: '#c9a227'
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
      var rc = document.getElementById('reportCategoryChart');
      if (rc && r.categoryDemand) {
        new Chart(rc, {
          type: 'bar',
          data: {
            labels: r.categoryDemand.labels,
            datasets: [{
              data: r.categoryDemand.values,
              backgroundColor: '#0d1b2a'
            }]
          },
          options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
      var rp = document.getElementById('reportPaymentChart');
      if (rp && r.payments) {
        new Chart(rp, {
          type: 'doughnut',
          data: {
            labels: r.payments.labels,
            datasets: [{
              data: r.payments.values,
              backgroundColor: ['#f59e0b', '#16a34a', '#ef4444', '#64748b']
            }]
          },
          options: { responsive: true, maintainAspectRatio: false }
        });
      }
      var ros = document.getElementById('reportOrderStatusChart');
      if (ros && r.orderStatus) {
        new Chart(ros, {
          type: 'bar',
          data: {
            labels: r.orderStatus.labels,
            datasets: [{
              label: 'Orders',
              data: r.orderStatus.values,
              backgroundColor: '#1e3a5f'
            }]
          },
          options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
      }
    }
  }

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

    initHanzoCharts();
  });
})();
