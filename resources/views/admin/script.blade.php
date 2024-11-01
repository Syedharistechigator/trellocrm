<style>
    .btn-warning {
        background-color: #ff9948 !important; /* Add !important to override any conflicting styles */
    }

    .box.loading_div p {
        margin: 0px 0px 0px 20px;
        color: black;
        font-size: 17px;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        <?php if (isset($data['statusValue'])){ ?>
        var chart = c3.generate({
            bindto: '#chart-pie', // id of chart wrapper
            data: {
                columns: <?php echo isset($data['statusValue']) ? $data['statusValue'] : ''; ?>,
                type: 'pie', // default type of chart
                colors: {
                    'data1': Aero.colors["default"],
                    'data2': Aero.colors["success"],
                    'data3': Aero.colors["primary"],
                    'data4': Aero.colors["info"],
                    'data5': Aero.colors["dark"],
                    'data6': Aero.colors["warning"],
                    'data7': Aero.colors["danger"],
                },
                names:  <?php echo isset($data['leadstatus']) ? $data['leadstatus'] : ''; ?>
            },
            axis: {},
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
        <?php } ?>
    });

    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-area-spline-sracked', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    //['data1', 21, 8, 32, 18, 19, 17, 23, 12]
                    [
                        'data1',
                        '<?php echo isset($data['m2']['January'][1]) ? $data['m2']['January'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['February'][1]) ? $data['m2']['February'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['March'][1]) ? $data['m2']['March'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['April'][1]) ? $data['m2']['April'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['May'][1]) ? $data['m2']['May'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['June'][1]) ? $data['m2']['June'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['July'][1]) ? $data['m2']['July'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['August'][1]) ? $data['m2']['August'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['September'][1]) ? $data['m2']['September'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['October'][1]) ? $data['m2']['October'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['November'][1]) ? $data['m2']['November'][1] : 0; ?>',
                        '<?php echo isset($data['m2']['December'][1]) ? $data['m2']['December'][1] : 0; ?>',
                    ],
                    ['data2',
                        '<?php echo isset($data['yearExpMonthWiseData']['January'][1]) ? $data['yearExpMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['February'][1]) ? $data['yearExpMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['March'][1]) ? $data['yearExpMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['April'][1]) ? $data['yearExpMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['May'][1]) ? $data['yearExpMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['June'][1]) ? $data['yearExpMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['July'][1]) ? $data['yearExpMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['August'][1]) ? $data['yearExpMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['September'][1]) ? $data['yearExpMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['October'][1]) ? $data['yearExpMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['November'][1]) ? $data['yearExpMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['yearExpMonthWiseData']['December'][1]) ? $data['yearExpMonthWiseData']['December'][1] : 0; ?>',
                    ]
                ],
                type: 'area-spline', // default type of chart
                groups: [
                    ['data1', 'data2', 'data3']
                ],
                colors: {
                    'data1': Aero.colors["lime"],
                    'data2': Aero.colors["teal"]
                },
                names: {
                    // name of each serie
                    'data1': 'Revenue',
                    'data2': 'Expense'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($data['m2']['January'][0]) ? $data['m2']['January'][0] : 'January'; ?>',
                        '<?php echo isset($data['m2']['February'][0]) ? $data['m2']['February'][0] : 'February'; ?>',
                        '<?php echo isset($data['m2']['March'][0]) ? $data['m2']['March'][0] : 'March'; ?>',
                        '<?php echo isset($data['m2']['April'][0]) ? $data['m2']['April'][0] : 'April'; ?>',
                        '<?php echo isset($data['m2']['May'][0]) ? $data['m2']['May'][0] : 'May'; ?>',
                        '<?php echo isset($data['m2']['June'][0]) ? $data['m2']['June'][0] : 'June'; ?>',
                        '<?php echo isset($data['m2']['July'][0]) ? $data['m2']['July'][0] : 'July'; ?>',
                        '<?php echo isset($data['m2']['August'][0]) ? $data['m2']['August'][0] : 'August'; ?>',
                        '<?php echo isset($data['m2']['September'][0]) ? $data['m2']['September'][0] : 'September'; ?>',
                        '<?php echo isset($data['m2']['October'][0]) ? $data['m2']['October'][0] : 'October'; ?>',
                        '<?php echo isset($data['m2']['November'][0]) ? $data['m2']['November'][0] : 'November'; ?>',
                        '<?php echo isset($data['m2']['December'][0]) ? $data['m2']['December'][0] : 'December'; ?>',
                    ]

                },
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });

    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-bar', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    [
                        'data1',
                        '<?php echo isset($data['refundYearMonthWiseData']['January'][1]) ? $data['refundYearMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['February'][1]) ? $data['refundYearMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['March'][1]) ? $data['refundYearMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['April'][1]) ? $data['refundYearMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['May'][1]) ? $data['refundYearMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['June'][1]) ? $data['refundYearMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['July'][1]) ? $data['refundYearMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['August'][1]) ? $data['refundYearMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['September'][1]) ? $data['refundYearMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['October'][1]) ? $data['refundYearMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['November'][1]) ? $data['refundYearMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['December'][1]) ? $data['refundYearMonthWiseData']['December'][1] : 0; ?>',
                    ],
                    ['data2',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['January'][1]) ? $data['chargebackYearMonthWiseData']['January'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['February'][1]) ? $data['chargebackYearMonthWiseData']['February'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['March'][1]) ? $data['chargebackYearMonthWiseData']['March'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['April'][1]) ? $data['chargebackYearMonthWiseData']['April'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['May'][1]) ? $data['chargebackYearMonthWiseData']['May'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['June'][1]) ? $data['chargebackYearMonthWiseData']['June'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['July'][1]) ? $data['chargebackYearMonthWiseData']['July'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['August'][1]) ? $data['chargebackYearMonthWiseData']['August'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['September'][1]) ? $data['chargebackYearMonthWiseData']['September'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['October'][1]) ? $data['chargebackYearMonthWiseData']['October'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['November'][1]) ? $data['chargebackYearMonthWiseData']['November'][1] : 0; ?>',
                        '<?php echo isset($data['chargebackYearMonthWiseData']['December'][1]) ? $data['chargebackYearMonthWiseData']['December'][1] : 0; ?>',
                    ]
                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["lime"],
                    'data2': Aero.colors["cyan"]
                },
                names: {
                    // name of each serie
                    'data1': 'Refund',
                    'data2': 'Charge Back'
                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($data['refundYearMonthWiseData']['January'][0]) ? $data['refundYearMonthWiseData']['January'][0] : 'January'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['February'][0]) ? $data['refundYearMonthWiseData']['February'][0] : 'February'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['March'][0]) ? $data['refundYearMonthWiseData']['March'][0] : 'March'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['April'][0]) ? $data['refundYearMonthWiseData']['April'][0] : 'April'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['May'][0]) ? $data['refundYearMonthWiseData']['May'][0] : 'May'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['June'][0]) ? $data['refundYearMonthWiseData']['June'][0] : 'June'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['July'][0]) ? $data['refundYearMonthWiseData']['July'][0] : 'July'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['August'][0]) ? $data['refundYearMonthWiseData']['August'][0] : 'August'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['September'][0]) ? $data['refundYearMonthWiseData']['September'][0] : 'September'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['October'][0]) ? $data['refundYearMonthWiseData']['October'][0] : 'October'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['November'][0]) ? $data['refundYearMonthWiseData']['November'][0] : 'November'; ?>',
                        '<?php echo isset($data['refundYearMonthWiseData']['December'][0]) ? $data['refundYearMonthWiseData']['December'][0] : 'December'; ?>',
                    ]
                },
            },
            bar: {
                width: 16
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });
    // current Week Income Chart
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#week-chart-bar', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    [
                        'data1',
                        '<?php echo isset($data['weekDaysWise']['Monday'][1]) ? $data['weekDaysWise']['Monday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Tuesday'][1]) ? $data['weekDaysWise']['Tuesday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Wednesday'][1]) ? $data['weekDaysWise']['Wednesday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Thursday'][1]) ? $data['weekDaysWise']['Thursday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Friday'][1]) ? $data['weekDaysWise']['Friday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Saturday'][1]) ? $data['weekDaysWise']['Saturday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Sunday'][1]) ? $data['weekDaysWise']['Sunday'][1] : 0; ?>',
                    ]

                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["lime"],

                },
                names: {
                    // name of each serie
                    'data1': 'Weekly Revenue',

                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($data['weekDaysWise']['Monday'][0]) ? $data['weekDaysWise']['Monday'][0] : 'Monday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Tuesday'][0]) ? $data['weekDaysWise']['Tuesday'][0] : 'Tuesday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Wednesday'][0]) ? $data['weekDaysWise']['Wednesday'][0] : 'Wednesday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Thursday'][0]) ? $data['weekDaysWise']['Thursday'][0] : 'Thursday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Friday'][0]) ? $data['weekDaysWise']['Friday'][0] : 'Friday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Saturday'][0]) ? $data['weekDaysWise']['Saturday'][0] : 'Saturday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Sunday'][0]) ? $data['weekDaysWise']['Sunday'][0] : 'Sunday'; ?>',

                    ]
                },
            },
            bar: {
                width: 30
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });

    // Authorize Current Month Usage Chart
    $(document).ready(function () {

        @if(app('dm_payment_method_authorize'))
        let merchants = {!! app('dm_payment_method_authorize')->pluck('merchant') !!};
        let usages = {!! app('dm_payment_method_expigate')->pluck('cap_usage') !!};

        let chart = c3.generate({
            bindto: '#authorize-current-month-chart-bar', // id of chart wrapper
            data: {
                columns: [
                    ['Usage'].concat(usages)
                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["lime"],

                },
                names: {
                    // name of each series
                    'data1': 'Usage',

                }
            },
            axis: {
                x: {
                    type: 'category',// name of each category
                    categories: merchants
                },
            },
            bar: {
                width: 30
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
        @endif
    });

    // Expigate Current Month Usage Chart
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#expigate-current-month-chart-bar', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    [
                        'data1',
                        '<?php echo isset($data['weekDaysWise']['Monday'][1]) ? $data['weekDaysWise']['Monday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Tuesday'][1]) ? $data['weekDaysWise']['Tuesday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Wednesday'][1]) ? $data['weekDaysWise']['Wednesday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Thursday'][1]) ? $data['weekDaysWise']['Thursday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Friday'][1]) ? $data['weekDaysWise']['Friday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Saturday'][1]) ? $data['weekDaysWise']['Saturday'][1] : 0; ?>',
                        '<?php echo isset($data['weekDaysWise']['Sunday'][1]) ? $data['weekDaysWise']['Sunday'][1] : 0; ?>',
                    ]

                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["lime"],

                },
                names: {
                    // name of each series
                    'data1': 'Usage',

                }
            },
            axis: {
                x: {
                    type: 'category',// name of each category
                    categories: [
                        '<?php echo isset($data['weekDaysWise']['Monday'][0]) ? $data['weekDaysWise']['Monday'][0] : 'Monday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Tuesday'][0]) ? $data['weekDaysWise']['Tuesday'][0] : 'Tuesday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Wednesday'][0]) ? $data['weekDaysWise']['Wednesday'][0] : 'Wednesday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Thursday'][0]) ? $data['weekDaysWise']['Thursday'][0] : 'Thursday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Friday'][0]) ? $data['weekDaysWise']['Friday'][0] : 'Friday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Saturday'][0]) ? $data['weekDaysWise']['Saturday'][0] : 'Saturday'; ?>',
                        '<?php echo isset($data['weekDaysWise']['Sunday'][0]) ? $data['weekDaysWise']['Sunday'][0] : 'Sunday'; ?>',

                    ]
                },
            },
            bar: {
                width: 30
            },
            legend: {
                show: true, //hide legend
            },
            padding: {
                bottom: 0,
                top: 0
            },
        });
    });

    {{--$(document).ready(function () {--}}

    {{--    var loading_div = $('.loading_div')--}}
    {{--    $('#profile_update_form').on('submit', function (e) {--}}
    {{--        e.preventDefault();--}}
    {{--        Swal.fire({--}}
    {{--            title: 'Password Confirmation',--}}
    {{--            input: 'password',--}}
    {{--            inputPlaceholder: 'Enter your password to confirm',--}}
    {{--            inputAttributes: {--}}
    {{--                autocapitalize: 'off',--}}
    {{--                autocorrect: 'off',--}}
    {{--                id: 'swal-input-password',--}}
    {{--                class: 'form-control',--}}
    {{--                required: 'true',--}}
    {{--            },--}}
    {{--            inputValidator: (value) => {--}}
    {{--                if (!value) {--}}
    {{--                    return "You need to write something!";--}}
    {{--                }--}}
    {{--            },--}}
    {{--            allowOutsideClick: () => !Swal.isLoading(),--}}
    {{--            backdrop: true,--}}
    {{--            confirmButtonColor: '#ff9948',--}}
    {{--            confirmButtonText: 'Submit',--}}
    {{--            customClass: {--}}
    {{--                confirmButton: 'btn btn-warning btn-round',--}}
    {{--            },--}}
    {{--            showCloseButton: true,--}}
    {{--            showLoaderOnConfirm: false,--}}
    {{--            allowEscapeKey: true,--}}
    {{--            allowEnterKey: true,--}}
    {{--            preConfirm: (password) => {--}}
    {{--                return new Promise((resolve, reject) => {--}}
    {{--                    let url = '{{ route("admin.profile.password.confirmation") }}';--}}
    {{--                    $(".box.loading_div").append(`<p id="loading-text">Confirming Password ...</p>`);--}}

    {{--                    var pFormData = new FormData();--}}
    {{--                    pFormData.append('password', password);--}}
    {{--                    AjaxRequestPostPromise(url, pFormData, null, false, null, false, true, false).then((response) => {--}}
    {{--                        if (response.message) {--}}
    {{--                            resolve();--}}
    {{--                        } else {--}}
    {{--                            reject(response.errors && response.errors.password ? response.errors.password[0] : 'Failed to verify password.');--}}
    {{--                        }--}}
    {{--                    }).catch((error) => {--}}
    {{--                        console.log(error);--}}
    {{--                        swal.getConfirmButton().removeAttribute('disabled');--}}
    {{--                        var errorMessage = "Incorrect Password.";--}}
    {{--                        if (error.status === 401 && error.responseJSON.error && error.responseJSON.message) {--}}
    {{--                            errorMessage = error.responseJSON.message;--}}
    {{--                            window.location.href = '{{ route('admin.login') }}';--}}
    {{--                            Swal.close();--}}
    {{--                        } else if (error.responseJSON.errors && error.responseJSON.errors.password) {--}}
    {{--                            errorMessage = error.responseJSON.errors.password[0];--}}
    {{--                        }--}}
    {{--                        reject(errorMessage);--}}
    {{--                        Swal.showValidationMessage(errorMessage);--}}

    {{--                    }).finally(() => {--}}
    {{--                        $("#loading-text").remove();--}}

    {{--                        loading_div.css('display', 'none');--}}
    {{--                    })--}}
    {{--                });--}}
    {{--            },--}}
    {{--        }).then((result) => {--}}
    {{--            if (result.isConfirmed) {--}}
    {{--                $(".box.loading_div").append(`<p id="loading-text">Updating Profile ...</p>`);--}}
    {{--                let url = `{{route('admin.profile.update')}}`;--}}
    {{--                AjaxRequestPostPromise(url, new FormData(this), null, false, null, false, true, true).then((res) => {--}}

    {{--                }).catch((error) => {--}}
    {{--                    console.log(error);--}}
    {{--                }).finally(() => {--}}
    {{--                    $("#loading-text").remove();--}}
    {{--                    loading_div.css('display', 'none');--}}
    {{--                })--}}
    {{--            } else {--}}
    {{--                console.log('User canceled or dismissed');--}}
    {{--            }--}}
    {{--        }).catch((error) => {--}}
    {{--            console.log(error);--}}
    {{--        });--}}
    {{--    });--}}

    {{--    $('#password_update_form').on('submit', function (e) {--}}
    {{--        e.preventDefault();--}}

    {{--        $.ajaxSetup({--}}
    {{--            headers: {--}}
    {{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--            }--}}
    {{--        });--}}

    {{--        $.ajax({--}}
    {{--            url: '{{route('admin.profile.password.update')}}',--}}
    {{--            method: 'post',--}}
    {{--            data: $(this).serialize(),--}}
    {{--            success: function () {--}}
    {{--                var successMessage = "Password updated successfully.";--}}
    {{--                swal("Good job!", successMessage, "success");--}}
    {{--                $('.password').val('');--}}
    {{--            },--}}
    {{--            error: function (xhr) {--}}
    {{--                if (xhr.status === 422) {--}}
    {{--                    var errors = xhr.responseJSON.errors;--}}
    {{--                    console.log(errors);--}}
    {{--                    $('.error-message').remove();--}}
    {{--                    $.each(errors, function (field, message) {--}}
    {{--                        var input = $('[name="' + field + '"]');--}}
    {{--                        input.addClass('is-invalid');--}}
    {{--                        input.after('<span class="error-message text-danger">' + message[0] + '</span>');--}}
    {{--                    });--}}
    {{--                } else {--}}
    {{--                    console.log(xhr.responseJSON);--}}
    {{--                    swal("Error!", "Request Fail!", "error");--}}
    {{--                }--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}

    {{--    //Change Image--}}
    {{--    $('#ChangeImage').change(function () {--}}

    {{--        swal({--}}
    {{--            title: "Change Profile Image",--}}
    {{--            text: "Are you sure?",--}}
    {{--            icon: "warning",--}}
    {{--            buttons: true,--}}
    {{--            dangerMode: true,--}}
    {{--        }).then((willDelete) => {--}}
    {{--            if (willDelete) {--}}
    {{--                let reader = new FileReader();--}}
    {{--                reader.onload = (e) => {--}}
    {{--                    $('#profile-image').attr('src', e.target.result);--}}
    {{--                    $('#profile-image-side-bar').attr('src', e.target.result);--}}
    {{--                }--}}
    {{--                if (this.files[0] == null) {--}}
    {{--                    return false;--}}
    {{--                }--}}
    {{--                reader.readAsDataURL(this.files[0]);--}}
    {{--                var formData = new FormData();--}}
    {{--                formData.append("image", this.files[0]);--}}
    {{--                $.ajax({--}}
    {{--                    type: 'POST',--}}
    {{--                    url: "{{ route('admin.profile.update.image') }}",--}}
    {{--                    data: formData,--}}
    {{--                    cache: false,--}}
    {{--                    contentType: false,--}}
    {{--                    processData: false,--}}
    {{--                    headers: {--}}
    {{--                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--                    },--}}
    {{--                    success: (data) => {--}}
    {{--                        console.log(data);--}}
    {{--                        createToast('success', 'Profile image successfully updated.');--}}
    {{--                    },--}}
    {{--                    error: function (data) {--}}
    {{--                        createToast('success', 'Failed to update profile image.');--}}
    {{--                    }--}}
    {{--                });--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}
    {{--});--}}
</script>
