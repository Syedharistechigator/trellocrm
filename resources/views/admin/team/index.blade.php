@extends('admin.layouts.app')@section('cxmTitle', 'Team')

@section('content')

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Team List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i>  {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Team</li> <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        <a href="{{ route('team.create') }}" class="btn btn-success btn-icon rounded-circle" type="button"><i class="zmdi zmdi-plus"></i></a>
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="table-responsive">
                                <table id="TeamTable" class="table table-striped table-hover theme-color js-exportable" xdata-sorting="false">
                                    <thead>
                                    <tr>
                                        <th>Team Name</th>
                                        <th width="300">Team Lead</th>
                                        <th width="300">Team Members</th>
                                        <th>Assign Brands</th>
                                        <!-- <th data-breakpoints="xs md">Created Date</th> -->
                                        <th data-breakpoints="xs md">Status</th>
                                        <th data-breakpoints="sm xs md">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($teamsData as $team)
                                        <tr>
                                            <td class="align-middle">
                                                {{$team->name}}
                                                <div class="col-amber">#{{$team->team_key}}</div>
                                            </td>
                                            <td class="align-middle">{{$team->teamLead}}</td>
                                            <td class="align-middle">
                                                <ul class="list-unstyled team-info margin-0">
                                                    @foreach(App\Models\User::where(['team_key'=>$team->team_key,'status'=>1,'type'=>'staff'])->get() as $user)
                                                        @if($user->image  && file_exists(public_path('assets/images/profile_images/'). $user->image))
                                                            <li><img src="{{ asset('assets/images/profile_images/'.$user->image) }}" title="{{$user->name}}" alt="Avatar" style="height:30px; width:30px; object-fit:cover;"></li>
                                                        @else
                                                            <li><img src="{{ asset('assets/images/xs/avatar3.jpg') }}" title="{{$user->name}}" alt="Avatar" style="height:30px; width:30px; object-fit:cover;"></li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td class="align-middle">{{ $team->assignBrands != " " ? substr(trim($team->assignBrands), 0, -1) :"No Brand Assing" }}</td>
                                            {{--<td class="align-middle">{{ $team->created_at->diffForHumans() }}</td>--}}
                                            <td class="align-middle">
                                                <div class="custom-control custom-switch">
                                                    <!-- <span style="left: -41px; position: relative; top: 2px;">Unpublish</span> -->
                                                    <input data-id="{{$team->id}}" type="checkbox" class="custom-control-input team-toggle-class" id="customSwitch{{$team->id}}" {{ $team->status ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitch{{$team->id}}"></label>
                                                    <!-- <span style="position: relative; top: 2px;">Publish</span> -->
                                                </div>
                                            </td>
                                            <td class="align-middle text-nowrap">
                                                <button data-id="{{$team->team_key}}" title="Create Team Member" type="button" class="btn btn-warning btn-sm btn-round xyz" data-toggle="modal" data-target="#teamModal">
                                                    <i class="zmdi zmdi-account-add"></i></button>
                                                <button data-id="{{$team->team_key}}" title="Assign Brands" type="button" class="btn btn-info btn-sm btn-round abc" data-toggle="modal" data-target="#defaultModal">
                                                    <i class="zmdi zmdi-plus-circle"></i></button>
                                                <a href="{{route('team.edit',[$team->id],'/edit')}}" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-edit"></i></a>
                                                <a data-id="{{$team->id}}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round teamDelButton"><i class="zmdi zmdi-delete"></i></a>
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

    <!-- Modal Dialogs ====== -->
    <!-- Default Size -->
    <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Assign Brands</h4>
                </div>
                <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$team->team_key }}">
                <div class="modal-body">
                    <select id="brand_key" name="brand[]" class="form-control show-tick ms select2" multiple data-placeholder="Select" required>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->brand_key }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" id="assingBrandBtn" class="btn btn-success btn-round">SAVE CHANGES</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Team Member -->
    <div class="modal fade" id="teamModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">Assign Members</h4>
                </div>
                <!-- <form method="POST" id="team-member-Form"> -->
                <input type="hidden" id="team_hnd" class="form-control" name="team_key" value="{{$team->team_key }}">
                <div class="modal-body">
                    <!--  -->
                    <div class="text-center">
                        @foreach(App\Models\User::where(['type'=>'staff','status'=>1])->get() as $cxmUser)
                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-success rounded-circle p-1 cxm-btn-user-w-icon" title="{{$cxmUser->name}}">
                                    <span class="cxm-user-check"><i class="zmdi zmdi-check"></i></span>
                                    <input type="checkbox" autocomplete="off" name="agents" value="{{$cxmUser->id}}">
                                    <img class="rounded-pill" src="{{ ($cxmUser->image)?($cxmUser->image) :asset('assets/images/xs/avatar3.jpg') }}" alt="{{$cxmUser->name}}" style="height:50px; width:50px; object-fit:cover;">

                                    @if($cxmUser->image && filter_var($cxmUser->image, FILTER_VALIDATE_URL) && in_array(strtolower(pathinfo($cxmUser->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                        <object data="{!! $cxmUser->image !!} " height="80px" width="80px" class="img-thumbnail rounded-circle">
                                            <img class="rounded-pill" src="{{$cxmUser->image}}" alt="{{$cxmUser->name}}" style="height:50px; width:50px; object-fit:cover;" id="profile-image" loading="lazy">
                                        </object>
                                    @else
                                        @if($cxmUser->image && file_exists(public_path('assets/images/profile_images/'). $cxmUser->image) && in_array(strtolower(pathinfo($cxmUser->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                            <img class="rounded-pill" src="{{ asset('assets/images/profile_images/' . $cxmUser->image) }}" alt="{{$cxmUser->name}}" style="height:50px; width:50px; object-fit:cover;" id="profile-image" loading="lazy">

                                        @else
                                            <img class="rounded-pill" src="{{ asset('assets/images/xs/avatar3.jpg') }}" alt="{{$cxmUser->name}}" style="height:50px; width:50px; object-fit:cover;" id="profile-image" loading="lazy">
                                        @endif
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    {{--
                    <div class="col-sm-12">
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
                    </div>--}}
                </div>
                <div class="modal-footer">
                    <button type="button" id="addTeamBtn" class="btn btn-success btn-round waves-effect">SAVE</button>
                    <button type="button" class="btn btn-danger btn-round" data-dismiss="modal">CLOSE</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('cxmScripts')
    @include('admin.team.script')
@endpush
