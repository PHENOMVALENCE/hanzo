@props(['documents' => [], 'downloadRoute' => null])

<ul class="hanzo-doc-list">
  @forelse($documents as $doc)
  @php
    $name = is_object($doc) ? ($doc->name ?? $doc->filename ?? 'Document') : ($doc['name'] ?? $doc['filename'] ?? 'Document');
    $date = is_object($doc) ? optional($doc->created_at)->format('M j, Y') : ($doc['date'] ?? '');
    $url = $downloadRoute && is_object($doc) ? route($downloadRoute, $doc) : (is_array($doc) ? ($doc['url'] ?? '#') : '#');
    $icon = 'bx-file';
    if (is_object($doc) && isset($doc->type)) {
      if (str_contains(strtolower($doc->type ?? ''), 'pdf')) $icon = 'bx-file-blank';
      elseif (str_contains(strtolower($doc->type ?? ''), 'image')) $icon = 'bx-image';
    }
  @endphp
  <li class="hanzo-doc-item">
    <div class="hanzo-doc-icon"><i class="bx {{ $icon }}"></i></div>
    <div class="flex-grow-1">
      <span class="hanzo-doc-name">{{ $name }}</span>
      @if($date)<span class="hanzo-doc-meta d-block">{{ $date }}</span>@endif
    </div>
    <a href="{{ $url }}" class="btn btn-sm btn-outline-primary" download>Download</a>
  </li>
  @empty
  <li class="hanzo-doc-item">
    <span class="text-muted">No documents</span>
  </li>
  @endforelse
</ul>
