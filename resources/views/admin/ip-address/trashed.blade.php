@extends('admin.layouts.app')

@section('cxmTitle', 'Trashed')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed Ip Address List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Ip Addresses</li>
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
                        <div id="restoreAll" class="text-right">
                            <button type="button" class="btn btn-danger btn-round restoreAllButton">Restore All</button>
                        </div>
                        <div class="table-responsive">
                            <table id="IpAddressTrashedTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Id#</th>
                                        <th>Ip Address</th>
                                        <th>List Type</th>
                                        <th>Detail</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="xs md">Delete Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ip_addresses as $ip_address)
                                        <tr>
                                            <td class="align-middle">{{$ip_address->id}}</td>
                                            <td class="align-middle">{{$ip_address->ip_address}}</td>
                                            <td class="align-middle">{!! ($ip_address->list_type == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>' !!}</td>
                                            <td class="align-middle">{{ Str::limit($ip_address->detail, $limit = 50, $end = '...')}}</td>
                                            <td class="text-center">{!! ($ip_address->status == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Publish"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Unpublish"></i>' !!}</td>
                                            <td>{{$ip_address->deleted_at->format('j F, Y')}}
                                                <br>{{$ip_address->deleted_at->format('h:i:s A')}}
                                                <br>{{$ip_address->created_at->diffForHumans()}}
                                            </td>
                                            <td class="text-center">
                                                <a title="Restore" data-id="{{$ip_address->id}}" class="btn btn-warning btn-sm btn-round restoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                                <a title="Force Delete" data-id="{{$ip_address->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round ip-address-force-del"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.ip-address.script')
@endpush
