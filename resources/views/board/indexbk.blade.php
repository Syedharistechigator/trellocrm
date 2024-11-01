@extends('layouts.app')

@section('cxmTitle', 'Board')

@section('content')
<link rel="stylesheet" href="{{asset('assets/css/board/plugin.css')}}">
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>{{Auth::user()->getTeam->name}} Board</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i>
                                TG</a></li>
                        <li class="breadcrumb-item">{{Auth::user()->getTeam->name}}</li>
                        <li class="breadcrumb-item active"> List</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                            class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    @include('includes.cxm-top-right-toggle-btn')
                </div>
            </div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="modal-edit-task trigger--fire-modal-4"></div>
            <div class="modal-add-task-details trigger--fire-modal-3"></div>
            <div class="col-12">
                <div class="board" data-plugin="dragula"
                    data-containers="[{!! $board_lists->pluck('id')->map(function($id) {return '&quot;board-list-'.$id.'&quot;';})->implode(',') !!}]">
                    @foreach ($board_lists as $key => $board_list)
                    <div class="tasks animated {{$board_list->sort_tasks}}" data-sr-id="{{$board_list->id}}"
                        data-cardname="{{$board_list->title}}">
                        <div class="mt-0 task-header text-uppercase">{{$board_list->title}} ( <span
                                class="count">{{$board_list->getBoardListCards->count()}}</span>)
                        </div>
                        <div id="board-list-{{$board_list->id}}" data-status="{{$board_list->id}}"
                            class="task-list-items">
                            @foreach($board_list->getBoardListCards as $key => $board_list_card)
                            <div class="card mb-0" id="board-list-card-{{$board_list_card->id}}"
                                data-id="{{$board_list_card->id}}" data-sort="{{$board_list->id}}">
                                <div class="card-body p-3">
                                    <div class="card-header-action float-right">
                                        <div class="dropdown card-widgets">
                                            <a href="javascript:void(0);" class="btn dropdown-toggle"
                                                data-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item has-icon edit-board-card-modal"
                                                    data-board-list-card-id="{{$board_list_card->id}}"
                                                    data-toggle="modal" data-target="#edit-board-card-modal"
                                                    href="javascript:void(0);">
                                                    <i class="fas fa-pencil-alt"></i> Edit </a>
                                                <a class="dropdown-item has-icon delete-task-alert"
                                                    data-board-list-card-id="{{$board_list_card->id}}"
                                                    data-project-id="936" href="javascript:void(0);">
                                                    <i class="far fa-trash-alt"></i> Delete </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="{{route('project.show.new', $board_list_card->project_id)}}"
                                            data-id="{{$board_list_card->id}}"
                                            class="text-body modal-add-task-details-ajax">{{$board_list_card->title}}</a>
                                    </div>
                                    <span
                                        class="badge badge-{{$board_list_card->priority === 1 ? "info" : ($board_list_card->priority === 2 ? "warning" :($board_list_card->priority === 3 ?"danger": "secondary"))}} projects-badge">{{$board_list_card->priority === 1 ? "Low" : ($board_list_card->priority === 2 ? "Medium" :($board_list_card->priority === 3 ?"High": "Unknown"))}}</span>
                                    <p class="mt-2 mb-2">
                                        <span class="text-nowrap d-inline-block">
                                            <i class="fas fa-comments text-muted"></i>
                                            <b>1</b>
                                            Comments
                                        </span>
                                    </p>
                                    <small
                                        class="float-right text-muted mt-2">{{$board_list_card->created_at->format('j F, Y')}}
                                        , {{$board_list_card->created_at->format('h:i:s A')}}<br><span
                                            class="float-right">{{$board_list_card->created_at->diffForHumans()}}</span></small>
                                    <figure class="avatar mr-2 avatar-sm" data-toggle="tooltip" data-title="Bhavna">
                                        <img alt="image" src="#" class="rounded-circle">
                                    </figure>

                                    <p class="mt-2 mb-2">
                                        {{--                                                  <span class="badge {{ $board_list_card->remainingDays()['badgeClass'] }}">--}}
                                        <span>
                                            {{ $board_list_card->remainingDays()['message'] }}
                                        </span>
                                    </p>

                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 p-0 task-header text-center">
                            <button class="btn btn-success btn-icon rounded-circle add-card-btn"
                                data-board-list-id="{{$board_list->id}}" type="button" data-toggle="modal"
                                data-target="#create-board-card-modal" title="Add Task"><i
                                    class="zmdi zmdi-plus"></i></button>
                            Add a card
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>


