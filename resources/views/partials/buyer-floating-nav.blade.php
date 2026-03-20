<nav class="buyer-floating-nav" aria-label="Quick actions">
  <a href="{{ route('buyer.rfqs.create') }}" class="buyer-floating-btn rfq-btn" title="{{ __('labels.request_quote') }}">
    <i class="bx bx-message-square-detail"></i>
    <span>RFQ</span>
  </a>
  <a href="#" class="buyer-floating-btn" id="buyer-scroll-top" title="{{ __('labels.back_to_top') ?? 'Back to top' }}">
    <i class="bx bx-chevron-up"></i>
    <span>TOP</span>
  </a>
</nav>
