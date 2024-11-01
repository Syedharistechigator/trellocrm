@extends('admin.layouts.app')

@section('cxmTitle', 'File')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>File List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">File</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                     <a href="{{ route('file.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                     @include('includes.admin.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="table-responsive">
                            <table id="SpendingTables" class="table table-hover c_table theme-color">
                                <thead>
                                    <tr>
                                        <th class="hidden-md-down">Serial NO</th>
                                        <th class="hidden-md-down">File Name</th>
                                        <th class="hidden-md-down">Team</th>
                                        <th class="hidden-md-down">Brand</th>
                                        <th class="hidden-md-down">Size</th>
                                        <th class="hidden-md-down">Type</th>
                                        <th class="hidden-md-down">Thumb Name</th>
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

                                        <td>{{$spendings->created_at}}</td>
                                        <td>
                                        <a href="#" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                        <a data-id="#" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round spendingDelButton"><i class="zmdi zmdi-delete"></i></a>
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

<!-- Create Spending Spending -->
<div class="modal fade" id="spendingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create Spend</h4>
            </div>

            <!-- <form method="POST" id="team-Spending-Form"> -->

            <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <select id="team_hnd" name="team_key" class="form-control show-tick ms" data-placeholder="Select Team" required>
                             <option value="" selected>Select Team</option>
                            @foreach($teams as $team)
                            <option value="{{$team->team_key}}">{{$team->name}}</option>
                            @endforeach
                        </select>
                        </div>

                        <div class="form-group">
                        <select id="brand_hnd" name="brand_key" class="form-control show-tick ms" data-placeholder="Select Team" required>
                            <option value="" selected>Select Brand</option>
                            @foreach($brand as $brand)
                            <option value="{{$brand->brand_key}}">{{$brand->name}}</option>
                            @endforeach
                        </select>
                        </div>

                        <div class="form-group">
                            <input type="date" id="spending_date" class="form-control" placeholder="Spending Date" name="spending_date">
                        </div>
                        <div class="form-group">
                            <input type="text" id="spending_platform" class="form-control" placeholder="Spending Platform" name="spending_platform">
                        </div>
                        <div class="form-group">
                            <input type="text" id="spending_amount" class="form-control" placeholder="Spending Amount" name="spending_amount">
                        </div>

                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="addspendingBtn" class="btn btn-success btn-round">SAVE</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('cxmScripts')
    @include('admin.Spending.script')
@endpush
