<script>


    {{--// search agent payment--}}
    {{--$('#team').on('change', function () {--}}
    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    var teamKey = $(this).val();--}}
    {{--    console.log(teamKey);--}}

    {{--    $.ajax({--}}
    {{--        url: "{{ route('teamleads') }}",--}}
    {{--        method: 'POST',--}}
    {{--        data: {search: teamKey},--}}
    {{--        success: function (result) {--}}
    {{--            console.log(result);--}}
    {{--            $("#LeadTable").html(result);--}}
    {{--            $('#LeadTable').DataTable({--}}
    {{--                "destroy": true, //use for reinitialize datatable--}}
    {{--            });--}}
    {{--        }--}}
    {{--    });--}}

    {{--});--}}


    {{--// search brand payment--}}
    {{--$('#brand').on('change', function () {--}}

    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    var teamKey = $(this).val();--}}

    {{--    console.log(teamKey);--}}

    {{--    $.ajax({--}}
    {{--        url: "{{ route('brandleads') }}",--}}
    {{--        method: 'POST',--}}
    {{--        data: {search: teamKey},--}}
    {{--        success: function (result) {--}}
    {{--            console.log(result);--}}
    {{--            $("#LeadTable").html(result);--}}
    {{--            $('#LeadTable').DataTable({--}}
    {{--                "destroy": true, //use for reinitialize datatable--}}
    {{--            });--}}
    {{--        }--}}
    {{--    });--}}

    {{--});--}}
    $(document).ready(function() {
        function getParam(){
            window.location.href = "{{ route('admin.leads.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }
        $('#team, #brand').on('change',getParam);
    });
    // search monthly payment - not use this function
    // $('#month-data').on('change', function() {
    //   console.log('cxm');

    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });

    //     var month = $(this).val();
    //     console.log('Selected Date: '+month);
    //     $.ajax({
    //         url: "{{ route('monthlydata') }}",
    //         method:'POST',
    //         data:{search :month},
    //         success: function(result){
    //             console.log('result');
    //             $("#LeadTable").html(result);
    //             $('#LeadTable').DataTable({
    //                       "destroy": true, //use for reinitialize datatable
    //             });
    //         }
    //     });
    // });


    $("#LeadTable").on("click", ".statusChange", function () {
        console.log('test');
        var lead_id = $(this).data('id');
        document.getElementById("status_hdn").value = lead_id;
        console.log(lead_id);
    });


    //Assing Brands to Team
    $('#changeStatusBtn').on('click', function (e) {
        e.preventDefault();

        var lead_id = $('#status_hdn').val();
        var LeadStatus = $('#lead-status').val();

        if (LeadStatus != '') {
            console.log(LeadStatus);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('changeleadStatus') }}",
                data: {'lead_id': lead_id, 'LeadStatus': LeadStatus},
                success: function (data) {
                    swal("Good job!", "Status change successfully!", "success");
                    console.log(data);
                    setInterval('location.reload()', 2000);        // Using .reload() method.
                },
                error: function (data) {
                    console.log(data);
                    swal("aaaaError!", "Request Fail!", "error");
                }
            });
        } else {
            swal("Error!", "Select Brand", "error");
        }
    });

    // Delete Lead
    $("#LeadTable").on("click", ".delButton", function () {
        swal({
            title: "Are you sure?",
            text: "Once deleted, not be able to recover this Lead!",
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
                        url: "lead/" + cid,
                        success: function (data) {
                            swal("Poof! Your Lead has been deleted!", {
                                icon: "success",
                            });
                            setInterval('location.reload()', 2000);        // Using .reload() method.
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your brand is safe!", {buttons: false, timer: 1000});
                }
            });
    });


