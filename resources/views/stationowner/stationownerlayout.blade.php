@extends('layouts.alluserdashboardlayout')

@section('page_title')
    @yield('stationowner_page_title')
@endsection

@section('head_section')
    @yield('stationowner_head_section')
@endsection


@section('page_content')
    @include('stationowner.leftsidebar')
    @yield('stationowner_page_content')
@endsection

@section('after_body_section')
    @yield('stationowner_after_body_section')
@endsection
