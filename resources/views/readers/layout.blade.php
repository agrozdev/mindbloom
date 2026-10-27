@extends('layouts.app')

@section('meta_robots', 'noindex, nofollow')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/readers.css') }}?v={{ filemtime(public_path('css/readers.css')) }}" />
@endpush

@section('content')
  <div class="mad-breadcrumb">
    <div class="container wide">
      <h1 class="mad-page-title">@yield('page_heading')</h1>
    </div>
  </div>

  <div class="mad-content">
    <div class="container">
      <div class="reader-page mad-text-medium">
        @yield('page_body')
      </div>
    </div>
  </div>
@endsection
