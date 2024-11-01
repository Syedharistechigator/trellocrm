@extends('admin.layouts.app')

@section('cxmTitle', 'Project')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Project list</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> TG</a></li>
                        <li class="breadcrumb-item">Project</li>
                        <li class="breadcrumb-item active">list</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">                
                    {{--<button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#projecteModal"><i class="zmdi zmdi-plus"></i></button>--}}
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
                                        <th>Brand</th>                                        
                                        <th>Sales Agent</th>
                                        <th>Account Manager</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                       
                                        <th class="hidden-md-down">Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($projectsData as $project)
                                    <tr>
                                        <td class="align-middle">
                                            <a class="text-info" href="{{route('adminproject.show', $project->id)}}"><i class="zmdi zmdi-open-in-new"></i> <strong>{{$project->project_title}}</strong></a>
                                            <div><small>Cost: ${{$project->project_cost}}</small></div>
                                        </td>
                                        <td class="align-middle">{{$project->brandName}}</td>
                                        <td class="align-middle">
                                            <div class="form-row">
                                                <div class="col-auto">
                                                    <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->agentImage?$project->agentImage :asset('assets/images/crown.png') !!}" alt="{{$project->agentName}}">
                                                </div>
                                                <div class="col">
                                                    <div class="text-info">{{$project->agentName}}</div>
                                                    <small>{{$project->agentDesignation}}</small>
                                                </div>
                                            </div>
                                        </td>  
                                        <td class="align-middle">
                                            <div class="form-row">
                                                <div class="col-auto">
                                                    <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->pmImage?$project->pmImage :asset('assets/images/crown.png') !!}" alt="{{$project->pmName}}">
                                                </div>
                                                <div class="col">
                                                    <div class="text-info">{{$project->pmName}}</div>
                                                    <small>{{$project->pmDesignation}}</small>
                                                </div>
                                            </div>
                                        </td>                                                                        
                                        <td class="align-middle">{{$project->project_date_start}}</td>
                                        <td class="align-middle">{{$project->project_date_due}}</td>                                        
                                        <td class="align-middle"><span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span></td>
                                        <td class="align-middle">
                                        @if(Auth::user()->type == 'lead')
                                            <a title="Assign Account Manager" data-id="{{$project->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round AccountManager" data-toggle="modal" data-target="#assignAccountManagerModal">
                                                <span class="zmdi zmdi-plus-circle"></span>
                                            </a>
                                        @endif    
                                        <button data-id="{{$project->id}}" title="Edit" class="btn btn-info btn-sm btn-round editproject" data-toggle="modal" data-target="#EditProjecteModal"><i class="zmdi zmdi-edit"></i></button>    
                                        <a title="Change Status" data-id="{{$project->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round statusChange" data-toggle="modal" data-target="#changeStatusModal"><span class="zmdi zmdi-settings"></span></a>
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
    @include('admin.project.script')
@endpush