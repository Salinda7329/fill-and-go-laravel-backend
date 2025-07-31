@extends('layouts.alluserdashboardlayout')

@section('page_title')
    @yield('customer_page_title')
@endsection

@section('head_section')
    @yield('customer_head_section')
@endsection


@section('page_content')
    @include('customer.leftsidebar')
    @yield('customer_page_content')
@endsection

@section('after_body_section')
    @yield('customer_after_body_section')
@endsection
