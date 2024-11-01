<!doctype html>
<html class="no-js " lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>:: @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else {{ env('APP_NAME') }} @endif :: Sign In</title>
    <!-- Favicon-->
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
    <link rel="icon" href="https://tgcrm.net/assets/images/uspto-colored.png" type="image/x-icon">
    @else
    <link rel="icon" href="{{ asset('assets/images/favicon.webp') }}" type="image/x-icon">
    @endif
    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css')}}">
</head>
<body class="theme-blush">
<div class="authentication">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-12">
                <!-- <form class="card auth_form"> -->
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @elseif ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="card auth_form" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="header">
                        @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                            <img class="logo" src="https://tgcrm.net/assets/images/uspto-colored.png" alt="">
                        @else
                        <img class="logo" src="https://portal.techigator.com/assets/images/logo-colored.png" alt="">
                        @endif
                        <h5>Log in</h5>
                    </div>
                    <div class="body">
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password" placeholder="Password">
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('admin.password.request') }}" class="forgot" title="Forgot Password"><i class="zmdi zmdi-lock"></i></a>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <input id="remember_me" type="checkbox">
                                    <label for="remember_me">Remember Me</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('password.request') }}">
                                    <p class="mb-0">Forgot Password</p>
                                </a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">
                            {{ __('Log in') }}
                        </button>
                        @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') == false)
                            <div class="signin_with mt-3">
                                <a href="{{ route('admin.login') }}">
                                    <p class="mb-0"><strong>Go to Admin Login </strong>
                                        <i class="zmdi zmdi-arrow-forward"></i></p>
                                </a>
                            </div>
                        @endif
                    </div>
                </form>
                <div class="copyright text-center">
                    &copy;
                    <script>document.write(new Date().getFullYear())</script>
                    , <span><a href="@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) # @else https://techigator.com/ @endif">@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else Techigator @endif</a></span>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <img src="{{ asset('assets/images/signin.svg')}}" alt="Sign In"/>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Jquery Core Js -->
<script src="{{ asset('assets/bundles/libscripts.bundle.js')}}"></script>
<script src="{{ asset('assets/bundles/vendorscripts.bundle.js')}}"></script> <!-- Lib Scripts Plugin Js -->
</body>
</html>
