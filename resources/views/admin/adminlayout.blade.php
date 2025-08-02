@extends('layouts.alluserdashboardlayout')

@section('page_title')
    @yield('admin_page_title')
@endsection

@section('head_section')
    @yield('admin_head_section')
@endsection


@section('page_content')
    @include('admin.leftsidebar')
    @yield('admin_page_content')
@endsection

@section('after_body_section')
    @yield('admin_after_body_section')
@endsection