{{--    //data range filter--}}
{{--    $('#data-range').on('apply.daterangepicker', function (ev, picker) {--}}
{{--//   console.log(picker.startDate.format('YYYY-MM-DD'));--}}
{{--//   console.log(picker.endDate.format('YYYY-MM-DD'));--}}

{{--        setTimeout(function () {--}}
{{--            $('.page-loader-wrapper').show();--}}
{{--        }, 1000);--}}
{{--        setTimeout(function () {--}}
{{--            $('.page-loader-wrapper').hide();--}}
{{--        }, 10000);--}}
{{--        $.ajaxSetup({--}}
{{--            headers: {--}}
{{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--            }--}}
{{--        });--}}

{{--        $.ajax({--}}
{{--            url: "{{ route('monthlydata') }}",--}}
{{--            method: 'POST',--}}
{{--            data: {startDate: picker.startDate.format('YYYY-MM-DD'), endDate: picker.endDate.format('YYYY-MM-DD')},--}}
{{--            success: function (result) {--}}
{{--                console.log('result');--}}
{{--                $("#LeadTable").html(result);--}}
{{--                $('#LeadTable').DataTable({--}}
{{--                    "destroy": true, //use for reinitialize datatable--}}
{{--                });--}}
{{--                $('.page-loader-wrapper').hide();--}}
{{--            }--}}
{{--        });--}}

{{--    });--}}

    $("#LeadTable").on("click", ".leadRestoreButton", function () {
        swal({
            title: "Are you sure?",

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
                    text: "Yes, Restore!"
                }
            },
            dangerMode: true,
        })
            .then((willRestore) => {
                if (willRestore) {
                    var lid = $(this).data("id");

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "put",
                        url: "leadRestore/" + lid,
                        success: function (data) {
                            swal("Poof! Your Lead has been Restored!", {
                                icon: "success",
                            });
                            setInterval('location.reload()', 2000);        // Using .reload() method.
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your Lead is safe!", {buttons: false, timer: 1000});
                }
            });
    });

    $("#LeadTable").on("click", ".leadDelButton", function () {
        swal({
            title: "Are you a Super Admin?",
            text: "Once deleted, not be able to recover this Lead!",
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
                    var lid = $(this).data("id");
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "DELETE",
                        url: "leadforceDelete/" + lid,
                        success: function (data) {
                            swal("Poof! Your Lead has been deleted!", {
                                icon: "success",
                            });
                            setInterval('location.reload()', 2000);        // Using .reload() method.
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your lead is safe!", {buttons: false, timer: 1000});
                }
            });
    });


    //Get Lead Comments
    $("#LeadTable").on("click", ".LeadComments", function () {
        console.log('test');
        var lead_id = $(this).data('id');
        document.getElementById("leadId").value = lead_id;

        console.log(lead_id);

        $.ajax({
            type: "GET",
            datatype: 'JSON',
            url: "/leadcomments/" + lead_id,
            success: function (data) {
                console.log(data);
                console.log('{{Auth::guard('admin')->user()->id}}');
                let LoginUserId = '{{Auth::guard('admin')->user()->id}}';


                $("#lead_comments_data").empty();
                if (data.length < 1) {
                    $("#lead_comments_data").append('<div class="alert alert-warning mx-3">Data not found.</div>');
                }
                $.each(data, function (i, v) {
                    let activeClass = 'text-right';
                    if (LoginUserId == v.creatorid) {
                        activeClass = 'text-left';
                    }

                    let colorClass = '';
                    if (v.type == 'admin') {
                        colorClass = 'bg-blue';
                    } else if (v.type == 'lead') {
                        colorClass = 'bg-indigo';
                    } else if (v.type == 'ppc') {
                        colorClass = 'bg-greensss';
                    } else if (v.type == 'qa') {
                        colorClass = 'bg-lime';
                    } else {
                        colorClass = 'bg-amber';
                    }


                    console.log(i + '|' + v.id);

                    $("#lead_comments_data").append('<div class="' + activeClass + '"><div class="mb-0"><span class="badge ' + colorClass + ' rounded-0 mb-0" style="text-transform: capitalize"><i class="zmdi zmdi-account"></i> ' + v.userName + ' - ' + v.type + '</span><span class="badge ' + colorClass + ' rounded-0 mb-0"><i class="zmdi zmdi-calendar"></i> ' + v.commentDate + '</span></div><div class="p-2 pb-0 mb-2">' + v.comment_text + '</div></div>');
                });

            }
        });
    });

    // Create Lead Comments

    $('#lead_comments_form').on('submit', function (e) {
        e.preventDefault();

        console.log('Comments Form');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('adminCreateComments') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                console.log(data.leadid);
                $("#lead_comments_form")[0].reset();
                //swal("Good job!", "Invoice successfully Paid!", "success");
                $.ajax({
                    type: "GET",
                    datatype: 'JSON',
                    url: "/leadcomments/" + data.leadid,
                    success: function (data) {
                        let LoginUserId = '{{Auth::guard('admin')->user()->id}}';
                        $("#lead_comments_data").empty();
                        if (data.length < 1) {
                            $("#lead_comments_data").append('<div class="alert alert-warning">Data not found.</div>');
                        }
                        $.each(data, function (i, v) {
                            let activeClass = 'text-right';
                            if (LoginUserId == v.creatorid) {
                                activeClass = 'text-left';
                            }

                            let colorClass = '';
                            if (v.type == 'admin') {
                                colorClass = 'bg-blue';
                            } else if (v.type == 'lead') {
                                colorClass = 'bg-indigo';
                            } else if (v.type == 'ppc') {
                                colorClass = 'bg-green';
                            } else if (v.type == 'qa') {
                                colorClass = 'bg-lime';
                            } else {
                                colorClass = 'bg-amber';
                            }

                            console.log(i + '|' + v.id);

                            $("#lead_comments_data").append('<div class="' + activeClass + '"><div class="mb-0"><span class="badge ' + colorClass + ' rounded-0 mb-0"><i class="zmdi zmdi-account"></i> ' + v.userName + ' - ' + v.type + '</span><span class="badge ' + colorClass + ' rounded-0 mb-0"><i class="zmdi zmdi-calendar"></i> ' + v.commentDate + '</span></div><div class="p-2 pb-0 mb-2">' + v.comment_text + '</div></div>');
                        });
                    }
                });
            },
            error: function () {
                swal("Errors!", "Request Fail!", "error");
            }
        });


    });


</script>
