@props(['label' => null])
<span class="hanzo-badge-verified" {{ $attributes }}>
  <i class="bx bx-check-shield"></i>
  {{ $label ?? $slot ?? __('Verified') }}
</span>
