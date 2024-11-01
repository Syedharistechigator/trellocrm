@extends('layouts.app')@section('cxmTitle', 'Project')

@section('content')

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Project list</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Project</li>
                            <li class="breadcrumb-item active">list</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#projecteModal">
                            <i class="zmdi zmdi-plus"></i></button>
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
                                        <th>ID #</th>
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
                                            <td>{{$project->id}}</td>
                                            <td class="align-middle">
                                                <a class="text-info" href="{{route('project.show', $project->id)}}"><i class="zmdi zmdi-open-in-new"></i>
                                                    <strong>{{$project->project_title}}</strong></a>
                                                <div><small>Cost: ${{$project->project_cost}}</small></div>
                                            </td>
                                            <td class="align-middle">{{$project->brandName}}</td>
                                            <td class="align-middle">
                                                <div class="form-row">
                                                    <div class="col-auto">
                                                        <img class="img-thumbnail rounded-circle" style="width:50px; height:50px;" src="{!! $project->agentImage && file_exists(public_path('assets/images/profile_images/'). $project->agentImage) ? asset('assets/images/profile_images/'.$project->agentImage) :asset('assets/images/crown.png') !!}" alt="{{$project->agentName}}">
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
                                            <td class="align-middle">
                                                <span class="badge badge-{{$project->statusColor}} rounded-pill">{{$project->status}}</span>
                                            </td>
                                            <td class="align-middle">
                                                @if(Auth::user()->type == 'lead')
                                                    <a title="Assign Account Manager" data-id="{{$project->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-info btn-sm btn-round AccountManager" data-toggle="modal" data-target="#assignAccountManagerModal">
                                                        <span class="zmdi zmdi-plus-circle"></span> </a>
                                                @endif
                                                <button data-id="{{$project->id}}" title="Edit" class="btn btn-info btn-sm btn-round editproject" data-toggle="modal" data-target="#EditProjecteModal">
                                                    <i class="zmdi zmdi-edit"></i></button>
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

    <!-- Create Project -->
    <div class="modal fade" id="projecteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" style="max-width: 880px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create New Project</h4>
                </div>
                <div class="alert alert-danger print-error-msg" style="width: 850px; margin: auto; margin-top:10px; display:none">
                    <ul></ul>
                </div>
                <form method="POST" id="create_project_form">
                    <input type="hidden" id="client_type" name="type">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div id="showClient" class="form-group">
                                <select name="client_id" class="form-control show-tick ms select2" data-placeholder="Select Client" required>
                                    <option value="0">Select Client</option>
                                    @foreach($teamClients as $client)
                                        <option value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="name_div" style="display: none;">
                                <input type="text" id="first_name" class="form-control" placeholder="Name" name="first_name">
                            </div>
                            <div class="form-group" id="email_div" style="display: none;">
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email">
                            </div>
                            <div class="form-group" id="phone_div" style="display: none;">
                                <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone">
                            </div>
                            <div style="width: 221px; float: right; font-size: 12px; text-align: right; margin: 10px 0 10px 0;">
                                <a href="javascript:void(0)" class="clientType" data-type="new" data-target-container="client-new-container">New Client</a> |
                                <a href="javascript:void(0)" class="clientType" style="background-color: gray; color: white;  padding: 4px; border: gray solid 1px; border-radius: 8px;" data-type="existing" data-target-container="client-existing-container">Existing Client</a>
                            </div>
                            <div id="" class="form-group">
                                <select id="brand_key" name="brand_key" class="form-control show-tick ms select2" data-placeholder="Select Brand" required>
                                    <option>Select Brand</option>
                                    @foreach($teamBrand as $brand)
                                        <option value="{{$brand->brandKey}}">{{$brand->brandName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="agent_id" name="agent_id" class="form-control show-tick ms select2" data-placeholder="Select Agent" required>
                                    <option>Select Sales Agent</option>
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="logo_category" name="category_id" class="form-control show-tick ms select2" data-placeholder="Select Project Category" required>
                                    <option>Select Project Category</option>
                                    @foreach($projectCategories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" id="title" class="form-control" placeholder="Project Title" name="title">
                            </div>
                            <div class="form-group">
                                <textarea id="description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                            </div>
                            <!-- <div class="summernote">
                                Description & Details
                                <br/>
                                <p>Enter Project Details (Package Details)</p>
                            </div> -->
                            <div class="form-group">
                                <label style="font-size:10px;">Start Date*</label>
                                <input type="date" id="start_date" class="form-control" placeholder="Due Date" name="start_date" required/>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 12px;">Deadline</label>
                                <input type="date" id="due_date" class="form-control" placeholder="Due Date" name="due_date" required/>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="" class="form-control" placeholder="Project Cost" name="project_cost" required/>
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

    <!-- Default Size -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Change Status</h4>
                    <input type="hidden" id="status_hdn" class="form-control" name="status_hdn" value="">
                </div>
                <div class="modal-body">
                    <select id="project-status" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>
                        @foreach($projectStatus as $status)
                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="changeProjectStatus" class="btn btn-success btn-round">SAVE CHANGES</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
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
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div id="" class="form-group">
                                <select id="edit_brand_key" name="brand_key" class="form-control show-tick ms" data-placeholder="Select Brand" required>
                                    <option>Select Brand</option>
                                    @foreach($teamBrand as $brand)
                                        <option value="{{$brand->brand_key}}">{{$brand->brandName}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="edit_agent_id" name="agent_id" class="form-control show-tick ms" data-placeholder="Select Agent" required>
                                    <option>Select Sales Agent</option>
                                    @foreach($members as $member)
                                        <option value="{{$member->id}}">{{$member->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="" class="form-group">
                                <select id="edit_logo_category" name="category_id" class="form-control show-tick ms" data-placeholder="Select Project Category" required>
                                    <option>Select Project Category</option>
                                    @foreach($projectCategories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" id="edit_project_title" class="form-control" placeholder="Project Title" name="title">
                            </div>
                            <div class="form-group">
                                <textarea id="edit_project_description" class="form-control" placeholder="Description & Details" name="description"></textarea>
                            </div>
                            <div class="form-group">
                                <label style="font-size:10px;">Start Date*</label>
                                <input type="date" id="edit_start_date" class="form-control" placeholder="Due Date" name="start_date" required/>
                            </div>
                            <div class="form-group">
                                <label style="font-size: 12px;">Deadline</label>
                                <input type="date" id="edit_due_date" class="form-control" placeholder="Due Date" name="due_date" required/>
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="zmdi zmdi-money"></i></span>
                                </div>
                                <input type="number" id="edit_project_cost" class="form-control" placeholder="Project Cost" name="project_cost" required/>
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


    <!-- Default Size -->
    <div class="modal fade" id="assignAccountManagerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Assign Account Manager</h4>
                    <input type="hidden" id="account_hdn" class="form-control" name="account_hdn" value="">
                </div>
                <div class="modal-body">
                    <select id="account-manager" name="status" class="form-control show-tick ms select2" data-placeholder="Select" required>
                        @foreach($members as $am)
                            <option value="{{ $am->id }}">{{ $am->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="changeAccountManager" class="btn btn-success btn-round">SAVE CHANGES</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    @include('project.script')
@endpush
