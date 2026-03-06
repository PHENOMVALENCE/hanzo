@props(['status', 'label' => null])

@php
$statusMap = [
  'new' => 'hanzo-badge-new',
  'assigned' => 'hanzo-badge-assigned',
  'quoted' => 'hanzo-badge-quoted',
  'pricing_received' => 'hanzo-badge-quoted',
  'accepted' => 'hanzo-badge-accepted',
  'in_production' => 'hanzo-badge-in_production',
  'shipped' => 'hanzo-badge-shipped',
  'delivered' => 'hanzo-badge-delivered',
  'deposit_pending' => 'hanzo-badge-pending',
  'pending' => 'hanzo-badge-pending',
  'approved' => 'hanzo-badge-approved',
  'rejected' => 'hanzo-badge-rejected',
  'suspended' => 'hanzo-badge-suspended',
];
$class = $statusMap[strtolower($status ?? '')] ?? 'hanzo-badge-pending';
$displayLabel = $label ?? ucfirst(str_replace('_', ' ', $status ?? ''));
@endphp

<span class="hanzo-badge {{ $class }}" {{ $attributes->merge([]) }}>{{ $displayLabel }}</span>
