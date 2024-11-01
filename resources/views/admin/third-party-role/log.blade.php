@extends('admin.layouts.app')@section('cxmTitle', 'Log')
@section('content')
    @push('css')
        @include('admin.third-party-role.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Log</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.third.party.role.index')}}"> Third Party Roles </a></li>
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
                                        <th class='text-nowrap'>Invoice #</th>
                                        <th>Team</th>
                                        <th>Client</th>
                                        <th>Order Id #</th>
                                        <th>Order Status</th>
                                        <th>Description</th>
                                        <th class='text-nowrap'>Amount</th>
                                        <th class='text-nowrap'>Transaction Id</th>
                                        <th>Merchant</th>
                                        <th>Payment Status</th>
                                        <th class='text-nowrap'>Action</th>
                                        <th class='text-nowrap'>Action By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($logs as $log)
                                        @php
                                            $loggable = optional($log->loggable);
                                            $action = $log->action;
                                            if (!$loggable->id) {
                                                $loggable = json_decode($log->previous_record, true);
                                                $invoice_num = App\Models\Invoice::where('invoice_key', $loggable['invoice_id'])->value('invoice_num');
                                                $team_name = App\Models\Team::where('team_key', $loggable['team_key'])->value('name');
                                                $client_name = App\Models\Client::where('id', $loggable['client_id'])->value('name');
                                                $action = "Permanently deleted";
                                            } else {
                                                $invoice_num = optional($loggable->getInvoice)->invoice_num;
                                                $team_name = optional($loggable->getTeam)->name;
                                                $client_name = optional($loggable->getClient)->name;
                                            }
                                        @endphp
                                        <tr>
                                            <td class="align-middle">{{$log->id}}</td>
                                            <td class="align-middle">
                                                <a class="text-warning invoice-trigger" data-invoice-num="{{ $invoice_num}}" href="#{{$invoice_num}}">{{$invoice_num}}</a>
                                                <div class="">
                                                    <span class="badge badge-info rounded-pill">{{ $loggable['invoice_id']}}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle">{{$team_name}}</td>
                                            <td class="align-middle">{{$client_name}}</td>
                                            <td class="align-middle">{{$loggable['order_id']??null}}</td>
                                            <td class="align-middle">{{$loggable['order_status']??null}}</td>
                                            <td class="align-middle td-make-desc-short" title="{{$loggable['description']}}">{{$loggable['description']}}</td>
                                            <td class="align-middle">${{$loggable['amount']}}</td>
                                            <td class="align-middle">{{$loggable['transaction_id']}}</td>
                                            <td class="align-middle">{{$loggable['merchant_type'] == 1 ? "Authorize" : ($loggable['merchant_type'] == 2 ? "Expigate" : ($loggable['merchant_type'] == 3 ? "PayArc" : ($loggable['merchant_type'] == 4 ? "Paypal" : "Unknown Merchant"))) }}</td>
                                            <td class="align-middle">{{$loggable['payment_status'] == 0 ? "Pending" : ($loggable['payment_status'] == 1 ? "In Review" : ($loggable['payment_status'] == 2 ? "Completed" : null)) }}</td>
                                            <td class="align-middle">{{$action}}</td>
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
    @include('admin.third-party-role.script')
@endpush
