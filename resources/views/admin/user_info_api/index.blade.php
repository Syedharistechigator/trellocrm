@extends('admin.layouts.app')

@section('cxmTitle', 'User Info API')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>User Info API List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">User Info API</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="user_info_apiTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Email</th>
                                        <th>Balance</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($user_info_apis as $user_info_api)
                                    <tr>
                                        <td class="align-middle">{{$user_info_api->key}}</td>
                                        <td class="align-middle">{{$user_info_api->email}}</td>
                                        <td class="align-middle">{{$user_info_api->balance}}</td>

                                        <td class="align-middle">
                                            <div class="custom-control custom-switch">
                                              {{--<span style="left: -41px; position: relative; top: 2px;">Unpublish</span>--}}
                                              <input data-id="{{$user_info_api->id}}" type="checkbox" class="custom-control-input toggle-class" id="customSwitch{{$user_info_api->id}}" {{ $user_info_api->status ? 'checked' : '' }}>
                                              <label class="custom-control-label" for="customSwitch{{$user_info_api->id}}"></label>
                                              {{--<span style="position: relative; top: 2px;">Publish</span>--}}
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <a title="Edit" href="{{route('user_info_api.edit',[$user_info_api->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a title="Delete" data-id="{{$user_info_api->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.user_info_api.script')
@endpush
