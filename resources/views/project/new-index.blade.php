@extends('layouts.app')

@section('cxmTitle', 'Project')

@section('content')
    <!-- projects -->
    <section class="content">
        <div class="body_scroll">
            <!-- <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Project lists</h2>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>
                                {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Project</li>
                        <li class="breadcrumb-item active">list</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                            class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal"
                        data-target="#projecteModal"><i class="zmdi zmdi-plus"></i></button>
                    @include('includes.cxm-top-right-toggle-btn')
            </div>
            </div>
        </div> -->
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        <div class="proj-head">
                            <h3>Projects</h3>
                            <div class="filter-buttons">
                                <div class="grid-view-button btn-views"><i class="zmdi zmdi-view-module"
                                                                           aria-hidden="true"></i> Grid
                                    view
                                </div>
                                <div class="list-view-button btn-views active"><i class="zmdi zmdi-view-list-alt"
                                                                                  aria-hidden="true"></i>
                                    List view
                                </div>

                                <a href="javascript:;" class="btn-project" data-toggle="modal"
                                   data-target="#projecteModal">Create Projects</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <main class="view-filter grid-view-filter">
        <section class="tabs-list" id="tabs-list">
            <div class="container-fluid">
                <div class="sec-list-form">
                    <form>
                        {{--                    <form action="{{route('project.new.index')}}" method="post" id="searchForm">--}}
                        {{--                        @csrf--}}
                        <div class="row">
                            {{--                            <div class="col-md-3">--}}
                            {{--                                <div class="form-group">--}}
                            {{--                                    <select id="project-status-filter-1"  name="project_status_filter" class="form-control">--}}
                            {{--                                        <option selected>All</option>--}}
                            {{--                                        @if($projectStatus)--}}
                            {{--                                            @foreach($projectStatus as $psKey=>$psVal)--}}
                            {{--                                                <option>{{$psVal->status}}</option>--}}
                            {{--                                            @endforeach--}}
                            {{--                                        @endif--}}
                            {{--                                    </select>--}}
                            {{--                                </div>--}}
                            {{--                            </div>--}}
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select id="project-client-filter-1" name="project_client_filter"
                                            class="form-control cxm-live-search-fix"
                                            data-icon-base="zmdi"
                                            data-tick-icon="zmdi-check" data-show-tick="true"
                                            title="All Clients"
                                            data-live-search="true">
                                        <option value="0">All Clients</option>
                                        @foreach($teamClients as $client)
                                            <option value="{{$client->id}}"
                                                    {{$clientId == $client->id ? "selected" : "" }}
                                                    data-clientId="{{$client->id}}">{{$client->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select id="project-agent-filter-1" name="project_agent_filter"
                                            class="form-control cxm-live-search-fix"
                                            data-icon-base="zmdi"
                                            data-tick-icon="zmdi-check" data-show-tick="true"
                                            title="All Agents"
                                            data-live-search="true">
                                        <option value="0">All Agents</option>
                                        @foreach($members as $agent)
                                            <option value="{{$agent->id}}"
                                                    {{$agentId == $agent->id ? "selected" : "" }}
                                                    data-agentId="{{$agent->id}}">{{$agent->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="search-box-1" name="search_box" value=""
                                           placeholder="Search Title... "/>
                                </div>
                            </div>
                            {{--                                        <div class="col-md-2">--}}
                            {{--                                            <button type="submit" class="btn btn-primary">Filter</button>--}}
                            {{--                                        </div>--}}
                        </div>
                    </form>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="show-project">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="project_status_grid_view nav-link active" id="project-status-0-tab"
                                       data-toggle="tab"
                                       href="#project-status-0" role="tab"
                                       data-id="0"
                                       aria-controls="project-status-0" aria-selected="true">All</a>
                                </li>
                                @if($projectStatus)
                                    @foreach($projectStatus as $psKey => $psVal)
                                        <li class="nav-item">
                                            <a class="project_status_grid_view nav-link"
                                               id="project-status-{{ $psKey + 1 }}-tab"
                                               data-toggle="tab" href="#project-status-{{ $psKey + 1 }}" role="tab"
                                               aria-controls="project-status-{{ $psKey + 1 }}"
                                               data-id="{{ $psVal->id}}"
                                               aria-selected="false">{{ $psVal->status }}</a>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="show-projects">
                            <p class="showing-records"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tiles">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="project-status-0" role="tabpanel"
                                     aria-labelledby="project-status-0-tab">
                                    <div class="row" id="all-projects"></div>
                                </div>
                                <div class="loader" style="text-align: center;">
                                    <img id="loading-spinner" src="{{asset('assets/images/loading.gif')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="tabs-list-two">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="sec-list-form">
                                <form action="{{route('project.new.index')}}" method="post" id="project-filter-form">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select id="project-status-filter-2" name="project_status"
                                                        class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi"
                                                        data-tick-icon="zmdi-check" data-show-tick="true"
                                                        title="All Status"
                                                        data-live-search="true">
                                                    <option value="0" {{$statusId == 0 ? "selected" : "" }}>All Status
                                                    </option>
                                                    @foreach($projectStatus as $psKey=>$psVal)
                                                        <option value="{{$psVal->id}}"
                                                                {{$statusId == $psVal->id ? "selected" : "" }}
                                                                data-statusId="{{$psVal->id}}">{{$psVal->status}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select id="project-client-filter-2" name="project_client_filter"
                                                        class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi"
                                                        data-tick-icon="zmdi-check" data-show-tick="true"
                                                        title="All Clients"
                                                        data-live-search="true">
                                                    <option value="0" {{$clientId == 0 ? "selected" : "" }}>All
                                                        Clients
                                                    </option>
                                                    @foreach($teamClients as $client)
                                                        <option value="{{$client->id}}"
                                                                {{$clientId == $client->id ? "selected" : "" }}
                                                                data-clientId="{{$client->id}}">{{$client->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <select id="project-agent-filter-2" name="project_agent_filter"
                                                        class="form-control cxm-live-search-fix"
                                                        data-icon-base="zmdi"
                                                        data-tick-icon="zmdi-check" data-show-tick="true"
                                                        title="All Agents"
                                                        data-live-search="true">
                                                    <option value="0" {{$agentId == 0 ? "selected" : "" }}>All Agents
                                                    </option>
                                                    @foreach($members as $agent)
                                                        <option value="{{$agent->id}}"
                                                                {{$agentId == $agent->id ? "selected" : "" }}
                                                                data-agentId="{{$agent->id}}">{{$agent->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        {{--                                        <div class="col-md-2">--}}
                                        {{--                                            <button type="submit" class="btn btn-primary">Filter</button>--}}
                                        {{--                                        </div>--}}
                                    </div>
                                </form>
                            </div>

                            <div class="card">
                                <div class="table-responsive">
                                    <table id="projects_list"
                                           class="table table-striped table-hover theme-color xjs-exportable"
                                           xdata-sorting="false">
                                        <thead>
                                        <tr>
                                            <th>Id#</th>
                                            <th>Project</th>
                                            <th>Tasks</th>
                                            <th>Users</th>
                                            <th>Client</th>
                                            <th>Remaining Days</th>
                                            <th>Priority</th>
                                            <th data-breakpoints="xs md">Status</th>
                                            <th data-breakpoints="sm xs md">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($projectsData as $pKey => $project)

                                            <tr data-index="{{$pKey}}">
                                                <td>{{$project->id}}</td>
                                                <td>
                                                    <a href="{{route('project.show.new', $project->id)}}">{{$project->project_title}}</a>
                                                    {{--
                                                    <a href="{{route('project.new.detail', $project->id)}}">{{$project->project_title}}</a>
                                                    --}}
                                                </td>
                                                <td>Task number</td>
                                                <td>
                                                    @if($members)
                                                        @foreach($members as $mKey => $member)
                                                            <li class="media">
                                                                <a href="{{route('project.new.detail', $project->id)}}"
                                                                   data-lightbox="images" data-title="Main">
                                                                    <img alt="{{$member->name}}"
                                                                         title="{{$member->name}}"
                                                                         class="mr-1 rounded-circle"
                                                                         width="30"
                                                                         src="{{ $member->image && file_exists(public_path('assets/images/profile_images/'). $member->image) ? asset('assets/images/profile_images/'.$member->image) :asset('assets/images/no-results-found.png')}}">
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td>
                                                    <li class="media">
                                                        @php
                                                            $ClientImage = asset('assets/images/no-results-found.png') ;
                                                            $ClientName = $project->getClientUser->name ?? "";
                                                            if(isset($project->getClientUser) && $project->getClientUser->image && file_exists(public_path('assets/images/profile_images/'). $project->getClientUser->image)){
                                                               $ClientImage = asset('assets/images/profile_images/'.$project->getClientUser->image);
                                                            }
                                                        @endphp
                                                        <a href="{{$ClientImage}}" data-lightbox="images"
                                                           data-title="Sara">
                                                            <img alt="{{$ClientName}}" title="{{$ClientName}}"
                                                                 class="mr-3 rounded-circle" width="50"
                                                                 src="{{ $ClientImage}}">
                                                        </a>
                                                    </li>
                                                </td>
                                                <td><span
                                                        class="badge {{ $project->remainingDays()['badgeClass'] }}">{{ $project->remainingDays()['message'] }}</span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge badge-{{$project->priority === 1 ? "info" : ($project->priority === 2 ? "warning" :($project->priority === 3 ?"danger": "secondary"))}}"> {{$project->priority === 1 ? "Low" : ($project->priority === 2 ? "Medium" :($project->priority === 3 ?"High": "Unknown"))}} </span>
                                                </td>
                                                <td>
                                                    <div
                                                        class="badge badge-{{isset($project->getStatus)? $project->getStatus->status_color : "info"}} projects-badge">{{isset($project->getStatus)? $project->getStatus->status : ""}}</div>
                                                </td>
                                                <td>
                                                    <div class="btn-group no-shadow">
                                                        <a href="{{route('project.new.detail', $project->id)}}"
                                                           class="btn btn-light mr-2"><i class="zmdi zmdi-eye"></i></a>
                                                        <div class="dropdown card-widgets">
                                                            <a href="javascript:void(0)" class="btn btn-light"
                                                               data-toggle="dropdown"
                                                               aria-expanded="false"><i
                                                                    class="zmdi zmdi-settings"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a data-id="{{$project->id}}" data-table-id="2"
                                                                   class="dropdown-item has-icon modal-edit-project-ajax editproject"
                                                                   data-toggle="modal"
                                                                   data-target="#EditProjecteModal"
                                                                   href="javascript:void(0);"
                                                                   title="Edit"><i
                                                                        class="zmdi zmdi-edit"></i>
                                                                    Edit</a>
                                                                <a class="dropdown-item has-icon delete-project-alert"
                                                                   href="#" data-project_id="1011"><i
                                                                        class="zmdi zmdi-delete"></i>
                                                                    Delete</a>
                                                            </div>
                                                        </div>
                                                    </div>
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
        </div>
    </main>

    <!-- projects end -->

    <!-- Modals Start-->

    <!-- Create Project Modal-->
    <div class="modal fade" id="projecteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 880px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create New Project</h4>
                </div>
                <div class="alert alert-danger print-error-msg"
                     style="width: 850px; margin: auto; margin-top:10px; display:none">
                    <ul></ul>
                </div>

                <form method="POST" id="create_project_form">
                    <input type="hidden" id="client_type" name="type" value="existing">
                    <div class="modal-body">
                        <div class="col-sm-12">

                            <div>
                                <a href="javascript:void(0)" class="clientType" data-type="new"
                                   data-target-container="client-new-container">New Client</a> |
                                <a href="javascript:void(0)" class="clientType" data-type="existing"
                                   data-target-container="client-existing-container" id="existingClient">Existing
                                    Client</a>
                            </div>

                            <div id="showClient" class="form-group">
                                <select name="client_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Client">
                                    <option value="">Select Client</option>
                                    @foreach($teamClients as $client)
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="name_div">
                                <input type="text" id="name" class="form-control" placeholder="Name"
                                       name="name">
                            </div>
                            <div class="form-group" id="email_div">
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email">
                            </div>
                            <div class="form-group" id="phone_div">
                                <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone">
                            </div>

                            <div id="" class="form-group">
                                <select id="brand_key" name="brand_key" class="form-control show-tick ms select2"
                                        data-placeholder="Select Brand">
                                    <option value="">Select Brand</option>
                                    @foreach($teamBrand as $brand)
                                        <option value="{{$brand->brandKey}}">{{$brand->brandName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="agent_id" name="agent_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Agent">
                                    <option value="">Select Sales Agent</option>
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="logo_category" name="category_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Project Category">
                                    <option value="">Select Project Category</option>
                                    @foreach($projectCategories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" id="title" class="form-control" placeholder="Project Title"
                                       name="title">
                            </div>
                            <div class="form-group">
                            <textarea id="description" class="form-control" placeholder="Description & Details"
                                      name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <select id="priority" name="priority_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Priority">
                                    <option value="" disabled>Select Priority</option>
                                    <option value="1" selected>Low Priority</option>
                                    <option value="2">Medium Priority</option>
                                    <option value="3">High Priority</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-size:10px;">Start Date*</label>
                                <input type="date" id="start_date" class="form-control" placeholder="Start Date"
                                       name="start_date" required value="{{ now()->addDay()->format('Y-m-d') }}"/>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 12px;">DueDate</label>
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date"
                                       name="due_date" value="{{ now()->addDays(2)->format('Y-m-d') }}"
                                       required/>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="" class="form-control" placeholder="Project Cost"
                                           name="project_cost" value="10" required/>
                                </div>
                            </div>

                            {{--                            <div class="form-group">--}}
                            {{--                                <label>Project Status</label>--}}
                            {{--                                <select id="status" name="status_id" class="form-control show-tick ms select2"--}}
                            {{--                                        data-placeholder="Select status" required>--}}
                            {{--                                    <option value="0" disabled>Select Status</option>--}}
                            {{--                                    @if($projectStatus)--}}
                            {{--                                        @foreach($projectStatus as $psKey=>$psVal)--}}
                            {{--                                            <option value="{{$psVal->id}}">{{$psVal->status}}</option>--}}
                            {{--                                        @endforeach--}}
                            {{--                                    @endif--}}
                            {{--                                </select>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addStatusBtn" class="btn btn-primary">SAVE</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Project -->
    <div class="modal fade" id="EditProjecteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 880px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Project</h4>
                </div>

                <form method="POST" id="project_update_form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="project_hdn" class="form-control" name="hdn" value="">
                    <input type="hidden" id="edit_table_id" class="form-control">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div id="showClient" class="form-group">
                                <label style="font-size:12px;">Project Client</label>
                                <select id="edit_client_id" name="client_id" class="form-control show-tick ms select2 "
                                        data-placeholder="Select Client">
                                    @foreach($teamClients as $client)
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <label style="font-size:12px;">Project Brand</label>
                                <select id="edit_brand_key" name="brand_key" class="form-control show-tick ms select2"
                                        data-placeholder="Select Brand" required>
                                    @foreach($teamBrand as $brand)
                                        <option value="{{$brand->brand_key}}">{{$brand->brandName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <label style="font-size:12px;">Project Agent</label>
                                <select id="edit_agent_id" name="agent_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Agent" required>
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div id="" class="form-group">
                                <label style="font-size:12px;">Project Category</label>
                                <select id="edit_category" name="category_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select Project Category" required>
                                    @foreach($projectCategories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-size:12px;">Project Title</label>
                                <input type="text" id="edit_project_title" class="form-control"
                                       placeholder="Project Title" name="title">
                            </div>
                            <div class="form-group">
                                <label style="font-size:12px;">Project Description</label>
                                <textarea id="edit_project_description" class="form-control"
                                          placeholder="Description & Details" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label style="font-size:12px;">Project Priority</label>
                                <select id="edit_priority_id" name="priority_id"
                                        class="form-control show-tick ms select2"
                                        data-placeholder="Select Priority">
                                    <option value="1">Low Priority</option>
                                    <option value="2">Medium Priority</option>
                                    <option value="3">High Priority</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="font-size:12px;">Start Date*</label>
                                <input type="date" id="edit_start_date" class="form-control" placeholder="Due Date"
                                       name="start_date" required/>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 12px;">DueDate</label>
                                <input type="date" id="edit_due_date" class="form-control" placeholder="Due Date"
                                       name="due_date" required/>
                            </div>

                            <div class="form-group">

                                <label style="font-size:12px;">Project Cost</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                    </div>
                                    <input type="number" id="edit_project_cost" class="form-control"
                                           placeholder="Project Cost" name="project_cost" required/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Project Status</label>
                                <select id="edit_status_id" name="status_id" class="form-control show-tick ms select2"
                                        data-placeholder="Select status" required>
                                    @if($projectStatus)
                                        @foreach($projectStatus as $psKey=>$psVal)
                                            <option value="{{$psVal->id}}">{{$psVal->status}}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="addStatusBtn" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    <script>
        $(document).ready(function () {
            /** 1 = grid view and 2 = list view*/
            var list_view = 2;
            list_value = getQueryParam('list-view');
            if (list_value != null) {
                list_view = list_value;
            }

            /** Initializing these empty variables for grid view filter*/
            var isLoading = false, totalProjects = 0, receivedProjects = 0, search_box = null, project_status = 0,
                project_client_filter = null, project_agent_filter = null;

            /** Tab 1 search filter for Grid view*/
            $(' #project-client-filter-1, #project-agent-filter-1').on('change', function () {
                receivedProjects = 0;
                project_client_filter = $("#project-client-filter-1").val();
                project_agent_filter = $("#project-agent-filter-1").val();
                clearAndGetProperty();
            });

            /** Tab 2 search filter for List View */
            $('#project-status-filter-2,#project-client-filter-2, #project-agent-filter-2').on('change', function () {
                $("#project-filter-form").submit();
            });

            /** For Project status grid view & clear and get projects acording to status*/
            $('.project_status_grid_view').click(function () {
                receivedProjects = 0;
                project_status = $(this).attr('data-id');
                clearAndGetProperty();
            });

            /**On Click grid tab updating variable list_view to 1 and received projects to 0 (means null) & clear and get first 10 projects */
            $("body").delegate(".grid-view-button", "click", function () {
                list_view = 1;
                if(receivedProjects == 0) {
                    get_property();
                }
            });

            /** On Click List view tab updating variable to 2 so that we can use is later.  */
            $('.list-view-button').on('click', function () {
                list_view = 2;
            });

            /**By default run get projects if list_view == 1 (1 = grid view)*/
            if (list_view == 1) {
                get_property();
            }

            /** Search box for grid view box-1 == grid view*/
            $('#search-box-1').on("input", function () {
                search_box = $(this).val();
                if (search_box.trim() === '') {
                    search_box = null;
                }
                receivedProjects = 0;
                clearAndGetProperty();
            });

            /** On Scroll get grid view projects is list_view == 1 (1 = grid view)*/
            $(window).scroll(function () {
                if (list_view == 1 && !isLoading) {
                    if ($(window).scrollTop() + $(window).height() > $(document).height() - 500) {
                        if (receivedProjects == totalProjects) {
                            $('#loading-spinner').hide();
                        } else {
                            isLoading = true;
                            if (receivedProjects > 9) {
                                $('#loading-spinner').show();
                            }
                            get_property(function () {
                                isLoading = false;
                            });
                        }
                    }
                }
            });

            /** Clear and get projects for grid view*/
            function clearAndGetProperty() {
                $('#all-projects').empty();
                get_property();
            }

            /** Get projects for grid view*/
            function get_property() {
                var url = '{{ route('project.load.more.project') }}';
                var params = {
                    'list-view': list_view,
                    'offset': receivedProjects,
                    'project_status': project_status,
                    'search_box': search_box,
                    'project_client_filter': project_client_filter,
                    'project_agent_filter': project_agent_filter,
                };
                $.ajax({
                    type: "Get",
                    url: url,
                    data: params,
                    cache: false,
                    async: false,
                    processing: true,
                    dataType: "json",
                    success: function (res) {
                        console.log('html', res);
                        console.log('project_status', project_status);
                        totalProjects = res.total_projects;
                        console.log('Total Projects:', totalProjects);
                        receivedProjects += res.received_projects;
                        console.log('Received Projects:', receivedProjects);

                        var yes;
                        if (receivedProjects > 0) {
                            yes = 1;
                        } else {
                            yes = 0;
                        }
                        $(".show-projects > .showing-records").text('Showing ' + yes + ' to ' + receivedProjects + ' of ' + totalProjects + ' entries')
                        if (receivedProjects == totalProjects) {
                            isLoading = true;
                            $('#loading-spinner').hide();
                        } else {
                            $('#loading-spinner').show();
                        }
                        if (receivedProjects < 10) {
                            isLoading = true;
                            $('#loading-spinner').hide();
                        } else {
                            isLoading = false;
                            $('#loading-spinner').show();
                        }

                        if (receivedProjects < 1) {

                            $('#all-projects').append('<div class="col-md-12"><div class="alert alert-warning" style="margin:12% 0 ;color:rgb(97 123 231);background-color: #fff;border: 2px solid;border-color: rgb(97 123 231); text-align: center;"><strong>No Records Found</strong></div></div>');
                        }
                        $('#all-projects').append(res.all_projects);
                    },
                    error: function (xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
            }

            /** Function to get url param*/
            function getQueryParam(param) {
                const urlSearchParams = new URLSearchParams(window.location.search);
                return urlSearchParams.get(param);
            }

            /** Function to set url param*/
            function setQueryParam(key, value) {
                const url = new URL(window.location.href);
                url.searchParams.set(key, value);
                window.history.replaceState(null, null, url);
            }

            /** Making condition for active and select tab (grid view or list view)*/
            if (list_view == '1') {
                $(".grid-view-button").addClass("active");
                $(".list-view-button").removeClass("active");
                $(".view-filter").removeClass("list-view-filter").addClass("grid-view-filter");
            } else {
                $(".list-view-button").addClass("active");
                $(".grid-view-button").removeClass("active");
                $(".view-filter").addClass("list-view-filter").removeClass("grid-view-filter");
            }

            /** Initializing Datatable For List View*/
            $('#projects_list').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']]
            });

            /** Condition to check New Or Existing Client On Create project*/

            /** On Default, we have to hide these fields so that we will show on condition*/
            $('#name_div, #email_div, #phone_div').hide();

            /** On Default, client dropdown on show*/
            $('#showClient').show();

            /** On selecting whether the client is new or existing*/
            $('.clientType').on('click', function () {
                var client_type = $(this).attr('data-type');
                document.getElementById("client_type").value = client_type;
                /** If new then select client drop down disabled and showing client record fields*/
                if (client_type == 'new') {
                    $('#showClient').hide();
                    $('#name_div').show();
                    $('#email_div').show();
                    $('#phone_div').show();
                } else {
                    /** If existing then select client drop down enabled and hiding client record fields*/
                    $('#name_div').hide();
                    $('#phone_div').hide();
                    $('#email_div').hide();
                    $('#showClient').show();
                }
            });

            /** Project create form submit*/
            $('#create_project_form').on('submit', function (e) {
                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ route('project.new.store') }}",
                    method: 'POST',
                    data: $(this).serialize(), // get all form field value in serialize form
                    success: function (data) {
                        if ($.isEmptyObject(data.error)) {
                            $("#create_project_form")[0].reset();
                            console.log(data);
                            $("#projecteModal").modal('hide');

                            swal("Good job!", "Project successfully Created!", "success")
                                .then(() => {
                                    location.reload();
                                });

                        } else {
                            displayErrors(data.error);
                        }
                    },
                    error: function (data) {
                        displayErrors(data.responseJSON.errors);
                    }
                });
            });

            /** Showing Project details on edit modal */
            $("body").delegate(".editproject", "click", function () {
                console.log('Edit project');
                var project_id = $(this).data('id');
                var table_id = $(this).data('table-id');
                document.getElementById("project_hdn").value = project_id;
                document.getElementById("edit_table_id").value = table_id;
                console.log(project_id);
                $.ajax({
                    type: "GET",
                    url: "{{url('project/')}}/" + project_id + '/edit',
                    success: function (data) {
                        console.log(data);

                        var cxmToDay = (data.project_date_start ? new Date(data.project_date_start) : new Date());
                        let cxmStartDate = cxmToDay.getFullYear() + '-' + (cxmToDay.getMonth() + 1) + '-' + ((cxmToDay.getDate() < '10') ? ('0' + cxmToDay.getDate()) : cxmToDay.getDate());

                        var cxmToDay1 = (data.project_date_due ? new Date(data.project_date_due) : new Date());
                        let cxmDueDate = cxmToDay1.getFullYear() + '-' + (cxmToDay1.getMonth() + 1) + '-' + ((cxmToDay1.getDate() < '10') ? ('0' + cxmToDay1.getDate()) : cxmToDay1.getDate());

                        $('#edit_client_id').val(data.clientid).change();
                        $('#edit_brand_key').val(data.brand_key).change();
                        $('#edit_agent_id').val(data.agent_id).change();
                        $('#edit_category').val(data.category_id).change();
                        $('#edit_priority_id').val(data.priority).change();

                        $('#edit_project_title').val(data.project_title);
                        $('#edit_project_description').val(data.project_description);
                        $('#edit_start_date').val(cxmStartDate);
                        $('#edit_due_date').val(cxmDueDate);
                        $('#edit_project_cost').val(data.project_cost);
                        $('#edit_status_id').val(data.project_status).change();

                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            });

            /** Project update form submit */
            $('#project_update_form').on('submit', function (e) {
                e.preventDefault();
                var bid = $('#project_hdn').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: "{{url('project/')}}/" + bid,
                    method: 'post',
                    data: $(this).serialize(),
                    success: function (result) {
                        console.log(result);
                        $("#EditProjecteModal").modal('hide');
                        swal("Good job!", "Project successfully Updated!", "success");
                        setTimeout(function () {
                            if ($("#edit_table_id").val() == '2') {
                                setQueryParam('list-view', '2');
                            } else if ($("#edit_table_id").val() == '1') {
                                setQueryParam('list-view', '1');
                            }
                            window.location.href = window.location.href;
                        }, 1000);
                    },
                    error: function (data) {
                        displayErrors(data.responseJSON.errors);
                    }
                });


            });

            /** Project Display error*/
            function displayErrors(errors) {
                // Clear previous error messages
                $(".error-msg").remove();

                $.each(errors, function (field, messages) {
                    var inputField = $('[name="' + field + '"]');
                    var errorDiv = $('<div class="error-msg" style="color:red;"></div>');

                    $.each(messages, function (index, message) {
                        errorDiv.append('<p>' + message + '</p>');
                    });

                    inputField.closest('.form-group').append(errorDiv);
                });
            }
        });


    </script>

@endpush


{{--<!-- Todo modals start -->--}}
{{--<div class="create-project-modal">--}}
{{--    <div class="modal fade" id="creatprojectModal" tabindex="-1" role="dialog"--}}
{{--         aria-labelledby="exampleModalCenterTitle"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalCenterTitle">Create Project</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-6">--}}
{{--                                <label for="title">Title</label>--}}
{{--                                <input type="text" class="form-control" id="title" placeholder="Title">--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-md-3">--}}
{{--                                <label for="status">Status</label>--}}
{{--                                <select id="status" class="form-control">--}}
{{--                                    <option selected>Not Start</option>--}}
{{--                                    <option>Ongoing</option>--}}
{{--                                    <option>Finished</option>--}}
{{--                                    <option>On Hold</option>--}}
{{--                                    <option>Cancelled</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-md-3">--}}
{{--                                <label for="inputState">Cost</label>--}}
{{--                                <div class="input-group mb-3">--}}
{{--                                    <div class="input-group-prepend">--}}
{{--                                        <span class="input-group-text" id="basic-addon1">$</span>--}}
{{--                                    </div>--}}
{{--                                    <input type="number" class="form-control" aria-label="Username"--}}
{{--                                           aria-describedby="basic-addon1" type="number" id="quantity"--}}
{{--                                           name="quantity"--}}
{{--                                           min="1" max="5">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}


{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-6">--}}
{{--                                <label for="start_date">Start Date</label>--}}
{{--                                <input type="date" id="start_date" class="form-control" placeholder="Due Date"--}}
{{--                                       name="start_date" required="">--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-md-6">--}}
{{--                                <label for="end_date">End Date</label>--}}
{{--                                <input type="date" id="end_date" class="form-control" placeholder="Due Date"--}}
{{--                                       name="end_date" required="">--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="description">Description</label>--}}
{{--                                <textarea class="form-control" id="description" rows="3"--}}
{{--                                          placeholder="Description"></textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="priority">Priority</label>--}}
{{--                                <select id="priority" class="form-control">--}}
{{--                                    <option selected>Select Priroity</option>--}}
{{--                                    <option>Low Priority</option>--}}
{{--                                    <option>Medium Priority</option>--}}
{{--                                    <option>High Priority</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="slUser">Select Users (Make Sure You Add Yourself To Project)</label>--}}
{{--                                <select id="slUser" class="form-control">--}}
{{--                                    <option selected></option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                    <option>Select Users</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="slClients">Select Clients</label>--}}
{{--                                <select id="slClients" class="form-control">--}}
{{--                                    <option selected></option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                    <option>Select Clients</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <button type="button" class="btn btn-primary">Add</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="create-project-modal">--}}
{{--    <div class="modal fade" id="addmilestone" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"--}}
{{--         aria-hidden="true">--}}
{{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}
{{--            <div class="modal-content">--}}
{{--                <div class="modal-header">--}}
{{--                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Milestone</h5>--}}
{{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                        <span aria-hidden="true">&times;</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                <div class="modal-body">--}}
{{--                    <form>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-6">--}}
{{--                                <label for="title">Title</label>--}}
{{--                                <input type="text" class="form-control" id="title" placeholder="Title">--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-md-3">--}}
{{--                                <label for="status">Status</label>--}}
{{--                                <select id="status" class="form-control">--}}
{{--                                    <option selected>Incomplete</option>--}}
{{--                                    <option>Complete</option>--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-md-3">--}}
{{--                                <label for="inputState">Cost</label>--}}
{{--                                <div class="input-group mb-3">--}}
{{--                                    <div class="input-group-prepend">--}}
{{--                                        <span class="input-group-text" id="basic-addon1">$</span>--}}
{{--                                    </div>--}}
{{--                                    <input type="number" class="form-control" aria-label="Username"--}}
{{--                                           aria-describedby="basic-addon1" type="number" id="quantity"--}}
{{--                                           name="quantity"--}}
{{--                                           min="1" max="5">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="form-row">--}}
{{--                            <div class="form-group col-md-12">--}}
{{--                                <label for="description">Description</label>--}}
{{--                                <textarea class="form-control" id="description" rows="3"--}}
{{--                                          placeholder="Description"></textarea>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--                <div class="modal-footer">--}}
{{--                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->--}}
{{--                    <button type="button" class="btn btn-primary">Add</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!-- Default Size -->--}}
{{--<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h4 class="title" id="defaultModalLabel">Change Status</h4>--}}
{{--                <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">--}}
{{--            </div>--}}

{{--            <div class="modal-body">--}}
{{--                <select id="project-status" name="status" class="form-control show-tick ms select2"--}}
{{--                        data-placeholder="Select" required>--}}
{{--                    @foreach($projectStatus as $status)--}}
{{--                        <option value="{{ $status->id }}">{{ $status->status }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" id="changeProjectStatus" class="btn btn-success btn-round">SAVE CHANGES--}}
{{--                </button>--}}
{{--                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!-- Default Size -->--}}
{{--<div class="modal fade" id="assignAccountManagerModal" tabindex="-1" role="dialog">--}}
{{--    <div class="modal-dialog" role="document">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h4 class="title" id="defaultModalLabel">Assign Account Manager</h4>--}}
{{--                <input type="hidden" id="account_hdn" class="form-control" name="account_hdn" value="">--}}
{{--            </div>--}}

{{--            <div class="modal-body">--}}
{{--                <select id="account-manager" name="status" class="form-control show-tick ms select2"--}}
{{--                        data-placeholder="Select" required>--}}
{{--                    @foreach($members as $am)--}}
{{--                        <option value="{{ $am->id }}">{{ $am->name }}</option>--}}
{{--                    @endforeach--}}
{{--                </select>--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <button type="button" id="changeAccountManager" class="btn btn-success btn-round">SAVE CHANGES--}}
{{--                </button>--}}
{{--                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<!-- Todo modals end -->--}}
