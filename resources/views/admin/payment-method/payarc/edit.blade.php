@extends('admin.layouts.app')@section('cxmTitle', 'Edit')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payment Method PayArc</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.payment.method.payarc.index')}}">Payment Methods PayArc</a></li>
                            <li class="breadcrumb-item active">PayArc</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-warning btn-icon right_icon_toggle_btn rounded-circle" type="button">
                            <i class="zmdi zmdi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- Basic Validation -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Update</strong> Payment Method</h2>
                            </div>
                            <div class="body">
                                <form id="payarc_update_form">
                                    @csrf
                                    @method('POST')
                                    <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$method->id}}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="merchant">Merchant</label>
                                                <input type="text" class="form-control" id="merchant" name="merchant" value="{{$method->merchant}}" minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" value="{{$method->email}}" id="email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="login_id">Login Id</label>
                                                <input type="text" class="form-control" value="{{$method->live_login_id}}" id="login_id" name="login_id" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="transaction_key">Transaction Key</label>
                                                <input type="text" class="form-control" value="{{$method->live_transaction_key}}" id="transaction_key" name="transaction_key" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="capacity">Capacity</label>
                                                <input id="capacity" type="number" class="form-control" name="capacity" value="{{$method->capacity}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="amount-limit">Amount Limit Per Transaction</label>
                                                <input id="amount-limit" type="number" class="form-control" name="amount_limit" value="{{$method->limit}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch mb-3">
                                        <input data-id="{{$method->id}}" type="checkbox" class="custom-control-input toggle-class sandbox-mode" id="customSwitch{{$method->id}}" {{ $method->mode ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch{{$method->id}}">Sandbox Mode</label>
                                    </div>
                                    <div class="custom-control custom-switch mb-3">
                                        <input data-id="{{$method->id}}" type="checkbox" class="custom-control-input toggle-class change-status" id="customSwitchstatus{{$method->id}}" {{ $method->status ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitchstatus{{$method->id}}">Enable Payment Method</label>
                                    </div>
                                    <input id="update_data" type="submit" value="Submit" class="btn btn-warning btn-round">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('cxmScripts')
    @include('admin.payment-method.payarc.script')
@endpush
