<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
            Uspto
        @else
            Uspto
        @endif</title>
    @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        <link rel="icon" href="{{asset('assets/images/uspto-colored.png')}}" type="image/x-icon">
    @endif
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}">
    <!-- Bootstrap Select Css -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/css/bootstrap-select.css') }}"/>
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.css') }}"/>
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/toastr.css') }}"/>
    <style>
        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-width: 230px;
            margin-bottom: 20px;
        }

        .custom-file-label::after {
            content: 'Browse';
        }

        .custom-file-input:focus ~ .custom-file-label {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        .custom-file-input::after {
            content: 'No file chosen';
        }

        .custom-file-input[aria-invalid="true"] ~ .custom-file-label::after {
            content: 'Invalid file(s)';
        }

        .custom-btn {
            background-color: #0d3b61;
            border-color: #0d3b61;
            color: #fff;
            width: 150px;
        }
    </style>
</head>
<body>
<ul class="design_notifications_toaster"></ul>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="logo-container">
                @if (str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false  || (request()->server('REMOTE_ADDR') == '::1' || request()->server('REMOTE_ADDR') == '127.0.0.1'))
                <img src="{{asset('assets/images/uspto-colored.png')}}" alt="USPTO Logo" class="img-fluid">
                @endif
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" id="form" action="{{route('request.trademark.submit')}}" 
                    enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="form-control" placeholder="Enter Name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" class="form-control" placeholder="Enter Email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" class="form-control" placeholder="Enter Phone Number" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="order_date">Order Date</label>
                            <input type="date" id="order_date" class="form-control" placeholder="Enter Order Date" name="order_date"  value="{{ old('order_date') }}" min="{{ now()->subYears(10)->addDay()->format('Y-m-d') }}"  required>
                        </div>
                        <div class="form-group">
                            <label for="order_type">Order Type</label>
                            <select id="order_type" class="form-control" name="order_type" required>
                                <option value="" disabled selected>Select Order Type</option>
                                <option value="1" {{ old('order_type') == '1' ? 'selected' : '' }}>Copyright</option>
                                <option value="2" {{ old('order_type') == '2' ? 'selected' : '' }}>Trademark</option>
                                <option value="3" {{ old('order_type') == '3' ? 'selected' : '' }}>Attestation</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filling">Filling</label>
                            <select id="filling" class="form-control" name="filling" required>
                                <option value="" disabled selected>Select Filling</option>
                                <option value="1" {{ old('filling') == '1' ? 'selected' : '' }}>Logo</option>
                                <option value="2" {{ old('filling') == '2' ? 'selected' : '' }}>Slogan</option>
                                <option value="3" {{ old('filling') == '3' ? 'selected' : '' }}>Business Name</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount_charged">Amount Charged</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="number" id="amount_charged" class="form-control" placeholder="Amount Charged" name="amount_charged" value="{{ old('amount_charged') }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="customer-attachments">Upload Attachments (Optional)</label>
                            <input type="file" id="customer-attachments" class="form-control" name="attachments[]" multiple>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="submit" class="btn custom-btn">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="{{ asset('assets/bundles/libscripts.bundle.js')}}"></script>
<script src="{{ asset('assets/plugins/select2/select2.min.js')}}"></script> <!-- Select2 Js -->
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
        if (toast.timeoutId) clearTimeout(toast.timeoutId);
        setTimeout(() => toast.remove(), 10000);
    }
    const createToast = (id, msg) => {
        const {icon, text} = toastDetails[id];
        const toast = document.createElement("li");
        toast.className = `toast ${id}`;
        toast.innerHTML = `<div class="column">
                              <i class="fa-solid ${icon}"></i>
                              <span>${msg}</span>
                           </div>
                           <i class="fa-solid fa-xmark" onclick="removeToast(this.parentElement)"></i>`;
        notifications.appendChild(toast);
        toast.timeoutId = setTimeout(() => removeToast(toast), toastDetails.timer);
    }
</script>
<script>
    const idleTimeThreshold = 30000;
    let refreshTimer;

    const startRefreshTimer = () => {
        refreshTimer = setTimeout(() => window.location.reload(), idleTimeThreshold);
    };

    const resetRefreshTimer = () => {
        clearTimeout(refreshTimer);
        startRefreshTimer();
    };

    startRefreshTimer();

    $(document).on('mousemove keydown scroll', resetRefreshTimer);
</script>

@if(session('success'))
    <script>createToast('success', `{{ session('success') }}`);</script>
@endif

@if($errors->any())
    <script>
        @foreach($errors->all() as $error_key => $error)
        @if($error_key < 4)
        setTimeout(function () {
            createToast('error', `{{ $error }}`);
        }, {{$error_key}} *  1000)
        @endif
        @endforeach
    </script>
@elseif(session('error'))
    <script>createToast('error', `{{ session('error') }}`);</script>
@endif
</body>
</html>
