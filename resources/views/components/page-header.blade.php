@props(['title', 'breadcrumbs' => []])

<div class="hanzo-page-header d-flex flex-wrap justify-content-between align-items-start gap-3">
  <div>
    <h1 class="hanzo-page-title">{{ $title }}</h1>
    @if(!empty($breadcrumbs))
    <nav class="hanzo-breadcrumb" aria-label="Breadcrumb">
      @foreach($breadcrumbs as $i => $item)
        @if($i > 0)<span class="mx-1">/</span>@endif
        @if(isset($item['url']))
          <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
          <span>{{ $item['label'] ?? $item }}</span>
        @endif
      @endforeach
    </nav>
    @endif
  </div>
  @if(isset($actions))
  <div class="hanzo-page-header-actions">{{ $actions }}</div>
  @endif
</div>
