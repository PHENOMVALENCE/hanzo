@props(['existingImages' => [], 'maxImages' => 5])

@php
  $existing = is_array($existingImages) ? $existingImages : [];
@endphp

<div class="product-image-upload" data-max="{{ $maxImages }}">
  <label class="form-label">{{ __('labels.product_images') ?? 'Product images' }}</label>
  <small class="d-block text-muted mb-2">{{ trans('labels.product_images_hint', ['max' => $maxImages]) ?? 'JPG, PNG or WebP, max 2MB each. First image is primary. Up to ' . $maxImages . ' images.' }}</small>

  <div class="product-image-previews mb-2">
    @foreach($existing as $idx => $path)
    <div class="product-image-preview-item" data-path="{{ $path }}">
      <img src="{{ Storage::url($path) }}" alt="">
      <button type="button" class="product-image-remove" title="{{ __('labels.remove') ?? 'Remove' }}">
        <i class="bx bx-x"></i>
      </button>
      @if($idx === 0)
        <span class="product-image-primary-badge">{{ __('labels.primary') ?? 'Primary' }}</span>
      @endif
    </div>
    @endforeach
    <div class="product-image-preview-item product-image-add">
      <label class="product-image-dropzone mb-0">
        <i class="bx bx-cloud-upload"></i>
        <span>{{ __('labels.add_images') ?? 'Add images' }}</span>
        <span class="product-image-drop-hint">{{ __('labels.drag_drop') ?? 'or drag & drop' }}</span>
        <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="d-none product-image-input">
      </label>
    </div>
  </div>

  <div class="product-image-new-previews"></div>
  <div class="product-image-remove-inputs"></div>
</div>

@push('page-css')
<style>
.product-image-previews { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-start; }
.product-image-preview-item {
  width: 100px; height: 100px; border-radius: 8px; overflow: hidden;
  position: relative; background: #f5f5f5; border: 2px dashed #ddd;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.product-image-preview-item img { width: 100%; height: 100%; object-fit: cover; }
.product-image-remove {
  position: absolute; top: 4px; right: 4px; width: 24px; height: 24px;
  border: none; background: rgba(0,0,0,0.6); color: white; border-radius: 50%;
  cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; padding: 0;
}
.product-image-remove:hover { background: #dc3545; }
.product-image-primary-badge {
  position: absolute; bottom: 4px; left: 4px; right: 4px;
  font-size: 0.65rem; background: rgba(0,0,0,0.6); color: white; text-align: center; padding: 2px;
}
.product-image-add .product-image-dropzone {
  width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;
  cursor: pointer; color: #6c757d; transition: all 0.2s;
}
.product-image-add .product-image-dropzone:hover { color: var(--hanzo-gold, #D89B2B); border-color: var(--hanzo-gold, #D89B2B); }
.product-image-add .product-image-dropzone i { font-size: 1.5rem; margin-bottom: 4px; }
.product-image-add .product-image-dropzone span { font-size: 0.75rem; }
.product-image-drop-hint { font-size: 0.65rem !important; opacity: 0.8; }
.product-image-add .product-image-dropzone.drag-over { border-color: var(--hanzo-gold, #D89B2B); background: rgba(216,155,43,0.05); }
.product-image-preview-item.new-preview { border-style: solid; border-color: #e5e5e5; }
.product-image-new-previews { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 0.5rem; }
</style>
@endpush

@push('page-js')
<script>
(function() {
  var container = document.querySelector('.product-image-upload');
  if (!container) return;
  var max = parseInt(container.dataset.max || 5, 10);
  var previews = container.querySelector('.product-image-previews');
  var newPreviews = container.querySelector('.product-image-new-previews');
  var addZone = container.querySelector('.product-image-add');
  var removeInputs = container.querySelector('.product-image-remove-inputs');
  var addInput = container.querySelector('.product-image-input');

  function countExisting() {
    return previews.querySelectorAll('.product-image-preview-item[data-path]').length;
  }
  function countNew() {
    return newPreviews.children.length;
  }
  function totalCount() { return countExisting() + countNew(); }

  function updateAddZone() {
    addZone.style.display = totalCount() >= max ? 'none' : 'flex';
    if (addInput) addInput.disabled = totalCount() >= max;
  }

  previews.querySelectorAll('.product-image-remove').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var item = btn.closest('.product-image-preview-item');
      var path = item.dataset.path;
      if (path) {
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'remove_images[]';
        hidden.value = path;
        removeInputs.appendChild(hidden);
      }
      item.remove();
      updateAddZone();
    });
  });

  function handleFiles(files) {
    var slot = max - totalCount();
    (files || []).slice(0, slot).forEach(function(file) {
      if (!file.type || !file.type.match(/^image\/(jpeg|png|webp)$/)) return;
      var reader = new FileReader();
      reader.onload = function(e) {
        var div = document.createElement('div');
        div.className = 'product-image-preview-item new-preview';
        var fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.name = 'images[]';
        fileInput.style.display = 'none';
        var dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        div.innerHTML = '<img src="' + e.target.result + '" alt=""><button type="button" class="product-image-remove" title="Remove"><i class="bx bx-x"></i></button>';
        div.appendChild(fileInput);
        var rm = div.querySelector('.product-image-remove');
        rm.addEventListener('click', function() { div.remove(); updateAddZone(); });
        newPreviews.appendChild(div);
        updateAddZone();
      };
      reader.readAsDataURL(file);
    });
  }

  if (addInput) {
    addInput.addEventListener('change', function() { handleFiles(Array.from(this.files || [])); this.value = ''; updateAddZone(); });
    var dropzone = addZone.querySelector('.product-image-dropzone');
    if (dropzone) {
      ['dragenter','dragover','dragleave','drop'].forEach(function(ev) {
        dropzone.addEventListener(ev, function(e) {
          e.preventDefault(); e.stopPropagation();
          if (ev === 'dragenter' || ev === 'dragover') dropzone.classList.add('drag-over');
          else dropzone.classList.remove('drag-over');
          if (ev === 'drop') handleFiles(Array.from(e.dataTransfer.files || []));
          updateAddZone();
        });
      });
    }
  }
  updateAddZone();
})();
</script>
@endpush
