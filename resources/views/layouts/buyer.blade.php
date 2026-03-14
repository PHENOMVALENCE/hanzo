@extends('layouts.sneat.base')

@push('layout-css')
<link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-buyer-b2b.css') }}" />
@endpush

@section('navbar')
@include('partials.topbar-buyer-b2b')
@endsection

@section('sidebar')
@include('partials.sidebar-buyer-b2b')
@endsection
