@extends('layouts.app')

@section('cxmTitle', 'Payments')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Search Payments</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Sales</li>
                        <li class="breadcrumb-item active"> Payments</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                {{-- @if(Auth::user()->type == 'lead' or Auth::user()->type == 'staff')  --}}
                @if(FALSE)
                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#paymentModal"><i class="zmdi zmdi-plus"></i></button>
                @endif
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card">
                    <div class="body" style="height: 80px;">
                        <div class="row clearfix justify-content-between">
                            <div class="col-lg-4 col-md-6">


                            </div>
                            <div class="col-lg-4 col-md-6">

                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="card">
                                    <div class="search">
                                            <form class="input-group mb-0" action="{{ route('searchPayment') }}" method="GET">
                                            @csrf
                                                <input type="text" class="form-control"  name="searchText" required placeholder="Search...">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-raised btn-info  waves-effect" id="basic-addon2">
                                                        <i class="zmdi zmdi-search"></i>
                                                    </button>
                                                </div>
                                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table id="LeadTable" class="table table-striped table-hover xjs-basic-example theme-color">
                    <thead>
                        <tr>
                            <th>ID#</th>
                            <th>Client</th>
                            <th>Sales Type</th>
                            <th>Amount</th>
                            <th data-breakpoints="sm xs">Payment Date</th>
                            <th>Payment Gateway</th>
                            <th>Transction ID</th>
                            <th class="text-center" data-breakpoints="xs md">Status</th>
                            <th class="text-center" data-breakpoints="xs md">Compliance Verified</th>
                            <th class="text-center" data-breakpoints="xs md">Operation Verified</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(count($payments) > 0)
                        @foreach($payments as $payment)
                        <tr>
                            <td class="align-middle">{{$payment->id}}</td>
                            <td class="text-info align-middle">{{$payment->name}}<br>{{$payment->email}}</td>
                            <td class="align-middle">{{$payment->sales_type}}</td>
                            <td class="align-middle">${{$payment->amount}}</td>
                            <td class="align-middle">{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}</td>
                            <td class="align-middle">{{$payment->payment_gateway}}</td>
                            <td class="align-middle">{{$payment->authorizenet_transaction_id}}</td>
                            <td class="text-center align-middle">
                                @if($payment->payment_status == 1)
                                <span class="badge badge-success rounded-pill">Success</span>
                                @elseif($payment->payment_status == 2)
                                <span class="badge badge-warning rounded-pill">Refund</span>
                                @else
                                <span class="badge badge-danger rounded-pill">Charge Back</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                {!! ($payment->compliance_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                            </td>
                            <td class="text-center align-middle">
                                {!! ($payment->head_verified == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                            </td>

                            <td class="text-center align-middle text-nowrap">
                                @if(Auth::user()->type == 'qa')
                                <button data-id="{{$payment->id}}" title="Refund" type="button" class="btn btn-warning btn-sm btn-round cxm-btn-refund"><i class="zmdi zmdi-replay"></i></button>
                                @else
                                <a title="View Payment Details" href="{{route('showPaymentDetail',$payment->id)}}" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
                                <a title="View Invoice" href="{{route('payment.show',$payment->invoice_id)}}" class="btn btn-warning btn-sm btn-round" target="_blank"><i class="zmdi zmdi-file-text"></i></a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" style="text-align: center;">
                                <h3>Data Not Found</h3>
                            </td>
                        <tr>
                    @endif
                    </tbody>
                </table>
            </div>
            {{-- $payments->links() --}}
        </div>
    </div>
</section>

<!-- Cxm Refund Modal -->
<div class="modal fade" id="cxmRefundModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title">Refund Request</h4>
            </div>
            <form method="post" id="refund_form">
                <input type="hidden" id="team_key" name="team_key" value="">
                <input type="hidden" id="brand_key" name="brand_key" value="">
                <input type="hidden" id="agent_id" name="agent_id" value="">
                <input type="hidden" id="invoice_id" name="invoice_id" value="">
                <input type="hidden" id="client_id" name="client_id" value="">
                <input type="hidden" id="payment_id" name="payment_id" value="">
                <input type="hidden" id="auth_transaction_id" name="auth_transaction_id" value="">
                <input type="hidden" id="cxm_card" name="cxm_card" value="">

                <div class="modal-body">
                    <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input id="amount" name="amount" type="number" class="form-control" placeholder="Amount" required />
                            </div>
                    </div>
                    <div class="form-group">
                        <textarea id="reason" class="form-control" placeholder="Enter Reason" name="reason"></textarea>
                    </div>
                    <div class="form-group">
                            <select id="type" name="type" class="form-control" data-placeholder="Select Type" required>
                                <option>Select Type</option>
                                <option value="refund">Refund</option>
                                <option value="chargeback">Charge Back</option>
                            </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-round">Send</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection

@push('cxmScripts')
    @include('payment.script')
@endpush
