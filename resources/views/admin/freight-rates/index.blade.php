@extends('layouts.admin')

@section('title', 'Freight Rates')

@section('content')
<h4 class="fw-bold mb-4">Freight Rates</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<p class="text-muted small mb-2">Sea freight (per CBM), Air freight (per KG), or per container. Used for cost estimation and quotations.</p>
<div class="d-flex flex-wrap gap-2 mb-3">
  <a href="{{ route('admin.freight-rates.create') }}" class="btn btn-primary">Add Rate</a>
  <a href="{{ route('admin.transport-defaults.edit') }}" class="btn btn-outline-secondary">Transport Defaults</a>
  <a href="{{ route('admin.estimate-defaults.edit') }}" class="btn btn-outline-secondary">Estimate Defaults (freight &amp; clearing brackets)</a>
</div>
<div class="card">
  <div class="card-body">
    @if($rates->isEmpty())
      <p class="text-muted mb-0">No freight rates. Add one to get started.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Method</th>
              <th>Destination</th>
              <th>Rate Type</th>
              <th>Rate</th>
              <th>Min Charge</th>
              <th>Active</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rates as $rate)
            <tr>
              <td>{{ $rate->method }}</td>
              <td>{{ $rate->destination_port ?? $rate->destination_city ?? '-' }}</td>
              <td>{{ $rate->rate_type }}</td>
              <td>{{ number_format($rate->rate_value, 2) }}</td>
              <td>{{ number_format($rate->min_charge, 2) }}</td>
              <td><span class="badge bg-{{ $rate->active ? 'success' : 'secondary' }}">{{ $rate->active ? 'Yes' : 'No' }}</span></td>
              <td>
                <a href="{{ route('admin.freight-rates.edit', $rate) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                <form method="POST" action="{{ route('admin.freight-rates.destroy', $rate) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      {{ $rates->links() }}
    @endif
  </div>
</div>
@endsection
