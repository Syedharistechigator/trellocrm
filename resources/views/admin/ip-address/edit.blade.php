@extends('admin.layouts.app')

@section('cxmTitle', 'Edit')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Ip Address</h2>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.ip.address.index')}}">Ip Address</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.ip.address.index') }}" class="btn btn-success btn-icon rounded-circle"
                           type="button"><i class="zmdi zmdi-arrow-left"></i></a>
                        <button class="btn btn-warning btn-icon right_icon_toggle_btn rounded-circle" type="button"><i
                                class="zmdi zmdi-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <!-- Basic Validation -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Update</strong> Ip Address</h2>
                            </div>
                            <div class="body">
                                <form id="ip_address_update_form" method="POST">
                                    @csrf
                                    <input type="hidden" id="hdn" class="form-control" name="hdn"
                                           value="{{$ip_address->id}}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="merchant">Ip Address</label>
                                                <input type="text" class="form-control" id="ip-address" value="{{$ip_address->ip_address}}"
                                                       name="ip_address" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group ">
                                                <label for="list-type">Select List Type</label>
                                                <select class="form-control" data-placeholder="Select" id='list-type'
                                                        name='list_type' required>
                                                    <option value="0" {{$ip_address->list_type == 0 ? "selected" : ""}}>Black List</option>
                                                    <option value="1" {{$ip_address->list_type == 1 ? "selected" : ""}}>White List</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="details">Detail</label>
                                                <textarea class="form-control" id="detail" name="detail">{{$ip_address->detail}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" data-id="{{$ip_address->id}}"
                                                       class="custom-control-input toggle-class change-status"
                                                       id="customSwitchstatus{{$ip_address->id}}" name="status" {{$ip_address->status == 1 ? "checked" : ""}}>
                                                <label class="custom-control-label"
                                                       for="customSwitchstatus{{$ip_address->id}}">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_data" type="submit" value="Submit"
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
