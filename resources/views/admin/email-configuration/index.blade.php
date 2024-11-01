@extends('admin.layouts.app')

@section('cxmTitle', 'Email Configuration')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Email Configuration</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Email Configuration</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <a href="{{ route('admin.email.configuration.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>

            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="EmailConfigurationTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                <tr>
                                    <th>Id#</th>
                                    <th>Parent Email</th>
                                    <th>Brand</th>
                                    <th>Provider</th>
                                    <th>Email</th>
                                    <th>Token</th>
                                    <th>Token2</th>
                                    <th>Status</th>
                                    <th class="text-center" data-breakpoints="xs md">Action</th>
                                </tr>
                                <tbody>
                                	@foreach($emails as $email)
                                    <tr id="tr-{{$email->id}}">
                                        <td class="align-middle">{{$email->id}}</td>
                                        <td class="align-middle">{{$email->parent_id != 0 ? $email->parent->email : "No Parent" }}</td>
                                        <td class="align-middle"><a href="{{route('brand.edit',[$email->getBrand->id],'/edit')}}">{{$email->getBrand->name}}</a><br>{{$email->getBrand->brand_key}}</td>
                                        <td class="align-middle">{{$email->provider === 0 ? "Google" : null}}</td>
                                        <td class="align-middle">{{$email->email}}</td>
                                        <!-- HTML template -->
                                        <td class="align-middle text-center">
                                            <span class="zmdi token_status" id="token_status_{{$email->id}}" data-expiration="{{$email->parent_id != 0 && !empty($email->parent->access_token) ? json_decode($email->parent->access_token, true)['expires_at'] : (isset(json_decode($email->access_token, true)['expires_at']) ? json_decode($email->access_token, true)['expires_at'] : 0)}}"></span>
                                        </td>
                                        <td class="align-middle text-center">{!! $email->token_expire === null || $email->token_expire == true  ? '<span class="zmdi zmdi-close-circle text-danger"></span>' :'<span class="zmdi zmdi-check-circle text-success"></span>' !!}</td>
                                        <td class="align-middle text-center">
                                            <div class="custom-control custom-switch">
                                                <input data-id="{{$email->id}}" type="checkbox"
                                                       class="custom-control-input toggle-class  change-status"
                                                       id="customSwitch{{$email->id}}" {{ $email->status ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                       for="customSwitch{{$email->id}}"></label>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a title="Edit" href="{{route('admin.email.configuration.edit',[$email->id])}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a title="Delete" data-id="{{$email->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.email-configuration.script')
@endpush
