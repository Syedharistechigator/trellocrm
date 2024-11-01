@extends('admin.layouts.app')

@section('cxmTitle', 'Ip Address')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Ip Address</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Ip Address</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <a href="{{ route('admin.ip.address.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>

            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="IpAddressTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Id#</th>
                                        <th>Ip Address</th>
                                        <th>List Type</th>
                                        <th>Detail</th>
                                        <th class="text-center" data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($ip_addresses as $ip_address)
                                    <tr>
                                        <td class="align-middle">{{$ip_address->id}}</td>
                                        <td class="align-middle">{{$ip_address->ip_address}}</td>
                                        <td class="align-middle">{!! ($ip_address->list_type == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>' !!}</td>
{{--                                        <td class="align-middle">{{$ip_address->list_type == 0 ? "Black List" : "White List"}}</td>--}}
                                        <td class="align-middle">{{ Str::limit($ip_address->detail, $limit = 50, $end = '...')}}</td>
                                        <td class="align-middle  text-center">
                                            <div class="custom-control custom-switch">
                                                <input data-id="{{$ip_address->id}}" type="checkbox"
                                                       class="custom-control-input toggle-class  change-status"
                                                       id="customSwitch{{$ip_address->id}}" {{ $ip_address->status ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                       for="customSwitch{{$ip_address->id}}"></label>
                                            </div>
                                        </td>

                                        <td class="align-middle">
                                            <a title="Edit" href="{{route('admin.ip.address.edit',[$ip_address->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a title="Delete" data-id="{{$ip_address->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
