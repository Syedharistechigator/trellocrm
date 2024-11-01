@extends('admin.layouts.app')

@section('cxmTitle', 'Create Board List')

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
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('admin.board.list.index')}}">Board List</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button"><i
                                class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button"><i
                                class="zmdi zmdi-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Create New</strong> Board List</h2>
                            </div>
                            <div class="body">
                                <form id="board_list_form" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="title">Title</label>
                                                <input type="text" class="form-control" name="title" minlength="3"
                                                       required>
                                            </div>
                                        </div>
{{--                                        <div class="col-md-6">--}}
{{--                                            <div class="form-group form-float">--}}
{{--                                                <label for="team_key">Team</label>--}}
{{--                                                <select id="team_key" name="team_key[]" class="form-control show-tick" multiple data-placeholder="Select" required>--}}
{{--                                                    <option value="" disabled >Select Team</option>--}}
{{--                                                    <option value="0">All Teams</option>--}}
{{--                                                    @foreach($teams as $team)--}}
{{--                                                        <option value="{{ $team->team_key }}">{{ $team->name }}</option>--}}
{{--                                                    @endforeach--}}
{{--                                                </select>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="department">Department</label>
                                                <select id="department" name="department" class="form-control show-tick" data-placeholder="Select Department">
                                                    <option value="" selected >None</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label for="position">Position</label>
                                                <input type="number" class="form-control" name="position" required>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
                                        </div>

                                    </div>
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
