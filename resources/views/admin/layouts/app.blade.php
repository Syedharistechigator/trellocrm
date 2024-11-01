<!doctype html>
<html class="no-js " lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('cxmTitle', 'Home') | @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
            Uspto
        @else
            {{ config('app.name', 'Laravel') }}
        @endif
    </title>
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        <link rel="icon" href="{{ asset('assets/images/uspto-colored.png') }}" type="image/x-icon">
    @else
        <link rel="icon" href="{{ asset('assets/images/favicon.webp') }}" type="image/x-icon"> <!-- Favicon-->
    @endif
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/charts-c3/plugin.css') }}"/>
    <!-- Morris Chart Css-->
    <link rel="stylesheet" href="{{ asset('assets/plugins/morrisjs/morris.css') }}"/>
    <!-- Colorpicker Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}"/>
    <!-- Multi Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/multi-select/css/multi-select.css') }}">
    <!-- Bootstrap Spinner Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-spinner/css/bootstrap-spinner.css') }}">
    <!-- Bootstrap Tagsinput Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}"/>
    <!-- noUISlider Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/nouislider/nouislider.min.css') }}"/>
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.css') }}"/>
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/footable-bootstrap/css/footable.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/footable-bootstrap/css/footable.standalone.min.css') }}">
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.css') }}"/>
    @stack('stats-css')
    @stack('css')
    <style>
        table.footable > tbody > tr > td {
            display: table-cell;
        }

        .breadcrumb .breadcrumb-item a {
            color: #FF9948;
        }

        .footable .pagination > .active > a,
        .footable .pagination > .active > a:focus,
        .footable .pagination > .active > a:hover,
        .footable .pagination > .active > span,
        .footable .pagination > .active > span:focus,
        .footable .pagination > .active > span:hover {
            color: #fff;
            background-color: #ff9948;
            border-color: #ff9948;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        .cxm-btn-user-w-icon .cxm-user-check {
            position: absolute;
            right: 0;
            left: 0;
            top: 36%;
            opacity: 0;
            background-color: rgba(30, 126, 52, 0.7);
        }

        .cxm-btn-user-w-icon.active .cxm-user-check {
            opacity: 1;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            color: #fff;
            border-color: #ff9948;
            background-color: #ff9948;
        }

        .cxm-font-sm {
            font-size: 12px;
            font-weight: 600;
        }

        .cxm-live-search-fix.show {
            z-index: 9;
        }
        .bootstrap-select .dropdown-menu li.selected a {
            background-color: #ff9948 !important;
            color: #fff !important;
        }
        .dropdown-menu ul{
            max-height: 200px !important;
        }
        .bootstrap-select > .dropdown-toggle {
            padding: 0.7rem 0.75rem !important;
            line-height: 16px !important;
        }

        .loading_div {
            position: fixed;
            top: 50%;
            left: 0;
            width: 100%;
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            transform: translateY(-50%);
            height: 100%;
            color: #ff9948;
        }

        .loader-05 {
            display: inline-block;
            width: 5.5em;
            height: 5.5em;
            color: inherit;
            vertical-align: middle;
            pointer-events: none;
        }

        .loader-05 {
            border: 0.2em solid transparent;
            border-top-color: currentcolor;
            border-radius: 50%;
            -webkit-animation: 1s loader-05 linear infinite;
            animation: 1s loader-05 linear infinite;
            position: relative;
        }

        .loader-05:before {
            content: "";
            display: none;
            width: inherit;
            height: inherit;
            position: absolute;
            top: -0.3em;
            left: -0.3em;
            border: 0.3em solid currentcolor;
            border-radius: 50%;
            opacity: 0.5;
        }

        @-webkit-keyframes loader-05 {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes loader-05 {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .loading_div {
            display: none;
        }
    </style>
</head>
<body class="theme-orange">
<ul class="design_notifications_toaster"></ul>
<audio id="notificationSound">
    <source src="{{asset('assets/mixkit-correct-answer-tone-2870.wav')}}" type="audio/mpeg">
    Your browser does not support the audio element.
</audio>
<div class="box loading_div">
    <div class="loader-05"></div>
</div>
@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') == false)
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            {{--
        <div class="m-t-30"><img class="zmdi-hc-spin" src="{{ asset('assets/images/loader.svg') }}" width="48"
            height="48" alt="Aero"></div>
        --}}
            <div class="m-t-30">
                <img class="zmdi-hc-spin" src="{{ asset('assets/images/cxm-loader.webp') }}" width="48" height="48"
                     alt="TG.">
            </div>
            <p>Please wait...</p>
        </div>
    </div>
@endif
<!-- Overlay For Sidebars -->
<div class="overlay"></div>
<!-- Main Search -->
<div id="search">
    <button id="close" type="button" class="close btn btn-primary btn-icon btn-icon-mini btn-round">x</button>
    <form>
        <input type="search" value="" placeholder="Search..."/>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
</div>
<!-- Right Icon menu Sidebar -->
<div class="navbar-right">
    <ul class="navbar-nav">
        <li><a href="#search" class="main_search" title="Search..."><i class="zmdi zmdi-search"></i></a></li>
        <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle" title="Leads Notifications" data-toggle="dropdown"
               role="button"><i class="zmdi zmdi-notifications"></i>
                <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
            </a>
            <ul class="dropdown-menu slideUp2">
                <li class="header">Leads Notifications</li>
                <li class="body">
                    <ul class="menu list-unstyled">
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-blue"><i class="zmdi zmdi-account"></i></div>
                                <div class="menu-info">
                                    <h4>8 New Members joined</h4>
                                    <p><i class="zmdi zmdi-time"></i> 14 mins ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-amber"><i class="zmdi zmdi-shopping-cart"></i></div>
                                <div class="menu-info">
                                    <h4>4 Sales made</h4>
                                    <p><i class="zmdi zmdi-time"></i> 22 mins ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-red"><i class="zmdi zmdi-delete"></i></div>
                                <div class="menu-info">
                                    <h4><b>Nancy Doe</b> Deleted account</h4>
                                    <p><i class="zmdi zmdi-time"></i> 3 hours ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-green"><i class="zmdi zmdi-edit"></i></div>
                                <div class="menu-info">
                                    <h4><b>Nancy</b> Changed name</h4>
                                    <p><i class="zmdi zmdi-time"></i> 2 hours ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-grey"><i class="zmdi zmdi-comment-text"></i></div>
                                <div class="menu-info">
                                    <h4><b>John</b> Commented your post</h4>
                                    <p><i class="zmdi zmdi-time"></i> 4 hours ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-purple"><i class="zmdi zmdi-refresh"></i></div>
                                <div class="menu-info">
                                    <h4><b>John</b> Updated status</h4>
                                    <p><i class="zmdi zmdi-time"></i> 3 hours ago </p>
                                </div>
                            </a></li>
                        <li><a href="javascript:void(0);">
                                <div class="icon-circle bg-light-blue"><i class="zmdi zmdi-settings"></i></div>
                                <div class="menu-info">
                                    <h4>Settings Updated</h4>
                                    <p><i class="zmdi zmdi-time"></i> Yesterday </p>
                                </div>
                            </a></li>
                    </ul>
                </li>
                <li class="footer"><a href="javascript:void(0);">View All Notifications</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button">
                <i class="zmdi zmdi-accounts"></i>
                <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
            </a>
            <ul class="dropdown-menu slideUp2">
                <li class="header">Tasks List
                    <small class="float-right"><a href="javascript:void(0);">View All</a></small></li>
                <li class="body">
                    <ul class="menu tasks list-unstyled">
                        <li>
                            <div class="progress-container progress-primary">
                                <span class="progress-badge">eCommerce Website</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="86"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 86%;">
                                        <span class="progress-value">86%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar3.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar4.jpg') }}" alt="Avatar"></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div class="progress-container">
                                <span class="progress-badge">iOS Game Dev</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 45%;">
                                        <span class="progress-value">45%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar10.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar9.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar8.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar7.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar6.jpg') }}" alt="Avatar"></li>
                                </ul>
                            </div>
                        </li>
                        <li>
                            <div class="progress-container progress-warning">
                                <span class="progress-badge">Home Development</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="29"
                                         aria-valuemin="0" aria-valuemax="100" style="width: 29%;">
                                        <span class="progress-value">29%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar5.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="Avatar"></li>
                                    <li>
                                        <img src="{{ asset('assets/images/xs/avatar7.jpg') }}" alt="Avatar"></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <a class="mega-menu" href="route('admin.logout')"
               onclick="event.preventDefault(); document.getElementById('abc').submit();">
                <i class="zmdi zmdi-power"></i> </a>
            <form method="POST" id="abc" action="{{ route('admin.logout') }}">
                @csrf
            </form>
        </li>
    </ul>
</div>
<!-- Left Sidebar -->
<aside id="leftsidebar" class="sidebar">
    <div class="navbar-brand">
        <button class="btn-menu ls-toggle-btn" type="button"><i class="zmdi zmdi-menu"></i></button>
        <a href="{{ route('admin.dashboard')}}">
            @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                <img src="{{ asset('assets/images/uspto-colored.png') }}" alt="Uspto">
            @else
                <img src="https://portal.techigator.com/assets/images/logo-colored.png" alt="tg">
            @endif
        </a>
    </div>
    <div class="menu">
        <ul class="list">
            <li>
                <div class="user-info">
                    <a class="image" href="{{ route('admin.profile.index')}}">
                        <img
                            src="{{ Auth::guard('admin')->user() && Auth::guard('admin')->user()->image && in_array(strtolower(pathinfo(Auth::guard('admin')->user()->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']) && file_exists(public_path('assets/images/profile_images/admin/'). Auth::guard('admin')->user()->image) ? asset('assets/images/profile_images/admin/'.Auth::guard('admin')->user()->image) :asset('assets/images/profile_av.jpg')}} "
                            alt="User" id="profile-image-side-bar">
                    </a>
                    <div class="detail">
                        <h4>{{ Auth::guard('admin')->user()->name }}</h4>
                        <small>Admin</small>
                    </div>
                </div>
            </li>
            <li class="{{ (request()->is('admin/dashboard')) ? 'active' : '' }} xactive xopen"><a
                    href="{{ route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a>
            </li>
            @if(Auth::guard('admin')->user()->type == 'super')
                <li class="{{ (request()->is('admin/account*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment-account"></i><span>Admin Users</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{ route('account.index') }}">List</a></li>
                    </ul>
                </li>

                <li class="{{ (request()->is('admin/stats*') || request()->is('admin/stats/spending') || request()->is('admin/stats/target') || request()->is('admin/stats/carry-forward') || request()->is('admin/stats/fixed-costing') || request()->is('admin/stats/third-party-role')
//|| request()->is('admin/stats/indirect-costing')
 ) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-chart"></i><span>Stats</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{ route('admin.stats') }}">Dashboard</a></li>
                        <li class="{{ request()->is('admin/stats/spending') ? 'active' : ''}}"><a
                                href="{{ route('admin.stats.spending.index') }}">Spendings</a></li>
                        <li class="{{ request()->is('admin/stats/target') ? 'active' : ''}}"><a
                                href="{{ route('admin.stats.target.index') }}">Targets</a></li>
                        <li class="{{ request()->is('admin/stats/carry-forward') ? 'active' : ''}}"><a
                                href="{{ route('admin.stats.carry-forward.index') }}">Carry Forwards</a></li>
                        <li class="{{ request()->is('admin/stats/fixed-costing') ? 'active' : ''}}"><a
                                href="{{ route('admin.stats.fixed-costing.index') }}">Fixed Costings</a></li>
                        {{--                        <li class="{{ request()->is('admin/stats/indirect-costing') ? 'active' : ''}}"><a href="{{ route('admin.stats.indirect-costing.index') }}">Indirect Costings</a></li>--}}
                        <li class="{{ request()->is('admin/stats/third-party-role') ? 'active' : ''}}"><a
                                href="{{ route('admin.stats.third-party-role.index') }}">Third Party Roles</a></li>
                    </ul>
                </li>
                <li class="{{ request()->is('admin/department*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i
                            class="zmdi zmdi-chart"></i><span>Department</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{ route('admin.department.index') }}">List</a></li>
                    </ul>
                </li>
            @endif
            <li class="{{ (request()->is('admin/brand*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-blogger"></i><span>Brands</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('brand.index') }}">List</a></li>
                    <li><a href="{{ route('brand.create') }}">Add Brand </a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li><a href="{{ route('trashedbrand') }}">Trashed Brand</a></li>
                    @endif
                </ul>
            </li>
            <li class="{{ (request()->is('memberlist*') || request()->is('inactivememberlist*') || request()->is('memberprofile*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-account"></i><span>Employees</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('memberList') }}">Members</a></li>
                    <li><a href="{{ route('inactivememberlist') }}">InActive Member</a></li>
                </ul>
            </li>
            @if(Auth::guard('admin')->user()->type == 'super' && (config('app.home_name') != 'Uspto'))
                <li class="{{ (request()->is('admin/email-configurations*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-email"></i>
                        <span>Email Configuration</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{route('admin.email.configuration.index')}}">List</a></li>
                        <li><a href="{{ route('admin.email.configuration.create') }}">Add Email Configuration </a></li>
                        <li><a href="{{ route('admin.email.configuration.trashed') }}">Trashed Email Config</a></li>
                    </ul>
                </li>
                {{--                <li class="{{ (request()->is('admin/zoom-configurations*')) ? 'active' : '' }}">--}}
                {{--                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-videocam"></i>--}}
                {{--                        <span>Zoom Configuration</span></a>--}}
                {{--                    <ul class="ml-menu">--}}
                {{--                        <li><a href="{{route('admin.zoom.configuration.index')}}">List</a></li>--}}
                {{--                        <li><a href="{{ route('admin.zoom.configuration.create') }}">Add Zoom Configuration </a></li>--}}
                {{--                        <li><a href="{{ route('admin.zoom.configuration.trashed') }}">Trashed Zoom Config</a></li>--}}
                {{--                    </ul>--}}
                {{--                </li>--}}
            @endif
            @if(config('app.home_name') != 'Uspto')
                <li class="{{ (request()->is('admin/customer-sheets*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i>
                        <span>Customer Sheet</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{route('admin.customer.sheet.index')}}">List</a></li>
                        @if(Auth::guard('admin')->user()->type == 'super')
                            <li><a href="{{route('admin.customer.sheet.trashed')}}">Trashed</a></li>
                        @endif
                        <li><a href="{{route('admin.customer.sheet.log')}}">Log</a></li>
                    </ul>
                </li>
            @endif
            <li class="{{ (request()->is('admin/team*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-accounts-outline"></i><span>Teams</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('team.index') }}">List</a></li>
                    <li class="{{ request()->is('admin/team/spending') ? 'active' : ''}}"><a
                            href="{{ route('admin.team.spending.index') }}">Spendings</a></li>
                    <li class="{{ request()->is('admin/team/target') ? 'active' : ''}}"><a
                            href="{{ route('admin.team.target.index') }}">Targets</a></li>
                    <li class="{{ request()->is('admin/team/carry-forward') ? 'active' : ''}}"><a
                            href="{{ route('admin.team.carry-forward.index') }}">Carry Forwards</a></li>
                    <li class="{{ request()->is('admin/team/fixed-costing') ? 'active' : ''}}"><a
                            href="{{ route('admin.team.fixed-costing.index') }}">Fixed Costings</a></li>
                    {{--                    <li class="{{ request()->is('admin/team/indirect-costing') ? 'active' : ''}}"><a href="{{ route('admin.team.indirect-costing.index') }}">Indirect Costings</a></li>--}}
                    <li><a href="{{ route('team.create') }}">Add Team</a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li><a href="{{ route('trashedteam') }}">Trashed Team</a></li>
                    @endif
                </ul>
            </li>
            <li class="{{ (request()->is('admin/leads') || request()->is('admin/lead*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                <ul class="ml-menu">
                    <li class="{{ request()->is('admin/leads') ? 'active' : ''}}"><a
                            href="{{ route('admin.leads.index') }}">List</a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li><a href="{{ route('onlyTrashedlead') }}">Trashed Leads</a></li>
                    @endif
                    <li><a href="{{ route('leadstatus.index') }}">Status</a></li>
                </ul>
            </li>
            <li class="{{ (request()->is('admin/client*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('clientadmin.index') }}">Clients</a></li>
                </ul>
            </li>
            @if(config('app.home_name') != 'Uspto')
                <li class="{{ (request()->is('admin/adminproject*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i
                            class="zmdi zmdi-assignment"></i><span>Projects</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{ route('adminproject.index') }}">List</a></li>
                        <li><a href="#">Status</a></li>
                    </ul>
                </li>
            @endif
            @if(config('app.home_name') != 'Uspto')
                <li class="{{ (request()->is('admin/board-list*')) && !request()->is('admin/board-list-cards*') ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment"></i>
                        <span>Board Lists</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{route('admin.board.list.index')}}">List</a></li>
                        <li><a href="{{ route('admin.board.list.create') }}">Add Board List </a></li>
                        @if(Auth::guard('admin')->user()->type == 'super')
                            <li><a href="{{ route('admin.board.list.trashed') }}">Trashed Board List</a></li>
                        @endif
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/board-list-cards*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment"></i>
                        <span>Board List Cards</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{route('admin.board.list.cards.index')}}">List</a></li>
{{--                        <li><a href="{{ route('admin.board.list.cards.create') }}">Add</a></li>--}}
                    </ul>
                </li>
            @endif
            <li class="{{ ( request()->is('admin/invoices') || request()->is('admin/invoice*') || request()->is('admin/paymentadmin*') || request()->is('admin/split-payments') ||
                            request()->is('admin/wire-payments') || request()->is('admin/payments/unsettled') || request()->is('admin/payment-transaction-logs') ||
                            request()->is('admin/payment-multiple-responses')) ? 'active' : '' }}">
                {{--            <li class="{{ (request()->is('admin/invoice*') || request()->is('admin/paymentadmin*')) ? 'active' : '' }}">--}}
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-balance"></i><span>Sales</span></a>
                <ul class="ml-menu">
                    <li class="{{request()->is('admin/invoices') ? 'active' : ''}}"><a
                            href="{{ route('admin.invoices.index') }}">Invoice</a></li>
                    <li><a href="{{ route('paymentadmin.index') }}">Payment</a></li>
                    <li class="{{request()->is('admin/wire-payments') ? 'active' : ''}}"><a
                            href="{{ route('admin.wire.payments.index') }}">Wire Payment</a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li class="{{request()->is('admin/payments/unsettled') ? 'active' : ''}}"><a
                                href="{{ route('admin.payment.unsettled.index') }}">Unsettled Payments</a></li>
                    @endif
                    <li class="{{request()->is('admin/payment-transaction-logs') ? 'active' : ''}}"><a
                            href="{{ route('admin.payment.transaction.log.index') }}">Payment Transaction Log</a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li class="{{request()->is('admin/payment-multiple-responses') ? 'active' : ''}}"><a
                                href="{{ route('admin.payment.multiple.response.index') }}">Payment Multi Response</a>
                        </li>
                    @endif
                    <li class="{{ (request()->is('admin/split-payments')) ? 'active' : '' }}"><a
                            href="{{ route('admin_split_payments') }}">Split Payments</a></li>
                    <li><a href="{{ route('adminRefundList') }}">Refunds</a></li>
                </ul>
            </li>
            <li class="{{ (request()->is('admin/invoice*') || request()->is('admin/paymentadmin*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i
                        class="zmdi zmdi-hc-fw"></i><span>Expense</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('admin.invoices.index') }}">List</a></li>
                </ul>
            </li>
            <li class="{{ (request()->is('admin/spending*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-google"></i><span>PPC
                            Spendings</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('spending.index') }}">List</a></li>
                </ul>
            </li>
            @if(Auth::guard('admin')->user()->type == 'super')
                <li class="{{ (request()->is('admin/ip-address*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-pin"></i>
                        <span>Ip Address</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{route('admin.ip.address.index')}}">List</a></li>
                        <li><a href="{{ route('admin.ip.address.create') }}">Add Ip Address </a></li>
                        <li><a href="{{ route('admin.ip.address.trashed') }}">Trashed Ip Address</a></li>
                    </ul>
                </li>
            @endif
            <li class="{{ (request()->is('admin/user_info_api*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-blogger"></i><span>User Info
                            API</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('user_info_api.index') }}">List</a></li>
                    <li><a href="{{ route('user_info_api.create') }}">Add User Info API </a></li>
                    @if(Auth::guard('admin')->user()->type == 'super')
                        <li><a href="{{ route('trashed_user_info_api') }}">Trashed User Info API</a></li>
                    @endif
                </ul>
            </li>
            <li class="{{ (request()->is('admin/website_view*')) ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-run zmdi-hc-fw"></i><span>User
                            Tracking</span></a>
                <ul class="ml-menu">
                    <li><a href="{{ route('website_view.index') }}">List</a></li>
                </ul>
            </li>
            @if(config('app.home_name') != 'Uspto')
                <li class="{{ (request()->is('admin/third-party-roles*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-money"></i><span>Third Party Role</span></a>
                    <ul class="ml-menu">
                        <li class="{{request()->is('admin/third-party-roles') ? 'active' : ''}}"><a
                                href="{{ route('admin.third.party.role.index') }}">List</a></li>
                        @if(Auth::guard('admin')->user()->type == 'super')
                            <li class="{{request()->is('admin/third-party-roles/trashed') ? 'active' : ''}}"><a
                                    href="{{route('admin.third.party.role.trashed')}}">Trashed</a></li>
                        @endif
                        <li class="{{request()->is('admin/third-party-roles/logs') ? 'active' : ''}}"><a
                                href="{{route('admin.third.party.role.log')}}">Log</a></li>
                    </ul>
                </li>
            @endif
            @if(config('app.home_name') != 'Uspto')
                <li class="{{ (request()->is('admin/card*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i
                            class="zmdi zmdi-card"></i><span>Card</span></a>
                    <ul class="ml-menu">
                        <li><a href="{{ route('card.index') }}">List</a></li>
                        <li><a href="{{ route('card.create') }}">Add Card </a></li>
                    </ul>
                </li>
            @endif
            @if(Auth::guard('admin')->user()->type == 'super')
                <li class="{{ (request()->is('admin/payment-method*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-money"></i>
                        <span>Payments Methods</span></a>
                    <ul class="ml-menu">
                        {{--@foreach(App\Models\PaymentMethod::all() as $method)
                            <!-- <li><a href="{{route('paymentmethod.show',$method->id)}}">{{$method->name}}</a>
                </li> -->
                @endforeach--}}
                        <li class="{{ (request()->is('admin/payment-method/authorize*')) ? 'active' : '' }}"><a
                                href="{{route('admin.payment.method.authorize.index')}}">Authorize.Net</a></li>
                        <li class="{{ (request()->is('admin/payment-method/expigate*')) ? 'active' : '' }}"><a
                                href="{{route('admin.payment.method.expigate.index')}}">Expigate</a></li>
                        <li class="{{ (request()->is('admin/payment-method/payarc*')) ? 'active' : '' }}"><a
                                href="{{route('admin.payment.method.payarc.index')}}">PayArc</a></li>
                    </ul>
                </li>

                <li class="{{ (request()->is('admin/setting*')) ? 'active' : '' }}">
                    <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-settings"></i>
                        <span>Settings</span></a>
                    <ul class="ml-menu">
                        <li><a id="clearCache" href="#">Email</a></li>
                        <li><a id="xclearCache" href="#">Clear Cache</a></li>
                    </ul>
                </li>
                <li class="{{ (request()->is('admin/logs*')) ? 'active' : '' }}">
                    <a href="{{route('admin.log')}}" class="menu-toggle"><i class="zmdi zmdi-settings"></i>
                        <span>Logs</span></a>
                    <ul class="ml-menu">
                        <li><a id="clearCache" href="#">List</a></li>
                    </ul>
                </li>
            @endif
            @if(Auth::guard('admin')->user()->type == 'super')
                <li>
                    <div class="progress-container progress-primary m-t-10">
                        <span class="progress-badge">Traffic this Month</span>
                        <div class="progress">
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="67"
                                 aria-valuemin="0" aria-valuemax="100" style="width: 67%;">
                                <span class="progress-value">67%</span>
                            </div>
                        </div>
                    </div>
                    <div class="progress-container progress-info">
                        <span class="progress-badge">Server Load</span>
                        <div class="progress">
                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="86"
                                 aria-valuemin="0" aria-valuemax="100" style="width: 86%;">
                                <span class="progress-value">86%</span>
                            </div>
                        </div>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</aside>
