@extends('admin.layouts.app')@section('cxmTitle', 'Edit Board List')

@section('content')
    @push('css')
        <style>
            .bootstrap-select .dropdown-menu li.selected a {
                background-color: #ff9948 !important;
                color: #fff !important;
            }
        </style>
    @endpush
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Board List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i
                                        class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.board.list.index')}}">Board List</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('admin.board.list.index') }}" class="btn btn-success btn-icon rounded-circle"
                           type="button"><i class="zmdi zmdi-arrow-left"></i></a>
                        <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button">
                            <i class="zmdi zmdi-arrow-right"></i></button>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <!-- Basic Validation -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Edit</strong> Board List</h2>
                            </div>
                            <div class="body">
                                <form id="board_list_update_form" method="POST">
                                    @csrf
                                    <input type="hidden" id="hdn" class="form-control" name="hdn"
                                           value="{{$board_list->id}}">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="title">Title</label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                       value="{{$board_list->title}}" minlength="3" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="department">Department</label>
                                                <select id="department" name="department" class="form-control"
                                                        title="Select Department" data-placeholder="Select Department"
                                                        data-icon-base="zmdi" data-tick-icon="zmdi-check"
                                                        data-show-tick="true" data-live-search="true"
                                                        data-live-search-id="department-search">
                                                    <option value="" selected>None</option>
                                                    @foreach($departments as $department)
                                                        <option
                                                            value="{{ $department->id }}" {{ optional($board_list->getDepartment)->id == $department->id ? 'selected class="btn-warning"' : '' }}>{{ $department->name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="position">Position</label>
                                                <input id="position" type="number" class="form-control"
                                                       value="{{$board_list->position}}" name="position" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" data-id="{{$board_list->id}}"
                                                       class="custom-control-input toggle-class change-status"
                                                       id="customSwitchstatus{{$board_list->id}}"
                                                       name="status" {{$board_list->status == 1 ? "checked" : ""}}>
                                                <label class="custom-control-label"
                                                       for="customSwitchstatus{{$board_list->id}}">Status</label>
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_data" type="submit" value="Submit"
                                           class="btn btn-warning btn-round">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('cxmScripts')
    @include('admin.board-list.script')
@endpush
