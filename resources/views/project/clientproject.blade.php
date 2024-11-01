@extends('layouts.app')

@section('cxmTitle', 'Project')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Project list</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Project</li>
                        <li class="breadcrumb-item active">list</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="card project_list">
                        <div class="table-responsive">
                            <table id="projectTable" class="table table-striped table-hover js-basic-example theme-color">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th class="hidden-md-down">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>
                                            <a class="text-info" href="{{route('project.show', $project->id)}}"><i class="zmdi zmdi-open-in-new"></i> <strong>{{$project->project_title}}</strong></a>
                                            <div><small>Cost: ${{$project->project_cost}}</small></div>
                                        </td>
                                        <td>{{$project->project_date_start}}</td>
                                        <td>{{$project->project_date_due}}</td>
                                        <td><span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span></td>
                                        <td>
                                            <a class="btn btn-info btn-sm btn-round" href="{{route('project.show', $project->id)}}"><i class="zmdi zmdi-eye"></i></a>
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
    @include('project.script')
@endpush
