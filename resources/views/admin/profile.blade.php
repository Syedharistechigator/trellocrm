@extends('admin.layouts.app')@section('cxmTitle', 'Profile')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Profile</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}"><i class="zmdi zmdi-home"></i> {{ config('app.home_name') }}
                                </a></li>
                            <li class="breadcrumb-item active "><a href="{{route('admin.profile.index')}}"> Profile</a></li>
                        </ul>
                        <button class="btn btn-primary btn-icon mobile_menu" type="button">
                            <i class="zmdi zmdi-sort-amount-desc"></i></button>
                    </div>
                    <div class="col-lg-5 col-md-6 col-sm-12 text-right">
                        @include('includes.admin.cxm-top-right-toggle-btn')
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-12">
                        <div class="card mcard_3">
                            <div class="body">
                                <input type="file" id="ChangeImage" style="display: none;">
                                <label for="ChangeImage" id="ChangeImageLabel" title="edit image"> <img src="{{Auth::guard('admin')->user()->image
                                     && in_array(strtolower(pathinfo(Auth::guard('admin')->user()->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif'])
                                     && file_exists(public_path('assets/images/profile_images/admin/'). Auth::guard('admin')->user()->image) ? asset('assets/images/profile_images/admin/'.Auth::guard('admin')->user()->image) : asset('assets/images/profile_av.jpg')}}" class="img-thumbnail rounded-circle shadow" id="profile-image" alt="{{Auth::guard('admin')->user()->name}}" style="height:200px; width:200px; object-fit:cover;cursor: pointer;">
                                </label>
                                <h4 class="m-t-10 text-warning mb-0">
                                <span class="position-relative">
                                    @if(Auth::guard('admin')->user()->type == 'lead')
                                        <img class="crown_profile" src="{{ asset('assets/images/crown.png') }}" style="top:-20px; left:-20px;">
                                    @endif
                                    {{Auth::guard('admin')->user()->name}}
                                </span>
                                </h4>
                                <h5>{{Auth::guard('admin')->user()->pseudo_name}}</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <small>Designation</small>
                                        <p class="text-muted text-capitalize">{{Auth::guard('admin')->user()->designation ?? "---"}}</p>
                                    </div>
                                    <div class="col-6">
                                        <small>Role</small>
                                        <p class="text-muted text-capitalize">{{Auth::guard('admin')->user()->type == "super" ? "Super Admin" : "Admin"}}</p>
                                    </div>
                                </div>
                                <hr class="mt-0">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="body">
                                <h4 style="text-align: center;">Profile</h4>
                                <form id="profile_update_form">
                                    <input type="hidden" id="hdn" class="form-control" name="hdn" value="{{Auth::guard('admin')->user()->id}}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{Auth::guard('admin')->user()->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="email">Email Address:</label>
                                                <input type="text" class="form-control" id="email" value="{{Auth::guard('admin')->user()->email}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="pseudo_email">Pseudo Email Address:</label>
                                                <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" value="{{Auth::guard('admin')->user()->pseudo_email}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="phone">Phone:</label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{Auth::guard('admin')->user()->phone}}">
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_data" type="submit" value="Update" class="btn btn-warning btn-round">
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="body">
                                <h4 style="text-align: center;">Password</h4>
                                <form id="password_update_form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="old_password">Old Password</label>
                                                <input type="password" class="form-control password" id="old_password" name="old_password">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="password">New Password</label>
                                                <input type="password" class="form-control password" id="password" name="password">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="c_password">Confirm New Password</label>
                                                <input type="password" class="form-control password" id="c_password" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>
                                    <input id="update_password" type="submit" value="Change Password" class="btn btn-warning btn-round">
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
    @include('admin.script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            var loading_div = $('.loading_div')

            function removeError() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            }

            $('.password').on('input focus', removeError);

            $('#password_update_form').on('submit', function (e) {
                e.preventDefault();

                // Client-side validation
                let isValid = true;
                $('.error-message').remove();
                $('.password').each(function () {
                    if ($(this).val() === '') {
                        isValid = false;
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message text-danger">This field is required</span>');
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').remove();
                    }
                });

                if (!isValid) {
                    Swal.fire(
                        'Error!',
                        'Please fill out all required fields.',
                        'error'
                    );

                    return;
                }

                loading_div.css('display', 'flex');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{ route('admin.profile.password.update') }}',
                    method: 'post',
                    data: $(this).serialize(),
                    success: function () {
                        createToast('success', 'Password updated successfully.');
                        $('.password').val('');
                        loading_div.css('display', 'none');
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            console.log(errors);
                            $('.error-message').remove();
                            $.each(errors, function (field, message) {
                                var input = $('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<span class="error-message text-danger">' + message[0] + '</span>');
                            });

                            var firstError = Object.values(errors)[0][0];
                            createToast('error', firstError);

                        } else {
                            console.log(xhr.responseJSON);
                            createToast('error', 'Request failed!');

                        }

                        loading_div.css('display', 'none');
                    }
                });
            });
            //Change Image
            $('#ChangeImage').change(function () {
                Swal.fire({
                    title: 'Change Profile Image',
                    text: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, change it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true,
                    dangerMode: true,
                }).then((result) => {
                    if (result.isConfirmed) {

                        loading_div.css('display', 'flex');
                        let reader = new FileReader();
                        reader.onload = (e) => {
                            $('#profile-image').attr('src', e.target.result);
                            $('#profile-image-side-bar').attr('src', e.target.result);
                        }
                        if (this.files[0] == null) {
                            return false;
                        }
                        reader.readAsDataURL(this.files[0]);
                        var formData = new FormData();
                        formData.append("image", this.files[0]);
                        $.ajax({
                            type: 'POST',
                            url: "{{ route('admin.profile.update.image') }}",
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: (data) => {
                                console.log(data);
                                createToast('success', 'Profile image successfully updated.');
                                loading_div.css('display', 'none');
                            },
                            error: function (data) {
                                createToast('error', 'Failed to update profile image.');
                                loading_div.css('display', 'none');
                            }
                        });
                    }
                });
            });
            $('#profile_update_form').on('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Password Confirmation',
                    input: 'password',
                    inputPlaceholder: 'Enter your password to confirm',
                    inputAttributes: {
                        autocapitalize: 'off',
                        autocorrect: 'off',
                        id: 'swal-input-password',
                        class: 'form-control',
                        required: 'true',
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return "You need to write something!";
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                    backdrop: true,
                    confirmButtonColor: '#ff9948',
                    confirmButtonText: 'Submit',
                    customClass: {
                        confirmButton: 'btn btn-warning btn-round',
                    },
                    showCloseButton: true,
                    showLoaderOnConfirm: false,
                    allowEscapeKey: true,
                    allowEnterKey: true,
                    preConfirm: (password) => {
                        return new Promise((resolve, reject) => {
                            let url = '{{ route("admin.profile.password.confirmation") }}';
                            $(".box.loading_div").append(`<p id="loading-text">Confirming Password ...</p>`);

                            var pFormData = new FormData();
                            pFormData.append('password', password);
                            AjaxRequestPostPromise(url, pFormData, null, false, null, false, true, false).then((response) => {
                                if (response.message) {
                                    resolve();
                                } else {
                                    reject(response.errors && response.errors.password ? response.errors.password[0] : 'Failed to verify password.');
                                }
                            }).catch((error) => {
                                console.log(error);
                                swal.getConfirmButton().removeAttribute('disabled');
                                var errorMessage = "Incorrect Password.";
                                if (error.status === 401 && error.responseJSON.error && error.responseJSON.message) {
                                    errorMessage = error.responseJSON.message;
                                    window.location.href = '{{ route('admin.login') }}';
                                    Swal.close();
                                } else if (error.responseJSON.errors && error.responseJSON.errors.password) {
                                    errorMessage = error.responseJSON.errors.password[0];
                                }
                                reject(errorMessage);
                                Swal.showValidationMessage(errorMessage);

                            }).finally(() => {
                                $("#loading-text").remove();

                                loading_div.css('display', 'none');
                            })
                        });
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(".box.loading_div").append(`<p id="loading-text">Updating Profile ...</p>`);
                        let url = `{{route('admin.profile.update')}}`;
                        AjaxRequestPostPromise(url, new FormData(this), null, false, null, false, true, true).then((res) => {

                        }).catch((error) => {
                            console.log(error);
                        }).finally(() => {
                            $("#loading-text").remove();
                            loading_div.css('display', 'none');
                        })
                    } else {
                        console.log('User canceled or dismissed');
                    }
                }).catch((error) => {
                    console.log(error);
                });
            });
        });
    </script>
@endpush
