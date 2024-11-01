@extends('admin.layouts.app')

@section('cxmTitle', 'Create')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Email Configuration</h2>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.email.configuration.index')}}">Email Configuration</a>
                            </li>
                            <li class="breadcrumb-item active">Create</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.email.configuration.index') }}" class="btn btn-success btn-icon rounded-circle"
                           type="button"><i class="zmdi zmdi-arrow-left"></i></a>
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
                                <h2><strong>Create </strong> Email Configuration</h2>
                            </div>
                            <div class="body">
                                <form id="email_configuration_create_form" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="brand_key">Brands</label>
                                                <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                                        title="Select Brand" data-live-search="true" required>
                                                    <option value="">Select Brand</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->brand_key}}">{{$brand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="parent_id">Parent</label>
                                                <select id="parent_id" name="parent_id" class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true"
                                                        title="Select Parent" data-live-search="true" required>
                                                    <option value="" disabled>Select Parent</option>
                                                    <option value="0">No Parent</option>
                                                    @foreach($parent_ids as $parent_id)
                                                        <option value="{{$parent_id->id}}">{{$parent_id->email}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="client_id">Client Id</label>
                                                <input type="text" class="form-control" id="client_id" name="client_id">
                                                <div id="client_id_message">
                                                    <div class="text-danger"><small><span class="zmdi zmdi-info"></span> (required)</small></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="client_secret">Client Secret</label>
                                                <input type="text" class="form-control" id="client_secret" name="client_secret">
                                                <div id="client_secret_message">
                                                    <div class="text-danger"><small><span class="zmdi zmdi-info"></span> (required)</small></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="api_key">Api Key</label>
                                                <input type="text" class="form-control" id="api_key" name="api_key">
                                                <div class="text-warning"><small><span class="zmdi zmdi-info"></span> (optional)</small></div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 d-none">
                                            <div class="form-group ">
                                                <label for="provider">Select Provider</label>
                                                <select class="form-control" data-placeholder="Select provider" id='provider'
                                                        name='provider' required>
                                                    <option value="0" selected>Google</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox"
                                                       class="custom-control-input toggle-class change-status"
                                                       id="customSwitchstatus" name="status" checked>
                                                <label class="custom-control-label"
                                                       for="customSwitchstatus">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="create-button" type="submit" value="Submit"
                                           class="btn btn-warning btn-round ec-submit">
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
    @include('admin.email-configuration.script')
@endpush
