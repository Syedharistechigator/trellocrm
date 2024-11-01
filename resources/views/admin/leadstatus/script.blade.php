<script>
    //var placementFrom = 'top';
    //var placementAlign = 'center';
    //var animateEnter = "";
    //var animateExit = "";
    //var colorName = 'alert-success';
    //var notifText = 'Status change successfully.';
    //showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);

    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    $('[type=search],.bs-searchbox input[type=text]').each(function () {
        var randomNumber = getRandomInt(111111111, 999999999);
        $(this).attr('id', "dt-search-box-" + randomNumber);
    });
    $(document).ready(function () {
        var loading_div = $('.loading_div')
        var TableId = $('table').first().attr('id');
        if (TableId) {
            if (TableId === "StatusTable") {
                $('#StatusTable').DataTable().destroy();
                $('#StatusTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    order: [[0, 'desc']],
                    scrollX: false,
                });
                $('[type=search],.bs-searchbox input[type=text]').each(function () {
                    var randomNumber = getRandomInt(111111111, 999999999);
                    $(this).attr('id', "dt-search-box-" + randomNumber);
                });
            }
        }

        $('#create-lead-status-modal-btn, .editStatus').click(function () {
            $('[type=search]').prop('disabled', true);
        });
        $('#create-lead-status-modal-btn, .editStatus').on('hidden.bs.modal', function () {
            $('[type=search]').prop('disabled', false);
        });

        $('#create-lead-status-modal-btn').on('click', function () {
            $('#lead-status-Form')[0].reset();
            $('#create_status_color').val('').selectpicker('refresh');
        });

        //Create Lead Status
        $('#lead-status-Form').on('submit', function (e) {
            e.preventDefault();

            AjaxRequestPostPromise(`{{ route('leadstatus.store') }}`, new FormData(this), null, false, `{{route('leadstatus.index')}}`, true, true, false).then((res) => {
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });

        // Delete Status
        $("#StatusTable").on("click", ".statusDelButton", function () {

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
                            url: "leadstatus/" + cid,
                            success: function (data) {
                                swal("Poof! Your team has been deleted!", {
                                    icon: "success",
                                });
                                setInterval('location.reload()', 2000);        // Using .reload() method.
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

        /** On Click Edit Show Edit Details */
        $("#StatusTable").on("click", ".editStatus", function (e) {
            e.preventDefault();
            $('#Update-lead-status-Form')[0].reset();
            let url = `{{ route('leadstatus.edit', ':leadstatus') }}`.replace(':leadstatus', $(this).attr('data-id'));
            AjaxRequestGetPromise(url, null, null, false, '{{route("leadstatus.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    $("#updateStatusModal").attr('data-id', res.data.id);
                    $('#edit_status').val(res.data.status);
                    $('#edit_status_color').selectpicker('val', res.data.leadstatus_color);
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        //Update Lead Status
        $('#Update-lead-status-Form').on('submit', function (e) {
            e.preventDefault();
            let url = '{{ route('admin.lead_status.update','/')}}/' + $('#updateStatusModal').attr('data-id');
            AjaxRequestPostPromise(url, new FormData(this), null, false, `{{route('leadstatus.index')}}`, true, true, false).then((res) => {
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });
    });
</script>
