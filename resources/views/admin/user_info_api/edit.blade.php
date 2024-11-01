@extends('admin.layouts.app')

@section('cxmTitle', 'Edit user_info_api')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>User Info Api</h2>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="#">user info api</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button"><i
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
                                <h2><strong>Edit</strong> user info api</h2>
                            </div>
                            <div class="body">
                                <form id="user_info_api_update_form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="hdn" class="form-control" name="hdn"
                                           value="{{$user_info_api->id}}">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email_address">Key</label>
                                                <input type="text" class="form-control" id="user_info_apikey" name="key"
                                                       value="{{$user_info_api->key}}" minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email_address">Email</label>
                                                <input id="user_info_apiLogo" type="email" class="form-control"
                                                       value="{{$user_info_api->email}}" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email_address">Balance</label>
                                                <input id="user_info_apiUrl" type="number" class="form-control"
                                                       value="{{$user_info_api->balance}}" name="balance"
                                                       required>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="email_address">Publish</label>
                                                <select class="form-control show-tick ms select2"
                                                        data-placeholder="Select" name='status' required>
                                                    <option></option>
                                                    <option value="1" <?php if ($user_info_api->status == 1) {
                                                        echo "selected";
                                                    }?>>Yes
                                                    </option>
                                                    <option value="0" <?php if ($user_info_api->status == 0) {
                                                        echo "selected";
                                                    }?>>No
                                                    </option>
                                                </select>
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
    @include('admin.user_info_api.script')
@endpush
