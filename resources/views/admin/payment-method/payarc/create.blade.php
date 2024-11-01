@extends('admin.layouts.app')@section('cxmTitle', 'Create')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Payment Method PayArc</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.payment.method.payarc.index')}}">Payment Method PayArc</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.payment.method.payarc.index') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-arrow-left"></i></a>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- Basic Validation -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Create </strong> Payment Method PayArc</h2>
                            </div>
                            <div class="body">
                                <form id="payarc_create_form">
                                    @csrf
                                    @method('POST')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="merchant">Merchant</label>
                                                <input type="text" class="form-control" id="merchant" name="merchant" minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email">Email</label>
                                                <input id="email" type="email" class="form-control" name="email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="login-id">Login Id</label>
                                                <input id="login-id" type="text" class="form-control" name="login_id" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="transaction-key">Transaction Key</label>
                                                <input id="transaction-key" type="text" class="form-control" name="transaction_key" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-float">
                                                <label for="currency">Select Currency</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id='currency' name='currency' required>
                                                    <option value="USD">USD</option>
                                                    <option value="AUD">AUD</option>
                                                    <option value="GBP">GBP</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-float">
                                                <label for="environment">Select Environment</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id='environment' name='environment' required>
                                                    <option value="0">Production</option>
                                                    <option value="1">Sandbox</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-float">
                                                <label for="capacity">Capacity</label>
                                                <input id="capacity" type="number" class="form-control" name="capacity" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group form-float">
                                                <label for="amount-limit">Amount Limit Per Transaction</label>
                                                <input id="amount-limit" type="number" class="form-control" name="amount_limit" required>
                                            </div>
                                        </div>
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