<!-- Right Sidebar -->
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs sm">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting"><i
                    class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#chat"><i class="zmdi zmdi-comments"></i></a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="setting">
            <div class="slim_scroll">
                <div class="card">
                    <h6>Theme Option</h6>
                    <div class="light_dark">
                        <div class="radio">
                            <input type="radio" name="radio1" id="lighttheme" value="light" checked="">
                            <label for="lighttheme">Light Mode</label>
                        </div>
                        <div class="radio mb-0">
                            <input type="radio" name="radio1" id="darktheme" value="dark">
                            <label for="darktheme">Dark Mode</label>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <h6>Color Skins</h6>
                    <ul class="choose-skin list-unstyled">
                        <li data-theme="purple">
                            <div class="purple"></div>
                        </li>
                        <li data-theme="blue">
                            <div class="blue"></div>
                        </li>
                        <li data-theme="cyan">
                            <div class="cyan"></div>
                        </li>
                        <li data-theme="green">
                            <div class="green"></div>
                        </li>
                        <li data-theme="orange">
                            <div class="orange"></div>
                        </li>
                        <li data-theme="blush" class="active">
                            <div class="blush"></div>
                        </li>
                    </ul>
                </div>
                <div class="card">
                    <h6>General Settings</h6>
                    <ul class="setting-list list-unstyled">
                        <li>
                            <div class="checkbox">
                                <a id="clearCache" href="{{route('clearCache')}}">Clear Cache</a>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox1" type="checkbox"> <label for="checkbox1">Report Panel Usage</label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox2" type="checkbox" checked="">
                                <label for="checkbox2">Email Redirect</label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox3" type="checkbox" checked="">
                                <label for="checkbox3">Notifications</label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox4" type="checkbox"> <label for="checkbox4">Auto Updates</label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox5" type="checkbox" checked=""> <label for="checkbox5">Offline</label>
                            </div>
                        </li>
                        <li>
                            <div class="checkbox">
                                <input id="checkbox6" type="checkbox" checked="">
                                <label for="checkbox6">Location Permission</label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-pane right_chat" id="chat">
            <div class="slim_scroll">
                <div class="card">
                    <ul class="list-unstyled">
                        <li class="online"><a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar4.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Sophia <small class="float-right">11:00AM</small></span>
                                        <span class="message">There are many variations of passages of Lorem Ipsum
                                                available</span> <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a></li>
                        <li class="online"><a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar5.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Grayson <small class="float-right">11:30AM</small></span>
                                        <span class="message">All the Lorem Ipsum generators on the</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a></li>
                        <li class="offline"><a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Isabella <small class="float-right">11:31AM</small></span>
                                        <span class="message">Contrary to popular belief, Lorem Ipsum</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a></li>
                        <li class="me"><a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar1.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">John <small class="float-right">05:00PM</small></span>
                                        <span class="message">It is a long established fact that a reader</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a></li>
                        <li class="online"><a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar3.jpg')}}" alt="">
                                    <div class="media-body">
                                        <span class="name">Alexander <small class="float-right">06:08PM</small></span>
                                        <span class="message">Richard McClintock, a Latin professor</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</aside>
