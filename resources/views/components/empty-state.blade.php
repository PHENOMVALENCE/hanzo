@props(['icon' => 'bx-folder-open', 'title' => 'No data', 'text' => null, 'action' => null, 'actionLabel' => null, 'actionUrl' => null])

<div class="hanzo-empty-state">
  <div class="hanzo-empty-state-icon">
    <i class="bx {{ $icon }}"></i>
  </div>
  <p class="hanzo-empty-state-title">{{ $title }}</p>
  @if($text)
  <p class="hanzo-empty-state-text">{{ $text }}</p>
  @endif
  @if($action || $actionLabel)
  <div>
    {{ $action ?? ( $actionUrl ? '<a href="' . e($actionUrl) . '" class="btn btn-hanzo-primary btn-sm">' . e($actionLabel) . '</a>' : '' ) }}
  </div>
  @endif
</div>
