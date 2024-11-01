@extends('admin.layouts.app')

@section('cxmTitle', 'Clients')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Clients List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Clients</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
            <div class="card">
                <div class="table-responsive">
                         <!-- <table id="BrandTable" class="table table-hover product_item_list c_table theme-color mb-0"> -->
                    <table id="LeadTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                        <thead>
                            <tr>
                                <th>ID #</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th data-breakpoints="sm xs">phone</th>
                                <th>Total Spending</th>
                                <th>Date</th>
                                <th class="text-center" data-breakpoints="xs md">Status</th>
                                <th data-breakpoints="sm xs md">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                    @foreach($clients as $client)
                                    <tr>
                                        <td class="align-middle">{{$client->id}}</td>
                                        <td class="align-middle"><a class="text-warning" href="{{route('clientadmin.show',$client->id)}}">{{$client->name}}</a></td>
                                        <td class="align-middle"><span class="zmdi zmdi-email text-warning"></span> {{$client->email}}</td>
                                        <td class="align-middle"><span class="zmdi zmdi-phone text-warning"></span> {{$client->phone}}</td>
                                        <td class="align-middle"><span class="zmdi zmdi-phone text-"></span> {{$client->phone}}</td>
                                        <td class="align-middle"><span class="zmdi zmdi-calendar text-warning"></span> {{ $client->created_at ? $client->created_at->format('j F, Y') : '' }}</td>
                                        <td class="text-center align-middle">{!! ($client->status == 1)?'<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' :'<i class="zmdi zmdi-close-circle text-danger" title="Suspended"></i>' !!}</td>
                                        <td class="align-middle">
                                            <a title="View" href="" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>
                                            <a title="Change Status" data-id="" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><i class="zmdi zmdi-settings"></i></a>
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
