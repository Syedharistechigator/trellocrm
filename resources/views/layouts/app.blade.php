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
        @endif</title>
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        <link rel="icon" href="https://tgcrm.net/assets/images/uspto-colored.png" type="image/x-icon">
    @else
        <link rel="icon" href="{{ asset('assets/images/favicon.webp') }}" type="image/x-icon"> <!-- Favicon-->
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/front/assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/jvectormap/jquery-jvectormap-2.0.3.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/charts-c3/plugin.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome_5.css') }}">
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.css') }}"/>
    <!-- JQuery DataTable Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/jquery-datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/footable-bootstrap/css/footable.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/footable-bootstrap/css/footable.standalone.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css"/>
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert/sweetalert.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/plugins/summernote/dist/summernote.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/responsive-custom.css') }}"/>
    @if(str_contains(request()->url(),'board'))
        <link rel="stylesheet" href="{{ asset('assets/css/board/plugin.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.css') }}"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    @stack('css')
    <style>
        .cxm-online,
        .cxm-offline {
            position: absolute;
            right: 0;
            top: 15%;
        }

        .user-info .cxm-online,
        .user-info .cxm-offline {
            position: absolute;
            left: 5px;
            top: 10px;
            right: auto;
        }

        table.footable > tbody > tr > td {
            display: table-cell;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        .cxm-live-search-fix.show.open {
            z-index: 2;
        }

        .search .waves-effect {
            margin: 0 !important;
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

        .notifications-content .section:last-child .notification-item {
            border-bottom: 0;
            margin-bottom: 0;
        }

        .notifications-content .section:last-child {
            margin-bottom: 0;
        }
    </style>
    @if(Auth::user()->type == 'client')
        @php $cxmTheme = 'purple' @endphp
        <style>
            .breadcrumb .breadcrumb-item a {
                color: #6f42c1;
            }

            .dropdown-item.active,
            .dropdown-item:active {
                background-color: #6f42c1;
            }
        </style>
    @elseif(Auth::user()->type == 'staff')
        @php $cxmTheme = 'green' @endphp
        <style>
            .breadcrumb .breadcrumb-item a {
                color: #04BE5B;
            }

            .dropdown-item.active,
            .dropdown-item:active {
                background-color: #04BE5B;
            }
        </style>
    @elseif(Auth::user()->type == 'QA')
        @php $cxmTheme = 'blush' @endphp
        <style>
            .breadcrumb .breadcrumb-item a {
                color: #e47297;
            }

            .dropdown-item.active,
            .dropdown-item:active {
                background-color: #e47297;
            }

            .custom-control-input:checked ~ .custom-control-label::before {
                color: #fff;
                border-color: #e47297;
                background-color: #e47297;
            }
        </style>
    @elseif(Auth::user()->type == 'tm-user' || Auth::user()->type == 'tm-client' || ((Auth::user()->type == 'lead') && (config('app.home_name') == 'Uspto')))
        @php $cxmTheme = 'dark-blue' @endphp
        <style>
            .breadcrumb .breadcrumb-item a {
                color: #144368;
            }

            .dropdown-item.active,
            .dropdown-item:active {
                background-color: #144368;
            }

            span.cxm-online.pulse i.zmdi.zmdi-circle.text-success {
                color: #144368 !important;
            }

            .custom-control-input:checked ~ .custom-control-label::before {
                color: #fff;
                border-color: #144368;
                background-color: #144368;
            }
        </style>
    @else
        @php $cxmTheme = 'cyan' @endphp
        <style>
            .breadcrumb .breadcrumb-item a {
                color: #1cbfd0;
            }

            .dropdown-item.active,
            .dropdown-item:active {
                background-color: #1cbfd0;
            }

            .custom-control-input:checked ~ .custom-control-label::before {
                color: #fff;
                border-color: #1cbfd0;
                background-color: #1cbfd0;
            }
        </style>
    @endif
</head>
<body class="theme-{{$cxmTheme}}">
<ul class="design_notifications_toaster"></ul>
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
                <img class="zmdi-hc-spin" src="{{ asset('assets/images/cxm-loader.webp') }}" width="48" height="48" alt="TG.">
            </div>
            <p>Please wait...</p>
        </div>
    </div>
@endif
<!-- Overlay For Sidebars -->
<div class="overlay"></div>
<!-- Right Icon menu Sidebar -->
<div class="navbar-right">
    <ul class="navbar-nav">
        @include('includes.notification')
        <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"><i class="zmdi zmdi-accounts"></i>
                <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
            </a>
            <ul class="dropdown-menu slideUp2">
                <li class="header">Tasks List
                    <small class="float-right"><a href="javascript:void(0);">View All</a></small> </li>
                <li class="body">
                    <ul class="menu tasks list-unstyled">
                        <li>
                            <div class="progress-container progress-primary">
                                <span class="progress-badge">eCommerce Website</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="86" aria-valuemin="0" aria-valuemax="100" style="width: 86%;">
                                        <span class="progress-value">86%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar3.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar4.jpg') }}" alt="Avatar"> </li>
                                </ul>
                            </div>
                        </li> <li>
                            <div class="progress-container">
                                <span class="progress-badge">iOS Game Dev</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%;">
                                        <span class="progress-value">45%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar10.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar9.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar8.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar7.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar6.jpg') }}" alt="Avatar"> </li>
                                </ul>
                            </div>
                        </li> <li>
                            <div class="progress-container progress-warning">
                                <span class="progress-badge">Home Development</span>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="29" aria-valuemin="0" aria-valuemax="100" style="width: 29%;">
                                        <span class="progress-value">29%</span>
                                    </div>
                                </div>
                                <ul class="list-unstyled team-info">
                                    <li class="m-r-15"><small>Team</small></li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar5.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="Avatar"> </li> <li>
                                        <img src="{{ asset('assets/images/xs/avatar7.jpg') }}" alt="Avatar"> </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </li> <li>
            <a class="mega-menu" href="route('logout')" onclick="event.preventDefault(); document.getElementById('abc').submit();">
                <i class="zmdi zmdi-power"></i> </a>
            <form method="POST" id="abc" action="{{ route('logout') }}">
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
                <img src="https://tgcrm.net/assets/images/uspto-colored.png" alt="Uspto">
            @else
                <img src="https://portal.techigator.com/assets/images/logo-colored.png" alt="tg">
            @endif
        </a>
    </div>
    <div class="menu">
        <ul class="list">
            <li>
                <div class="user-info">
                    <a class="image" href="{{ route('user.profile')}}">
                        <img src="{{ Auth::user() && Auth::user()->image && in_array(strtolower(pathinfo(Auth::user()->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif']) && file_exists(public_path('assets/images/profile_images/'). Auth::user()->image) ? asset('assets/images/profile_images/'.Auth::user()->image) :asset('assets/images/profile_av.jpg')}} " alt="User" id="profile-image-side-bar">
                    </a>
                    <div class="detail">
                        <h4>{{ Auth::user()->name }}</h4>
                        <small style="text-transform: capitalize;">{{ Auth::user()->designation }}
{{--                            {{ Auth::user()->type }}--}}
                        </small>
                        @if(Cache::has('user-is-online-' . Auth::user()->id))
                            <span class="cxm-online pulse"><i class="zmdi zmdi-circle text-success" title="Online"></i></span>
                        @else
                            <span class="cxm-offline pulse"><i class="zmdi zmdi-circle-o text-danger" title="Offline"></i></span>
                        @endif
                    </div>
                </div>
            </li>
            <li class="{{ (request()->is('dashboard')) ? 'active' : '' }} xactive xopen"><a href="{{route('dashboard')}}"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a></li>
            @if(Auth::check() && in_array(Auth::user()->user_access, [0, 2]))
                {{-- Full Access / Trello Access --}}
                <li class=" xactive xopen"><a href="{{route('user.redirect-to-trello')}}"><i class="zmdi zmdi-home"></i><span>Trello</span></a>
                </li>
            @endif
            
            
            @if(Auth::check() && in_array(Auth::user()->user_access, [0, 1]))
                {{-- Full Access / Crm Access --}}
                @if(Auth::user()->type == 'lead')
                    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') == false)
                        <li class="{{ request()->is('emails*') ? 'active' : 'mailAreamenu' }}">
                            <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-email"></i><span>Emails</span></a>
                            <ul class="ml-menu">
                                @if(auth()->user() && isset(auth()->user()->getUserBrandEmailNames))
                                    @foreach(auth()->user()->getUserBrandEmails as $email)
                                        <li><a href="javascript:void(0);" class="menu-toggle {{request()->is('emails/'.$email->email."*") ? "mailactive" :"" }}">@if(request()->is('emails/'.$email->email."*") )
                                                    <span class="cxm-online pulse"><i class="zmdi zmdi-circle text-success" title="Online"></i></span>
                                                @endif<span>{{$email->email}}</span></a>
                                            <ul class="mail-menu-list mt-0">
                                                <li>
                                                    <a href="{{route('user.email.system.index', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/inbox") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    inbox
                                                </span>Inbox <span class="totalmsg">1,012</span></a> </li> <li>
                                                    <a href="{{route('user.email.system.sent', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/sent") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    send
                                                </span>Sent</a> </li> <li>
                                                    <a href="{{route('user.email.system.spam', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/spam") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    report
                                                </span>Spam</a> </li> <li>
                                                    <a href="{{route('user.email.system.trash', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/trash") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    delete
                                                </span>Trash</a> </li>
                                            </ul>
                                        </li>
                                    @endforeach
                                @else
                                    <li><a href="javascript:void(0);" class="mail-menu-btn"><span class="material-symbols-outlined">
                                                    No
                                                </span>No Email Found</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    <li class="{{ (request()->is('brand*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-blogger"></i><span>Brands</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('brand') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('team*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account"></i><span>Teams</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('team') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('lead*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('leads') ? 'active' : '' }}"><a href="{{ route('user.leads.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('client*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('client.index') }}">Clients</a></li>
                        </ul>
                    </li>
                    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') == false)
                        <li class="{{ (request()->is('project*')) ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment"></i><span>projects</span></a>
                            <ul class="ml-menu">
                                <li><a href="{{ route('project.index') }}">List</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="{{ (request()->is('refunds') || request()->is('expense') || request()->is('invoice*') || request()->is('invoices') || request()->is('user-payments')  || request()->is('wire-payments') || request()->is('userpayment*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Sales</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">Invoice</a></li>
                            <li class="{{ request()->is('user-payments') ? 'active' : '' }}"><a href="{{ route('user.payments.index') }}">Payments</a></li>
                            <li class="{{ request()->is('wire-payments') ? 'active' : '' }}"><a href="{{ route('user.wire.payments.index') }}">Wire Payments</a></li>
                            <li class="{{ request()->is('refunds') ? 'active' : '' }}"><a href="{{ route('refundList') }}">Refunds</a>
                            </li>
                            <li class="{{ request()->is('expense') ? 'active' : '' }}"><a href="{{ route('expense.index') }}">Expense</a></li>
                        </ul>
                    </li>
                    <li class="{{ request()->is('board*') ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Board</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('user.board.index') }}">List</a> </li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->type == 'client')
                    <li> <a href="{{ route('clientProjects') }}" class="menu-toggle">
                            <i class="zmdi zmdi-account"></i><span>Projects</span> </a> </li>
                    <li> <a href="{{ route('clientInvoice')}}" class="menu-toggle">
                            <i class="zmdi zmdi-account"></i><span>Invoices</span> </a> </li>
                    <li> <a href="{{ route('clientPyament')}}" class="menu-toggle">
                            <i class="zmdi zmdi-account"></i><span>Payments</span> </a> </li>
                @endif
                @if(Auth::user()->type == 'tm-user')
                    <li class="{{ (request()->is('customer-sheets') || request()->is('customer-sheets*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customer Sheets</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('customer-sheets') ? 'active' : '' }}"><a href="{{ route('user.customer.sheet.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('lead*') || request()->is('leads')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('leads') ? 'active' : '' }}"><a href="{{ route('user.leads.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('invoices') || request()->is('user-payments') || request()->is('invoice*') || request()->is('userpayment*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Sales</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">Invoice</a></li>
                            <li class="{{ request()->is('user-payments') ? 'active' : '' }}"><a href="{{ route('user.payments.index') }}">Payments</a></li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->type == 'tm-client')
                    <li class="{{ (request()->is('customer-sheets') || request()->is('customer-sheets*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customer Sheets</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('customer-sheets') ? 'active' : '' }}"><a href="{{ route('user.customer.sheet.index') }}">List</a></li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->type == 'tm-ppc')
                    <li class="{{ (request()->is('leads') || request()->is('lead*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('leads') ? 'active' : '' }}"><a href="{{ route('user.leads.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('invoices') || request()->is('invoice*') ) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Invoices</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">List</a></li>
                        </ul>
                    </li>
                @endif
                @if(Auth::user()->type == 'staff')
    
                    <li class="{{ request()->is('emails*') ? 'active' : 'mailAreamenu' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-email"></i><span>Emails</span></a>
                        <ul class="ml-menu">
                            @if(auth()->user() && isset(auth()->user()->getUserBrandEmailNames))
                                @foreach(auth()->user()->getUserBrandEmails as $email)
                                    <li><a href="javascript:void(0);" class="menu-toggle {{request()->is('emails/'.$email->email."*") ? "mailactive" :"" }}">@if(request()->is('emails/'.$email->email."*") )
                                                <span class="cxm-online pulse"><i class="zmdi zmdi-circle text-success" title="Online"></i></span>
                                            @endif<span>{{$email->email}}</span></a>
                                        <ul class="mail-menu-list mt-0">
                                            <li>
                                                <a href="{{route('user.email.system.index', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/inbox") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    inbox
                                                </span>Inbox <span class="totalmsg">1,012</span></a> </li> <li>
                                                <a href="{{route('user.email.system.sent', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/sent") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    send
                                                </span>Sent</a> </li> <li>
                                                <a href="{{route('user.email.system.spam', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/spam") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    report
                                                </span>Spam</a> </li> <li>
                                                <a href="{{route('user.email.system.trash', ['email' => $email->email])}}" class="mail-menu-btn {{request()->is('emails/'.$email->email."/trash") ? "mailactiveinbox" :"" }}">
                                                <span class="material-symbols-outlined">
                                                    delete
                                                </span>Trash</a> </li>
                                        </ul>
                                    </li>
                                @endforeach
                            @else
                                <li><a href="javascript:void(0);" class="mail-menu-btn"><span class="material-symbols-outlined">
                                                    No
                                                </span>No Email Found</a></li>
                            @endif
                        </ul>
                    </li>
                    <li class="{{ (request()->is('leads') || request()->is('lead*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('leads') ? 'active' : '' }}"><a href="{{ route('user.leads.index') }}">List</a></li>
                        </ul>
                    </li>
    
                    <li class="{{ (request()->is('client*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('client.index') }}">Clients</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('project*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment"></i><span>projects</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('project.index') }}">List</a></li>
                            <li><a href="{{ route('accountManagerProjects') }}">as Account Manager</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('invoices') || request()->is('user-payments') || request()->is('invoice*') || request()->is('userpayment*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Sales</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">Invoice</a></li>
                            <li class="{{ request()->is('user-payments') ? 'active' : '' }}"><a href="{{ route('user.payments.index') }}">Payments</a></li>
                            <li class="{{ request()->is('refunds') ? 'active' : '' }}"><a href="{{ route('refundList') }}">Refunds</a>
                            </li>
                        </ul>
                    </li>
                @endif
    
    
                @if(Auth::user()->type == 'qa')
                    <li class="{{ (request()->is('client*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('client.index') }}">Clients</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('project*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-assignment"></i><span>projects</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('project.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('invoices') || request()->is('user-payments') || request()->is('invoice*') || request()->is('userpayment*') || request()->is('showpayment*') || request()->is('refunds*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Sales</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">Invoice</a></li>
                            <li class="{{ request()->is('user-payments') ? 'active' : '' }}"><a href="{{ route('user.payments.index') }}">Payments</a></li>
                            <li class="{{ request()->is('refunds') ? 'active' : '' }}"><a href="{{ route('refundList') }}">Refunds</a>
                            </li>
                        </ul>
                    </li>
                @endif
    
    
                @if(Auth::user()->type == 'ppc')
                    <li class="{{ (request()->is('leads*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-account-box-phone"></i><span>Leads</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('user.leads.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('client*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('client.index') }}">Clients</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('invoices') || request()->is('user-payments') || request()->is('invoice*') || request()->is('userpayment*') || request()->is('showpayment*') || request()->is('refunds*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-balance"></i><span>Sales</span></a>
                        <ul class="ml-menu">
                            <li class="{{ request()->is('invoices') ? 'active' : '' }}"><a href="{{ route('user.invoices.index') }}">Invoice</a></li>
                            <li class="{{ request()->is('user-payments') ? 'active' : '' }}"><a href="{{ route('user.payments.index') }}">Payments</a></li>
                        </ul>
                    </li>
                    {{--				<li class="{{ (request()->is('spending*')) ? 'active' : '' }}">--}}
                    {{--					<a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-google"></i><span>PPC--}}
                    {{--                        Spendings</span></a>--}}
                    {{--					<ul class="ml-menu">--}}
                    {{--						<li><a href="javascript:void(0);">List</a></li>--}}
                    {{--					</ul>--}}
                    {{--				</li>--}}
                @endif
    
                @if(Auth::user()->type == 'third-party-user')
                    <li class="{{ (request()->is('third-party-roles*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-money"></i><span>Third Party Role</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('user.third.party.role.index') }}">List</a></li>
                        </ul>
                    </li>
                    <li class="{{ (request()->is('client*')) ? 'active' : '' }}">
                        <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-accounts"></i><span>Customers</span></a>
                        <ul class="ml-menu">
                            <li><a href="{{ route('client.index') }}">Clients</a></li>
                        </ul>
                    </li>
                @endif
    
                @if(Auth::user()->type != 'tm-user' && Auth::user()->type != 'tm-client' )
                    {{--                <li>--}}
                    {{--                    <div class="progress-container progress-primary m-t-10">--}}
                    {{--                        <span class="progress-badge">Traffic this Month</span>--}}
                    {{--                        <div class="progress">--}}
                    {{--                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="67" aria-valuemin="0" aria-valuemax="100" style="width: 67%;">--}}
                    {{--                                <span class="progress-value">67%</span>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                    <div class="progress-container progress-info">--}}
                    {{--                        <span class="progress-badge">Server Load</span>--}}
                    {{--                        <div class="progress">--}}
                    {{--                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="86" aria-valuemin="0" aria-valuemax="100" style="width: 86%;">--}}
                    {{--                                <span class="progress-value">86%</span>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                </li>--}}
                @endif
            
            @endif
        </ul>
    </div>
</aside>
<!-- Right Sidebar -->
<aside id="rightsidebar" class="right-sidebar">
    <ul class="nav nav-tabs sm">
        <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#setting"><i class="zmdi zmdi-settings zmdi-hc-spin"></i></a></li>
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
                        </li> <li data-theme="blue">
                            <div class="blue"></div>
                        </li> <li data-theme="cyan">
                            <div class="cyan"></div>
                        </li> <li data-theme="green">
                            <div class="green"></div>
                        </li> <li data-theme="orange">
                            <div class="orange"></div>
                        </li> <li data-theme="blush" class="active">
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
                        </li> <li>
                            <div class="checkbox">
                                <input id="checkbox1" type="checkbox"> <label for="checkbox1">Report Panel Usage</label>
                            </div>
                        </li> <li>
                            <div class="checkbox">
                                <input id="checkbox2" type="checkbox" checked="">
                                <label for="checkbox2">Email Redirect</label>
                            </div>
                        </li> <li>
                            <div class="checkbox">
                                <input id="checkbox3" type="checkbox" checked="">
                                <label for="checkbox3">Notifications</label>
                            </div>
                        </li> <li>
                            <div class="checkbox">
                                <input id="checkbox4" type="checkbox"> <label for="checkbox4">Auto Updates</label>
                            </div>
                        </li> <li>
                            <div class="checkbox">
                                <input id="checkbox5" type="checkbox" checked=""> <label for="checkbox5">Offline</label>
                            </div>
                        </li> <li>
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
                        <li class="online"> <a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar4.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Sophia <small class="float-right">11:00AM</small></span>
                                        <span class="message">There are many variations of passages of Lorem Ipsum
                                                available</span> <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a> </li> <li class="online"> <a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar5.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Grayson <small class="float-right">11:30AM</small></span>
                                        <span class="message">All the Lorem Ipsum generators on the</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a> </li> <li class="offline"> <a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar2.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">Isabella <small class="float-right">11:31AM</small></span>
                                        <span class="message">Contrary to popular belief, Lorem Ipsum</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a> </li> <li class="me"> <a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar1.jpg') }}" alt="">
                                    <div class="media-body">
                                        <span class="name">John <small class="float-right">05:00PM</small></span>
                                        <span class="message">It is a long established fact that a reader</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a> </li> <li class="online"> <a href="javascript:void(0);">
                                <div class="media">
                                    <img class="media-object " src="{{ asset('assets/images/xs/avatar3.jpg')}}" alt="">
                                    <div class="media-body">
                                        <span class="name">Alexander <small class="float-right">06:08PM</small></span>
                                        <span class="message">Richard McClintock, a Latin professor</span>
                                        <span class="badge badge-outline status"></span>
                                    </div>
                                </div>
                            </a> </li>
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
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>--}}
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
<script src="{{ asset('assets/plugins/dragables/dragable.min.js')}}"></script>
<!-- <script src="{{ asset('assets/js/pages/tables/footable.js')}}"></script> -->
<!-- Custom Js -->
<!-- Notifications -->
<script src="{{ asset('assets/plugins/bootstrap-notify/bootstrap-notify.js')}}"></script>
<!-- Bootstrap Notify Plugin Js -->
<script src="{{ asset('assets/js/pages/ui/notifications.js')}}"></script> <!-- Custom Js -->
<script src="{{ asset('assets/plugins/sweetalert/sweetalert.min.js')}}"></script> <!-- SweetAlert Plugin Js -->
<script src="{{ asset('assets/js/pages/ui/sweetalert.js')}}"></script>
<script src="{{ asset('assets/plugins/summernote/dist/summernote.js')}}"></script>
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.min.js')}}"></script> <!-- Jquery Knob Plugin Js -->
<script src="{{ asset('assets/js/pages/charts/jquery-knob.js')}}"></script>
<script src="{{ asset('assets/js/pages/charts.min.js')}}"></script>
<script src="{{ asset('assets/js/pages/dropify.min.js')}}"></script>
<script src="{{ asset('assets/js/pages/dropzone.min.js')}}"></script>
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
        setTimeout(() => toast.remove(), toast.timeoutId); // Removing the toast after 500ms
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
        toast.timeoutId = setTimeout(() => removeToast(toast), toastDetails.timer);
    }
