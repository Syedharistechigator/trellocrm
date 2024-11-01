@extends('layouts.app')
@section('cxmTitle', 'Spendings')
@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Spending List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Spending</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#spendingModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="SpendingTables" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                <thead>
                                    <tr>
                                        <th class="hidden-md-down">S.No</th>
                                        <th class="hidden-md-down">Team</th>
                                        <th class="hidden-md-down">Brand</th>
                                        <th class="hidden-md-down">Spending Date</th>
                                        <th class="hidden-md-down" >Platform</th>
                                        <th class="hidden-md-down" >Amount</th>
                                        <th class="hidden-md-down" >Created Date</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="SpendingTable">


                                       @foreach($spending as $spendings)
                                         <tr>
                                            <td>{{$spendings->id}}</td>
                                        @foreach(App\Models\Team::where('team_key',$spendings->team_key)->get() as $team)
                                                  <td>{{$team->name}}</td>
                                         @endforeach

                                         @foreach(App\Models\Brand::where('brand_key',$spendings->brand_key)->get() as $team)
                                                  <td>{{$team->name}}</td>
                                         @endforeach
                                         <td>{{$spendings->spending_date}}</td>
                                         <td>{{$spendings->platform}}</td>
                                         <td>{{$spendings->amount}}</td>
                                         <td>{{$spendings->created_at}}</td>
                                            <td>
                                         <a href="{{route('spending.edit',$spendings->id)}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a data-id="{{$spendings->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round spendingDelButton"><i class="zmdi zmdi-delete"></i></a>
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
    @include('spending.script')
@endpush
