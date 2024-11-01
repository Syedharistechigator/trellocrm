@extends('layouts.app')

@section('cxmTitle', 'Payments')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Payments</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Sales</li>
                        <li class="breadcrumb-item active"> Payments</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table id="LeadTable" class="table table-striped table-hover js-basic-example theme-color">
                    <thead>
                        <tr>
                            <th>ID #</th>
                            <th data-breakpoints="sm xs">Payment Date</th>
                            <th>Project Title</th>
                            <th>Amount</th>
                            <th class="text-center" data-breakpoints="xs md">Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentData as $payment)
                        <tr>
                            <td>{{$payment->id}}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y')}}</td>
                            <td class="text-info">{{$payment->projectTitle}}</td>
                            <td>${{$payment->amount}}</td>
                            <td class="text-center">
                                @if($payment->payment_status == 1)
                                <span class="badge badge-success rounded-pill">Success</span>
                                @elseif($payment->payment_status == 2)
                                <span class="badge badge-warning rounded-pill">Refund</span>
                                @else
                                <span class="badge badge-danger rounded-pill">Charge Back</span>
                                @endif
                           </td>
                            <td>
                            <a title="View Invoice" href="{{route('payment.show',$payment->invoiceid)}}" class="btn btn-primary btn-sm btn-round"><i class="zmdi zmdi-eye"></i></a>
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
