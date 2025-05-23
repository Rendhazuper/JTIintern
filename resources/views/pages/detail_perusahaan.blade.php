@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Perusahaan'])
    <div class="container-fluid py-4">
        <div class="card">
            <!-- existing content -->
        </div>
    </div>
@endsection

@push('css')
<link href="{{ asset('assets/css/data_perusahaan.css') }}" rel="stylesheet" />
@endpush
