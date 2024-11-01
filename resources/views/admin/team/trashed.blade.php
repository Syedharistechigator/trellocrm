@extends('admin.layouts.app')

@section('cxmTitle', 'Trashed')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Trashed Team List</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Team</li>
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
                        <div id="restoreAllTean" class="text-right">
                            <button type="button" class="btn btn-warning btn-round teamRestoreAllButton">Restore All</button>
                        </div>
                        <div class="table-responsive">
                            <table id="TeamTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th>Team Name</th>
                                        <th>Team Key</th>
                                        <th data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="xs md">Delete Date</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                	@foreach($teams as $team)
                                    <tr>

                                        <td><h5>{{$team->name}}</h5></td>
                                        <td><h5>{{$team->team_key}}</h5></td>

                                        <td>
                                        	@if($team->status == 1)
                                        	<span class="col-green">Publish</span>
                                        	@else
                                        	<span class="col-red">Unpublish</span>
                                        	@endif
                                        </td>
                                        <td><h5>{{$team->deleted_at->diffForHumans()}}</h5></td>
                                        <td>
                                            <a data-id="{{$team->id}}" class="btn btn-warning btn-sm btn-round teamRestoreButton"><i class="zmdi zmdi-refresh"></i></a>
                                            <a data-id="{{$team->id}}" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
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
