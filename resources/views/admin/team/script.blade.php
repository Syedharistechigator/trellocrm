<script>
    //var placementFrom = 'top';
    //var placementAlign = 'center';
    //var animateEnter = "";
    //var animateExit = "";
    //var colorName = 'alert-success';
    //var notifText = 'Status change successfully.';
    //showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);
    /** Profile Page */
    $(document).ready(function () {

        const loading_div = $('.loading_div');
        {{--$('.email-checkbox').on('click', function () {--}}
        {{--    var email_id = $(this).val();--}}
        {{--    var url = '{{ route('admin.assign.unassign.user.brand.email','/')}}/' + $(this).attr('data-id');--}}
        {{--    AjaxRequestPost(url, {email_id: email_id,checked:$(this).is(':checked')}, 'Assign user brand email updated successfully!', false, null, false);--}}
        {{--});--}}

        $(document).on('shown.bs.modal', '.modal', function () {
            $('[type=search]').prop('disabled', true);
        });
        $(document).on('hidden.bs.modal', '.modal', function () {
            $('[type=search]').prop('disabled', false);
        });

        $('.email-checkbox').on('click', function (e) {
            e.preventDefault();
            let id = $(this).attr('data-id');
            let url = '{{ route('admin.assign.unassign.user.brand.email','/')}}/' + id;

            let formData = new FormData();
            let email_id = $(this).val();
            formData.append('email_id', email_id);
            formData.append('checked', $(this).is(':checked'));
            loading_div.css('display', 'flex');
            AjaxRequestPostPromise(url, formData, null, false, null, false, false, true, false)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            let [toastType, message, checked] = res.assign === 1 ? ['success', 'Assign user brand email successfully!', true] : ['warning', 'Unassign user brand email successfully!', false];
                            $('#checkbox_' + email_id).prop('checked', checked);

                            if (res.success) {
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
                    createToast('error', 'Failed to assign user brand email');
                })
                .finally(() => {
                    loading_div.css('display', 'none');
                })
        });

        function toggleAssignedTeamsVisibility(userType, modalSelector) {
            const assignedTeamsContainer = $(`${modalSelector} .assigned_teams_container`);
            assignedTeamsContainer.toggle(userType === 'ppc');
        }
        function toggleDepartmentVisibility(userType, modalSelector) {
            const assignedDepartmentsContainer = $(`${modalSelector} .department-div`);
            assignedDepartmentsContainer.toggle(['executive', 'ppc', 'third-party-user'].includes(userType));
        }

        // function toggleDepartmentVisibility(isChecked, modalSelector) {
        //     const departmentContainer = $(`${modalSelector} .department-div`);
        //     departmentContainer.toggle(isChecked);
        // }

        function checkModalExistenceAndAttachHandler(modalSelector) {
            const modal = $(modalSelector);
            if (modal.length) {
                $(`${modalSelector} #type, ${modalSelector} #edit_type`).change(function () {
                    toggleAssignedTeamsVisibility($(this).val(), modalSelector);
                    toggleDepartmentVisibility($(this).val(), modalSelector);
                });
                // $(`${modalSelector} #has-department, ${modalSelector} #has-department-edit`).change(function () {
                //     toggleDepartmentVisibility($(this).is(':checked'), modalSelector);
                // });
            }
        }

        function resetForm(modalSelector) {
            const form = $(modalSelector).find('form')[0];
            if (form) {
                form.reset();
            }
            $(modalSelector).find('select').each(function () {
                $(this).val($(this).find('option:first').val()).trigger('change');
            });
            $(modalSelector).find('.assigned_teams_container').hide();
            // $(modalSelector).find('.department-div').hide();
        }

        checkModalExistenceAndAttachHandler('#teamModal');
        checkModalExistenceAndAttachHandler('#edit_member_Modal');

        $(document).on('shown.bs.modal', '#teamModal, #edit_member_Modal', function () {
            const modalId = $(this).attr('id');
            checkModalExistenceAndAttachHandler(`#${modalId}`);
            const userType = $(`#${modalId} #type`).val();
            toggleAssignedTeamsVisibility(userType, `#${modalId}`);
            toggleDepartmentVisibility(userType, `#${modalId}`);
            // const departmentChecked = $(`#${modalId} #has-department`).is(':checked');
            // toggleDepartmentVisibility(departmentChecked, `#${modalId}`);
        });

        $(document).on('hide.bs.modal', '#teamModal, #edit_member_Modal', function () {
            $(this).find('.assigned_teams_container , .department-div').hide();
            resetForm(`#${$(this).attr('id')}`);
            // $(this).find('.assigned_teams_container, .department-div').hide();
        });


    });
    /** Profile Page */
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
            text: "You will not be able to recover member!",
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
                    text: "Yes, Delete!"
                }
            },
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
                            swal("Poof! Your team Member has been deleted!", {
                                icon: "success",
                            });
                            setInterval('location.reload()', 2000);        // Using .reload() method.
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your team member is safe!", {buttons: false, timer: 1000});
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
                setInterval('location.reload()', 2000);        // Using .reload() method.
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
                            window.location = '{{url("admin/team")}}';
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

    //update Brand new code
    $('#team_update_form').on('submit', function (e) {
        e.preventDefault();
        var tid = $('#hdn').val();
        var brandName = $('#brandName').val();
        var team_lead = $('#team_lead').find(":selected").val();
        var status = $('#status').find(":selected").val();
        let agents = [];
        $("input:checkbox[name=agents]:checked").each(function () {
            agents.push($(this).val());
        });

        let assignbrands = [];
        $("input:checkbox[name=brands]:checked").each(function () {
            assignbrands.push($(this).val());
        });
        let tm_ppc = $('#tm-ppc').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "/admin/team/" + tid,
            method: 'PUT',
            data: {
                'brandName': brandName,
                'team_lead': team_lead,
                'status': status,
                'agents': agents,
                'assignbrands': assignbrands,
                'tm_ppc': tm_ppc
            },
            success: function (result) {
                // console.log(result);
                swal("Update Successfully!", "Team successfully Update successfully.", "success", {
                    buttons: false,
                    timer: 2000
                })
                    .then((cxmVal) => {
                        location.reload();
                    });
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    //update Brand old code
    /*
    $('#team_update_form_old').on('submit', function(e){
        e.preventDefault();
        var tid = $('#hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "/admin/team/"+tid,
            method:'post',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(result){
                console.log(result);
                swal("Good job!", "Team successfully Updated!", "success");
                setTimeout(function(){
                    window.location='{{url('admin/team')}}';
            }, 2000);
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
    });
});
*/

    $("#TeamTable").on("click", ".abc", function () {
        console.log('test');
        var team_id = $(this).data('id');
        document.getElementById("hdn").value = team_id;
        console.log(team_id);
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

        let agents = [];
        $("input:checkbox[name=agents]:checked").each(function () {
            agents.push($(this).val());
        });

        var team_key = $('#team_hnd').val();

        console.log(team_key);
        console.log(agents);

        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('creatTeam') }}",
            data: {
                'team_key': team_key,
                'agents': agents
            },
            success: function (data) {
                $("#teamModal").modal('hide');
                swal("Good job!", "Team successfully Created!", "success");
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    $(".memberInactiveButton").on("click", function () {
        var status = 0;
        var member_id = $(this).data('id');
        swal({
            title: "Are you sure?",
            text: "You want to inactive the member?",
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
                    text: "Yes, inactive!"
                }
            },
            dangerMode: true,
        })
            .then((cxmInactiveMember) => {
                if (cxmInactiveMember) {
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: '/changeMemberStatus',
                        data: {'status': status, 'member_id': member_id},
                        success: function (data) {
                            location.reload();
                            swal("Done job!", "Member inactive successfully.", "success", {
                                buttons: false,
                                timer: 1000
                            });
                        },
                        error: function () {
                            swal("Error!", "Request Fail!", "error");
                        }
                    });
                } else {
                    swal("Member remain active!", {buttons: false, timer: 1000});
                }
            });
    });
    $(".custom-switch-member").on("change", ".toggle-class", function () {
        var status = $(this).prop('checked') == true ? 1 : 0;
        var member_id = $(this).data('id');
        //  alert(member_id);
        swal({
            title: "Are you sure?",
            text: "You want to inactive the member?",
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
                    text: "Yes, inactive!"
                }
            },
            dangerMode: true,
        })
            .then((cxmInactiveMember) => {
                if (cxmInactiveMember) {
                    $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: '/changeMemberStatus',
                        data: {'status': status, 'member_id': member_id},
                        success: function (data) {
                            // window.location.href = "/memberlist";
                            // console.log(data.success)
                            swal("Done job!", "Member inactive successfully.", "success", {
                                buttons: false,
                                timer: 1000
                            });
                        },
                        error: function () {
                            swal("Error!", "Request Fail!", "error");
                        }
                    });
                } else {
                    swal("Member remain active!", {buttons: false, timer: 1000});
                    $(this).prop('checked', true);
                }
            });
    });

    //Create Employee
    $('#team-Emp-Form').on('submit', function (e) {
        e.preventDefault();
        $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('createEmployee') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                $('.page-loader-wrapper').css('display', 'none');
                if ($.isEmptyObject(data.error)) {
                    $("#team-Emp-Form")[0].reset();
                    console.log(data);
                    $("#teamModal").modal('hide');

                    swal("Good job!", "Employee successfully Created!", "success")
                        .then(() => {
                            location.reload();
                        });

                } else {
                    printErrorMsg(data.error);
                    //swal("Errors!", data.error, "error");
                }
            },
            error: function () {
                $('.page-loader-wrapper').css('display', 'none');
                swal("Errors!", "Request Fail!", "error");
            }
        });
    });


    $("#TeamTable").on("click", ".edit_member", function () {

        $('.loading_div').css('display', 'flex');
        var form_fields = $('form').find('input,textarea,input[type="checkbox"]');
        form_fields.prop('disabled', true);
        var edit_member_id = $(this).data('id');

        document.getElementById("member_hdn").value = edit_member_id;

        $.ajax({
            type: "GET",
            url: "editemployee/" + edit_member_id,
            success: function (data) {
                console.log('editemployee__', data);
                $('#edit_name').val(data.name);
                $('#edit_email').val(data.email);
                $('#edit_phone').val(data.phone);
                $('#edit_designation').val(data.designation);
                $('#edit_pseudo_name').val(data.pseudo_name);
                $('#edit_pseudo_email').val(data.pseudo_email);
                $('#edit_target').val(data.target);
                $('#edit_image').val(data.image);
                $('#edit_type').val(data.type).selectpicker('refresh');
                $('#edit_status').val(data.status);
                $('#edit-user-access').val([data.user_access]).selectpicker('refresh');
                if (data.lead_special_access == 1) {
                    $('#edit_lead_special_access').attr('checked', true)
                } else {
                    $('#edit_lead_special_access').attr('checked', false)
                }
                $('#edit_assigned_team_key').val("").selectpicker('refresh');
                let assigned_count;
                @if(isset($teams))
                    assigned_count = {{ count($teams) }};
                @endif

                if (data.type == 'ppc') {
                    if (data.assigned_teams) {
                        if (data.assigned_teams.length == assigned_count) {
                            $('#edit_assigned_team_key').val([0]).selectpicker('refresh');
                        } else {
                            $('#edit_assigned_team_key').val(data.assigned_teams).selectpicker('refresh');
                        }
                    } else {
                        $('#edit_assigned_team_key').val([]).selectpicker('refresh');
                    }
                    $('#edit_member_Modal .assigned_teams_container').show();
                } else {
                    $('#edit_assigned_team_key').val([]).selectpicker('refresh');
                    $('#edit_member_Modal .assigned_teams_container').hide();
                }

                /** Department for executive , ppc , third-party-user*/
                let departmentIds = data.get_department ? data.get_department.map(dept => dept.id) : $('#edit_assigned_departments option:first').val();
                $('#edit_assigned_departments').val(departmentIds).selectpicker('refresh');
                if (['executive', 'ppc', 'third-party-user'].includes(data.type)) {
                    $('#edit_member_Modal .department-div').show();
                } else {
                    $('#edit_member_Modal .department-div').hide();
                }
                $('.loading_div').css('display', 'none');
                form_fields.prop('disabled', false);
            },
            error: function (data) {
                console.log('Error:', data);
                $('.loading_div').css('display', 'none');
                form_fields.prop('disabled', false);
            }
        });
    });

    $('.updateemployeeBtn').on('click', function (e) {
        e.preventDefault();

        var mid = $('#member_hdn').val();

        var edit_name = $('#edit_name').val();
        var edit_email = $('#edit_email').val();
        var edit_phone = $('#edit_phone').val();
        var edit_designation = $('#edit_designation').val();
        var edit_pseudo_name = $('#edit_pseudo_name').val();
        var edit_pseudo_email = $('#edit_pseudo_email').val();
        var edit_target = $('#edit_target').val();
        var edit_image = $('#edit_image').val();
        var edit_type = $('#edit_type').val();
        var edit_status = $('#edit_status').val();
        var edit_lead_special_access = $('#edit_lead_special_access').is(":checked");
        const assigned_team_key = $('#edit_assigned_team_key').val();
        // const has_department = $('#has-department-edit').is(":checked") ? "yes" : 'no';
        const assigned_departments = $('#edit_assigned_departments').val();
        const edit_user_access = $('#edit-user-access').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            // url: "account/"+aid,
            url: "updateemployee/" + mid,

            // url: "account/"+aid+'/edit',
            method: 'PUT',
            //data: $(this).serialize(), // get all form field value in serialize form
            data: {
                'edit_name': edit_name,
                'edit_email': edit_email,
                'edit_phone': edit_phone,
                'edit_designation': edit_designation,
                'edit_pseudo_name': edit_pseudo_name,
                'edit_pseudo_email': edit_pseudo_email,
                'edit_target': edit_target,
                'edit_image': edit_image,
                'edit_type': edit_type,
                'edit_status': edit_status,
                'edit_lead_special_access': edit_lead_special_access,
                'assigned_team_key': assigned_team_key,
                // 'has_department': has_department,
                'assigned_departments': assigned_departments,
                'edit_user_access': edit_user_access
            },


            success: function (data) {

                console.log(data);
                $("#edit_team-Emp-Form").modal('hide');
                swal("Good job!", "Employee successfully Updated!", "success");
                setTimeout('location.reload()', 2000);
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    // password generate for members on click edit password

    $("#TeamTable").on("click", ".edit_member_pass", function () {
        var edit_member_id = $(this).data('id');
        document.getElementById("member_hdn").value = edit_member_id;
    });


    function generateRandomPassword() {
        const length = 12; // You can adjust the desired length
        const lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        const uppercaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        const symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        const characters = lowercaseLetters + uppercaseLetters + numbers + symbols;

        let generatedPassword = '';
        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * characters.length);
            generatedPassword += characters.charAt(randomIndex);
        }

        return generatedPassword;
    }

    // Event handler for clicking the "Edit Member Password" icon
    const editMemberPassIcons = document.querySelectorAll('.edit_member_pass');
    editMemberPassIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const generatedPassword = generateRandomPassword();
            document.getElementById('edit_pass').value = generatedPassword;
        });
    });
    //on click generate password
    document.getElementById('generatePassword').addEventListener('click', function () {
        const generatedPassword = generateRandomPassword();
        document.getElementById('edit_pass').value = generatedPassword;
    });

    // copy password
    function myFunction() {
        /* Get the text field */
        var copyText = document.getElementById("edit_pass");
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");
        /* Alert the copied text */
        //alert("Copied the text: " + copyText.value);
    }

    // password update employee
    $('.updateemployeePassBtn').on('click', function (e) {
        e.preventDefault();
        var mid = $('#member_hdn').val();
        var edit_pass = $('#edit_pass').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('update_employee_pass')}}",
            method: 'POST',
            data: {'edit_pass': edit_pass, 'mid': mid},
            success: function (data) {
                $("#edit-emp-pass-form").modal('hide');
                swal("Good job!", "Employee password successfully Updated!", "success");
                location.reload();
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });


    //Team Member Paymnet Bar Chart
    $(document).ready(function () {
        var chart = c3.generate({
            bindto: '#chart-bar', // id of chart wrapper
            data: {
                columns: [
                    // each columns data
                    [
                        'data1',
                        '<?php echo isset($agentFreshPaymentData['January'][1]) ? $agentFreshPaymentData['January'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['February'][1]) ? $agentFreshPaymentData['February'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['March'][1]) ? $agentFreshPaymentData['March'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['April'][1]) ? $agentFreshPaymentData['April'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['May'][1]) ? $agentFreshPaymentData['May'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['June'][1]) ? $agentFreshPaymentData['June'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['June'][1]) ? $agentFreshPaymentData['June'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['August'][1]) ? $agentFreshPaymentData['August'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['September'][1]) ? $agentFreshPaymentData['September'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['October'][1]) ? $agentFreshPaymentData['October'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['November'][1]) ? $agentFreshPaymentData['November'][1] : 0; ?>',
                        '<?php echo isset($agentFreshPaymentData['December'][1]) ? $agentFreshPaymentData['December'][1] : 0; ?>',
                    ],
                    [
                        'data2',
                        '<?php echo isset($agentUpsalePaymentData['January'][1]) ? $agentUpsalePaymentData['January'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['February'][1]) ? $agentUpsalePaymentData['February'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['March'][1]) ? $agentUpsalePaymentData['March'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['April'][1]) ? $agentUpsalePaymentData['April'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['May'][1]) ? $agentUpsalePaymentData['May'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['June'][1]) ? $agentUpsalePaymentData['June'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['June'][1]) ? $agentUpsalePaymentData['June'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['August'][1]) ? $agentUpsalePaymentData['August'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['September'][1]) ? $agentUpsalePaymentData['September'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['October'][1]) ? $agentUpsalePaymentData['October'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['November'][1]) ? $agentUpsalePaymentData['November'][1] : 0; ?>',
                        '<?php echo isset($agentUpsalePaymentData['December'][1]) ? $agentUpsalePaymentData['December'][1] : 0; ?>',
                    ],
                    [
                        'data3',
                        '<?php echo isset($refundYearMonthWiseData['January'][1]) ? $refundYearMonthWiseData['January'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['February'][1]) ? $refundYearMonthWiseData['February'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['March'][1]) ? $refundYearMonthWiseData['March'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['April'][1]) ? $refundYearMonthWiseData['April'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['May'][1]) ? $refundYearMonthWiseData['May'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['June'][1]) ? $refundYearMonthWiseData['June'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['June'][1]) ? $refundYearMonthWiseData['June'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['August'][1]) ? $refundYearMonthWiseData['August'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['September'][1]) ? $refundYearMonthWiseData['September'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['October'][1]) ? $refundYearMonthWiseData['October'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['November'][1]) ? $refundYearMonthWiseData['November'][1] : 0; ?>',
                        '<?php echo isset($refundYearMonthWiseData['December'][1]) ? $refundYearMonthWiseData['December'][1] : 0; ?>',
                    ],
                    [
                        'data4',
                        '<?php echo isset($chargebackYearMonthWiseData['January'][1]) ? $chargebackYearMonthWiseData['January'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['February'][1]) ? $chargebackYearMonthWiseData['February'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['March'][1]) ? $chargebackYearMonthWiseData['March'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['April'][1]) ? $chargebackYearMonthWiseData['April'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['May'][1]) ? $chargebackYearMonthWiseData['May'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['June'][1]) ? $chargebackYearMonthWiseData['June'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['June'][1]) ? $chargebackYearMonthWiseData['June'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['August'][1]) ? $chargebackYearMonthWiseData['August'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['September'][1]) ? $chargebackYearMonthWiseData['September'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['October'][1]) ? $chargebackYearMonthWiseData['October'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['November'][1]) ? $chargebackYearMonthWiseData['November'][1] : 0; ?>',
                        '<?php echo isset($chargebackYearMonthWiseData['December'][1]) ? $chargebackYearMonthWiseData['December'][1] : 0; ?>',
                    ]
                ],
                type: 'bar', // default type of chart
                colors: {
                    'data1': Aero.colors["blue"],
                    'data2': Aero.colors["pink"],
                    'data3': Aero.colors["cyan"],
                    'data4': Aero.colors["indigo"]
                },
                names: {
                    // name of each serie
                    'data1': 'Fresh',
                    'data2': 'Upsale',
                    'data3': 'Refund',
                    'data4': 'Chargeback',

                }
            },
            axis: {
                x: {
                    type: 'category',
                    // name of each category
                    categories: [
                        '<?php echo isset($agentFreshPaymentData['January'][0]) ? $agentFreshPaymentData['January'][0] : 'January'; ?>',
                        '<?php echo isset($agentFreshPaymentData['February'][0]) ? $agentFreshPaymentData['February'][0] : 'February'; ?>',
                        '<?php echo isset($agentFreshPaymentData['March'][0]) ? $agentFreshPaymentData['March'][0] : 'March'; ?>',
                        '<?php echo isset($agentFreshPaymentData['April'][0]) ? $agentFreshPaymentData['April'][0] : 'April'; ?>',
                        '<?php echo isset($agentFreshPaymentData['May'][0]) ? $agentFreshPaymentData['May'][0] : 'May'; ?>',
                        '<?php echo isset($agentFreshPaymentData['June'][0]) ? $agentFreshPaymentData['June'][0] : 'June'; ?>',
                        '<?php echo isset($agentFreshPaymentData['July'][0]) ? $agentFreshPaymentData['July'][0] : 'July'; ?>',
                        '<?php echo isset($agentFreshPaymentData['August'][0]) ? $agentFreshPaymentData['August'][0] : 'August'; ?>',
                        '<?php echo isset($agentFreshPaymentData['September'][0]) ? $agentFreshPaymentData['September'][0] : 'September'; ?>',
                        '<?php echo isset($agentFreshPaymentData['October'][0]) ? $agentFreshPaymentData['October'][0] : 'October'; ?>',
                        '<?php echo isset($agentFreshPaymentData['November'][0]) ? $agentFreshPaymentData['November'][0] : 'November'; ?>',
                        '<?php echo isset($agentFreshPaymentData['December'][0]) ? $agentFreshPaymentData['December'][0] : 'December'; ?>',
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
</script>