<!-- Main Content -->
@yield('content')
<!--Main-->
<!-- Jquery Core Js -->
<script src="{{ asset('assets/bundles/libscripts.bundle.js')}} "></script>
<!-- Lib Scripts Plugin Js ( jquery.v3.2.1, Bootstrap4 js) -->
<script src="{{ asset('assets/bundles/vendorscripts.bundle.js')}} "></script>
<!-- slimscroll, waves Scripts Plugin Js -->
<script src="{{ asset('assets/bundles/jvectormap.bundle.js')}} "></script> <!-- JVectorMap Plugin Js -->
<script src="{{ asset('assets/bundles/sparkline.bundle.js')}} "></script> <!-- Sparkline Plugin Js -->
<script src="{{ asset('assets/bundles/c3.bundle.js')}} "></script>
<script src="{{ asset('assets/bundles/mainscripts.bundle.js')}} "></script>
<script src="{{ asset('assets/js/pages/index.js')}} "></script>
<script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.js')}}"></script>
<!-- Jquery Validation Plugin Css -->
<script src="{{ asset('assets/plugins/jquery-steps/jquery.steps.js')}}"></script> <!-- JQuery Steps Plugin Js -->
<script src="{{ asset('assets/js/pages/forms/form-validation.js')}}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js')}}"></script> <!-- Select2 Js -->
<!-- cxm Start -->
<script src="{{ asset('assets/plugins/jquery-inputmask/jquery.inputmask.bundle.js')}}"></script>
<script src="{{ asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js')}}"></script>
<script src="{{ asset('assets/plugins/multi-select/js/jquery.multi-select.js')}}"></script>
<script src="{{ asset('assets/plugins/nouislider/nouislider.js')}}"></script>
<!-- cxm End -->
<script src="{{ asset('assets/js/pages/forms/advanced-form-elements.js')}}"></script>
<script src="{{ asset('assets/bundles/footable.bundle.js')}}"></script> <!-- Lib Scripts Plugin Js -->
<!-- Jquery DataTable Plugin Js -->
<script src="{{ asset('assets/bundles/datatablescripts.bundle.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/dataTables.buttons.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.colVis.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.flash.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.html5.min.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-datatable/buttons/buttons.print.min.js')}}"></script>
<script src="{{ asset('assets/js/pages/tables/jquery-datatable.js')}}"></script>
<!-- <script src="{{ asset('assets/js/pages/tables/footable.js')}}"></script> -->
<!-- Custom Js -->
<!-- Notifications -->
<script src="{{ asset('assets/plugins/bootstrap-notify/bootstrap-notify.js')}}"></script>
<!-- Bootstrap Notify Plugin Js -->
<script src="{{ asset('assets/js/pages/ui/notifications.js')}}"></script> <!-- Custom Js -->
<script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script> <!-- SweetAlert Plugin Js -->
<script src="{{ asset('assets/js/pages/ui/sweetalert.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.min.js')}}"></script> <!-- Jquery Knob Plugin Js -->
<script src="{{ asset('assets/js/pages/charts/jquery-knob.js')}}"></script>
<!-- Include Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.js"></script>
<!-- Toastr -->
<script>
    const notifications = document.querySelector(".design_notifications_toaster");
    const toastDetails = {
        timer: 5000,
        success: {
            icon: 'fa-circle-check',
        },
        error: {
            icon: 'fa-circle-xmark',
        },
        warning: {
            icon: 'fa-triangle-exclamation',
        },
        info: {
            icon: 'fa-circle-info',
        }
    }
    const removeToast = (toast) => {
        toast.classList.add("hide");
        if (toast.timeoutId) clearTimeout(toast.timeoutId); // Clearing the timeout for the toast
        setTimeout(() => toast.remove(), 10000); // Removing the toast after 500ms
    }
    const createToast = (id, msg) => {
        const {icon, text} = toastDetails[id];
        const toast = document.createElement("li"); // Creating a new 'li' element for the toast
        toast.className = `toast ${id}`;
        toast.innerHTML = `<div class="column">
                              <i class="fa-solid ${icon}"></i>
                              <span>${msg}</span>
                           </div>
                           <i class="fa-solid fa-xmark" onclick="removeToast(this.parentElement)"></i>`;
        notifications.appendChild(toast); // Append the toast to the notification ul
        const audio = document.getElementById('notificationSound');
        // audio.play();
        toast.timeoutId = setTimeout(() => removeToast(toast), toastDetails.timer);
    }
