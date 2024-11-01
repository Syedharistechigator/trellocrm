@extends('admin.layouts.app')

@section('cxmTitle', 'Trashed')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed User Info Apis List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">User Info Apis</li>
                        <li class="breadcrumb-item active">Trashed</li>
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
                        <div id="user_info_api_restoreAll" class="text-right">
                            <button type="button" class="btn btn-danger btn-round user_info_api_restoreAllButton">Restore All</button>
                        </div>
                        <div class="table-responsive">
                            <table id="user_info_apiTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Key</th>
                                        <th>Email</th>
                                        <th>Balance</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="xs md">Delete Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($user_info_apis as $user_info_api)
                                    <tr>
                                        <td class="align-middle">{{$user_info_api->key}}</td>
                                        <td class="align-middle">{{$user_info_api->email}}</td>
                                        <td class="align-middle">{{$user_info_api->balance}}</td>

                                        <td class="text-center">{!! ($user_info_api->status == 'yes')?'<i class="zmdi zmdi-check-circle text-success" title="Publish"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Unpublish"></i>' !!}</td>
                                        <td>{{$user_info_api->deleted_at->diffForHumans()}}</td>
                                        <td class="text-center">
                                            <a title="Restore" data-id="{{$user_info_api->id}}" class="btn btn-warning btn-sm btn-round user_info_api_restoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                            <a title="Force Delete" data-id="{{$user_info_api->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round user_info_api_forcedel"><i class="zmdi zmdi-delete"></i></a>
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
