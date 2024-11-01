@extends('admin.layouts.app')@section('cxmTitle', 'Payment Methods')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payment Method Authorize.Net</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.payment.method.authorize.index')}}">Payment Method Authorize.Net</a></li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.payment.method.authorize.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table id="AuthorizeTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>ID#</th>
                                        <th>Merchant</th>
                                        <th>Login Id</th>
                                        <th>Transaction Key</th>
                                        <th><?php echo date("M") . "-" . date("Y"); ?> Amount</th>
                                        <th>Capacity</th>
                                        <th>Cap Usage</th>
                                        <th>Limit</th>
                                        <th class="text-center" data-breakpoints="sm xs">Test Mode</th>
                                        <th class="text-center" data-breakpoints="xs md">Authorization</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payment_methods as $method)
                                        <tr>
                                            <td class="align-middle">{{$method->id}}</td>
                                            <td class="align-middle">{{$method->merchant}}</td>
                                            <td class="align-middle">{{$method->live_login_id}}</td>
                                            <td class="align-middle">{{$method->live_transaction_key}}</td>
                                            <td class="align-middle">${{$method->paymentMonth}}.00</td>
                                            <td class="align-middle">${{$method->capacity}}</td>
                                            <td class="align-middle">${{$method->cap_usage}}</td>
                                            <td class="align-middle">${{$method->limit}}</td>
                                            <td class="align-middle text-center">{!! ($method->mode == 1)?'<span class="badge badge-danger rounded-pill">SANDBOX</span>' :'<span class="badge badge-success rounded-pill">PRODUCTION</span>' !!}</td>
                                            <td class="align-middle text-center">{!! ($method->authorization == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>' !!}</td>
                                            <td class="align-middle text-center">{!! ($method->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>' !!}</td>
                                            <td class="align-middle">
                                                <a title="Edit" href="{{route('admin.payment.method.authorize.edit',[$method->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                                <a title="Held Transctions" href="{{route('admin.payment.method.authorize.held.transaction',$method->id)}}" class="btn btn-success btn-sm btn-round"><i class="zmdi zmdi-card-off"></i></a>
                                                {{-- <button title="Show Held Transctions" class="btn badge-success btn-sm btn-round" data-toggle="modal" data-target="#upsalePaymentModal" data-login-id="{{$method->live_login_id}}" data-transaction-key="{{$method->live_transaction_key}}">
                                                    <i class="zmdi zmdi-balance"></i>
                                                </button> --}}
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
        </div>
    </section>

@endsection

@push('cxmScripts')
    @include('admin.payment-method.authorize.script')
    <script>
        $(document).ready(function () {
            $('#AuthorizeTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true
            });
        });
    </script>
@endpush