</script>
<script>
    function AjaxRequestPost(url, data, customMessage, reload = true, redirect, showSwal = true) {
        var res;
        $.ajax({
            url: url,
            data: data,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            success: function (data) {
                res = data;
                $('.page-loader-wrapper').css('display', 'none');
                var message = customMessage || "successful!";
                if (showSwal) {
                    swal({
                        title: "Good job!",
                        text: message,
                        icon: "success",
                        buttons: {
                            reload: {
                                text: "Reload This Page",
                                value: "reload",
                                className: "btn-danger",
                                closeModal: true,
                            },
                            ok: "OK",
                        },
                    }).then((value) => {
                        if (value === "reload") {
                            location.reload();
                        } else if (redirect) {
                            window.location.href = redirect;
                        } else if (reload === true) {
                            location.reload();
                        }
                    });
                }
            },
            error: function (data) {
                $('.page-loader-wrapper').css('display', 'none');
                var errorMessage = "An unknown error occurred.";
                if (data.responseJSON) {
                    if (data.responseJSON.errors) {
                        errorMessage = Object.values(data.responseJSON.errors)[0][0];
                    } else if (data.responseJSON.error) {
                        errorMessage = data.responseJSON.error;
                    } else if (data.responseJSON.message) {
                        errorMessage = data.responseJSON.message;
                    }
                }
                swal('Error', errorMessage, 'error');
            },
            type: "POST",
        });
        return res;
    }

    function AjaxRequestGet(url, data, customMessage, reload = true, redirect, showSwal = true) {
        var res;
        $.ajax({
            url: url,
            data: data,
            async: false,
            type: "GET",
            dataType: "json",
            success: function (data) {
                res = data;
                $('.page-loader-wrapper').css('display', 'none');
                var message = customMessage || "successful!";
                if (showSwal) {
                    swal({
                        title: "Good job!",
                        text: message,
                        icon: "success",
                        buttons: {
                            reload: {
                                text: "Reload This Page",
                                value: "reload",
                                className: "btn-danger",
                                closeModal: true,
                            },
                            ok: "OK",
                        },
                    }).then((value) => {
                        if (value === "reload") {
                            location.reload();
                        } else if (redirect) {
                            window.location.href = redirect;
                        } else if (reload === true) {
                            location.reload();
                        }
                    });
                }
            },
            error: function (data) {
                $('.page-loader-wrapper').css('display', 'none');
                var errorMessage = "An unknown error occurred.";
                if (data.responseJSON) {
                    if (data.responseJSON.errors) {
                        errorMessage = Object.values(data.responseJSON.errors)[0][0];
                    } else if (data.responseJSON.error) {
                        errorMessage = data.responseJSON.error;
                    } else if (data.responseJSON.message) {
                        errorMessage = data.responseJSON.message;
                    }
                }
                swal('Error', errorMessage, 'error');
            },
        });
        return res;
    }


    var activeRequest = null;

    function AjaxRequestPostPromise(url, data, customMessage, reload = true, redirect, showSwal = true, loader = true, showtoast = false, abort = false, toastType = 'success') {

        if (loader) {
            $('.loading_div').css('display', 'flex');
        }
        if (activeRequest != null) {
            console.log(activeRequest);
        }
        if (activeRequest !== null && activeRequest.readyState !== 4 && abort) {
            activeRequest.abort();
        }
        return new Promise((resolve, reject) => {
            activeRequest = $.ajax({
                url: url,
                data: data,
                method: "POST",
                dataType: "json",
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    let message;
                    if (data.success) {
                        message = data.success;
                    } else if (customMessage) {
                        message = customMessage;
                    } else {
                        message = "successful!";
                    }
                    if (showSwal) {
                        swal({
                            title: "Good job!",
                            text: message,
                            icon: "success",
                            buttons: {
                                reload: {
                                    text: "Reload This Page",
                                    value: "reload",
                                    className: "btn-danger",
                                    closeModal: true,
                                },
                                ok: "OK",
                            },
                        }).then((value) => {
                            if (value === "reload") {
                                location.reload();
                            } else if (redirect) {
                                window.location.href = redirect;
                            } else if (reload === true) {
                                location.reload();
                            }
                        });
                    } else if (showtoast && !redirect && reload === false) {
                        createToast(toastType, message);
                    }
                    resolve(data);
                },
                error: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    if (data.status === 500) {
                        createToast('info', "An internal server error occurred. Please try again later.");
                    } else {
                        var errorMessage = "An unknown error occurred.";
                        if (data.responseJSON) {
                            if (Array.isArray(data.responseJSON.errors) && data.responseJSON.errors.length > 0) {
                                errorMessage = data.responseJSON.errors[0];
                            } else if (data.responseJSON.errors && typeof data.responseJSON.errors === 'object') {
                                errorMessage = Object.values(data.responseJSON.errors)[0][0];
                            } else if (data.responseJSON.errors && typeof data.responseJSON.errors === 'string') {
                                errorMessage = data.responseJSON.errors;
                            } else if (data.responseJSON.error) {
                                errorMessage = data.responseJSON.error;
                            } else if (data.responseJSON.message) {
                                errorMessage = data.responseJSON.message;
                            }
                        }

                        if (!showtoast && showSwal) {
                            swal('Error', errorMessage, 'error');
                        } else if (showtoast && !showSwal) {
                            createToast('error', errorMessage);
                        }
                    }
                    reject(data);
                },
            }).always(() => {
                activeRequest = null;
                $('.loading_div').css('display', 'none');
            });

        });
    }

    function AjaxRequestGetPromise(url, data, customMessage, reload = true, redirect, showSwal = true, loader = true, showtoast = false, abort = false, toastType = 'success') {
        if (loader) {
            $('.loading_div').css('display', 'flex');
        }

        if (activeRequest != null) {
            console.log(activeRequest);
        }
        if (activeRequest !== null && activeRequest.readyState !== 4 && abort) {
            activeRequest.abort();
        }

        return new Promise((resolve, reject) => {
            activeRequest = $.ajax({
                url: url,
                data: data,
                method: "GET",
                dataType: "json",
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    let message;
                    if (data.success) {
                        message = data.success;
                    } else if (customMessage) {
                        message = customMessage;
                    } else {
                        message = "successful!";
                    }
                    if (showSwal) {
                        swal({
                            title: "Good job!",
                            text: message,
                            icon: "success",
                            buttons: {
                                reload: {
                                    text: "Reload This Page",
                                    value: "reload",
                                    className: "btn-danger",
                                    closeModal: true,
                                },
                                ok: "OK",
                            },
                        }).then((value) => {
                            if (value === "reload") {
                                location.reload();
                            } else if (redirect) {
                                window.location.href = redirect;
                            } else if (reload === true) {
                                location.reload();
                            }
                        });
                    } else if (showtoast && !redirect && reload === false) {
                        createToast(toastType, message);
                    }
                    resolve(data);
                },
                error: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    if (data.status === 500) {
                        createToast('info', "An internal server error occurred. Please try again later.");
                    } else {
                        var errorMessage = "An unknown error occurred.";
                        if (data.responseJSON) {
                            if (Array.isArray(data.responseJSON.errors) && data.responseJSON.errors.length > 0) {
                                errorMessage = data.responseJSON.errors[0];
                            } else if (data.responseJSON.errors && typeof data.responseJSON.errors === 'object') {
                                errorMessage = Object.values(data.responseJSON.errors)[0][0];
                            } else if (data.responseJSON.errors && typeof data.responseJSON.errors === 'string') {
                                errorMessage = data.responseJSON.errors;
                            } else if (data.responseJSON.error) {
                                errorMessage = data.responseJSON.error;
                            } else if (data.responseJSON.message) {
                                errorMessage = data.responseJSON.message;
                            }
                        }

                        if (!showtoast && showSwal) {
                            swal('Error', errorMessage, 'error');
                        } else if (showtoast && !showSwal) {
                            createToast('error', errorMessage);
                        }
                    }
                    reject(data);
                },
            }).always(() => {
                activeRequest = null;
                $('.loading_div').css('display', 'none');
            });
        });
    }
