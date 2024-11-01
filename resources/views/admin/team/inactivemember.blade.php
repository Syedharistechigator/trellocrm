@extends('admin.layouts.app')

@section('cxmTitle', 'Member')

@section('content')

<section class="content">
    <div class="body_scroll">
        <div class="block-header">
            <div class="row">
                <div class="col-lg-7 col-md-6 col-sm-12">
                    <h2>InActive Member List</h2>

                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                        <li class="breadcrumb-item">Team</li>
                        <li class="breadcrumb-item active"> List</li>
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
                        <div class="table-responsive">
                            <table id="TeamTable" class="table table-striped table-hover theme-color js-exportable" data-sorting="false">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">Member</th>
                                        <th></th>
                                        <th>Contact</th>
                                        <th class="hidden-md-down">Email</th>
                                        <th class="hidden-md-down">Target</th>
                                        <th class="hidden-md-down">Achived</th>
                                        <th class="text-center hidden-md-down">Status</th>
                                        <th>Created Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $member)
                                    <tr>
                                        <td class="align-middle">
                                            @if($member->image)
                                                <img class="rounded-circle img-thumbnail avatar" src="{{$member->image}}" alt="{{$member->name}}" style="height:70px; width:70px; object-fit:cover;">
                                            @else
                                                <img class="rounded-circle img-thumbnail avatar" src="assets/images/xs/avatar1.jpg" alt="No Image" style="height:70px; width:70px; object-fit:cover;">
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a class="text-warning single-user-name position-relative d-inline-block" href="{{route('memberProfile',$member->id)}}">
                                                @if($member->type == 'lead')
                                                <img class="crown" src="{{ asset('assets/images/crown.png') }}" >
                                                @endif
                                                {{$member->name}}
                                            </a><br>
                                            <small  style="text-transform:capitalize;">{{$member->designation}}</small>
                                        </td>

                                        <td class="align-middle hidden-md-down">
                                           <i class="zmdi zmdi-whatsapp mr-2"></i>
                                           {{$member->phone}}
                                        </td>
                                        <td class="align-middle">{{$member->email}}</td>
                                        <td class="align-middle"><strong>${{$member->target}}</strong></td>
                                        <td class="align-middle hidden-md-down">
                                            <strong>${{$member->amount}}</strong>
                                            <div class="progress">
                                                <?php
                                                   if($member->percentage >= 90){
                                                        $color = 'l-green';
                                                   }elseif($member->percentage >= 70){
                                                        $color = 'l-blue';
                                                   }elseif($member->percentage >= 50){
                                                        $color = 'l-amber';
                                                   }else{
                                                        $color = 'l-red';
                                                   }
                                                ?>

                                                <div class="progress-bar {{$color}}" role="progressbar" aria-valuenow="{{$member->percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$member->percentage}}%;"></div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            {!! ($member->status == 1)?'<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' :'<span class="zmdi zmdi-close-circle text-danger" title="Inactive"></span>' !!}
                                        </td>
                                        <td class="align-middle">{{$member->created_at->format('j F, Y')}}</td>
                                        <td class="align-middle">
                                            <a href="#" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                            <a data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round teamDelButton"><i class="zmdi zmdi-delete"></i></a>
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

<!-- Create Team Member -->
<div class="modal fade" id="teamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="title" id="defaultModalLabel">Create Team</h4>
            </div>

            <!-- <form method="POST" id="team-member-Form"> -->

            <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                        <select id="team_hnd" name="team_key" class="form-control show-tick ms" data-placeholder="Select Team" required>
                            @foreach($teams as $team)
                            <option value="{{$team->team_key}}">{{$team->name}}</option>
                            @endforeach
                        </select>
                        </div>

                        <div class="form-group">
                            <input type="text" id="name" class="form-control" placeholder="Name" name="name">
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" class="form-control" placeholder="Email" name="email" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" />
                        </div>
                        <div class="form-group">
                            <input type="text" id="designation" class="form-control" placeholder="Designation" name="designation" />
                        </div>
                        <div class="form-group">
                        <select id="type" name="type" class="form-control show-tick ms select2" data-placeholder="Select Type" required>
                            <option value="lead">Team Lead</option>
                            <option value="user">User</option>
                        </select>
                        </div>
                        <div class="form-group">
                            <input type="number" id="target" class="form-control" placeholder="Add Target" name="target" />
                        </div>

                        <div class="form-group">
                            <input type="text" id="image" class="form-control" placeholder="Image" name="image" />
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="addTeamBtn" class="btn btn-success btn-round">SAVE</button>
                <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('cxmScripts')
    @include('admin.team.script')
@endpush