<section class="modbtn-sec">
    <div class="container">
        <div class="row">
            <div class="col-mad-12">
                <button class="drag-mod" data-toggle="modal" data-target="#addmilestone">Card Pop UP</button>
            </div>
        </div>
    </div>
</section>

{{--modal start--}}
{{-- create modal--}}
<div class="modal fade" id="create-board-card-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Add Card</h4>
            </div>
            <form id="create_board_card_form" method="POST">
                @csrf
                <input type="hidden" name="board_list_id" value="">
                <div class="modal-body">
                    <div class="body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="Project">Project</label>
                                    <select name="project_id" required class="form-control p-0" id="Project">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project_key => $project_value)
                                        <option value="{{$project_value->id}}">{{$project_value->project_title}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="description">Description</label>
                                    <textarea class="form-control" id="description" name="description"
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{--                                <div class="col-md-6">--}}
                            {{--                                    <div class="form-group form-float">--}}
                            {{--                                        <label for="assign_to">Assign To</label>--}}
                            {{--                                        <input type="text" class="form-control" name="assign_to" required>--}}
                            {{--                                        <select class="form-control">--}}
                            {{--                                            <option value=""></option>--}}
                            {{--                                        </select>--}}
                            {{--                                    </div>--}}
                            {{--                                </div>--}}
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-round" type="submit">Create Card</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- edit modal--}}
<div class="modal fade" id="edit-board-card-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" id="edit-modal-content" style="background-repeat: no-repeat;
    background-size: contain;">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Edit Card</h4>
            </div>
            <form id="edit_board_card_form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="edit_board_list_card_id" name="edit_board_list_card_id" value="">
                <div class="modal-body">
                    <div class="body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="image">Cover Image</label>
                                    <input type="file" class="form-control" id="cover_image" name="cover_image">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="edit_project_id">Project</label>
                                    <select name="edit_project_id" required class="form-control p-0"
                                        id="edit_project_id">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project_key => $project_value)
                                        <option value="{{$project_value->id}}">{{$project_value->project_title}}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="edit_title">Title</label>
                                    <input type="text" class="form-control" id="edit_title" name="edit_title" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="edit_description">Description</label>
                                    <textarea class="form-control" id="edit_description" name="edit_description"
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="edit_assign_to">Assign To</label>
                                    <select name="edit_assign_to[]" required class="form-control p-0"
                                        id="edit_assign_to" title="Select user to assign card" multiple="multiple">
                                        @foreach($users as $user)
                                        <option value="{{$user->name}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-float">
                                    <label for="edit_due_date">Due Date</label>
                                    <input type="date" class="form-control" id="edit_due_date" name="edit_due_date"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-round" type="submit">Update Card</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </form>
        </div>
    </div>
</div>




