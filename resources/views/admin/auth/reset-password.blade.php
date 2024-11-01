<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>:: @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else {{ env('APP_NAME') }} @endif :: Reset Password</title>
    <!-- Favicon -->
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        <link rel="icon" href="https://tgcrm.net/assets/images/uspto-colored.png" type="image/x-icon">
    @else
        <link rel="icon" href="{{ asset('assets/images/favicon.webp') }}" type="image/x-icon">
    @endif
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
</head>
<body class="theme-blush">
<div class="authentication">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-sm-12">
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
                <form class="card auth_form" method="POST" action="{{ route('admin.password.update') }}">
                    @csrf
                    <div class="header">
                        @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                            <img class="logo" src="https://tgcrm.net/assets/images/uspto-colored.png" alt="Uspto">
                        @else
                            <img class="logo" src="https://portal.techigator.com/assets/images/logo-colored.png" alt="tg">
                        @endif
                        <h5>Admin Reset Password</h5>
                    </div>
                    <div class="body">
                        <div class="mb-3">
                            {{ __('Reset your password by entering a new password.') }}
                        </div>
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <div class="input-group mb-3  d-none">
                            <input type="email" name="email" class="form-control" placeholder="Email" value="{{isset($_GET['email']) ? $_GET['email']: "" }}" required autofocus>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control" placeholder="{{ __('Password') }}" required autocomplete="current-password">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="zmdi zmdi-lock"></i></span>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control"
                                   required autocomplete="new-password" placeholder="{{ __('Confirm Password') }}">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.login') }}">Back to Admin Login</a>
                        </div>
                    </div>
                </form>
                <div class="copyright text-center mt-3">
                    &copy; <script>document.write(new Date().getFullYear())</script>, <a href="@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) # @else https://techigator.com/ @endif">@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else Techigator @endif</a>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <img src="{{ asset('assets/images/signin.svg') }}" alt="Admin Reset Password"/>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Jquery Core Js -->
<script src="{{ asset('assets/bundles/libscripts.bundle.js') }}"></script>
<script src="{{ asset('assets/bundles/vendorscripts.bundle.js') }}"></script> <!-- Lib Scripts Plugin Js -->
</body>
</html>
