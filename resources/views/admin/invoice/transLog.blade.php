@extends('admin.layouts.app')

@section('cxmTitle', 'Payment Transactions Log')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Invoices</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Sales</li>
                        <li class="breadcrumb-item active"> Transactions Log</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#invoiceModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table id="InvoiceTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Invoice #</th>
                            <th>Team</th>
                            <th>Brand</th>
                            <th>Client</th>
                            <th>Project</th>
                            <th>Amount</th>
                            <th>Response Reason</th>
                            <th data-breakpoints="sm xs">Date</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transLog as $invoice)
                        <tr>
                            <td class="align-middle">{{$invoice->id}}</td>
                            <td class="align-middle">{{$invoice->invoiceid}}</td>
                            <td class="align-middle"></td>
                            <td class="align-middle"></td>
                            <td class="align-middle"><a class="text-warning" href="#"></a></td>
                            <td class="align-middle"></td>
                            <td class="align-middle">${{$invoice->amount}}</td>
                            <td class="align-middle">{{$invoice->response_reason}}</td>
                            <td class="align-middle">{{$invoice->created_at->format('j F, y')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>




@endsection

@push('cxmScripts')
    @include('admin.invoice.script')
@endpush
