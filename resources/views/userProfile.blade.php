@extends('layouts.app')@section('cxmTitle', 'Profile')

@section('content')
    <section class="content">
        <div class="body_scroll">
            <div class="block-header">
                <div class="row">
                    <div class="col-lg-7 col-md-6 col-sm-12">
                        <h2>Profile</h2>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard')}}"><i class="zmdi zmdi-home"></i> Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile</li>
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
                    <div class="col-lg-4 col-md-12">
                        <div class="card mcard_3">
                            <div class="body">
                                <input type="file" id="ChangeImage" style="display: none;">
                                <label for="ChangeImage" id="ChangeImageLabel" title="edit image"> <img src="{{$member->image
                                     && in_array(strtolower(pathinfo($member->image, PATHINFO_EXTENSION)), ['jpeg', 'png', 'jpg', 'gif'])
                                     && file_exists(public_path('assets/images/profile_images/'). $member->image) ? asset('assets/images/profile_images/'.$member->image) : asset('assets/images/profile_av.jpg')}}" class="img-thumbnail rounded-circle shadow" id="profile-image" alt="{{$member->name}}" style="height:200px; width:200px; object-fit:cover;cursor: pointer;">
                                </label>
                                <h4 class="m-t-10 text-warning mb-0">
                                <span class="position-relative">
                                    @if($member->type == 'lead')
                                        <img class="crown_profile" src="{{ asset('assets/images/crown.png') }}" style="top:-20px; left:-20px;">
                                    @endif
                                    {{$member->name}}
                                </span>
                                </h4>
                                <h5>{{$member->pseudo_name}}</h5>
                                <div class="row">
                                    <div class="col-6">
                                        <small>Designation</small>
                                    </div>
                                    <div class="col-6">
                                        <p class="text-muted text-capitalize">{{$member->designation}}</p>
                                    </div>
{{--                                    <div class="col-6">--}}
{{--                                        <small>Position</small>--}}
{{--                                        <p class="text-muted text-capitalize">{{$member->type}}</p>--}}
{{--                                    </div>--}}
                                </div>
                                <hr class="mt-0">
                                <div class="row">
                                    <div class="col-6">
                                        <small>Target</small>
                                        <p class="text-muted text-capitalize">${{$member->target}}</p>
                                    </div>
                                    <div class="col-6">
                                        <small>Achived</small>
                                        <p class="text-muted text-capitalize">
                                            ${{$member->achived_amount}}
                                            {!! ($member->achived_amount > $member->target)?'<i class="zmdi zmdi-trending-up text-success"></i>' :'<i class="zmdi zmdi-trending-down text-warning"></i>' !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="body">
                                <h4 style="text-align: center;">Profile</h4>
                                <form id="user_profile_update_form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="name">Name:</label>
                                                <input type="text" class="form-control" id="name" name="name" value="{{$member->name}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="email">Email Address:</label>
                                                <input type="text" class="form-control" id="email" value="{{$member->email}}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="pseudo_email">Pseudo Email Address:</label>
                                                <input type="text" class="form-control" id="pseudo_email" name="pseudo_email" value="{{$member->pseudo_email}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group form-float">
                                                <label for="phone">Phone:</label>
                                                <input type="text" class="form-control" id="phone" name="phone" value="{{$member->phone}}">
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
                                <form id="user_profile_pass_update_form">
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
    @include('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            var loading_div = $('.loading_div')
            function removeError() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            }

            $('.password').on('input focus', removeError);

            $('#user_profile_pass_update_form').on('submit', function(e) {
                e.preventDefault();

                // Client-side validation
                let isValid = true;
                $('.error-message').remove();
                $('.password').each(function() {
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
                    url: '{{ route('user_profile_password_update') }}',
                    method: 'post',
                    data: $(this).serialize(),
                    success: function() {
                        createToast('success', 'Password updated successfully.');

                        $('.password').val('');
                        loading_div.css('display', 'none');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            console.log(errors);
                            $('.error-message').remove();
                            $.each(errors, function(field, message) {
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
                            url: "{{ route('update_profile_image') }}",
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
        });
        $('#user_profile_update_form').on('submit', function (e) {
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
                        let url = '{{ route("user.password.confirmation") }}';
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
                                window.location.href = '{{ route('login') }}';
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
                    let url = `{{route('user.profile.update')}}`;
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
    </script>
@endpush
