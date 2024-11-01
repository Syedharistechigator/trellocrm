@extends('admin.layouts.app')
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
                        <li class="breadcrumb-item active">Edit</li>
                    </ul>
                    <button class="btn btn-primary btn-icon mobile_menu" type="button"><i class="zmdi zmdi-sort-amount-desc"></i></button>
                </div>
                <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                    <button class="btn btn-warning btn-icon btn-round right_icon_toggle_btn" type="button"><i class="zmdi zmdi-arrow-right"></i></button>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="header">
                            <h2><strong>Update</strong> Team</h2>
                        </div>

                        <div class="body">

                            <form id="team_update_form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{$team->id}}">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team Name</label>
                                            <input type="text" class="form-control" id="brandName" name="name" value="{{$team->name}}" minlength="3" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Team Lead</label>
                                            <select class="form-control show-tick ms select2" id="team_lead" data-placeholder="Select" name='team_lead' required>
                                                <option value="0">Select Team Lead</option>
                                                @foreach($members as $member)
                                                <option value="{{$member->id}}" {{($team->team_lead == $member->id) ? 'selected' : ''}}>{{$member->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="email_address">Publish</label>
                                            <select class="form-control show-tick ms select2" data-placeholder="Select" name='status' id="status" required>
                                                <option></option>
                                                <option value="1" <?php if($team->status == 1) {echo "selected";}?>>Yes</option>
                                                <option value="0" <?php if($team->status == 0) {echo "selected";}?>>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <label for="tm-ppc">Tm PPC</label>
                                            <select id="tm-ppc" name="tm_ppc[]" class="change form-control cxm-live-search-fix" data-icon-base="zmdi" data-tick-icon="zmdi-check" data-show-tick="true" title="Select Tm PPC" data-live-search="true" multiple>
                                                <option disabled>Select Tm PPC</option>
                                                @foreach($tm_ppc_users as $tm_ppc_user)
                                                    <option value="{{$tm_ppc_user->id}}"{{ optional($team->getTmPpcUsers)->contains('team_key', $tm_ppc_user->team_key) ? 'selected class="btn-warning"' : '' }}>{{$tm_ppc_user->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="header px-3 pb-0">
                                                <h2><strong>Assigned</strong> Members</h2>
                                            </div>
                                            <div class="body shadow">
                                                <div class="form-row">
                                                @foreach(App\Models\User::whereIn('type', ['staff', 'tm-user'])->where('status', 1)->get() as $gsbUser)
                                                    <div class="col-md-3 text-center">
                                                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                            <label class="btn btn-success rounded-circle p-1 cxm-btn-user-w-icon {{  $team->team_key === $gsbUser->team_key ? 'active' : '' }} " title="{{ $gsbUser->name }} ( {{ $gsbUser->email }} )">
                                                                <span class="cxm-user-check"><i class="zmdi zmdi-check"></i></span>
                                                                <input type="checkbox" autocomplete="off" name="agents" value="{{$gsbUser->id}}" {{  $team->team_key === $gsbUser->team_key ? 'checked' : '' }}>
                                                                <img class="rounded-pill" src="{{ $gsbUser->image && in_array(strtolower(pathinfo($gsbUser->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']) && file_exists(public_path('assets/images/profile_images/'). $gsbUser->image) ? asset('assets/images/profile_images/'.$gsbUser->image) :asset('assets/images/xs/avatar3.jpg')}}" alt="{{$gsbUser->name}}" style="height:50px; width:50px; object-fit:cover;" >
                                                            </label>
                                                        </div>
                                                        <div class="mt-n1 mb-2 cxm-assign-member-nm"><span class="badge badge-success rounded-pill text-wrap">{{$gsbUser->name}}</span></div>
                                                    </div>
                                                @endforeach
                                               </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border">
                                            <div class="header px-3 pb-0">
                                                <h2><strong>Assigned</strong> Brands</h2>
                                            </div>
                                            <div class="body shadow">
                                                <div class="row">
                                                    @foreach($brandData as $brand)
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="checkbox">
                                                                <input id="checkbox_{{$brand->id}}" type="checkbox" value="{{$brand->brand_key}}" name="brands" {{$brand->assingBrand}}>
                                                                <label for="checkbox_{{$brand->id}}">{{$brand->name}}</label>
                                                                <a href="{{$brand->brand_url}}" target="_blank"><i class="zmdi zmdi-link"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input id="update_data" type="submit" value="Submit" class="btn btn-warning btn-round">
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
