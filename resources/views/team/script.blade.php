<script>
    //var placementFrom = 'top';
    //var placementAlign = 'center';
    //var animateEnter = "";
    //var animateExit = "";
    //var colorName = 'alert-success';
    //var notifText = 'Status change successfully.';
    //showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);


    //Create Brand
    $('#team_form').on('submit', function (e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('team.store') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                $("#team_form")[0].reset();
                swal("Good job!", "Team successfully Created!", "success");
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    // Delete Brand
    $("#TeamTable").on("click", ".teamDelButton", function () {

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this team!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    var cid = $(this).data("id");
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: "team/" + cid,
                        success: function (data) {
                            swal("Poof! Your team has been deleted!", {
                                icon: "success",
                            });
                            setTimeout('location.reload()', 2000); /** Dm => update replacing setInterval from setTimeout */
                            // setInterval('location.reload()', 2000);        // Using .reload() method.
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your imaginary file is safe!");
                    console.log('error');
                }
            });
    });


    // Restore Team
    $("#TeamTable").on("click", ".teamRestoreButton", function () {

        var teamrestoreId = $(this).data("id");
        console.log(teamrestoreId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "Get",
            url: "team_restore/" + teamrestoreId,
            success: function (data) {
                console.log("tet");
                swal("Good job!", "Brand successfully Restore!", "success");
                setTimeout('location.reload()', 2000); /** Dm => update replacing setInterval from setTimeout */
                // setInterval('location.reload()', 2000);        // Using .reload() method.
            },
            error: function (data) {
                console.log('Error:', data);
                swal("Error!", "Request Fail!", "error");
            }
        });
    });


    //Restore All Team
    $("#restoreAllTean").on("click", ".teamRestoreAllButton", function () {

        swal({
            title: "Are you sure?",
            text: "want to Restore All Team !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    var cid = $(this).data("id");
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "Get",
                        url: "{{ route('restoreallteam') }}",
                        success: function (data) {
                            swal("Good job! Restore all team successfully!", {
                                icon: "success",
                            });
                            window.location = '{{url('admin/team')}}';
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your Team not restore!");
                    console.log('error');
                }
            });
    });

    // publish & unpublish function
    $("#TeamTable").on("change", ".team-toggle-class", function () {

        var status = $(this).prop('checked') == true ? 1 : 0;
        var brand_id = $(this).data('id');

        $.ajax({
            type: "GET",
            dataType: "json",
            url: '/teamchangeStatus',
            data: {'status': status, 'brand_id': brand_id},
            success: function (data) {
                swal("Good job!", "Status change successfully!", "success");
                console.log(data.success)
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });

    });


    //update Brand
    $('#team_update_form').on('submit', function (e) {
        e.preventDefault();

        var tid = $('#hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "/admin/team/" + tid,
            method: 'post',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (result) {
                console.log(result);
                swal("Good job!", "Team successfully Updated!", "success");
                setTimeout(function () {
                    window.location = '{{url('admin/team')}}';
                }, 2000);
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    $(document).ready(function () {

        const loading_div = $('.loading_div');
        const teamTable = $("#TeamTable");

        teamTable.on("click", ".abc", function () {
            console.log('test');
            var team_id = $(this).data('id');
            document.getElementById("hdn").value = team_id;
            console.log(team_id);
        });

        teamTable.on("click", ".assign-brands_emails", function () {
            $('#assign-email-member').val($(this).data('id'));
            let id = $('#assign-email-member').val();
            $('.email-checkbox').prop('checked', false);
            loading_div.css('display', 'flex');

            $('#email-config-row').empty();
            {{--let memberEmailConfigurations = @json($member_email_configurations ?? []);--}}

            {{--if (memberEmailConfigurations.hasOwnProperty(id.substring(2, id.length - 2))) {--}}
            {{--    let emailIds = memberEmailConfigurations[id.substring(2, id.length - 2)];--}}
            {{--    emailIds.forEach(function(emailId) {--}}
            {{--        $('#checkbox_' + emailId).prop('checked', true);--}}
            {{--    });--}}
            {{--}--}}

            var url = '{{ route('user.fetch.member.emails','/')}}/' + id;
            AjaxRequestGetPromise(url, null, null, false, null, false, false, false, false)
                .then((res) => {
                        if (res.status === 1) {
                            if (res.email_configurations && Object.keys(res.email_configurations).length > 0) {
                                Object.entries(res.email_configurations).forEach(([brandName, emailConfigurations]) => {
                                    $('#email-config-row').append(`
                                        <div class="col-md-12 col-email-${emailConfigurations[0].brand_key}">
                                            <div class="form-group">
                                                <strong>${brandName}</strong>
                                            </div>
                                        </div>
                                    `);

                                    emailConfigurations.forEach(email_configuration => {
                                        $('#email-config-row').append(`
                                            <div class="col-md-5 col-email-${emailConfigurations[0].brand_key}" id="col-email-config-${email_configuration.id}" style="margin: 0px 0px 0px 20px;">
                                                <div class="form-group">
                                                    <div class="checkbox">
                                                        <input id="checkbox_${email_configuration.id}" class="email-checkbox" type="checkbox" value="${email_configuration.id}" name="email" ${email_configuration.id} data-id="${id}">
                                                        <label for="checkbox_${email_configuration.id}">${email_configuration.email}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        `);
                                    });
                                });
                            }

                            let email_configuration_ids = res.email_configuration_ids;
                            email_configuration_ids.forEach(function (email_configuration_id) {
                                $('#checkbox_' + email_configuration_id).prop('checked', true);
                            });
                        } else {
                            createToast('error', 'Failed to fetch assign user brand emails.');
                        }
                        loading_div.css('display', 'none');
                    }
                )
                .catch((error) => {
                    console.log(error);
                    createToast('error', 'Failed to fetch assign user brand emails.');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });
        $("#assign-brands_emails-modal").on("click", ".email-checkbox", function (e) {
            e.preventDefault();
            var url = '{{ route('user.assign.unassign.user.brand.email','/')}}/' + $('#assign-email-member').val();

            var formData = new FormData();
            let email_id = $(this).val();
            formData.append('email_id', email_id);
            formData.append('checked', $(this).is(':checked'));
            loading_div.css('display', 'flex');
            AjaxRequestPostPromise(url, formData, null, false, null, false, false, true, false)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            var [toastType, message, checked, remove] = res.assign === 1 ? ['success', 'Assign user brand email successfully!', true, false] : ['warning', 'Unassign user brand email successfully!', false, true];
                            $('#checkbox_' + email_id).prop('checked', checked);

                            if (remove && res.remove_brand_key) {
                                $(`.col-email-${res.brand_key}`).remove();
                            } else if (remove && res.remove_email_id) {
                                $(`#col-email-config-${email_id}`).remove();
                            }
                            if(res.success){
                                message = res.success;
                                toastType = 'success';
                            }
                            createToast(toastType, message);
                        } else {
                            createToast('error', 'Failed to update user brand email');
                        }
                        loading_div.css('display', 'none');
                    }
                )
                .catch((error) => {
                    if (error.responseJSON && error.responseJSON.assign === 0) {
                        $('#checkbox_' + email_id).prop('checked', false);
                    }
                    if (error.responseJSON && error.responseJSON.brand_key && error.responseJSON.brand_key > 0) {
                        $(`.col-email-${error.responseJSON.brand_key}`).remove();
                    }
                    createToast('error', 'Failed to assign user brand email');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });


    });


    //Assing Brands to Team
    $('#assingBrandBtn').on('click', function (e) {
        e.preventDefault();

        var team_id = $('#hdn').val();
        var brand_key = $('#brand_key').val();
        var keys = String(brand_key);

        if (brand_key != '') {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/assignBrand',
                data: {'brand_key': keys, 'team_id': team_id},
                success: function (data) {
                    swal("Good job!", "Status change successfully!", "success");
                    console.log(data);
                    setInterval('location.reload()', 2000);        // Using .reload() method.
                },
                error: function () {
                    swal("Error!", "Request Fail!", "error");
                }
            });
        } else {
            swal("Error!", "Select Brand", "error");
        }
    });


    $("#TeamTable").on("click", ".xyz", function () {
        console.log('test');
        var team_key = $(this).data('id');
        document.getElementById("team_hnd").value = team_key
        console.log(team_key);
    });


    //Create Team Member
    $('#addTeamBtn').on('click', function (e) {
        e.preventDefault();

        var team_key = $('#team_hnd').val();
        var name = $('#name').val();
        var email = $('#email').val();
        var type = $('#type').val();
        var image = $('#image').val();

        console.log(team_key);

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('creatTeam') }}",
            data: {'team_key': team_key, 'name': name, 'email': email, 'type': type, 'image': image},
            success: function (data) {
                swal("Good job!", "Team successfully Created!", "success");
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });
</script>
