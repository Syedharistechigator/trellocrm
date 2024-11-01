@extends('admin.layouts.app')@section('cxmTitle', 'Member')

@section('content')

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Member List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li> <li class="breadcrumb-item">Team</li>
                            <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <button class="btn btn-success btn-icon rounded-circle" type="button" data-toggle="modal" data-target="#teamModal">
                            <i class="zmdi zmdi-plus"></i></button>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table id="TeamTable" class="table table-striped table-hover theme-color xjs-exportable" data-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>Picture</th>
                                        <th style="width:50px;">Member</th>
                                        <th>Contact</th>
                                        <th class="hidden-md-down">Email</th>
                                        @if(auth()->guard('admin')->user()->type === 'super')
                                            <th class="hidden-md-down">User Type</th>
                                        @endif
                                        <th class="hidden-md-down">Target</th>
                                        <th class="hidden-md-down">Achieved</th>
                                        <th class="hidden-md-down">Team Name</th>
                                        {{--                                        <th class="text-center hidden-md-down">Status</th>--}}
                                        <th>Created Date</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td class="align-middle">
                                                @if(filter_var($member->image, FILTER_VALIDATE_URL) && in_array(strtolower(pathinfo($member->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                                    <div style="position: relative; display: inline-block;">
                                                        <object data="{!! $member->image !!}" height="70px" width="70px" class="rounded-circle img-thumbnail avatar">
                                                            <img class="rounded-circle img-thumbnail avatar" src="{{$member->image}}" alt="{{$member->name}}" style="height:70px; width:70px; object-fit:cover;" loading="lazy">
                                                        </object>
                                                        <span class="status-icon" style="position: absolute; bottom: 0; right: 0; color: white;">
                                                            {!! ($member->status == 1) ? '<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' : '<span class="zmdi zmdi-close-circle text-danger" title="Inactive"></span>' !!}
                                                        </span>
                                                    </div>
                                                @else
                                                    @if($member->image && file_exists(public_path('assets/images/profile_images/') . $member->image) && in_array(strtolower(pathinfo($member->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                                        <div style="position: relative; display: inline-block;">
                                                            <img class="rounded-circle img-thumbnail avatar" src="{{ asset('assets/images/profile_images/' . $member->image) }}" alt="{{$member->name}}" style="height:70px; width:70px; object-fit:cover;" loading="lazy">
                                                            <span class="status-icon" style="position: absolute; bottom: 0; right: 0; color: white;">
                                                                {!! ($member->status == 1) ? '<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' : '<span class="zmdi zmdi-close-circle text-danger" title="Inactive"></span>' !!}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <div style="position: relative; display: inline-block;">
                                                            <img class="rounded-circle img-thumbnail avatar" src="{{ asset('assets/images/profile_av.jpg') }}" alt="{{$member->name}}" style="height:70px; width:70px; object-fit:cover;" loading="lazy">
                                                            <span class="status-icon" style="position: absolute; bottom: 0; right: 0; color: white;">
                                                                {!! ($member->status == 1) ? '<span class="zmdi zmdi-check-circle text-success" title="Active"></span>' : '<span class="zmdi zmdi-close-circle text-danger" title="Inactive"></span>' !!}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <a class="single-user-name text-warning position-relative d-inline-block" href="{{route('memberProfile',$member->id)}}">
                                                    @if($member->type == 'lead')
                                                        <img class="crown" src="{{ asset('assets/images/crown.png') }}">
                                                    @endif
                                                    {{$member->name}}
                                                </a><br> <small>{{$member->designation}}</small>
                                            </td>
                                            <td class="align-middle">
                                                <i class="zmdi zmdi-phone text-warning"></i> {{$member->phone}}</td>
                                            <td class="align-middle">
                                                <span class="zmdi zmdi-email text-warning"></span> {{$member->email}}
                                            </td>
                                            @if(auth()->guard('admin')->user()->type === 'super')
                                                <td class="align-middle">
                                                    <span class="zmdi zmdi-email text-warning"></span> {{ucfirst($member->type == 'tm-client' ? 'Tm-Viewer' : ($member->type == 'staff' ? 'Sales' :$member->type))}}
                                                </td>
                                            @endif
                                            <td class="align-middle">${{$member->target}}</td>
                                            <td class="align-middle">${{$member->amount}}
                                                <div class="progress">
                                                        <?php
                                                        if ($member->percentage >= 90) {
                                                            $color = 'l-green';
                                                        } elseif ($member->percentage >= 70) {
                                                            $color = 'l-blue';
                                                        } elseif ($member->percentage >= 50) {
                                                            $color = 'l-amber';
                                                        } else {
                                                            $color = 'l-red';
                                                        }
                                                        ?>
                                                    <div class="progress-bar {{$color}}" role="progressbar" aria-valuenow="{{$member->percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$member->percentage}}%;"></div>
                                                </div>
                                            </td>
                                            <td class="align-middle text-nowrap">{{$member->getTeamName->name}}
                                            </td>
                                            {{--                                            <td class="align-middle text-center">--}}
                                            {{--                                                --}}
                                            {{--                                            </td>--}}
                                            <td class="align-middle text-nowrap">{{$member->created_at->format('j F, Y')}}</td>
                                            <td class="align-middle text-nowrap">
                                                <a href="javascript:void(0);" class="btn btn-warning btn-sm btn-round edit_member_pass " data-toggle="modal" data-target="#edit_member_pass_Modal" data-id="{{$member->id}}"><i class="zmdi zmdi-key"></i></a>
                                                <a href="javascript:void(0);" class="btn btn-warning btn-sm btn-round edit_member" data-toggle="modal" data-target="#edit_member_Modal" data-id="{{$member->id}}"><i class="zmdi zmdi-edit"></i></a>
                                                <a data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round memberInactiveButton" data-id="{{$member->id}}"><i class="zmdi zmdi-delete"></i></a>
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

    <!-- Edit Team Member Pass -->
    <div class="modal fade" id="edit_member_pass_Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Team Password</h4>
                </div>
                <form id="edit-emp-pass-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" id="member_hdn" class="form-control" name="hdn" value="">
                                <input type="text" id="edit_pass" class="form-control" placeholder="Change Password" name="edit_pass">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="button" id="generatePassword" class="btn btn-secondary">Generate Password
                            </button>
                            <button type="button" id="copyPassword" class="btn btn-primary" onclick="myFunction()">Copy Password
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-round updateemployeePassBtn">Update</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Team Member -->
    <div class="modal fade" id="edit_member_Modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Edit Member</h4>
                </div>
                <form id="edit_team-Emp-Form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" id="member_hdn" class="form-control" name="hdn" value="">
                                <input type="text" id="edit_name" class="form-control" placeholder="Name" name="edit_name">
                            </div>
                            <div class="form-group">
                                <input type="email" id="edit_email" class="form-control" placeholder="Email" name="edit_email"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="edit_phone" class="form-control" placeholder="Phone" name="edit_phone"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="edit_designation" class="form-control" placeholder="Designation" name="edit_designation"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="edit_pseudo_name" class="form-control" placeholder="Pseudo Name" name="edit_pseudo_name">
                            </div>
                            <div class="form-group">
                                <input type="email" id="edit_pseudo_email" class="form-control" placeholder="Pseudo Email" name="edit_pseudo_email">
                            </div>
                            <div class="form-group">
                                <select id="edit-user-access" name="edit_user_access" class="form-control show-tick"
                                        data-placeholder="Select User Access" required>
                                    <option value="0">Full Access</option>
                                    <option value="1">Crm</option>
                                    <option value="2">Trello</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="edit_type" name="edit_type" class="form-control show-tick" data-placeholder="Select Type" required>
                                    <option value="staff" selected>Sales User</option>
                                    <option value="lead">Team Lead</option>
                                    <option value="ppc">PPC</option>
                                    <option value="executive">Executive</option>
                                    <option value="third-party-user">Third Party User</option>
                                    <option value="qa">Quality Assurance</option>
                                    <option value="hob">Head of Brand</option>
                                    <option value="hop">Head of Production</option>
                                    <option value="tm-user">Tm User</option>
                                    <option value="tm-client">Tm Viewer</option>
                                    <option value="tm-ppc">Tm PPC</option>
                                </select>
                            </div>
                            {{--                            <div class="form-group has-department-div">--}}
                            {{--                                <input type="checkbox" id="has-department-edit" style="transform: scale(1.5);--}}
                            {{--                                margin-right: 20px;" value="yes" placeholder="Department" name="has_department"/>--}}
                            {{--                                <label for="has-department-edit">Has Department</label>--}}
                            {{--                            </div>--}}
                            <div class="form-group department-div" style="display: none">
                                <select id="edit_assigned_departments" name="assigned_departments[]" class="form-control show-tick" multiple data-placeholder="Select Department" required>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group assigned_teams_container" style="display: none;">
                                <select id="edit_assigned_team_key" name="assigned_team_key[]" class="form-control show-tick" multiple data-placeholder="Select">
                                    <option value="" disabled>Select Team</option>
                                    <option value="0">All Teams</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->team_key }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="number" id="edit_target" class="form-control" placeholder="Add Target" name="edit_target"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="edit_image" class="form-control" placeholder="Image" name="edit_image"/>
                            </div>
                            <div class="form-group">
                                <select id="edit_status" name="edit_status" class="form-control show-tick" data-placeholder="Select Type" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" id="edit_lead_special_access" style="transform: scale(1.5);
                                margin-right: 20px;" value="yes" placeholder="Special Lead Access" name="edit_lead_special_access"/>
                                <label>Full Lead Access</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-round updateemployeeBtn">Update</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Team Member -->
    <div class="modal fade" id="teamModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Create Member</h4>
                </div>
                <form method="POST" id="team-Emp-Form">
                    <div class="modal-body">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="text" id="name" class="form-control" placeholder="Name" name="name" autocomplete="off"/>
                            </div>
                            <div class="form-group">
                                <input type="email" id="email" class="form-control" placeholder="Email" name="email" autocomplete="off"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="phone" class="form-control" placeholder="Phone" name="phone" autocomplete="off"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="designation" class="form-control" placeholder="Designation" name="designation"/>
                            </div>
                            <div class="form-group">
                                <input type="text" id="pseudo_name" class="form-control" placeholder="Pseudo Name" name="pseudo_name">
                            </div>
                            <div class="form-group">
                                <input type="email" id="pseudo_email" class="form-control" placeholder="Pseudo Email" name="pseudo_email">
                            </div>
                            <div class="form-group">
                                <select id="user-access" name="user_access" class="form-control show-tick"
                                        data-placeholder="Select User Access" required>
                                    <option value="0" selected>Full Access</option>
                                    <option value="1">Crm</option>
                                    <option value="2">Trello</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="type" name="type" class="form-control show-tick" data-placeholder="Select Type" required>
                                    <option value="staff" selected>Sales User</option>
                                    <option value="lead">Team Lead</option>
                                    <option value="ppc">PPC</option>
                                    <option value="executive">Executive</option>
                                    <option value="third-party-user">Third Party User</option>
                                    <option value="qa">Quality Assurance</option>
                                    <option value="hob">Head of Brand</option>
                                    <option value="hop">Head of Production</option>
                                    <option value="tm-user">Tm User</option>
                                    <option value="tm-client">Tm Viewer</option>
                                    <option value="tm-ppc">Tm PPC</option>
                                </select>
                            </div>
                            {{--                            <div class="form-group has-department-div">--}}
                            {{--                                <input type="checkbox" id="has-department" style="transform: scale(1.5);--}}
                            {{--                                margin-right: 20px;" value="yes" placeholder="Department" name="has_department"/>--}}
                            {{--                                <label for="has-department">Has Department</label>--}}
                            {{--                            </div>--}}
                            <div class="form-group department-div" style="display: none">
                                <select id="assigned_departments" name="assigned_departments[]" class="form-control show-tick" multiple data-placeholder="Select Department" required>
                                    @foreach($departments as $d_key =>$department)
                                        <option value="{{ $department->id }}" {{$d_key == 0 ? "selected" : ""}}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group assigned_teams_container" style="display: none;">
                                <select id="assigned_team_key" name="assigned_team_key[]" class="form-control show-tick" multiple data-placeholder="Select">
                                    <option value="" disabled>Select Team</option>
                                    <option value="0">All Teams</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->team_key }}">{{ $team->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="number" id="target" class="form-control" placeholder="Add Target" name="target"/>
                            </div>
                            {{-- <div class="form-group">
                                <input type="text" id="image" class="form-control" placeholder="Image" name="image"/>
                            </div> --}}
                            <div class="form-group">
                                <input type="checkbox" id="lead_special_access" style="transform: scale(1.5);
                                margin-right: 20px;" value="yes" placeholder="Special Lead Access" name="lead_special_access"/>
                                <label>Full Lead Access</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-round">SAVE</button>
                        <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('cxmScripts')
    @include('admin.team.script')
    <script>
        $(document).ready(function () {
            $('#TeamTable').DataTable().destroy();
            $('#TeamTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: false,
            });
            $('[type=search]').attr('id', "dt-search-box-" + randomNumber);
        });

        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        var randomNumber = getRandomInt(1, 20);
        $('[type=search]').attr('id', "dt-search-box-" + randomNumber);
    </script>
@endpush
