@extends('layouts.buyer')

@section('title', 'Create RFQ')

@section('content')
<h4 class="fw-bold mb-4">Create RFQ</h4>
<form method="POST" action="{{ route('buyer.rfqs.store') }}" enctype="multipart/form-data">
  @csrf
  <div class="card mb-4">
    <div class="card-body">
      <div class="mb-3">
        <label class="form-label">Category *</label>
        <select name="category_id" class="form-select" required>
          <option value="">Select category</option>
          @foreach($categories as $c)
          <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Quantity *</label>
        <input type="number" name="quantity" class="form-control" required min="1" value="{{ old('quantity') }}">
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Specifications</label>
        <textarea name="specs" class="form-control" rows="2">{{ old('specs') }}</textarea>
      </div>
      <div class="row g-2">
        <div class="col-md-4">
          <label class="form-label">Timeline (weeks)</label>
          <input type="number" name="timeline_weeks" class="form-control" min="1" value="{{ old('timeline_weeks') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Delivery Country</label>
          <input type="text" name="delivery_country" class="form-control" value="{{ old('delivery_country') }}">
        </div>
        <div class="col-md-4">
          <label class="form-label">Delivery City</label>
          <input type="text" name="delivery_city" class="form-control" value="{{ old('delivery_city') }}">
        </div>
      </div>
      <div class="mt-3">
        <label class="form-label">Attachments</label>
        <input type="file" name="attachments[]" class="form-control" multiple>
      </div>
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit RFQ</button>
  <a href="{{ route('buyer.rfqs.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
@endsection
