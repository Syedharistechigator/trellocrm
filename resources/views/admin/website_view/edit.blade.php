@extends('admin.layouts.app')@section('cxmTitle', 'View User Tracking')
@section('content')
    <?php
    $userData = App\Models\User::where("id", $website_view->user_id)->first(); ?>
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>User Tracking</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('website_view.index')}}">User Tracking</a></li>
                            <li class="breadcrumb-item active">View</li>
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
                                <h2><strong>View</strong> User Tracking</h2>
                            </div>
                            <div class="body">
                                <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$website_view->id}}"/>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">name</label>
                                            <input type="text" class="form-control" id="website_viewname" name="name" value="{{$userData->name}}" minlength="3" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Email</label>
                                            <input id="website_viewLogo" type="email" class="form-control" value="{{$userData->email}}" name="email" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">phone</label>
                                            <input id="website_viewpage_url" type="number" class="form-control" value="{{$userData->phone}}" name="phone" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="ip_address">IP Address</label>
                                            <input id="website_viewUrl" type="text" class="form-control" value="{{$website_view->ip_address}}" name="ip_address" disabled/>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="page_url">Page URL</label>
                                            <input id="website_viewpage_url" type="text" class="form-control" value="{{$website_view->page_url}}" name="page_url" disabled/>
                                        </div>
                                    </div>
                                    @if ($website_view->ip_response)
                                            <?php $jsonData = $website_view->ip_response; $data = json_decode($jsonData, true); ?>
                                        @if ($data !== null)
                                            @foreach ($data as $key => $value)

                                                    <div class="col-md-6">
                                                        <div class="form-group form-float">
                                                            <label for="{{$key}}">{{$key}}</label>
                                                            @if(!is_array($value))
                                                            <input id="website_view{{$value}}" type="text" class="form-control" value="{{$value}}" name="{{$value}}" disabled/>
                                                            @else
                                                                <textarea id="website_view{{$key}}" class="form-control" disabled>{{ json_encode($value) }}</textarea>
                                                            @endif
                                                        </div>
                                                    </div>

                                            @endforeach
                                        @else
                                            <div class="form-group form-float">
                                                <label for="{{$key}}">{{$key}}</label>
                                                <input id="website_view{{$key}}" type="text" class="form-control" value="-" disabled/>
                                            </div>
                                        @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('cxmScripts')
    {{--@include('admin.website_view.script')--}}
@endpush
