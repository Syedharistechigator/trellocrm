@extends('layouts.app')@section('cxmTitle', 'Dashboard')

@section('content')

    <section class="content">
        <div class="">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Dashboard</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li> <li class="breadcrumb-item active">Dashboard</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <!-- =================Lead Dashboard================= -->
            @if(Auth::user()->type == 'lead')
                <div class="container-fluid">
                    <div class="form-row">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-today')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.fresh-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.upsale-month')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-2">
                                <div class="form-row">
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="state_w1 body shadow">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5>$ {{$data['yearlyIncome']}}</h5>
                                                    <span><i class="zmdi zmdi-balance"></i> Revenue</span>
                                                </div>
                                                <div>
                                                    <i class="zmdi zmdi-balance text-info animated infinite pulse" style="font-size:4rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="state_w1 body shadow">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5>$ {{$data['yearRefund']}}</h5>
                                                    <span><i class="zmdi zmdi-turning-sign"></i> Refund</span>
                                                </div>
                                                <div>
                                                    <i class="zmdi zmdi-turning-sign text-info animated infinite pulse" style="font-size:4rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="state_w1 body shadow">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5>$ {{$data['yearchargeback']}}</h5>
                                                    <span><i class="zmdi zmdi-refresh-sync-alert"></i> Chargeback</span>
                                                </div>
                                                <div>
                                                    <i class="zmdi zmdi-refresh-sync-alert text-info animated infinite pulse" style="font-size:4rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6">
                                        <div class="state_w1 body shadow">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h5>$ {{$data['totalExpense']}}</h5>
                                                    <span><i class="zmdi zmdi-hc-fw"></i> Total Expense</span>
                                                </div>
                                                <div>
                                                    <i class="zmdi zmdi zmdi-hc-fw text-info animated infinite pulse" style="font-size:4rem;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            @include('includes.dashboard.sale-report')
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong><i class="zmdi zmdi-chart"></i> Charge Back</strong> Report <?php echo date("Y"); ?>
                                    </h2>
                                </div>
                                <div class="body" style="min-height:392px;">
                                    <div id="chart-bar" class="c3_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{--                <div class="row clearfix">--}}
                    {{--                    <div class="col-md-12 col-lg-8">--}}
                    {{--                        <div class="row">--}}
                    {{--                            <div class="col-md-6">--}}
                    {{--                                @include('includes.dashboard.ppc-spending')--}}
                    {{--                            </div>--}}
                    {{--                            <div class="col-md-6">--}}
                    {{--                                @include('includes.dashboard.team-project')--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="col-lg-4 col-md-12">--}}
                    {{--                        @include('includes.dashboard.leads-status')--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                    <div class="row clearfix">
                        <div class="col-md-12 col-lg-6">
                            @include('includes.dashboard.brands')
                        </div>
                        <div class="col-lg-6 col-md-12">
                            @include('includes.dashboard.teams')
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-12">
                            @include('includes.dashboard.recent-payments')
                        </div>
                    </div>
                </div>
            @endif
            <!-- ==============End Lead Dashboard============= -->

            <!-- ====================== TM User Dashboard =================== -->
            @if(Auth::user()->type == 'tm-user')
                <div class="container-fluid">
                    <div class="form-row">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-today')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.fresh-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.upsale-month')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            @include('includes.dashboard.sale-report')
                        </div>
                    </div>
                    <div class="row clearfix">
                        <div class="col-lg-12">
                            @include('includes.dashboard.recent-payments')
                        </div>
                    </div>
                </div>
            @endif
            <!-- ============== End TM User Dashboard ===================== -->

            <!-- ====================== Staff Dashboard =================== -->
            @if(Auth::user()->type == 'staff')
                <div class="container-fluid">
                    <div class="form-row">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-today')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.payment-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.fresh-month')
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            @include('includes.dashboard.top-boxes.upsale-month')
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            @include('includes.dashboard.sale-report')
                        </div>
                    </div>
                    {{--                <div class="row clearfix">--}}
                    {{--                    <div class="col-md-6">--}}
                    {{--                        @include('includes.dashboard.team-project')--}}
                    {{--                    </div>--}}
                    {{--                    <div class="col-lg-6 col-md-12">--}}
                    {{--                        <div class="card">--}}
                    {{--                            <div class="header">--}}
                    {{--                                <h2><strong>Latest </strong> Projects Activity</h2>--}}
                    {{--                            </div>--}}
                    {{--                            <div class="body">--}}
                    {{--                                <div class="chat-widget">--}}
                    {{--                                    <ul class="list-unstyled">--}}
                    {{--                                    @foreach($data['clientComments'] as $comment)--}}
                    {{--                                        <li class="left">--}}
                    {{--                                            <img src="assets/images/xs/avatar3.jpg" class="rounded-circle" alt="">--}}
                    {{--                                            <ul class="list-unstyled chat_info">--}}
                    {{--                                                <li><small>{{$comment->creatorName}} {{$comment->created_at->diffForHumans()}}</small></li>--}}
                    {{--                                                <li>Project:&nbsp;<a class="text-purple font-weight-bold" href="#">{{$comment->projectName}}</a></li>--}}
                    {{--                                                <li><span class="message bg-blue">{{$comment->comment_text}}</span></li>--}}
                    {{--                                            </ul>--}}
                    {{--                                        </li>--}}
                    {{--                                    @endforeach--}}
                    {{--                                    </ul>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                    <div class="row clearfix">
                        <div class="col-lg-12">
                            @include('includes.dashboard.recent-payments')
                        </div>
                    </div>
                </div>
            @endif
            <!-- ============== End Staff Dashboard ============= -->
            <!-- ============================ Client Dashboard ======================= -->
            @if(Auth::user()->type == 'client')
                <div class="container-fluid">
                    <div class="form-row">
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="card widget_2 traffic">
                                <div class="body xl-blue">
                                    <h2 style="color:black;">{{$data['pendingProject']}}</h2>
                                    <h6>Projects - On Hold</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="card widget_2 sales">
                                <div class="body xl-purple">
                                    <h2 style="color:black;">{{$data['completeProject']}}</h2>
                                    <h6>Projects - Completed</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="card widget_2 email">
                                <div class="body xl-green">
                                    <h2 style="color:black;">{{$data['dueInvoicePayment']}}</h2>
                                    <h6>Invoices - Due</h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="card widget_2 domains">
                                <div class="body xl-pink">
                                    <h2 style="color:black;">{{$data['overdueInvoicePayment']}}</h2>
                                    <h6>Invoices - Overdue</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-6">
                            <div class="card">
                                <div class="header">
                                    <h2><strong>My</strong> Project</h2>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover theme-color">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($data['clientProject'] as $project)
                                            <tr>
                                                <td class="align-middle">{{$project->id}}</td>
                                                <td class="align-middle">
                                                    <a href="javascript:void(0)" class="text-purple">{{$project->project_title}}</a>
                                                </td>
                                                <td class="align-middle">{{$project->project_date_due}}</td>
                                                <td class="align-middle">
                                                    <span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h2><strong>Latest </strong> Projects Activity</h2>
                                </div>
                                <div class="body">
                                    <div class="chat-widget">
                                        <ul class="list-unstyled">
                                            @foreach($data['clientComments'] as $comment)
                                                <li class="left">
                                                    <img src="assets/images/xs/avatar3.jpg" class="rounded-circle" alt="">
                                                    <ul class="list-unstyled chat_info">
                                                        <li><small>{{$comment->creatorName}} {{$comment->created_at->diffForHumans()}}</small></li>
                                                        <li>Project:&nbsp;<a class="text-purple font-weight-bold" href="#">{{$comment->projectName}}</a></li>
                                                        <li><span class="message bg-blue">{{$comment->comment_text}}</span></li>
                                                    </ul>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- ============== End Client Dashboard ============= -->
            <!-- ============== PPC Dashboard ============= -->
            @if(Auth::user()->type == 'ppc')
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class='text-success text-center'>Dashboard Developement Inprogress</h2>
                        </div>
                    </div>
                </div>

            @endif
            <!-- ============== End Head of Brand Dashboard ============= -->
            <!-- ============== Head of Brand Dashboard ============= -->
            @if(Auth::user()->type == 'qa')
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="header">
                                    <h2>
                                        <strong><i class="zmdi zmdi-chart"></i> Charge Back</strong> Report <?php echo date("Y"); ?>
                                    </h2>
                                </div>
                                <div class="body" style="min-height:392px;">
                                    <div id="chart-bar" class="c3_chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- ============== End Head of Brand Dashboard ============= -->
        </div>
    </section>
@endsection

@push('cxmScripts')
    @include('script')
@endpush
