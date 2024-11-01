@extends('admin.layouts.app')@section('cxmTitle', 'Create Brand')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Brand</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item"><a href="{{route('brand.index')}}">Brands</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button">
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
                                <h2><strong>Create New</strong> Brand</h2>
                            </div>
                            <div id="alerts"></div>
                            <div class="alert alert-success" role="alert" id="#show12" style="display:none;">
                                <strong>Bootstrap</strong> Better check yourself,
                                <a target="_blank" href="https://getbootstrap.com/docs/4.2/components/input-group/">View More</a>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true"><i class="zmdi zmdi-close"></i></span>
                                </button>
                            </div>
                            <div class="body">
                                <form id="brand_form" method="POST">
                                    @csrf
                                    <h4 class="mt-4">Brand Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="name">Brand Name</label>
                                                <input type="text" class="form-control" id="name" name="name" minlength="3" autocomplete="off" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="logo">Brand Logo (URL)</label>
                                                <input type="url" class="form-control" id="logo" name="logo" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="brand_url">Brand URL</label>
                                                <input type="url" class="form-control" id="brand_url" name="brand_url" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="brand_type">Select Brand Type</label>
                                                <select class="form-control show-tick ms select2" id="brand_type" data-placeholder="Select" name="brand_type" required>
                                                    @foreach($brand_types as $brand_type)
                                                        <option value="{{ $brand_type }}">{{ $brand_type }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="merchant">Select Authorize Merchant</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="merchant" name="merchant" required>
                                                    @foreach($methods as $method)
                                                        <option value="{{$method->id}}">{{$method->merchant}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="status">Publish</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="status" name='status' required>
                                                    <option></option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="is_paypal">Paypal Available (Optional)</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="is_paypal" name="is_paypal">
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="expigate_id">Expigate Available</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="expigate_id" name="expigate_id">
                                                    @foreach($payment_method_expigates as $payment_method_expigate)
                                                        <option value="{{$payment_method_expigate->id}}">{{$payment_method_expigate->merchant}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="payarc_id">PayArc Available</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="payarc_id" name="payarc_id">
                                                    @foreach($payment_method_payarcs as $payment_method_payarc)
                                                        <option value="{{$payment_method_payarc->id}}">{{$payment_method_payarc->merchant}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="default_merchant_id">Default Merchant</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select Default Merchant" id='default_merchant_id' name='default_merchant_id' required>
                                                    <option value="1">Authorize</option>
                                                    <option value="2">Expigate</option>
                                                    <option value="3">PayArc</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="crawl">Crawl</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id='crawl' name='crawl' required>
                                                    <option value="1" selected>Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="is_amazon">Amazon Available (Optional)</label>
                                                <select class="form-control show-tick ms select2" data-placeholder="Select" id="is_amazon" name="is_amazon">
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="checkout_version">Checkout Version</label>
                                                <input id="checkout_version" type="text" class="form-control" name="checkout_version">
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                    </div>
                                    <!-- New Fields -->
                                    <h4 class="mt-4">Additional Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="admin_email">Admin Email</label>
                                                <input type="email" class="form-control" id="admin_email" name="admin_email">
                                            </div>
                                        </div>
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="phone">Phone</label>
                                                <input type="text" class="form-control" id="phone" name="phone" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="phone_secondary">Secondary Phone</label>
                                                <input type="text" class="form-control" id="phone_secondary" name="phone_secondary">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email_href">Email Href</label>
                                                <input type="text" class="form-control" id="email_href" name="email_href">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="contact_email">Contact Email</label>
                                                <input type="email" class="form-control" id="contact_email" name="contact_email">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="contact_email_href">Contact Email Href</label>
                                                <input type="text" class="form-control" id="contact_email_href" name="contact_email_href">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="website_name">Website Name</label>
                                                <input type="text" class="form-control" id="website_name" name="website_name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="website_logo">Website Logo</label>
                                                <input type="text" class="form-control" id="website_logo" name="website_logo">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="address">Address</label>
                                                <input type="text" class="form-control" id="address" name="address" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="chat-id">Chat</label>
                                                <textarea class="form-control" id="chat-id" name="chat" rows="1" cols="50" autocomplete="off"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <h4 class="mt-4">SMTP Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card border">
                                                <div class="header border-bottom px-3 font-bold">SMTP Setting (Optional)
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <label for="smtp_host">SMTP Host</label>
                                                        <input type="text" class="form-control" id="smtp_host" name="smtp_host">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="smtp_email">SMTP Email</label>
                                                        <input type="email" class="form-control" id="smtp_email" name="smtp_email">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="smtp_password">SMTP Password</label>
                                                        <input type="text" class="form-control" id="smtp_password" name="smtp_password">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="smtp_port">SMTP Port</label>
                                                        <input type="number" class="form-control" id="smtp_port" name="smtp_port">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
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
    @include('admin.brand.script')
@endpush