<div class="create-project-modal">
    <div class="modal fade" id="addmilestone" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Urgent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="pop-detail">
                                    <div class="pd-one">
                                        <div class="membr">
                                            <h5>Members</h5>
                                            <ul class="mem-list">
                                                <li>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle orange"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            AB
                                                        </button>
                                                        <div class="dropdown-menu profile-card"
                                                            aria-labelledby="dropdownMenuButton">
                                                            <div class="pchead">
                                                                <div class="pc-name">
                                                                    <div class="short-name"><span
                                                                            class="orange">AB</span></div>
                                                                    <div class="short-detail">
                                                                        <h3>Abdul Basit</h3>
                                                                        <h4>@kennethbrown59</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="javascript:;" class="view-profile">View
                                                                Profile</a>
                                                            <div class="pcfoot">
                                                                <a href="javascript:;" class="view-profile">Remove from
                                                                    card</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle orange"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            AB
                                                        </button>
                                                        <div class="dropdown-menu profile-card"
                                                            aria-labelledby="dropdownMenuButton">
                                                            <div class="pchead">
                                                                <div class="pc-name">
                                                                    <div class="short-name"><span
                                                                            class="orange">AB</span></div>
                                                                    <div class="short-detail">
                                                                        <h3>Abdul Basit</h3>
                                                                        <h4>@kennethbrown59</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <a href="javascript:;" class="view-profile">View
                                                                Profile</a>
                                                            <div class="pcfoot">
                                                                <a href="javascript:;" class="view-profile">Remove from
                                                                    card</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle orange"
                                                            type="button" id="dropdownMenuButton" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <div class="dropdown-menu add-member-drop"
                                                            aria-labelledby="dropdownMenuButton">
                                                            <div class="add-member">
                                                                <h4 class="mem-heading">Member</h4>
                                                                <form>
                                                                    <div class="form-group">
                                                                        <input type="text" class="form-control"
                                                                            id="searchMember"
                                                                            aria-describedby="searchHelp"
                                                                            placeholder="Search members">
                                                                    </div>
                                                                </form>
                                                                <h4 class="bm-heading">Board Member</h4>
                                                                <div class="sletmem-prof">
                                                                    <ul>
                                                                        <li><span class="orange">SS</span></li>
                                                                        <li><span class="blue">AB</span></li>
                                                                        <li><span class="orange">AZ</span></li>
                                                                        <li><span class="blue">AK</span></li>
                                                                        <li><span class="orange">GT</span></li>
                                                                        <li><span class="blue">HT</span></li>
                                                                        <li><span class="orange">MA</span></li>
                                                                        <li><span class="blue">R</span></li>
                                                                    </ul>
                                                                </div>
                                                                <button class="show-btn">Show other Workspace
                                                                    members</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <div class="member-profile">

                                                </div>
                                            </ul>
                                        </div>
                                        <!-- <div class="d-flex">
                                            <div class="pop-list">
                                                <div class="">
                                                    <h4>Notification</h4>
                                                    <ul>
                                                        <li><a href="javascript:;"><i class="far fa-eye"></i> Watch</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>











                                    <h4 class="description-heading">Description</h4>
                                    <div class="comment-box">
                                        <form>
                                            <div class="form-group">
                                                <!-- <textarea class="form-control" rows="3"
                                                    placeholder="Enter your description here..."></textarea> -->
                                                <div>
                                                    <textarea
                                                        id="tiny"> &lt;p&gt;Welcome to the TinyMCE jQuery example!&lt;/p&gt;</textarea>
                                                </div>

                                            </div>
                                        </form>
                                    </div>

                                    <div class="des-div-flex">
                                        <h4 class="description-heading">Attachments</h4>
                                        <div class="dropdown">
                                            <a href="javascript:;" class="att-add" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">Add</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Attach</h4>
                                                    <form>
                                                        <h3 class="bm-heading">Attach a file from your computer</h3>
                                                        <p>You can also drag and drop files to upload them.</p>
                                                        <div class="upload-btn-wrapper">
                                                            <button class="btn">Upload a file</button>
                                                            <input type="file" name="file-name" />
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="bm-heading">Search or paste a link</label>
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Find recent links or paste a new link">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="bm-heading">Display text (optional)</label>
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Text to display">
                                                        </div>
                                                    </form>
                                                    <h3 class="bm-heading">Recently Viewed</h3>
                                                    <ul class="viewMain-list">
                                                        <li>
                                                            <a href="javascript:;">
                                                                <div class="view-list">
                                                                    <div class="v-icon"><i class="fas fa-desktop"></i>
                                                                    </div>
                                                                    <div class="v-detail">
                                                                        <div class="task-name">The Farmstead</div>
                                                                        <span class="board-title">Website
                                                                            Projects</span>
                                                                        <span class="board-time">Viewed 5 hours
                                                                            ago</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;">
                                                                <div class="view-list">
                                                                    <div class="v-icon"><i class="fas fa-desktop"></i>
                                                                    </div>
                                                                    <div class="v-detail">
                                                                        <div class="task-name">The Farmstead</div>
                                                                        <span class="board-title">Website
                                                                            Projects</span>
                                                                        <span class="board-time">Viewed 5 hours
                                                                            ago</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="last-button">
                                                        <button class="btn">Cancel</button>
                                                        <button class="btn">Insert</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="attachment-div">
                                        <a href="javascript:;" class="attachment-thumbnail">
                                            <span class="att-name">docx</span>
                                        </a>
                                        <div class="attachment-thumbnail-detail">
                                            <h6 class="attachment-file-name">Giving.docx</h6>
                                            <span class="attatchment-time">Added yesterday at 10:09 PM</span>
                                            <span class="attatchment-link"><a href="javasript:;">Comment</a></span>
                                            <span class="attatchment-link"><a href="javasript:;">Delete</a></span>
                                            <span class="attatchment-link"><a href="javasript:;">Edit</a></span>
                                        </div>
                                    </div>



                                    <div class="des-div-flex">
                                        <h4 class="description-heading">Activity</h4>
                                        <a href="javascript:;" class="att-add">Show Details</a>
                                    </div>
                                    <div class="coment-div">
                                        <div class="comt-one">
                                            <form>
                                                <div class="form-group">
                                                    <textarea class="form-control status-box" rows="3"
                                                        placeholder="Enter your comment here..."></textarea>
                                                </div>
                                            </form>
                                            <div class="button-group pull-right">
                                                <a href="#" class="btn com-btn btn-primary">Post</a>
                                            </div>
                                        </div>
                                        <ul class="posts">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="pop-list">
                                    <h4>Add to Card</h4>
                                    <ul>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="far fa-user"></i>Members</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Member</h4>
                                                    <form>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Search members">
                                                        </div>
                                                    </form>
                                                    <h4 class="bm-heading">Board Member</h4>
                                                    <div class="sletmem-prof">
                                                        <ul>
                                                            <li><span class="orange">SS</span></li>
                                                            <li><span class="blue">AB</span></li>
                                                            <li><span class="orange">AZ</span></li>
                                                            <li><span class="blue">AK</span></li>
                                                            <li><span class="orange">GT</span></li>
                                                            <li><span class="blue">HT</span></li>
                                                            <li><span class="orange">MA</span></li>
                                                            <li><span class="blue">R</span></li>
                                                        </ul>
                                                    </div>
                                                    <button class="show-btn">Show other Workspace
                                                        members</button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown">
                                            <a href="javascript:;" id="dropdownMenuButton" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"><i
                                                    class="fas fa-tag"></i>Labels</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Member</h4>
                                                    <form>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Search Labels">
                                                        </div>

                                                        <label class="bm-heading">Board Member</label>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="exampleCheck1">
                                                            <label class="form-check-label new-mock "
                                                                for="exampleCheck1">Need
                                                                More Amazing Mock Design</label>
                                                            <span><i class="far fa-edit"></i></span>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="exampleCheck2">
                                                            <label class="form-check-label new-mock"
                                                                for="exampleCheck2"></label>
                                                            <span><i class="far fa-edit"></i></span>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="exampleCheck3">
                                                            <label class="form-check-label urgent"
                                                                for="exampleCheck3">Urgent</label>
                                                            <span><i class="far fa-edit"></i></span>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="exampleCheck4">
                                                            <label class="form-check-label urgent"
                                                                for="exampleCheck4"></label>
                                                            <span><i class="far fa-edit"></i></span>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="exampleCheck5">
                                                            <label class="form-check-label high-priority"
                                                                for="exampleCheck5">Most
                                                                High Priority
                                                            </label>
                                                            <span><i class="far fa-edit"></i></span>
                                                        </div>
                                                    </form>

                                                    <button class="show-btn">Create a new label</button>
                                                    <button class="show-btn">Show more label</button>
                                                    <button class="show-btn">Enable Coloblind friendly mode</button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="far fa-check-square"></i>Checklist</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Add checklist</h4>
                                                    <form>
                                                        <div class="form-group">
                                                            <label class="bm-heading">Title</label>
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp" placeholder="Checklist">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="exampleFormControlSelect1">Copy items
                                                                from…</label>
                                                            <select class="form-control" id="exampleFormControlSelect1">
                                                                <option>none</option>
                                                                <option>Checklist</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Add</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="far fa-clock"></i>Dates</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Dates</h4>
                                                    <div id="datepicker" class="calendar"></div>
                                                    <button class="show-btn">Show other Workspace
                                                        members</button>
                                                </div>
                                            </div>
                                        </li>


                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="fas fa-paperclip"></i>Attachment</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Attach</h4>
                                                    <form>
                                                        <h3 class="bm-heading">Attach a file from your computer</h3>
                                                        <p>You can also drag and drop files to upload them.</p>
                                                        <div class="upload-btn-wrapper">
                                                            <button class="btn">Upload a file</button>
                                                            <input type="file" name="file-name" />
                                                        </div>

                                                        <div class="form-group">
                                                            <label class="bm-heading">Search or paste a link</label>
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Find recent links or paste a new link">
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="bm-heading">Display text (optional)</label>
                                                            <input type="text" class="form-control" id="searchMember"
                                                                aria-describedby="searchHelp"
                                                                placeholder="Text to display">
                                                        </div>
                                                    </form>
                                                    <h3 class="bm-heading">Recently Viewed</h3>
                                                    <ul class="viewMain-list">
                                                        <li>
                                                            <a href="javascript:;">
                                                                <div class="view-list">
                                                                    <div class="v-icon"><i class="fas fa-desktop"></i>
                                                                    </div>
                                                                    <div class="v-detail">
                                                                        <div class="task-name">The Farmstead</div>
                                                                        <span class="board-title">Website
                                                                            Projects</span>
                                                                        <span class="board-time">Viewed 5 hours
                                                                            ago</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="javascript:;">
                                                                <div class="view-list">
                                                                    <div class="v-icon"><i class="fas fa-desktop"></i>
                                                                    </div>
                                                                    <div class="v-detail">
                                                                        <div class="task-name">The Farmstead</div>
                                                                        <span class="board-title">Website
                                                                            Projects</span>
                                                                        <span class="board-time">Viewed 5 hours
                                                                            ago</span>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <div class="last-button">
                                                        <button class="btn">Cancel</button>
                                                        <button class="btn">Insert</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <!-- <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="far fa-folder"></i>Cover
                                            </a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Cover</h4>

                                                    <h3 class="bm-heading">Size</h3>
                                                    <figure>
                                                        <img src="" alt="img">
                                                        <img src="" alt="img">
                                                    </figure>

                                                    <h3 class="bm-heading">Colors</h3>
                                                    <ul class="color-list">
                                                        <li><span class="list-diamension m-green"></span></li>
                                                        <li><span class="list-diamension m-yellow"></span></li>
                                                        <li><span class="list-diamension m-orange"></span></li>
                                                        <li><span class="list-diamension m-red"></span></li>
                                                        <li><span class="list-diamension m-purple"></span></li>
                                                    </ul>

                                                    <form>
                                                        <h3 class="bm-heading">Attachments</h3>
                                                        <div class="upload-btn-wrapper border-none">
                                                            <button class="btn">Upload a file</button>
                                                            <input type="file" name="file-name" />
                                                        </div>
                                                        <p>Tip: Drag an image on to the card to upload it.</p>
                                                    </form>

                                                    <h3 class="bm-heading">Photos from Unsplash</h3>
                                                    <ul class="unsplash-list">
                                                        <li><a href="javascript:;">
                                                                <figure><img src="assest" alt=""></figure>
                                                            </a></li>
                                                    </ul>


                                                    <button class="show-btn">Search for Photos</button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="fas fa-comments"></i>Custom
                                                Field</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Custom Fields</h4>
                                                    <figure>
                                                        <img src="" alt="custom field img">
                                                    </figure>
                                                </div>
                                            </div>
                                        </li> -->
                                    </ul>
                                </div>

                                <!-- <div class="pop-list">
                                    <h4>Power-Ups</h4>
                                    <ul>
                                        <li><a href="javascript:;"><i class="fas fa-plus"></i>Add Power-Ups</a></li>
                                    </ul>
                                </div> -->

                                <!-- <div class="pop-list">
                                    <h4>Automation</h4>
                                    <ul>

                                        <li class="dropdown show">
                                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Add Button
                                            </a>

                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuLink">


                                                <h4 class="mem-heading">Add button</h4>
                                                <h3 class="bm-heading">Button templates</h3>

                                                <a class="dropdown-item" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">Move card to...</a>
                                                <div class="dropdown-menu add-member-drop new-drop-menu">
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                </div>


                                                <a class="dropdown-item" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">Copy card to...</a>
                                                <div class="dropdown-menu add-member-drop new-drop-menu">
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                </div>



                                                <a class="dropdown-item" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">Add Label...</a>
                                                <div class="dropdown-menu add-member-drop new-drop-menu">
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                </div>


                                                <a class="dropdown-item" href="#" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false">Join Card...</a>
                                                <div class="dropdown-menu add-member-drop new-drop-menu">
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                    <a href="#" class="dropdown-item">asdf</a>
                                                </div>








                                            </div>
                                        </li>

                                    </ul>
                                </div> -->


                                <div class="pop-list">
                                    <h4>Actions</h4>
                                    <ul>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="fas fa-arrow-right"></i>Move</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Move card</h4>
                                                    <h3 class="bm-heading">Select destination</h3>

                                                    <form>
                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">Board</span>
                                                                        <span class="bord-value">Web Projects</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <optgroup label="Alpha Design Crew">
                                                                            <option>Website Projects (current)
                                                                            </option>
                                                                        </optgroup>
                                                                        <optgroup label="Book Writing">
                                                                            <option>Chelsea Web Projects
                                                                            </option>
                                                                            <option>Professional Writers Help
                                                                            </option>
                                                                        </optgroup>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mx-0">
                                                            <div class="col-md-8 p-1">
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">List</span>
                                                                        <span class="bord-value">App
                                                                            Development</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <option>New Task (current)
                                                                        </option>
                                                                        <option>Inprogress
                                                                        </option>
                                                                        <option>Needs Clearification
                                                                        </option>

                                                                        <option>Incomplete Brief
                                                                        </option>

                                                                        <option>Review
                                                                        </option>

                                                                        <option>Revision
                                                                        </option>
                                                                        <option>Revision Review
                                                                        </option>
                                                                        <option>Waiting for Feedback
                                                                        </option>
                                                                        <option>Completed
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 p-1">
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">Position</span>
                                                                        <span class="bord-value">1</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <option>1 (current)
                                                                        </option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="last-button mt-2">
                                                            <button class="btn">Move</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </li>
                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="far fa-clone"></i>Copy</a>

                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Copy card</h4>


                                                    <form>
                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Title</h3>
                                                                <textarea class="form-control"
                                                                    id="exampleFormControlTextarea1"
                                                                    rows="3">Custom Card</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Keep…</h3>
                                                                <div class="form-check form-check-margin">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="exampleCheck1">
                                                                    <label class="form-check-label"
                                                                        for="exampleCheck1">Member (1)</label>
                                                                </div>
                                                                <div class="form-check form-check-margin">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="exampleCheck2">
                                                                    <label class="form-check-label"
                                                                        for="exampleCheck2">Attachments (2)</label>
                                                                </div>
                                                                <div class="form-check form-check-margin">
                                                                    <input type="checkbox" class="form-check-input"
                                                                        id="exampleCheck3">
                                                                    <label class="form-check-label"
                                                                        for="exampleCheck3">Comments (1)</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Copy to…</h3>
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">Board</span>
                                                                        <span class="bord-value">Web Projects</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <optgroup label="Alpha Design Crew">
                                                                            <option>Website Projects (current)
                                                                            </option>
                                                                        </optgroup>
                                                                        <optgroup label="Book Writing">
                                                                            <option>Chelsea Web Projects
                                                                            </option>
                                                                            <option>Professional Writers Help
                                                                            </option>
                                                                        </optgroup>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mx-0">
                                                            <div class="col-md-8 p-1">
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">List</span>
                                                                        <span class="bord-value">App
                                                                            Development</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <option>New Task (current)
                                                                        </option>
                                                                        <option>Inprogress
                                                                        </option>
                                                                        <option>Needs Clearification
                                                                        </option>

                                                                        <option>Incomplete Brief
                                                                        </option>

                                                                        <option>Review
                                                                        </option>

                                                                        <option>Revision
                                                                        </option>
                                                                        <option>Revision Review
                                                                        </option>
                                                                        <option>Waiting for Feedback
                                                                        </option>
                                                                        <option>Completed
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 p-1">
                                                                <div class="form-group select-top">
                                                                    <button class="select-btn">
                                                                        <span class="bord-label">Position</span>
                                                                        <span class="bord-value">1</span>
                                                                    </button>
                                                                    <select class="form-control"
                                                                        id="exampleFormControlSelect1">
                                                                        <option>1 (current)
                                                                        </option>

                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="last-button mt-2">
                                                            <button class="btn">Create Card</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </li>
                                        <li><a href="javascript:;"><i class="far fa-address-card"></i>Make
                                                Template</a></li>
                                        <li><a href="javascript:;"><i class="far fa-file-archive"></i>Archive</a></li>

                                        <li class="dropdown"><a href="javascript:;" id="dropdownMenuButton"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                                    class="fas fa-share-alt"></i>Share</a>
                                            <div class="dropdown-menu add-member-drop"
                                                aria-labelledby="dropdownMenuButton">
                                                <div class="add-member">
                                                    <h4 class="mem-heading">Share and Move</h4>
                                                    <a href="javascript:;" class="view-profile">Print...</a>
                                                    <a href="javascript:;" class="view-profile">Export JSON</a>

                                                    <form class="share-form">
                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Link to this card</h3>
                                                                <input type="text" class="form-control"
                                                                    id="exampleFormControlInput1"
                                                                    placeholder="Link here">

                                                                <a href="javascript:;" class="qr-code">Show QR Code</a>
                                                            </div>
                                                        </div>

                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Embed this card</h3>
                                                                <input type="text" class="form-control"
                                                                    id="exampleFormControlInput1"
                                                                    placeholder="Embed code  here">
                                                            </div>
                                                        </div>

                                                        <div class="row mx-0">
                                                            <div class="col-md-12 p-1">
                                                                <h3 class="bm-heading">Email for this card</h3>
                                                                <input type="text" class="form-control"
                                                                    id="exampleFormControlInput1"
                                                                    placeholder="Put email here">
                                                            </div>
                                                            <p class="share-paragraph">Emails sent to this address will
                                                                appear as
                                                                a
                                                                comment by
                                                                you on the card</p>
                                                        </div>

                                                    </form>

                                                    <p class="share-paragraph">Card #415</p>
                                                    <div class="v-detail sv-detail">
                                                        <span class="board-title">Added Nov 14 at 1:07 AM</span>
                                                        <span class="board-time"><a href="javascript:;" class="qr-code">
                                                                Delete</a></span>
                                                    </div>


                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Add</button>
                </div> -->
            </div>
        </div>
    </div>
</div>
{{--modal end--}}
@endsection
@push('cxmScripts')
@include('board.script')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-jquery@1/dist/tinymce-jquery.min.js"></script>



<script>
$(function() {
    $("#datepicker").datepicker({
        firstDay: 1
    });
});
</script>


<script>
$('textarea#tiny').tinymce({
    height: 500,
    menubar: false,
    plugins: [
        'a11ychecker', 'advlist', 'advcode', 'advtable', 'autolink', 'checklist', 'export',
        'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'searchreplace', 'visualblocks',
        'powerpaste', 'fullscreen', 'formatpainter', 'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | code table help'
});
</script>

@endpush