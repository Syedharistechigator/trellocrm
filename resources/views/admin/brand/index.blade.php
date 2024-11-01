@extends('admin.layouts.app')@section('cxmTitle', 'Brands')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Brand List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Brands</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
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
                                <table id="BrandTable" class="table table-striped table-hover theme-color xjs-exportable" xdata-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>Brand Logo</th>
                                        <th>Brand Type</th>
                                        <th>Brand Name</th>
                                        <th data-breakpoints="sm xs">Team Names</th>
                                        <th>Brand Key</th>
                                        <th data-breakpoints="sm xs">Brand URL</th>
                                        {{--										<th data-breakpoints="sm xs">Default Merchant</th>--}}
                                        <th data-breakpoints="sm xs">Expigate Available</th>
                                        <th data-breakpoints="sm xs">Merchant</th>
                                        <th data-breakpoints="sm xs">Paypal</th>
                                        <th data-breakpoints="sm xs">Amazon</th>
                                        <th data-breakpoints="sm xs">Crawl</th>
                                        <th data-breakpoints="sm xs">CheckOut Version</th>
                                        <th data-breakpoints="sm xs">Extras</th>
                                        <th>&nbsp;</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($brands as $brand)
                                        <tr>
                                            <td class="align-middle">
                                                <object data="{!! $brand->logo !!}" width="100">
                                                    <img src="{{asset('assets/images/logo-colored.png')}}" width="100" alt="{{$brand->name}}" loading="lazy">
                                                </object>
                                                {{--                                                <img src="{!! $brand->logo !!}" onerror="this.onerror=null;this.src='https://www.logoaspire.com/assets/images/logo.gif;" width="100"--}}
                                                {{--                                                                          alt="{{$brand->name}}" loading="lazy">--}}
                                            </td>
                                            <td class="align-middle">{{$brand->brand_type}}</td>
                                            <td class="align-middle">{{$brand->name}}</td>
                                            <td class="align-middle">{{ $brand->assignTeams != " " ? substr(trim($brand->assignTeams), 0, -1) :"No Team Assign" }}</td>
                                            <td class="align-middle">{{$brand->brand_key}}</td>
                                            <td class="align-middle">
                                                <a class="text-warning" href="{{$brand->brand_url}}" target="_blank">{{$brand->brand_url}}</a>
                                            </td>
                                            {{--											<td class="align-middle">{{$brand->default_merchant_id == 1 ? "Authorize" : ($brand->default_merchant_id == 2 ? "Expigate" : ($brand->default_merchant_id == 3 ? "PayArc" : "") )}}</td>--}}
                                            <td class="align-middle">{{$brand->getMerchantExpigate->merchant}}</td>
                                            <td class="align-middle">{{$brand->merchantName}}</td>
                                            <td class="align-middle">{{$brand->is_paypal == 0 ? "No" : "Yes"}}</td>
                                            <td class="align-middle">{{$brand->is_amazon == 0 ? "No" : "Yes"}}</td>
                                            <td class="align-middle">{{$brand->crawl == 0 ? "No" : "Yes"}}</td>
                                            <td class="align-middle">{{$brand->checkout_version}}</td>
                                            <td class="align-middle">
                                                <a title="More Details" href="javascript:void(0)" class="text-warning" data-toggle="modal" data-target="#detailsModal{{$brand->id}}"> View </a>
                                            </td>
                                            <td class="align-middle">
                                                {!! ($brand->assign_status == 1)?'<i class="zmdi zmdi-check-circle text-success" title="Active"></i>' :'<i class="zmdi zmdi-close-circle text-danger" title="Inactive"></i>' !!}
                                            </td>
                                            <td class="align-middle">
                                                <div class="custom-control custom-switch">
                                                    <span style="left: -41px; position: relative; top: 2px;">Unpublish</span>
                                                    <input data-id="{{$brand->id}}" type="checkbox" class="custom-control-input toggle-class" id="customSwitch{{$brand->id}}" {{ $brand->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitch{{$brand->id}}"></label>
                                                    <span style="position: relative; top: 2px;">Publish</span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle">
                                                <a title="Edit" href="{{route('brand.edit',[$brand->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                                <a title="Delete" data-id="{{$brand->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{--                            /**Yajra table*/--}}
                                {{--                                <table id="BrandTable" class="table table-striped table-hover theme-color data-table"--}}
                                {{--                                       xdata-sorting="false">--}}
                                {{--                                    <thead>--}}
                                {{--                                    <tr>--}}
                                {{--                                        <th>Brand Logo</th>--}}
                                {{--                                        <th>Brand Name</th>--}}
                                {{--                                        <th>Team Names</th>--}}
                                {{--                                        <th>Brand Key</th>--}}
                                {{--                                        <th data-breakpoints="sm xs">Brand URL</th>--}}
                                {{--                                        <th data-breakpoints="sm xs">Merchant</th>--}}
                                {{--                                        <th data-breakpoints="sm xs">Paypal</th>--}}
                                {{--                                        <th>&nbsp;</th>--}}
                                {{--                                        <th data-breakpoints="xs md">Status</th>--}}
                                {{--                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>--}}
                                {{--                                    </tr>--}}
                                {{--                                    </thead>--}}
                                {{--                                </table>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .brand-detail {
            margin-bottom: 10px;
        }

        .brand-key {
            font-weight: bold;
        }

        .brand-value {
            margin-left: 10px;
        }
    </style>
    @foreach($brands as $brand)
        <div class="modal fade" id="detailsModal{{$brand->id}}" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="title" id="defaultModalLabel">Details for {{$brand->name}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="detailsTable{{$brand->id}}" class="table table-striped table-hover xjs-basic-example theme-color">
                                <thead>
                                <tr>
                                    <th>Attribute</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="brand-key">Admin Email:</td>
                                    <td class="brand-value">{{$brand->admin_email??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Phone:</td>
                                    <td class="brand-value">{{$brand->phone??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Secondary Phone:</td>
                                    <td class="brand-value">{{$brand->phone_secondary??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Email:</td>
                                    <td class="brand-value">{{$brand->email??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Email Href:</td>
                                    <td class="brand-value">{{$brand->email_href??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Contact Email:</td>
                                    <td class="brand-value">{{$brand->contact_email??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Contact Email Href:</td>
                                    <td class="brand-value">{{$brand->contact_email_href??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Address:</td>
                                    <td class="brand-value">{{$brand->address??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Website Name:</td>
                                    <td class="brand-value">{{$brand->website_name??""}}</td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Website Logo:</td>
                                    <td class="brand-value">
                                        <object data="{!! $brand->website_logo !!}" width="100">
                                            <img src="{{asset('assets/images/logo-colored.png')}}" width="100" alt="{{$brand->name}}" loading="lazy">
                                        </object>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="brand-key">Chat:</td>
                                    <td class="brand-value">{{$brand->chat??""}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('cxmScripts')
    @include('admin.brand.script')
    <script>
        $(document).ready(function () {
            $('#BrandTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']]
            });
        });
        // Yajra table
        {{--$(function () {--}}
        {{--    var table = $('#BrandTable').DataTable({--}}
        {{--        processing: true,--}}
        {{--        serverSide: true,--}}
        {{--        ajax: "{{ route('brand.index') }}",--}}
        {{--        columns: [--}}
        {{--            {data: 'logo', name: 'logo', orderable: false, searchable: false,},--}}
        {{--            {data: 'name', name: 'name', orderable: true, searchable: true},--}}
        {{--            {data: 'assign_teams', name: 'assign_teams', orderable: true, searchable: true},--}}
        {{--            {data: 'brand_key', name: 'brand_key', orderable: true, searchable: true},--}}
        {{--            {data: 'brand_url', name: 'brand_url', orderable: true, searchable: true},--}}
        {{--            {data: 'merchant_name', name: 'merchant_name', orderable: true, searchable: true},--}}
        {{--            {data: 'is_paypal', name: 'is_paypal', orderable: true, searchable: true},--}}
        {{--            {data: 'assign_status', name: 'assign_status', orderable: false, searchable: false},--}}
        {{--            {data: 'status', name: 'status', orderable: false, searchable: false},--}}
        {{--            {data: 'action', name: 'action', orderable: false, searchable: false},--}}
        {{--        ],--}}
        {{--        dom: 'Bfrtip',--}}
        {{--        order: [[1, 'asc']],--}}
        {{--        buttons: [--}}
        {{--            'copy', 'csv', 'excel', 'pdf', 'print'--}}
        {{--        ],--}}
        {{--        createdRow: function (row, data, index) {--}}
        {{--            applySortingCondition(row, data, index);--}}
        {{--            console.log(data)--}}
        {{--            // $('td', row).eq(0).css('width', '15%');--}}
        {{--            // $('td', row).eq(1).css('width', '15%');--}}
        {{--            // $('td', row).eq(2).css('width', '15%');--}}
        {{--            // $('td', row).eq(3).css('width', '15%');--}}
        {{--            // $('td', row).eq(4).css('width', '15%');--}}
        {{--            // $('td', row).eq(5).css('width', '15%');--}}
        {{--            // $('td', row).eq(6).css('width', '15%');--}}
        {{--            //--}}
        {{--            // $('td', row).eq(7).addClass('action-right');--}}
        {{--        }--}}
        {{--    });--}}

        {{--    function applySortingCondition(row, data, index) {--}}
        {{--        var sortingDirection = table.order()[0][1];--}}
        {{--        var sortingColumnIndex = table.order()[0][0];--}}

        {{--        // Handle different sorting conditions here--}}
        {{--        switch (sortingColumnIndex) {--}}
        {{--            case 1:--}}
        {{--                // Sorting by 'name' column--}}
        {{--                // Perform your action for this sorting condition--}}
        {{--                break;--}}
        {{--            case 2:--}}
        {{--                // Sorting by 'assign_teams' column--}}
        {{--                // Perform your action for this sorting condition--}}
        {{--                break;--}}
        {{--            // Add more cases for other columns as needed--}}
        {{--            default:--}}
        {{--            // Default action for other columns--}}
        {{--        }--}}

        {{--        // Apply custom styling here if needed--}}
        {{--        $('td', row).eq(0).css('width', '15%');--}}
        {{--        $('td', row).eq(1).css('width', '15%');--}}
        {{--        $('td', row).eq(2).css('width', '15%');--}}
        {{--        $('td', row).eq(3).css('width', '15%');--}}
        {{--        $('td', row).eq(4).css('width', '15%');--}}
        {{--        $('td', row).eq(5).css('width', '15%');--}}
        {{--        $('td', row).eq(6).css('width', '15%');--}}
        {{--        // ...--}}
        {{--    }--}}

        {{--});--}}
    </script>
@endpush
