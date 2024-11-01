@extends('admin.layouts.app')

@section('cxmTitle', 'Create Team')

@section('content')
<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>Team</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{route('team.index')}}">Team</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-warning btn-icon rounded-circle right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Create New</strong> Team</h2>
                        </div>
                        <div id="alerts"></div>
                        <div class="alert alert-success" role="alert" id="#show12" style="display:none;">

                            <strong>Bootstrap</strong> Better check yourself, <a target="_blank" href="https://getbootstrap.com/docs/4.2/components/input-group/">View More</a>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="zmdi zmdi-close"></i></span>
                            </button>
                        </div>

                        <div class="body">
                            <form id="team_form" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team Name</label>
                                            <input type="text" class="form-control" name="name" minlength="3" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team Lead</label>
                                            <select class="form-control show-tick ms select2" data-placeholder="Select" name='team_lead' required>
                                                <option>Select Team Lead</option>
                                                @foreach($members as $member)
                                                <option value="{{$member->id}}">{{$member->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Publish</label>
                                            <select class="form-control show-tick ms select2" data-placeholder="Select" name='status' required>
                                                <option></option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-warning btn-round" type="submit">SUBMIT</button>
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
    @include('admin.team.script')
@endpush
