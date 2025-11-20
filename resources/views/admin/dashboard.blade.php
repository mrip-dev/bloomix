@extends('admin.layouts.app')

@section('panel')

@permit('admin.dashboard')

{{-- Widget Section (High Contrast Light Widgets) --}}
<div class="row gy-4 dashboard-widgets">

    {{-- Total Products --}}
    <div class="col-xxl-6 col-sm-6">
        <a href="{{ route('admin.product.index') }}" class="widget-card widget-light widget-blue">
            <div class="widget-icon">
                <i class="las la-truck-loading"></i>
            </div>
            <div class="widget-content">
                <p class="widget-title">Total Products</p>
                <h3 class="widget-value">{{ $widget['total_product'] }}</h3>
            </div>
        </a>
    </div>

    {{-- Total Customers --}}
    <div class="col-xxl-6 col-sm-6">
        <a href="{{ route('admin.customer.index') }}" class="widget-card widget-light widget-green">
            <div class="widget-icon">
                <i class="las la-check-circle"></i>
            </div>
            <div class="widget-content">
                <p class="widget-title">Total Delivered Orders</p>
                <h3 class="widget-value">{{ $widget['total_delivered_orders'] }}</h3>
            </div>
        </a>
    </div>

    {{-- Total Suppliers --}}
    <div class="col-xxl-6 col-sm-6">
        <a href="{{ route('admin.supplier.index') }}" class="widget-card widget-light widget-purple">
            <div class="widget-icon">
                <i class="las la-clock"></i>
            </div>
            <div class="widget-content">
                <p class="widget-title">Total Pending Orders</p>
                <h3 class="widget-value">{{ $widget['total_pending_orders'] }}</h3>
            </div>
        </a>
    </div>

    {{-- Total Categories --}}
    <div class="col-xxl-6 col-sm-6">
        <a href="{{ route('admin.product.category.index') }}" class="widget-card widget-light widget-orange">
            <div class="widget-icon">
                <i class="las la-tags"></i>
            </div>
            <div class="widget-content">
                <p class="widget-title">Total Categories</p>
                <h3 class="widget-value">{{ $widget['total_category'] }}</h3>
            </div>
        </a>
    </div>
</div>



@endpermit
@endsection


@push('script-lib')
{{-- Keeping original script libraries --}}
<script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
<script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/charts.js') }}"></script>
@endpush

@push('style-lib')
<link type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('style')
<style>
    /* --- General Dark Dashboard Styles --- */
    /* Assuming 'admin.layouts.app' provides the global dark background (#1a1a2e or similar) */

    /* Base Dark Card Style for Charts and Tables */
    .card.dark-card {
        background-color: #1f2038;
        /* Darker background for distinction */
        border: 1px solid #3d3f66;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.4);
        color: #f0f0f0;
    }

    .card.dark-card .card-header {
        background-color: transparent;
        border-bottom: 1px solid #3d3f66;
        padding: 1rem 1.5rem;
    }

    .card.dark-card .card-title {
        color: #f0f0f0;
        font-weight: 700;
    }

    /* Date Range Picker Button Styling (Dark Theme) */
    .date-range-picker-btn {
        background-color: #2e3053;
        border: 1px solid #3d3f66;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.3s;
        color: #a0a0a0;
        font-weight: 600;
    }

    .date-range-picker-btn:hover {
        border-color: #6366f1;
        color: #f0f0f0;
    }

    /* --- High Contrast Widget Card Styles (Light/Vibrant) --- */
    .widget-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.5rem;
        border-radius: 15px;
        text-decoration: none;
        color: #333;
        /* Dark text on light background */
        background-color: #f9fafb;
        /* Light base color */
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        /* More prominent shadow for pop-out effect */
    }

    .widget-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
    }

    .widget-icon {
        font-size: 3.2rem;
        margin-left: 1rem;
        opacity: 0.8;
        transition: opacity 0.3s;
    }

    .widget-card:hover .widget-icon {
        opacity: 1;
    }

    .widget-content {
        line-height: 1.2;
        flex-grow: 1;
    }

    .widget-value {
        font-size: 2.4rem;
        font-weight: 900;
        margin-bottom: 0;
        color: #1f2937;
        /* Dark value text */
    }

    .widget-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #4b5563;
        /* Subtle title text */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Icon & Hover Color Accents */
    .widget-blue {
        border-left: 5px solid #3b82f6;
        background: linear-gradient(90deg, #fff, #9cace7ff);
    }

    .widget-blue .widget-icon {
        color: #3b82f6;
    }

    .widget-green {
        border-left: 5px solid #10b981;
        background: linear-gradient(90deg, #fff, #9ce4c0ff);
    }

    .widget-green .widget-icon {
        color: #10b981;
    }

    .widget-purple {
        border-left: 5px solid #8b5cf6;
        background: linear-gradient(90deg, #fff, #c097d0ff);
    }

    .widget-purple .widget-icon {
        color: #8b5cf6;
    }

    .widget-orange {
        border-left: 5px solid #f97316;
        background: linear-gradient(90deg, #fff, #ebb987ff);
    }

    .widget-orange .widget-icon {
        color: #f97316;
    }


    /* --- Chart and Daterange Picker Overrides (Dark Theme) --- */
    .apexcharts-menu {
        min-width: 120px !important;
        background-color: #2e3053 !important;
        border: 1px solid #3d3f66 !important;
    }

    .widget-card {

        width: calc(100% / 1) !important;

    }

    .apexcharts-menu-item {
        color: #f0f0f0 !important;
    }

    .apexcharts-menu-item:hover {
        background-color: #6366f130 !important;
    }

    .daterangepicker.show-calendar {
        border: 1px solid #3d3f66;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
    }

    .daterangepicker.show-calendar .ranges {
        background: #2e3053;
        border-right: 1px solid #3d3f66;
    }

    .daterangepicker.show-calendar .ranges li {
        color: #f0f0f0;
        transition: background-color 0.2s;
    }

    .daterangepicker.show-calendar .ranges li.active,
    .daterangepicker.show-calendar .ranges li:hover {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
    }

    .daterangepicker.show-calendar .drp-selected {
        color: #6366f1;
        font-weight: bold;
    }

    .daterangepicker .calendar-table {
        background-color: #1f2038;
    }

    .daterangepicker .calendar-table td,
    .daterangepicker .calendar-table th {
        color: #f0f0f0;
        border-color: #3d3f66;
    }

    .daterangepicker .calendar-table td.available:hover,
    .daterangepicker .calendar-table td.active {
        background-color: #6366f1 !important;
        color: white !important;
    }

    .daterangepicker .calendar-table th.prev,
    .daterangepicker .calendar-table th.next {
        color: #6366f1;
    }
</style>
@endpush