@extends('layouts.app')
@section('cxmTitle', 'Invoices')
@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Invoices</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Sales</li>
                        <li class="breadcrumb-item active"> Invoices</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#invoiceModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>



        <div class="card">
            <div class="table-responsive">
                <table id="InvoiceTable" class="table table-striped table-hover js-basic-example theme-color">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th>Date</th>
                            <th>Project Title</th>
                            <th>Amount</th>
                            <th data-breakpoints="sm xs">Due Date</th>
                            <th class="text-center" data-breakpoints="xs md">Status</th>
                            <th class="text-center" data-breakpoints="sm xs md">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoiceData as $invoice)
                        <tr>
                            <td class="align-middle">{{$invoice->invoice_num}}</td>
                            <td class="align-middle">{{$invoice->created_at->format('j F, Y')}}</td>
                            <td class="align-middle">
                                <a class="text-info" href="{{route('project.show', $invoice->project_id)}}"><i class="zmdi zmdi-open-in-new"></i> <strong>
                                {{$invoice->projectTitle}}
                                </strong></a>
                            </td>
                            <td class="align-middle">${{$invoice->final_amount}}</td>
                            <td class="align-middle">{{ \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y')}}</td>
                            <td class="text-center align-middle">
                                @if($invoice->status == 'draft')
                                <span class="badge bg-grey rounded-pill">Draft</span>
                                @elseif($invoice->status == 'due')
                                <span class="badge bg-amber rounded-pill">Due</span>
                                @elseif($invoice->status == 'refund')
                                <span class="badge bg-pink rounded-pill">Refund</span>
                                @elseif($invoice->status == 'chargeback')
                                <span class="badge bg-red rounded-pill">Charge Back</span>
                                @else
                                <span class="badge badge-success rounded-pill">Paid</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <a title="View Invoice" href="{{route('payment.show',$invoice->invoice_key)}}" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-eye"></i><a>
                            </td>
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
    @include('invoice.script')
@endpush

