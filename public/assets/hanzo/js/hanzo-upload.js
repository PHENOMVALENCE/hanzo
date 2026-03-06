/**
 * HANZO File Upload UI
 * Multi-file: show filenames, size validation
 * Use .hanzo-file-upload and .hanzo-file-list
 */
(function() {
  const MAX_SIZE = 10 * 1024 * 1024; // 10MB
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hanzo-file-upload input[type="file"]').forEach(input => {
      const container = input.closest('.hanzo-file-upload');
      const listEl = container?.querySelector('.hanzo-file-list');
      if (!listEl) return;
      const updateList = () => {
        listEl.innerHTML = '';
        [].slice.call(input.files || []).forEach(file => {
          const li = document.createElement('div');
          li.className = 'hanzo-file-list-item';
          const over = file.size > MAX_SIZE;
          li.innerHTML = '<span>' + file.name + '</span><span class="text-' + (over ? 'danger' : 'muted') + '">' + (file.size / 1024).toFixed(1) + ' KB</span>';
          listEl.appendChild(li);
        });
      };
      input.addEventListener('change', updateList);
      container?.addEventListener('dragover', e => { e.preventDefault(); container.classList.add('dragover'); });
      container?.addEventListener('dragleave', () => container.classList.remove('dragover'));
      container?.addEventListener('drop', e => {
        e.preventDefault();
        container.classList.remove('dragover');
        if (e.dataTransfer?.files?.length) input.files = e.dataTransfer.files;
        updateList();
      });
    });
  });
})();
