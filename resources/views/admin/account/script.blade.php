<script>
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
            if (TableId === "AccountTable") {
                $('#AccountTable').DataTable().destroy();
                $('#AccountTable').DataTable({
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

        $('#create-account-modal-btn, .editAccount').click(function () {
            $('[type=search]').prop('disabled', true);
        });
        $('#create-account-modal-btn, .editAccount').on('hidden.bs.modal', function () {
            $('[type=search]').prop('disabled', false);
        });

        $('#create-account-modal-btn').on('click', function () {
            $('#create-account')[0].reset();
        });
        /**Create Account*/
        $('#create-account').on('submit', function (e) {
            e.preventDefault();
            AjaxRequestPostPromise(`{{ route('account.store') }}`, new FormData(this), null, false, `{{route('account.index')}}`, true, true, false).then((res) => {
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });

        /** Edit Account */
        $("#AccountTable").on("click", ".editAccount", function (e) {
            e.preventDefault();
            $('#account_update_form')[0].reset();
            let url = `{{ route('account.edit', ':id') }}`.replace(':id', $(this).attr('data-id'));
            AjaxRequestGetPromise(url, null, null, false, '{{route("account.index")}}', false, true, false).then((res) => {
                if (res.success) {
                    $("#EditModal").attr('data-id', res.data.id);
                    $('#edit_name').val(res.data.name);
                    $('#edit_email').val(res.data.email);
                    $('#edit_password').val(res.data.password);
                    $('#edit_designation').val(res.data.designation);
                    $('#edit_pseudo_email').val(res.data.pseudo_email);
                    $('#edit_phone').val(res.data.phone);
                    $("#status").val(res.data.status);
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
        $('#account_update_form').on('submit', function (e) {
            e.preventDefault();
            let url = '{{ route('admin.account.update','/')}}/' + $('#EditModal').attr('data-id');
            AjaxRequestPostPromise(url, new FormData(this), null, false, `{{route('account.index')}}`, true, true, false).then((res) => {
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });

        $("#AccountTable").on("click", ".adminDelButton", function () {
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover Account !",
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
                            url: "account/" + cid,
                            success: function (data) {
                                swal("Poof! Your Account   has been deleted!", {
                                    icon: "success",
                                });
                                setInterval('location.reload()', 2000);        // Using .reload() method.
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });
                    } else {
                        swal("Your Account is safe!", {buttons: false, timer: 1000});
                    }
                });
        });
    });
</script>
