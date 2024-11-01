<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Responsive Bootstrap 4 and web Application ui kit.">
    <title>:: @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else {{ env('APP_NAME') }} @endif :: Forgot Password</title>
    <!-- Favicon -->
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        <link rel="icon" href="https://tgcrm.net/assets/images/uspto-colored.png" type="image/x-icon">
    @else
        <link rel="icon" href="{{ asset('assets/images/favicon.webp') }}" type="image/x-icon">
    @endif

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css')}}">
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
                <form class="card auth_form" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="header">
                        @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
                            <img class="logo" src="https://tgcrm.net/assets/images/uspto-colored.png" alt="">
                        @else
                            <img class="logo" src="https://portal.techigator.com/assets/images/logo-colored.png" alt="">
                        @endif
                        <h5>Forgot Password</h5>
                    </div>
                    <div class="body">
                        <div class="mb-3">
                            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </div>
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required autofocus value="{{ old('email') }}">
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="zmdi zmdi-account-circle"></i></span>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block waves-effect waves-light">
                                {{ __('Email Password Reset Link') }}
                            </button>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}">Back to Login</a>
                        </div>
                    </div>
                </form>
                <div class="copyright text-center mt-3">
                    &copy;
                    <script>document.write(new Date().getFullYear())</script>
                    , <a href="@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) # @else https://techigator.com/ @endif">@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false) Uspto @else Techigator @endif</a>
                </div>
            </div>
            <div class="col-lg-8 col-sm-12">
                <div class="card">
                    <img src="{{ asset('assets/images/signin.svg')}}" alt="Forgot Password"/>
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