</script>
<!-- Include Scripts -->
<script>
    function AjaxRequestPost(url, data, customMessage, reload = true, redirect, showSwal = true) {
        var res;
        $.ajax({
            url: url,
            data: data,
            async: false,
            dataType: "json",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            success: function (data) {
                res = data;
                $('.page-loader-wrapper').css('display', 'none');
                var message = customMessage || "successful!";
                if (showSwal) {
                    swal("Good job!", message, "success")
                        .then(() => {
                            if (redirect) {
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

    function AjaxRequestGet(url, data, customMessage, reload = true, redirect, showSwal = true) {
        var res;
        $.ajax({
            url: url,
            data: data,
            async: false,
            method: "GET",
            dataType: "json",
            success: function (data) {
                res = data;
                $('.page-loader-wrapper').css('display', 'none');
                var message = customMessage || "successful!";
                if (showSwal) {
                    swal("Good job!", message, "success")
                        .then(() => {
                            if (redirect) {
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
        // console.log(activeRequest);
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
                        swal("Good job!", message, "success")
                            .then(() => {
                                if (redirect) {
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
                    } else if (data.status === 419) {
                        swal({
                            title: "Session Expired",
                            text: "Your session has expired. Please login again.",
                            icon: "error",
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    closeModal: false,
                                },
                            },
                        }).then(() => {
                            window.location.href = `{{route('login')}}`; // Redirect to login page
                        });
                    } else if (data.status === 502) {
                        swal({
                            title: "Temporary Unavailable",
                            text: "We apologize for the inconvenience, but this page is currently unavailable. Please check back again later.",
                            icon: "error",
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    closeModal: false,
                                },
                            },
                        }).then(() => {
                            location.reload();
                        });
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
        // console.log(activeRequest);
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
                        swal("Good job!", message, "success")
                            .then(() => {
                                if (redirect) {
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
                    } else if (data.status === 419) {
                        swal({
                            title: "Session Expired",
                            text: "Your session has expired. Please login again.",
                            icon: "error",
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    closeModal: false,
                                },
                            },
                        }).then(() => {
                            window.location.href = `{{route('login')}}`; // Redirect to login page
                        });
                    } else if (data.status === 502) {
                        swal({
                            title: "Temporary Unavailable",
                            text: "We apologize for the inconvenience, but this page is currently unavailable. Please check back again later.",
                            icon: "error",
                            buttons: {
                                confirm: {
                                    text: "OK",
                                    closeModal: false,
                                },
                            },
                        }).then(() => {
                            location.reload();
                        });
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

    function AjaxRequestDeletePromise(url, data, customMessage, reload = true, redirect, showSwal = true, loader = true, showtoast = false, abort = false) {

        // if (loader) {
        //     $('.loading_div').css('display', 'flex');
        // }
        if (activeRequest !== null && activeRequest.readyState !== 4 && abort) {
            activeRequest.abort();
        }
        return new Promise((resolve, reject) => {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this item!",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "No",
                        value: null,
                        visible: true,
                        className: "btn-warning",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, Delete!",
                    }
                },
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        $('.loading_div').css('display', 'flex');

                        activeRequest = $.ajax({
                            url: url,
                            data: data,
                            method: "DELETE",
                            dataType: "json",
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                $('.page-loader-wrapper').css('display', 'none');
                                var message = customMessage || "Deletion successful!";
                                if (showSwal) {
                                    swal("Success!", message, "success")
                                        .then(() => {
                                            if (redirect) {
                                                window.location.href = redirect;
                                            } else if (reload === true) {
                                                location.reload();
                                            }
                                            $('.loading_div').css('display', 'none');
                                        });
                                }
                                resolve(data);
                            },
                            error: function (data) {
                                $('.page-loader-wrapper').css('display', 'none');

                                if (data.status === 500) {
                                    createToast('info', "An internal server error occurred. Please try again later.");
                                } else if (data.status === 419) {
                                    swal({
                                        title: "Session Expired",
                                        text: "Your session has expired. Please login again.",
                                        icon: "error",
                                        buttons: {
                                            confirm: {
                                                text: "OK",
                                                closeModal: false,
                                            },
                                        },
                                    }).then(() => {
                                        window.location.href = `{{route('login')}}`;
                                    });
                                } else if (data.status === 502) {
                                    swal({
                                        title: "Temporary Unavailable",
                                        text: "We apologize for the inconvenience, but this page is currently unavailable. Please check back again later.",
                                        icon: "error",
                                        buttons: {
                                            confirm: {
                                                text: "OK",
                                                closeModal: false,
                                            },
                                        },
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    var errorMessage = "An unknown error occurred.";
                                    if (data.responseJSON) {
                                        if (data.responseJSON.errors) {
                                            errorMessage = Object.values(data.responseJSON.errors)[
                                                0][0];
                                        } else if (data.responseJSON.error) {
                                            errorMessage = data.responseJSON.error;
                                        } else if (data.responseJSON.message) {
                                            errorMessage = data.responseJSON.message;
                                        }
                                    }
                                    if (!showtoast) {
                                        swal('Error', errorMessage, 'error');
                                    }
                                    $('.loading_div').css('display', 'none');
                                }
                                reject(data);
                            },
                        }).always(() => {
                            activeRequest = null;
                            $('.loading_div').css('display', 'none');
                        });
                    } else {
                        if (!showtoast && showSwal) {
                            swal("Your Attachment is safe!", {
                                icon: "error",
                                buttons: false,
                                timer: 1000
                            });
                        } else if (showtoast && !showSwal) {
                            createToast('error', 'Your Attachment is safe!');
                        }
                    }
                });
        });
    }
</script>
@stack('cxmScripts')
<script>
    $(document).ready(function () {
        var loading_div = $('.loading_div')

        /** Notification */
        $('.notification-icon').on('click', function () {
            $('.notifications-dropdown').toggleClass('active');
        });
        $('.close-btn').on('click', function () {
            $('.notifications-dropdown').removeClass('active');
        });
        $('.notifications-tabs .tab').on('click', function () {
            $('.notifications-tabs .tab').removeClass('active');
            $('.notification-tab-content').removeClass('active');

            $(this).addClass('active');
            $('#' + $(this).data('tab')).addClass('active');
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest('.notificationDropdown').length) {
                $('.notifications-dropdown').removeClass('active');
            }
        });

        function markNotificationsAsRead(activeTab) {
            var url = '{{ route('user.notifications.mark.all.as.read')}}';
            var formData = new FormData();
            formData.append('tab', activeTab);

            AjaxRequestPostPromise(url, formData, null, false, null, false, false, false, false)
                .then((res) => {
                    $('#board-notification-count,#notification-dropdow-count').text('0').hide();
                })
                .catch((error) => {
                    console.error("Error in Ajax request:", error);
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        }

        $('#notification-dropdown').on('click', function () {
            var dropdown = $('.notifications-dropdown');
            if (dropdown.hasClass('active')) {
                var activeTab = $('.notifications-tabs .tab.active').data('tab');
                markNotificationsAsRead(activeTab);
            }
        });
        /** Notification End */


        @if($errors->any())
        if (!$('.toast').length || $('.toast').css('display') === 'none') {
            createToast('error', `{{ $errors->first() }}`);
        }
        @endif
        var TableId = $('table').first().attr('id');
        if (TableId) {
            var url = window.location.href;
            var invoice_number = url.split('#')[1];
            if (invoice_number && invoice_number.startsWith('INV-')) {
                var checkExist = setInterval(function () {
                    if ($('#' + TableId + '_filter').find('input[type="search"]').length) {
                        $('#' + TableId + '_filter').find('input[type="search"]').val(invoice_number).trigger('keyup');
                        clearInterval(checkExist);
                    } else if ($('#' + TableId + '_searchInput').length) {
                        $('#' + TableId + '_searchInput').val(invoice_number).trigger('keyup');
                        clearInterval(checkExist);
                    }
                }, 100);
            }

            function updateHash(value) {
                if (value.trim() !== '') {
                    if (value.startsWith('INV-')) {
                        window.location.hash = value;
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

        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        $('[type=search],.bs-searchbox input[type=text]').each(function () {
            var randomNumber = getRandomInt(111111111, 999999999);
            $(this).attr('id', "dt-search-box-" + randomNumber);
        });
    });
    setTimeout(function () {
        $('.page-loader-wrapper').hide();
        // console.log('cxm...');
    }, 2000);

    $(document).on('click', '.notificationDropdown .dropdown-menu', function (e) {
        e.stopPropagation();
    });
    $(window).on('load', function () {
        $('.page-loader-wrapper').hide();
        // console.log('cxm...');
    });
</script>

@if(session('success'))
    <script>
        createToast('success', `{{ session('success') }}`);
            @php session()->forget('success'); @endphp
    </script>
@endif

@if($errors->any())
    <script>
        @foreach($errors->all() as $error_key => $error)
        @if($error_key < 4)
        setTimeout(function () {
            createToast('error', `{{ $error }}`);
        }, {{$error_key}} * 1000);
        @endif
        @endforeach
            @php session()->forget('errors'); @endphp
    </script>
@elseif(session('error'))
    <script>
        createToast('error', `{{ session('error') }}`);
            @php session()->forget('error'); @endphp
    </script>
@endif
</body>
</html>
