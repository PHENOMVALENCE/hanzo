@extends('layouts.sneat.base')

@push('layout-css')
<link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-factory-b2b.css') }}" />
@endpush

@section('navbar')
@include('partials.topbar-factory-b2b')
@endsection

@section('sidebar')
@include('partials.sidebar-factory-b2b')
@endsection
