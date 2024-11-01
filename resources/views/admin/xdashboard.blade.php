@extends('admin.layouts.app')

@section('cxmTitle', 'Dashboard')

@section('content')

<section class="content">
    <div class="">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Dashboard</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="zmdi zmdi-home"></i> Techigator</a></li>
                        <li class="breadcrumb-item active">Dashboard </li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">                
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="form-row">
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card widget_2 traffic">
                        <div class="body xl-blue">
                            <h2 style="color:black;">${{$data['todayPayment']}}</h2>
                            <h6>Payments - Today</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card widget_2 sales">
                        <div class="body xl-purple">
                            <h2 style="color:black;">${{$data['monthPayment']}}</h2>
                            <h6>Payments - Month</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card widget_2  email">
                        <div class="body xl-green">
                            <h2 style="color:black;">${{$data['dueInvoicePayment']}}</h2>
                            <h6>FRESH  - Month</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <div class="card widget_2 domains">
                        <div class="body xl-pink">
                            <h2 style="color:black;">${{$data['overdueInvoicePayment']}}</h2>
                            <h6>UPSALE  - Month</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong><i class="zmdi zmdi-chart"></i> Sales</strong> Report <?php echo date("Y"); ?></h2>
                                <ul class="header-dropdown">
                                    <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                        <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <li><a href="javascript:void(0);">View All</a></li>
                                        </ul>
                                    </li>
                                    <li class="remove">
                                        <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                    </li>
                                </ul>
                        </div>
                        <div class="form-row">
                            <div class="col-md-3">
                                <div class="body mb-3 shadow">
                                    <div class="state_w1 mb-1 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5>$ {{$data['yearlyIncome']}}</h5>
                                                <span><i class="zmdi zmdi-balance"></i> Revenue</span>
                                            </div>
                                            <div><i class="zmdi zmdi-balance text-warning animated infinite pulse" style="font-size:4rem;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="body mb-3 shadow">
                                    <div class="state_w1 mb-1 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <div>                                
                                                <h5>$ {{$data['yearRefund']}}</h5>
                                                <span><i class="zmdi zmdi-turning-sign zmdi-hc-flip-horizontal"></i> Refund</span>
                                            </div>
                                            <div><i class="zmdi zmdi-turning-sign zmdi-hc-flip-horizontal text-warning animated infinite pulse" style="font-size:4rem;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="body mb-3 shadow">
                                    <div class="state_w1 mb-1 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h5>$ {{$data['yearchargeback']}}</h5>
                                                <span><i class="zmdi zmdi-refresh-sync-alert"></i> Charge Back</span>
                                            </div>
                                            <div><i class="zmdi zmdi-refresh-sync-alert text-warning animated infinite pulse" style="font-size:4rem;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="body mb-3 shadow">
                                    <div class="state_w1 mb-1 mt-1">
                                        <div class="d-flex justify-content-between">
                                            <div>                            
                                                <h5>$ {{$data['yearExpence']}}</h5>
                                                <span><i class="zmdi zmdi-balance-wallet"></i> Total Expense</span>
                                            </div>
                                            <div><i class="zmdi zmdi zmdi-balance-wallet text-warning animated infinite pulse" style="font-size:4rem;"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="body">
                                    <div id="chart-area-spline-sracked"></div>
                                </div>
                            </div> 
                            <div class="col-md-6">
                                <div class="body">
                                    <div id="chart-bar" class="c3_chart"></div>
                                </div>
                            </div>    
                        </div>    
                    </div>
                </div>
            </div> 
            <div class="row">
                <div class="col-md-12 col-lg-8">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="header">
                                    <h2><strong><i class="zmdi zmdi-google"></i> PPC</strong> Spendings</h2>
                                    <ul class="header-dropdown">
                                        <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                            <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <li><a href="javascript:void(0);">View All</a></li>
                                            </ul>
                                        </li>
                                        <li class="remove">
                                            <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="body">
                                    <div class="form-row align-items-center">
                                        <div class="col-md-6">
                                            <h3>$ {{$data['yearSpending']}}</h3>
                                            <h5>$ {{$data['monthSpending']}}</h5>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-center">
                                                <input type="text" class="knob" data-linecap="round" value="{{$data['monthSpending']}}" data-width="125" data-height="125" data-thickness="0.25" data-fgColor="#64c8c0" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="border-warning mb-5">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="card w_data_1">
                                                <div class="xbody">
                                                    <div class="w_icon pink"><i class="zmdi zmdi-google"></i></div>
                                                    <h4 class="mt-3 mb-0">$ {{$data['googleSpending']}}</h4>
                                                    <span class="text-muted">Google</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card w_data_1">
                                                <div class="xbody">
                                                    <div class="w_icon cyan"><i class="zmdi zmdi-blogger"></i></div>
                                                    <h4 class="mt-3 mb-0">$ {{$data['bingSpending']}}</h4>
                                                    <span class="text-muted">Bing</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="header">
                                <h2><strong><i class="zmdi zmdi-google"></i> Brands</strong> Spendings</h2>
                                    <ul class="header-dropdown">
                                        <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                            <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <li><a href="javascript:void(0);">View All</a></li>
                                            </ul>
                                        </li>
                                        <li class="remove">
                                            <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="cxm-slim-scroll">
                                    <div class="form-row align-items-center">
                                        @foreach($data['brandData'] as $brand)
                                        <div class="col-md-6">
                                            <div class="body mb-2">
                                                <div class="form-row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="text-warning d-flex justify-content-center align-items-center">
                                                            <i class="zmdi zmdi-globe-alt zmdi-hc-spin" style="font-size:4rem;"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <h6>{{$brand->name}}</h6>
                                                        <div><i class="zmdi zmdi-money"></i> {{$brand->spending}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong><i class="zmdi zmdi-account-box-phone"></i> Leads</strong> Status</h2>
                                <ul class="header-dropdown">
                                    <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                        <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <li><a href="javascript:void(0);">View All</a></li>
                                        </ul>
                                    </li>
                                    <li class="remove">
                                        <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                    </li>
                                </ul>
                        </div>
                        <div class="body text-center">
                            <div id="chart-pie" class="c3_chart d_distribution" style="height:350px;"></div>                            
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                <div class="card">
                    <div class="header">
                        <h2><strong><i class="zmdi zmdi-chart"></i>  Project</strong> Statistics</h2>
                        <ul class="header-dropdown">
                                    <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                        <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                            <li><a href="javascript:void(0);">View All</a></li>
                                        </ul>
                                    </li>
                                    <li class="remove">
                                        <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                    </li>
                        </ul>
                    </div>
                    <div class="body">
                        <div class="row">            
                            @foreach($data['projectStatus'] as $status)
                            <div class="col-md-6">
                                <div class="form-row align-items-center">
                                    <div class="col-auto">
                                        <div class="p-3 bg-{{$status->status_color}} shadow rounded-circle d-flex justify-content-center align-items-center" style="width:40px; height:40px; border:2px #fd4f33 solid;">
                                            <h4 class="my-0 text-white">{{$status->count}}</h4>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6>{{$status->status}}</h6>                
                                    </div>
                                </div>
                                <hr>
                            </div>
                            @endforeach
                        </div>                                        
                    </div>
                    <div class="body" style="margin-top:10px;">
                        <div class="row">            
                            @foreach($data['projectcategoriesdata'] as $category)
                            <div class="col-md-6">
                                <div class="form-row align-items-center">
                                    <div class="col-auto">
                                        <div class="p-3 bg-purple shadow rounded-circle d-flex justify-content-center align-items-center" style="width:40px; height:40px; border:2px #999 solid;">
                                            <h4 class="my-0 text-white">{{$category->count}}</h4>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h6>{{$category->name}}</h6>                
                                    </div>
                                </div>
                                <hr class="my-2">
                            </div>
                            @endforeach
                        </div>                                        
                    </div>
                </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="header">
                            <h2><strong><i class="zmdi zmdi-group"></i> RECENT</strong> Project</h2>
                            <ul class="header-dropdown">
                                <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                    <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                        <li><a href="javascript:void(0);">View All</a></li>
                                    </ul>
                                </li>
                                <li class="remove">
                                    <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                </li>
                            </ul>
                        </div>
                        <div class="cxm-slim-scrollx">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover theme-color" data-sorting="false">
                                    <thead>
                                    <tr>                                       
                                        <th>Title</th>
                                        <th>Brand</th>                                        
                                        <th>Sales Agent</th>
                                        <th>Account Manager</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th class="hidden-md-down">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['recentProject'] as $project)
                                        <tr>
                                            <td class="align-middle">
                                                <a class="text-warning" href="{{route('adminproject.show', $project->id)}}"><i class="zmdi zmdi-open-in-new"></i> <strong>{{$project->project_title}}</strong></a>
                                                <div><small>Cost: ${{$project->project_cost}}</small></div>
                                            </td>
                                            <td class="align-middle">{{$project->brandName}}</td>
                                            <td class="align-middle">
                                                <div class="form-row">
                                                    <div class="col-auto">
                                                        <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->agentImage?$project->agentImage :asset('assets/images/crown.png') !!}" alt="{{$project->agentName}}">
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-warning">{{$project->agentName}}</div>
                                                        <small>{{$project->agentDesignation}}</small>
                                                    </div>
                                                </div>
                                            </td>  
                                            <td class="align-middle">
                                                <div class="form-row">
                                                    <div class="col-auto">
                                                        <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->pmImage?$project->pmImage :asset('assets/images/crown.png') !!}" alt="{{$project->pmName}}">
                                                    </div>
                                                    <div class="col">
                                                        <div class="text-warning">{{$project->pmName}}</div>
                                                        <small>{{$project->pmDesignation}}</small>
                                                    </div>
                                                </div>
                                            </td>                                                                        
                                            <td class="align-middle">{{$project->project_date_start}}</td>
                                            <td class="align-middle">{{$project->project_date_due}}</td>                                        
                                            <td class="align-middle"><span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span></td>
                                        </tr>
                                        @endforeach  
                                    </tbody>
                                </table>
                            </div>
                        </div>              
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong><i class="zmdi zmdi-balance"></i> Recent</strong> Payments</h2>
                                    <ul class="header-dropdown">
                                        <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                            <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <li><a href="javascript:void(0);">View All</a></li>
                                            </ul>
                                        </li>
                                        <li class="remove">
                                            <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                        </li>
                                    </ul>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover theme-color" data-sorting="false">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Brand Name</th>
                                            <th>Team</th>
                                            <th>Client</th>                                    
                                            <th>Amount</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['recentPayments'] as $payment)   
                                        <tr>
                                            <td>{{$payment->id}}</td>
                                            <td>{{$payment->brandName}}</td>
                                            <td>{{$payment->teamName}}</td>
                                            <td>{{$payment->name}}</td>
                                            <td>${{$payment->amount}}</td>
                                            <td class="text-center"><span class="badge badge-success rounded-pill">Paid</span></td>
                                        </tr>
                                        @endforeach                                    
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-lg-6">
                    <div class="card">
                        <div class="header">
                            <h2><strong><i class="zmdi zmdi-blogger"></i> Techigator</strong> Brands</h2>
                                <ul class="header-dropdown">
                                        <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                            <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <li><a href="javascript:void(0);">View All</a></li>
                                            </ul>
                                        </li>
                                        <li class="remove">
                                            <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                        </li>
                                </ul>
                        </div>
                        <div class="cxm-slim-scroll">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover theme-color" data-sorting="false">
                                    <thead>
                                        <tr>
                                            <th>Brand</th>
                                            <th></th>
                                            <th>Last Month</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['brandData'] as $brand)
                                        <tr>
                                            <td class="w70"><img class="w50" src="{!! $brand->logo !!}" alt=""></td>
                                            <td><a class="text-warning" href="javascript:void(0)">{{$brand->name}}</a></td>
                                            <td>${{$brand->lastmonthamount}}</td>
                                            <td>${{$brand->amount}}
                                                {!! ($brand->amount > $brand->lastmonthamount)?'<i class="zmdi zmdi-trending-up text-success"></i>' :'<i class="zmdi zmdi-trending-down text-warning"></i>' !!}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>              
                    </div>      
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong><i class="zmdi zmdi-accounts-outline"></i> Techigator</strong> Teams</h2>
                            <ul class="header-dropdown">
                                        <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                            <ul class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(33px, 34px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <li><a href="javascript:void(0);">View All</a></li>
                                            </ul>
                                        </li>
                                        <li class="remove">
                                            <a role="button" class="boxs-close"><i class="zmdi zmdi-close"></i></a>
                                        </li>
                            </ul>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover theme-color" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Team</th>
                                        <th>Last Month</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['teamData'] as $team)
                                    <tr>
                                        <td><a href="javascript:void(0)" class="text-warning">{{$team->name}}</a></td>
                                        <td>${{$team->lastmonthamount}}</td>
                                        <td>${{$team->amount}}
                                            {!! ($team->amount > $team->lastmonthamount)?'<i class="zmdi zmdi-trending-up text-success"></i>' :'<i class="zmdi zmdi-trending-down text-warning"></i>' !!}                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>              
                    </div> 
                </div>
            </div>
    </div>
</section>

@endsection

@push('cxmScripts')
    @include('admin.script')
    <script>
        $(function(){
            $('.cxm-slim-scroll').slimScroll({
                height: '390px',
                size: '4px',
                borderRadius: '3px',
                railBorderRadius: '0',
                alwaysVisible: true,
            });
        });
    </script>
@endpush