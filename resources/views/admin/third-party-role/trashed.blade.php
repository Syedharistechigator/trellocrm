@extends('admin.layouts.app')@section('cxmTitle', 'Trashed')
@section('content')
    @push('css')
        @include('admin.third-party-role.style')
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Trashed List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.third.party.role.index')}}"> Third Party Roles </a></li>
                            <li class="breadcrumb-item active"><a href="{{route('admin.third.party.role.trashed')}}"> Trashed</a></li>
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
                            <div id="restoreAll" class="text-right">
                                <button type="button" class="btn btn-danger btn-round restoreAllButton">Restore All</button>
                            </div>
                            <div class="table-responsive">
                                <table id="RecordTrashedTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th class='text-nowrap'>ID #</th>
                                        <th class='text-nowrap'>Invoice #</th>
                                        <th class=''>Team</th>
                                        <th class=''>Client</th>
                                        <th class=''>Order Id</th>
                                        <th class=''>Order Status</th>
                                        <th>Description</th>
                                        <th class='text-nowrap'>Amount</th>
                                        <th class='text-nowrap'>Transaction Id</th>
                                        <th class='text-nowrap'>Merchant</th>
                                        <th class='text-nowrap'>Payment Status</th>
                                        <th data-breakpoints="xs md text-nowrap">Delete Date</th>
                                        <th class="text-center" data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($third_party_roles as $third_party_role)
                                        <tr id="tr-{{$third_party_role->id}}">
                                            <td class="align-middle">{{$third_party_role->id}}</td>
                                            <td class="align-middle">
                                                <a class="text-warning invoice-trigger" data-invoice-num="{{ optional($third_party_role->getInvoice)->invoice_num}}" href="#{{optional($third_party_role->getInvoice)->invoice_num}}">{{optional($third_party_role->getInvoice)->invoice_num}}</a>
                                                <div class="">
                                                    <span class="badge badge-info rounded-pill">{{ $third_party_role->invoice_id}}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle">{{optional($third_party_role->getTeam)->name}}</td>
                                            <td class="align-middle">{{optional($third_party_role->getClient)->name}}</td>
                                            <td class="align-middle">{{$third_party_role->order_id}}</td>
                                            <td class="align-middle">{{$third_party_role->order_status}}</td>
                                            <td class="align-middle td-make-desc-short" title="{{$third_party_role->description}}">{{$third_party_role->description}}</td>
                                            <td class="align-middle">${{$third_party_role->amount}}</td>
                                            <td class="align-middle">{{$third_party_role->transaction_id}}</td>
                                            <td class="align-middle">{{$third_party_role->merchant_type == 1 ? "Authorize" : ($third_party_role->merchant_type == 2 ? "Expigate" : ($third_party_role->merchant_type == 3 ? "PayArc" : ($third_party_role->merchant_type == 4 ? "Paypal" : "Unknown Merchant"))) }}</td>
                                            <td class="align-middle">{{$third_party_role->payment_status == 0 ? "Pending" : ($third_party_role->payment_status == 1 ? "In Review" : ($third_party_role->payment_status == 2 ? "Completed" : null)) }}</td>
                                            <td class="align-middle text-nowrap">{{$third_party_role->deleted_at->format('j F, Y')}}
                                                <br>{{$third_party_role->deleted_at->format('h:i:s A')}}
                                                <br>{{$third_party_role->created_at->diffForHumans()}}
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <a title="Restore" data-id="{{$third_party_role->id}}" class="btn btn-warning btn-sm btn-round restoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                                <a title="Force Delete" data-id="{{$third_party_role->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round force-del"><i class="zmdi zmdi-delete"></i></a>
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
    @include('admin.third-party-role.script')
@endpush
