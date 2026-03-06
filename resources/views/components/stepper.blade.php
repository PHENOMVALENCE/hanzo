@props(['steps' => [], 'currentStatus' => null])

@php
$statusOrder = ['deposit_pending','accepted','in_production','shipped','delivered'];
$idx = $currentStatus ? array_search(strtolower($currentStatus), array_map('strtolower', $statusOrder)) : 0;
$idx = $idx !== false ? $idx : 0;
@endphp

<div class="hanzo-stepper" role="list" aria-label="Order progress">
  @foreach($steps as $i => $step)
    @php
      $stepStatus = is_array($step) ? ($step['status'] ?? $step['key'] ?? '') : $step;
      $stepLabel = is_array($step) ? ($step['label'] ?? ucfirst(str_replace('_',' ',$stepStatus))) : ucfirst(str_replace('_',' ',$step));
      $stepIdx = array_search(strtolower($stepStatus), array_map('strtolower', $statusOrder));
      $done = $stepIdx !== false && $stepIdx < $idx;
      $active = $stepIdx === $idx;
    @endphp
    <div class="hanzo-stepper-step {{ $done ? 'done' : ($active ? 'active' : 'pending') }}" role="listitem">
      @if($done)<i class="bx bx-check text-success"></i>@endif
      {{ $stepLabel }}
    </div>
    @if(!$loop->last)
    <div class="hanzo-stepper-connector {{ $done ? 'done' : '' }}"></div>
    @endif
  @endforeach
</div>
