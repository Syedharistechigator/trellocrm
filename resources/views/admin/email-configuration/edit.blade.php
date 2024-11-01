@extends('admin.layouts.app')@section('cxmTitle', 'Edit')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Email Configuration</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.email.configuration.index')}}">Email Configuration</a></li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.email.configuration.index') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-arrow-left"></i></a>
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
                                <h2><strong>Update</strong> Email Configuration</h2>
                            </div>
                            <div class="body">
                                <form id="email_configuration_update_form" method="POST">
                                    @csrf
                                    <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$email->id}}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="brand_key">Brands</label>
                                                <select id="brand_key" name="brand_key" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Brand" data-live-search="true" required>
                                                    <option value="">Select Brand</option>
                                                    @foreach($brands as $brand)
                                                        <option value="{{$brand->brand_key}}" {{$email->brand_key && $email->brand_key == $brand->brand_key ? "selected" : ""}}>{{$brand->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="parent_id">Parent</label>
                                                <select id="parent_id" name="parent_id" class="form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Parent" data-live-search="true" required>
                                                    <option value="" disabled>Select Parent</option>
                                                    <option value="0" {{$email->parent_id == 0 ? "selected" : ""}}>No Parent</option>
                                                    @foreach($parent_ids as $parent_id)
                                                        <option value="{{$parent_id->id}}" {{$email->parent_id && $email->parent_id == $parent_id->id ? "selected" : ""}}>{{$parent_id->email}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" value="{{$email->email}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="client_id">Client Id</label>
                                                <input type="text" class="form-control" id="client_id" name="client_id" value="{{$email->parent_id != 0 ?$email->parent->client_id : $email->client_id}}" required>
                                                <div id="client_id_message">
                                                    <div class="text-danger">
                                                        <small><span class="zmdi zmdi-info"></span> (required if parent is none)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="client_secret">Client Secret</label>
                                                <input type="text" class="form-control" id="client_secret" name="client_secret" value="{{$email->parent_id != 0 ?$email->parent->client_secret : $email->client_secret}}" required>
                                                <div id="client_secret_message">
                                                    <div class="text-danger">
                                                        <small><span class="zmdi zmdi-info"></span> (required if parent is none)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="api_key">Api Key</label>
                                                <input type="text" class="form-control" id="api_key" name="api_key" value="{{$email->api_key}}">
                                                <div class="text-warning">
                                                    <small><span class="zmdi zmdi-info"></span> (optional)</small></div>
                                            </div>
                                        </div>
                                        @if(json_decode($email->access_token, true))
                                            @foreach(json_decode($email->access_token, true) as $key => $value)
                                                <div class="col-md-12">
                                                    <div class="form-group form-float">
                                                        <label for="api_key">{{ucfirst($key)}}</label>
                                                        @if($key == "access_token")
                                                            <textarea class="form-control" rows="2" id="{{$key}}" readonly>{{$value}}</textarea>
                                                        @elseif($key == 'expires_in')
{{--                                                            <input type="text" class="form-control" id="{{$key}}" name="{{$key}}" value="{{ json_decode($email->access_token, true)['expires_at']  - time()  > 0 ? json_decode($email->access_token, true)['expires_at']  - time() : "Expired"}}">--}}
                                                            <input type="text" class="form-control" id="{{$key}}" name="{{$key}}" value="" readonly>
                                                        @elseif($key == 'expires_at')
                                                            <input type="text" class="form-control" id="{{$key}}" name="{{$key}}" value="{{ Carbon\Carbon::createFromTimestamp($value)->format('h:i:s A d-m-Y').' - now '. Carbon\Carbon::createFromTimestamp(time())->format('h:i:s d-m-Y') }}" readonly>
                                                        @else
                                                            <input type="text" class="form-control" id="{{$key}}" name="{{$key}}" value="{{$value}}" readonly>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                        <div class="col-md-12 d-none">
                                            <div class="form-group ">
                                                <label for="provider">Select Provider</label>
                                                <select class="form-control" data-placeholder="Select provider" id='provider' name='provider' required>
                                                    <option value="0" {{$email->provider && $email->provider == 0 ? "selected" : ""}}>Google</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" data-id="{{$email->id}}" class="custom-control-input toggle-class change-status" id="customSwitchstatus{{$email->id}}" name="status" {{$email->status == 1 ? "checked" : ""}}>
                                                <label class="custom-control-label" for="customSwitchstatus{{$email->id}}">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_data" type="submit" value="Submit" class="btn btn-warning btn-round ec-submit">
                                    <a href="{{route('google.redirect',$email->id)}}" class="btn btn-warning btn-round">Google Auth</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="now">
@endsection

@push('cxmScripts')
    @include('admin.email-configuration.script')
    <script>
        function pad(number) {
            return (number < 10 ? '0' : '') + number;
        }
        function updateCurrentTime() {
            var now = new Date();
            var hstOffset = -15; // Offset for HST (UTC -10)
            now.setUTCHours(now.getUTCHours() + hstOffset); // Adjust to HST

            var hours = now.getHours();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            var formattedTime = pad(hours) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()) + ' ' + ampm;

            var formattedDate = pad(now.getDate()) + '-' + pad(now.getMonth() + 1) + '-' + now.getFullYear();
            var currentTime = formattedTime + ' ' + formattedDate;

            var expiresAtInput = document.getElementById("expires_at");
            expiresAtInput.value = expiresAtInput.value.replace(/- now(.*)/, '- now ' + currentTime);
        }
        setInterval(updateCurrentTime, 1000);
        updateCurrentTime();
    </script>
@endpush
