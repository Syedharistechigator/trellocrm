@extends('admin.layouts.app')@section('cxmTitle', 'Stats')

@push('stats-css')
    <!-- Typography CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/stats/style-stats.css') }}">
@endpush

@section('content')

    <section class="content stats-sec">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Stats</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                            </a></li> <li class="breadcrumb-item active">Stats </li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button">
                        <i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="card">
            <div class="body">
                <form id="searchForm">
                    @csrf
                    <div class="row clearfix">
                        <div class="col-lg-4 col-md-6">
                            <label for="search-team">Team</label>
                            <select id="search-team" name="teamKey" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Team" data-live-search="true" data-live-search-id="team-search">
                                <option value="">All Teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->team_key }}" {{ request('teamKey') == $team->team_key ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="search-month">Month</label>
                            <select id="search-month" name="month" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Month" data-live-search="true" data-live-search-id="team-search">
                                <option value="">All Months</option>
                                @foreach(config('app.months') as $m_key =>$m)
                                    <option value="{{ $m }}" {{ (request('month') == $m || (empty(request('month')) && Carbon\Carbon::now()->format('F') == $m)) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label for="search-year">Year</label>
                            <select id="search-year" name="year" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Year" data-live-search="true" data-live-search-id="team-search">
                                <option value="">All Years</option>
                                @for($y = 2021; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ (request('year') == $y || (empty(request('year')) && Carbon\Carbon::now()->year == $y)) ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Gross Sales</h5>
                                    <span class="badge badge-danger">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($gross_amount, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Gross Sales</p>
                                    <span class="text-danger">{{ number_format($gross_sales, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-danger" data-percent="{{ max(0, min(number_format($gross_sales, 2) , 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($gross_sales, 2) , 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 small-heading">Charge Back</h5>
                                    <span class="badge badge-info">This Month</span>
                                </div>
                                <h3 class="box-heading">
                                    <span class="counter">${{ number_format($charge_back, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Percentage </p>
                                    <span class="text-info">{{ number_format(-$average_charge_back_gross_sales, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-info" data-percent="{{max(0, min(number_format($average_charge_back_gross_sales, 2), 100)) }}" style="transition: width 2s; width: {{max(0, min(number_format($average_charge_back_gross_sales, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 small-heading">Refund</h5>
                                    <span class="badge badge-info">This Month</span>
                                </div>
                                <h3 class="box-heading">
                                    <span class="counter">${{ number_format($refund, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Percentage </p>
                                    <span class="text-info">{{ number_format(-$average_refund_gross_sales, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-info" data-percent="{{max(0, min(number_format($average_refund_gross_sales, 2), 100)) }}" style="transition: width 2s; width: {{max(0, min(number_format($average_refund_gross_sales, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 d-none">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Net Sales</h5>
                                    <span class="badge badge-primary">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($net_sales, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Target</p>
                                    <span class="text-primary">{{ number_format($net_sales_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-primary" data-percent="{{ max(0, min(number_format($net_sales_percentage, 2), 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($net_sales_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 small-heading">Target</h5>
                                    <span class="badge badge-warning">This Month</span>
                                </div>
                                <h3 class="box-heading">
                                    <span class="counter">${{ number_format($target_amount, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Achieved</p>
                                    <span class="text-warning">{{ number_format($net_sales_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-warning" data-percent="{{ max(0, min(number_format($net_sales_percentage, 2), 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($net_sales_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 small-heading">Spending</h5>
                                    <span class="badge badge-primary">Limit: ${{ number_format($team_spending_limit, 2) }}</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($team_spending_amount, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between mt-1">
                                    <p class="mb-0">Spent</p>
                                    <span class="text-primary">{{ number_format($team_spending_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-primary" data-percent="{{ max(0, min(number_format($team_spending_percentage, 2), 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($team_spending_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">ROAS</h5>
                                    <span class="badge badge-warning">Target : +{{ceil($roas_target_exp)}}%</span>
                                </div>
                                <h3 class="box-heading"><span class="counter">{{($roas >= 0 ? '+' : '-' ).  ceil($roas) }}</span></h3>
{{--                                <div class="d-flex align-items-center justify-content-between mt-1">--}}
{{--                                    <p class="mb-0">Acheived</p>--}}
{{--                                    <span class="text-warning">{{($roas >= 0 ? '+' : '-' ).  number_format($roas, 2) }}%</span>--}}
{{--                                </div>--}}
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-warning" data-percent="{{ max(0, min(ceil($roas_percentage), 100)) }}" style="transition: width 2s; width: {{ max(0, min(ceil($roas_percentage), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 small-heading">Accounts</h5>
                                    <span class="badge badge-info">Target : {{ intval($account_target) }}</span>
                                </div>
                                <h3 class="box-heading"><span class="counter">{{ intval($fresh_accounts) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Fresh Accounts</p>
                                    <span class="text-info">{{max(0, min(number_format($fresh_accounts_percentage, 2), 100))}}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-info" data-percent="{{max(0, min(number_format($fresh_accounts_percentage, 2), 100))}}" style="transition: width 2s; width: {{max(0, min(number_format($fresh_accounts_percentage, 2), 100))}}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Orders</h4>
                        </div>
                        <div class="iq-card-header-toolbar d-flex align-items-center">
                            <div class="dropdown">
                            <span class="dropdown-toggle text-primary" id="dropdownMenuButton5" data-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-2-fill"></i>
                            </span>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton5" style="">
                                    <a class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>
                                    <a class="dropdown-item" href="#"><i class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                    <a class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                    <a class="dropdown-item" href="#"><i class="ri-printer-fill mr-2"></i>Print</a>
                                    <a class="dropdown-item" href="#"><i class="ri-file-download-fill mr-2"></i>Download</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <!-- <div id="iq-chart-order" class="amcharts-chart-div" style="height: 400px;"></div> -->
                        <div id="apex-mixed-chart" class="amcharts-chart-div" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    {{-- <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">

                                <h5 class="mb-0 small-heading">Required (Avg Sales/day)</h5>
                                <span class="badge badge-danger">Per Day</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">27,640</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0">New Leads</p>
                                <span class="text-danger">50%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                <span class="bg-danger" data-percent="50" style="transition: width 2s; width: 50%;"></span>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">3rd Party Payment</h5>
                                    <span class="badge badge-primary">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($third_party_payments, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">Net Sales %</p>
                                    <span class="text-primary">{{ number_format($third_party_payments_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-primary" data-percent="{{ max(0, min(number_format($third_party_payments_percentage, 2), 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($third_party_payments_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Over All Profit or Loss</h5>
                                    <span class="badge badge-danger">This month</span>
                                </div>
                                <h3 class="box-heading">{{ $over_all_is_profit ? '' : '-' }}$<span class="counter">{{ number_format($over_all_profit, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">{{ $over_all_is_profit ? 'Profit' : 'Loss' }}</p>
                                    <span class="text-danger">{{ $over_all_is_profit ? '' : '-' }}{{ number_format($over_all_profit_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-danger" data-percent="{{max(0, min(number_format($over_all_profit_percentage, 2), 100))}}" style="transition: width 2s; width: {{max(0, min(number_format($over_all_profit_percentage, 2), 100))}}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between">
                                <h5 class="mb-0 small-heading">Accounts</h5>
                                <span class="badge badge-info">Target : 150</span>
                                </div>
                                <h3 class="box-heading"><span class="counter">35</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0">Fresh Accounts</p>
                                <span class="text-info">30%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                <span class="bg-info" data-percent="25" style="transition: width 2s; width: 25%;"></span>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Current Profit / Loss</h5>
                                    <span class="badge badge-primary">This Month</span>
                                </div>
                                <h3 class="box-heading">{{ $current_is_profit ? '' : '-' }}$<span class="counter">{{ number_format($current_profit, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">{{ $current_is_profit ? 'Profit' : 'Loss' }}</p>
                                    <span class="text-primary">{{ $current_is_profit ? '' : '-' }}{{ number_format($current_profit_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-primary" data-percent="{{max(0, min(number_format($current_profit_percentage, 2), 100))}}" style="transition: width 2s; width: {{ max(0, min(number_format($current_profit_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Remaining Sales for BE</h5>
                                    <span class="badge badge-warning">This Month</span>
                                </div>
                                <h3 class="box-heading">{{ $remaining_be_is_profit ? '' : '-' }}$<span class="counter">{{ number_format($remaining_be, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
                                    <p class="mb-0">{{ $remaining_be_is_profit ? 'Profit' : 'Loss' }}</p>
                                    <span class="text-warning">{{ $remaining_be_is_profit ? '' : '-' }}{{ number_format($remaining_be_percentage, 2) }}%</span>
                                </div>
                                <div class="iq-progress-bar mt-3">
                                    <span class="bg-warning" data-percent="{{ max(0, min(number_format($remaining_be_percentage, 2), 100)) }}" style="transition: width 2s; width: {{ max(0, min(number_format($remaining_be_percentage, 2), 100)) }}%;"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body daysworking">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Total Working Days</h5>
                                </div>
                                <h3 class="box-heading"><span class="counter badge-info d-inline-block mt-1">23</span></h3>
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Remaining Working Days</h5>
                                </div>
                                <h3 class="box-heading"><span class="counter badge-danger d-inline-block mt-1">12</span></h3>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Projected Net Sales</h5>
                                    <span class="badge badge-danger">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($projected_net_sales, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Fixed costing</h5>
                                    <span class="badge badge-info">This month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($fixed_costing_amount, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Carry Forward</h5>
                                    <span class="badge badge-warning">This month</span>
                                </div>
                                <h3 class="box-heading">-$<span class="counter">{{ number_format($carry_forward_amount, 2) }}</span>
                                </h3>
                                <div class="d-flex align-items-center justify-content-between"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body daysworking">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Total Working Days</h5>
                                    <h3 class="box-heading">
                                        <span class="counter badge-info d-inline-block mt-1">{{ $total_working_days }}</span>
                                    </h3>
                                </div>
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Remaining Working Days</h5>
                                    <h3 class="box-heading">
                                        <span class="counter badge-danger d-inline-block mt-1">{{ $remaining_working_days }}</span>
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Current ( Avg. Sales/Day )</h5>
                                    <span class="badge badge-primary">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($current_day_avg, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
{{--                                    <p class="mb-0">Achieved</p>--}}
{{--                                    <span class="text-primary">{{ number_format($current_day_percentage, 2) }}%</span>--}}
                                </div>
{{--                                <div class="iq-progress-bar mt-3">--}}
{{--                                    <span class="bg-primary" data-percent="{{max(0, min(number_format($current_day_percentage, 2), 100))}}" style="transition: width 2s; width: {{max(0, min(number_format($current_day_percentage, 2), 100))}}%;"></span>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height">
                            <div class="iq-card-body">
                                <div class="top-block d-flex align-items-center justify-content-between mt-1">
                                    <h5 class="mb-0 small-heading">Required ( Sales/Day )</h5>
                                    <span class="badge badge-primary">This Month</span>
                                </div>
                                <h3 class="box-heading">$<span class="counter">{{ number_format($req_sales_per_day, 2) }}</span></h3>
                                <div class="d-flex align-items-center justify-content-between">
{{--                                    <p class="mb-0">Achieved</p>--}}
{{--                                    <span class="text-primary">{{ number_format($current_day_percentage, 2) }}%</span>--}}
                                </div>
{{--                                <div class="iq-progress-bar mt-3">--}}
{{--                                    <span class="bg-primary" data-percent="{{max(0, min(number_format($percentage_achieved, 2), 100))}}" style="transition: width 2s; width: {{max(0, min(number_format($percentage_achieved, 2), 100))}}%;"></span>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@push('cxmScripts')
    <script src="{{ asset('assets/js/pages/stats/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/lottie.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/core.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/chart.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/animated.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/maps.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/worldLow.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/morris.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/style-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/pages/stats/chart-custom.js') }}"></script>

    @include('admin.stats.script')
@endpush