</script>
<!-- cxm Scripts -->
@stack('cxmScripts')
<script>
    $(document).ready(function () {
        var TableId = $('table').first().attr('id');
        if (TableId) {
            var url = new URL(window.location.href);
            var hashValue = url.hash.substring(1);
            var search = url.searchParams.get('search');
            var searchValue = search ? search : (hashValue && hashValue.startsWith('INV-')) ? hashValue : null;

            if (searchValue) {
                var checkExist = setInterval(function () {
                    if ($('#' + TableId + '_filter').find('input[type="search"]').length) {
                        $('#' + TableId + '_filter').find('input[type="search"]').val(searchValue).trigger('keyup');
                        clearInterval(checkExist);
                    } else if ($('#' + TableId + '_searchInput').length) {
                        $('#' + TableId + '_searchInput').val(searchValue).trigger('keyup');
                        clearInterval(checkExist);
                    }
                }, 100);
            }

            function updateHash(value) {
                if (value.trim() !== '') {
                    if (value.startsWith('INV-')) {
                        window.location.hash = value;
                    } else {
                        url.searchParams.set('search', value);
                        window.history.replaceState({}, '', url);
                    }
                } else {
                    history.replaceState('', document.title, window.location.pathname + window.location.search);
                }
            }

            $('#' + TableId + '_filter').find('input[type="search"]').on('input', function () {
                var searchValue = $(this).val();
                updateHash(searchValue);
            });
        }
        $(document).on('click', '.invoice-trigger', function (e) {
            e.preventDefault();
            var TableId = $('table').first().attr('id');
            var invoice_num = $(this).attr('data-invoice-num');
            window.location.hash = invoice_num;
            if ($('#' + TableId + '_filter').find('input[type="search"]').length) {
                $('#' + TableId + '_filter').find('input[type="search"]').val(invoice_num).trigger('keyup');
            } else if ($('#' + TableId + '_searchInput').length) {
                $('#' + TableId + '_searchInput').val(invoice_num).trigger('keyup');
            }
        });
    });
    setTimeout(function () {
        $('.page-loader-wrapper').hide();
        // console.log('cxm...');
    }, 2000);
    $('#xclearCache').on('click', function (evnt) {
        evnt.preventDefault();
        swal({
            title: "Are you sure?",
            text: "You will Clear Application Cache!",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "btn-warning",
                    closeModal: true,
                },
                confirm: {
                    text: "Yes, delete it!"
                }
            },
            dangerMode: true,
        })
            .then((cxmCacheClear) => {
                if (cxmCacheClear) {
                    $.ajax({
                        url: "{{ route('clearCache') }}",
                        method: 'Get',
                        success: function (result) {
                            console.log(result);
                            swal("Done job!", "Application cache cleared successfully!",
                                "success", {
                                    buttons: false,
                                    timer: 1000
                                });
                        }
                    });
                } else {
                    swal("Application cache file is safe!", {
                        buttons: false,
                        timer: 1000
                    });
                }
            });
    });
</script>
</body>
</html>
