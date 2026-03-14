@extends('layouts.sneat.base')

@push('layout-css')
<link rel="stylesheet" href="{{ asset('assets/hanzo/hanzo-admin-mc.css') }}" />
@endpush

@section('sidebar')
@include('partials.sidebar-admin')
@endsection
