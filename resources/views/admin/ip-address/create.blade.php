@extends('admin.layouts.app')

@section('cxmTitle', 'Create')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Ip Address</h2>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.ip.address.index')}}">Ip Address</a>
                            </li>
                            <li class="breadcrumb-item active">Create</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.ip.address.index') }}" class="btn btn-success btn-icon rounded-circle"
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
                                <h2><strong>Create </strong> Ip Address</h2>
                            </div>
                            <div class="body">
                                <form id="ip_address_create_form" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="ip-address">Ip Address</label>
                                                <input type="text" class="form-control" id="ip-address"
                                                       name="ip_address" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <label for="list-type">Select List Type</label>
                                                <select class="form-control" data-placeholder="Select" id='list-type'
                                                        name='list_type' required>
                                                    <option value="0">Black List</option>
                                                    <option value="1">White List</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="detail">Detail</label>
                                                <textarea class="form-control" id="detail" name="detail"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox"
                                                       class="custom-control-input toggle-class"
                                                       id="customSwitchstatus" name="status" checked>
                                                <label class="custom-control-label"
                                                       for="customSwitchstatus">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="create-button" type="submit" value="Submit"
                                           class="btn btn-warning btn-round">
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
    @include('admin.ip-address.script')
@endpush
