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
