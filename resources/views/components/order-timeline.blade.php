@props(['order', 'canApprove' => false, 'canUpdateProduction' => false])

@php
  $steps = [
    'awaiting_factory_approval' => ['label' => trans_status('awaiting_factory_approval'), 'action' => 'Factory approves order'],
    'in_production' => ['label' => trans_status('in_production'), 'action' => 'Factory updates production'],
    'ready_to_ship' => ['label' => trans_status('ready_to_ship'), 'action' => 'Ready for shipment'],
    'completed' => ['label' => trans_status('completed'), 'action' => 'Order delivered'],
  ];
  $stepKeys = array_keys($steps);
  $currentIdx = array_search($order->milestone_status, $stepKeys);
  if ($currentIdx === false) { $currentIdx = 0; }
@endphp

<div class="order-timeline mb-4">
  <h6 class="mb-3">Order Timeline</h6>
  <div class="hanzo-stepper">
    @foreach($stepKeys as $i => $key)
    <div class="step {{ $i < $currentIdx ? 'completed' : '' }} {{ $i === $currentIdx ? 'active' : '' }}">
      <div class="step-circle">{{ $i + 1 }}</div>
      <div class="small mt-1 {{ $i <= $currentIdx ? 'text-body' : 'text-muted' }}">{{ $steps[$key]['label'] }}</div>
      @if($i === $currentIdx && isset($steps[$key]['action']))
        <div class="text-muted small mt-0">{{ $steps[$key]['action'] }}</div>
      @endif
    </div>
    @endforeach
  </div>
</div>
