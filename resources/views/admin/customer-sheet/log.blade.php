@extends('admin.layouts.app')@section('cxmTitle', 'Customer Sheet Log')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Customer Sheet</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"> Customer Sheet</li>
                            <li class="breadcrumb-item active"> Log</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="LogTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Customer Id</th>
                                        <th class='text-nowrap'>Customer Name</th>
                                        <th class='text-nowrap'>Customer Email</th>
                                        <th class='text-nowrap'>Customer phone</th>
                                        <th class='text-nowrap'>Order Date</th>
                                        <th class='text-nowrap'>Order Type</th>
                                        <th class='text-nowrap'>Filling</th>
                                        <th class='text-nowrap'>Amount Charged</th>
                                        <th class='text-nowrap'>Order Status</th>
                                        <th class='text-nowrap'>Communication</th>
                                        <th class='text-nowrap'>Project Assigned</th>
                                        <th class='text-nowrap'>Action</th>
                                        <th class='text-nowrap'>Action By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($logs as $log)
                                        <tr>
                                            <td class="align-middle">{{$log->id}}</td>
                                            <td class="align-middle">{{optional($log->loggable)->customer_id ?? ""}}</td>
                                            <td class="align-middle" title="{{ optional($log->actor)->name ? 'Customer Name: ' . optional($log->actor)->name : ''}}" style="{{ optional($log->actor)->name ? 'cursor: pointer' : '' }}">{{optional($log->loggable)->customer_name}}</td>
                                            <td class="align-middle" title="{{ optional($log->actor)->email ? 'Customer Email: ' . optional($log->actor)->email : ''}}" style="{{ optional($log->actor)->email ? 'cursor: pointer' : '' }}">{{optional($log->loggable)->customer_email}}</td>
                                            <td class="align-middle" title="{{ optional($log->actor)->phone ? 'Customer Phone: ' . optional($log->actor)->phone : ''}}" style="{{ optional($log->actor)->phone ? 'cursor: pointer' : '' }}">{{optional($log->loggable)->customer_phone}}</td>
                                            <td class="align-middle text-nowrap">{{Carbon\Carbon::parse(optional($log->loggable)->order_date)->format('j F, Y')}}</td>
                                            <td class="align-middle">
                                                {{ optional($log->loggable)->order_type === 0 ? 'none' :
                                                   (optional($log->loggable)->order_type === 1 ? 'copyright' :
                                                   (optional($log->loggable)->order_type === 2 ? 'trademark' :
                                                   (optional($log->loggable)->order_type === 3 ? 'attestation' : ''))) }}
                                            </td>
                                            <td class="align-middle">
                                                {{ optional($log->loggable)->filling === 0 ? 'none' :
                                                   (optional($log->loggable)->filling === 1 ? 'logo' :
                                                   (optional($log->loggable)->filling === 2 ? 'slogan' :
                                                   (optional($log->loggable)->filling === 3 ? 'business-name' : ''))) }}
                                            </td>
                                            <td class="align-middle text-center">$ {{optional($log->loggable)->amount_charged}}</td>
                                            <td class="text-center align-middle">
                                                @if(optional($log->loggable)->order_status === 1)
                                                    <span class="badge bg-grey rounded-pill">requested</span>
                                                @elseif(optional($log->loggable)->order_status === 2)
                                                    <span class="badge bg-amber rounded-pill">applied</span>
                                                @elseif(optional($log->loggable)->order_status === 3)
                                                    <span class="badge bg-amber rounded-pill">received</span>
                                                @elseif(optional($log->loggable)->order_status === 4)
                                                    <span class="badge bg-pink rounded-pill">Refund</span>
                                                @elseif(optional($log->loggable)->order_status === 5)
                                                    <span class="badge bg-red rounded-pill">objection</span>
                                                @elseif(optional($log->loggable)->order_status === 6)
                                                    <span class="badge bg-red rounded-pill">delivered</span>
                                                @elseif(optional($log->loggable)->order_status === 7)
                                                    <span class="badge badge-success rounded-pill">approved</span>
                                                @else
                                                    <span class="badge badge-success rounded-pill">none</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                {{ optional($log->loggable)->communication === 0 ? 'none' :
                                                   (optional($log->loggable)->communication === 1 ? 'out-of-reached' :
                                                   (optional($log->loggable)->communication === 2 ? 'skeptic' :
                                                   (optional($log->loggable)->communication === 3 ? 'satisfied' :
                                                   (optional($log->loggable)->communication === 4 ? 'refunded' :
                                                   (optional($log->loggable)->communication === 5 ? 'refund-requested' :
                                                   (optional($log->loggable)->communication === 6 ? 'do-not-call' :
                                                   (optional($log->loggable)->communication === 7 ? 'not-interested' : ''))))))) }}
                                            </td>
                                            <td class="align-middle">{{optional($log->loggable)->project_assigned}}</td>
                                            <td class="align-middle">{{$log->action}}</td>
                                            <td class="align-middle">{{ optional($log->actor)->name }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('cxmScripts')
    @include('admin.customer-sheet.script')

    <script>
        $(document).ready(function () {
            $('#LogTable').DataTable().destroy();
            $('#LogTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true
            });
            $('[type=search]').attr('id', "dt-search-box");
        });
    </script>

@endpush
