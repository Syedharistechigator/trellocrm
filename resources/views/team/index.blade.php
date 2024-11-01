@extends('layouts.app')@section('cxmTitle', 'Team')

@section('content')

    @push('css')
        <style>
            .bootstrap-select .dropdown-menu li.selected a {
                background-color: #5cc5cd !important;
                color: #fff !important;
            }
        </style>
    @endpush

    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Team List</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}</a></li>
                            <li class="breadcrumb-item">Team</li> <li class="breadcrumb-item active"> List</li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="">
                            <div class="table-responsive">
                                <table id="TeamTable" class="table table-striped table-hover js-basic-example theme-color">
                                    <thead>
                                    <tr>
                                        <th style="width:80px;">Member</th>
                                        <th></th>
                                        <th>Contact</th>
                                        <th class="text-center hidden-md-down">Email</th>
                                        <th class="hidden-md-down">Target</th>
                                        <th class="hidden-md-down">Achived</th>
                                        <th class="hidden-md-down text-center">Status</th>
                                        <th>Created Date</th>
                                        @if(auth()->user()->type == 'lead')
                                            <th data-breakpoints="sm xs md">Action</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td class="align-middle">
                                                @if($member->image && filter_var($member->image, FILTER_VALIDATE_URL) && in_array(strtolower(pathinfo($member->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                                    <object data="{!! $member->image !!} " height="80px" width="80px" class="img-thumbnail rounded-circle">
                                                        <img class="img-thumbnail rounded-circle" src="{{$member->image}}" alt="{{$member->name}}" style="height:80px; width:80px; object-fit:cover;" id="profile-image" loading="lazy">
                                                    </object>
                                                @else
                                                    @if($member->image && file_exists(public_path('assets/images/profile_images/'). $member->image) && in_array(strtolower(pathinfo($member->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']))
                                                        <img class="img-thumbnail rounded-circle" src="{{ asset('assets/images/profile_images/' . $member->image) }}" alt="{{$member->name}}" style="height:80px; width:80px; object-fit:cover;" id="profile-image" loading="lazy">
                                                    @else
                                                        <img class="img-thumbnail rounded-circle" src="{{ asset('assets/images/sm/avatar1.jpg') }}" alt="{{$member->name}}" style="height:80px; width:80px; object-fit:cover;" id="profile-image" loading="lazy">
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                            <span class="text-info font-weight-bold position-relative d-inline-block">
                                                @if($member->type == 'lead')
                                                    <img class="crown" src="{{ asset('assets/images/crown.png') }}">
                                                @endif
                                                {{$member->name}} <br>
                                                Pseudo: {{$member->pseudo_name}}
                                            </span>
                                                <div class="text-capitalize">{{$member->designation}}</div>
                                            </td>
                                            <td class="hidden-md-down align-middle">
                                                <span class="zmdi zmdi-phone text-info"></span> {{ $member->phone?$member->phone :'Not Available' }}
                                            </td>
                                            <td class="text-center align-middle">
                                                <a href="mailto:{{$member->email}}" title="{{$member->email}}"><span class="zmdi zmdi-email text-info"></span></a>
                                            </td>
                                            <td class="align-middle"><strong>$ {{$member->target}}</strong></td>
                                            <td class="hidden-md-down align-middle">
                                                <strong>$ {{$member->amount}}</strong>
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
                                            <td class="text-center align-middle">
                                                {!! ($member->status == 1)?'<span class="zmdi zmdi-check-circle text-success"></span>' :'<span class="zmdi zmdi-close-circle text-danger"></span>'; !!}
                                            </td>
                                            <td class="align-middle">{{$member->created_at->format('j F, Y')}}</td>
                                            @if(auth()->user()->type == 'lead')
                                                <td class="align-middle text-nowrap">
                                                    <button data-id="{{(rand(10,99)) . $member->id . (rand(10,99))}}" title="Assign Brand's Emails" type="button" class="btn btn-info btn-sm btn-round assign-brands_emails" data-toggle="modal" data-target="#assign-brands_emails-modal">
                                                        <i class="zmdi zmdi-plus-circle"></i></button>
                                                </td>
                                            @endif
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

    <div class="modal fade" id="assign-brands_emails-modal" tabindex="-1" role="dialog" >
        <div class="modal-dialog" role="document" style="max-width: 900px">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel"><strong>Assign Brand's </strong> Emails</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" id="assign-email-member" class="form-control" name="hdn">
                <div class="modal-body">
                    <div class="row" id="email-config-row">
{{--                        @foreach($email_configurations as $email_configuration)--}}
{{--                            <div class="col-md-6">--}}
{{--                                <div class="form-group">--}}
{{--                                    <div class="checkbox">--}}
{{--                                        <input id="checkbox_{{$email_configuration->id}}" class="email-checkbox" type="checkbox" value="{{$email_configuration->id}}" name="email">--}}
{{--                                        <label for="checkbox_{{$email_configuration->id}}">{{$email_configuration->email}}</label>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('cxmScripts')
    @include('team.script')
@endpush
