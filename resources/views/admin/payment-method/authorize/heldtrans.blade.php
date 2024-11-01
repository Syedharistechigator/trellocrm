@extends('admin.layouts.app')

@section('cxmTitle', 'Payment Methods')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Held Transaction</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Authorize.Net</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @if(is_array($held_trans))
                    <a href="{{ route('admin.payment.method.authorize.approved.held.transaction', request()->segment(2)) }}" class="btn btn-success" type="button">
                        Approved Held Transaction
                    </a>
                    @endif
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>

            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="BrandTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Trans ID#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Account No</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @if(is_array($held_trans))
                                    @foreach($held_trans as $tx)
                                        <tr>
                                            <td class="align-middle">{{$tx->getTransId()}}</td>
                                            <td class="align-middle">{{$tx->getFirstName()}}</td>
                                            <td class="align-middle">{{$tx->getLastName()}}</td>
                                            <td class="align-middle">{{$tx->getAccountNumber()}}</td>
                                            <td class="align-middle">{{$tx->getSettleAmount()}}</td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="align-middle" align="center"><b>{{$held_trans}}</b></td>
                                    <tr>
                                    @endif


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
    @include('admin.payment-method.authorize.script')
@endpush
