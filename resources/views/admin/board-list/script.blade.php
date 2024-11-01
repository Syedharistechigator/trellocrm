<script>

    $(document).ready(function () {
        var loading_div = $('.loading_div');
        /** Create form*/
        $('#board_list_form').on('submit', function (e) {
            e.preventDefault();
            loading_div.css('display','flex');
            AjaxRequestPost('{{ route('admin.board.list.store') }}', $(this).serialize(), 'Board List created successfully!', true, '{{ route('admin.board.list.index') }}');
            loading_div.css('display','none');
        });
        /** Update form*/
        $('#board_list_update_form').on('submit', function (e) {
            e.preventDefault();
            var id = $('#hdn').val();
            var url = '{{ route('admin.board.list.update','/')}}/' + id;
            AjaxRequestPost(url, $(this).serialize(), 'Board List updated successfully!');

        });
        /** Change Status */
        if ($('#BoardListTable').length || $('#board_list_update_form').length) {
            $(document).on("change", ".change-status", function () {
                var status = $(this).prop('checked') === true ? 1 : 0;
                var id = $(this).data('id');
                AjaxRequestPost('{{ route('admin.board.list.change.status') }}', {
                    'status': status,
                    'board_list_id': id
                }, 'Status changed successfully!', false);
            });
        }

        /** Delete*/
        $("#BoardListTable").on("click", ".delButton", function () {
            swal({
                title: "Are you sure?",
                text: "Once deleted, not be able to recover this Board List!",
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
                        var id = $(this).data("id");
                        var url = '{{ route('admin.board.list.destroy','/')}}/' + id;
                        AjaxRequestGet(url, null, 'Poof! Your Board List has been deleted!', false, '{{ route('admin.board.list.index')}}');
                    } else {
                        swal("Your Board List is safe!", {icon: "error", buttons: false, timer: 1000});
                    }
                });
        });

        /** Trashed Blade*/
        $("#BoardListTrashedTable").on("click", ".restoreButton", function () {
            var restoreId = $(this).data("id");
            var url = '{{ route('admin.board.list.restore','/')}}/' + restoreId;
            AjaxRequestGet(url, null, 'Board List Successfully restored!', false, '{{ route('admin.board.list.trashed')}}');
        });

        /**Restore All Board List*/
        $("#restoreAll").on("click", function () {
            swal({
                title: "Are you sure?",
                text: "want to Restore All Board Lists !",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        AjaxRequestGet('{{ route('admin.board.list.restore.all')}}', null, 'Restore all Board Lists successfully!', false, '{{ route('admin.board.list.trashed')}}');
                    } else {
                        swal("Your Board Lists not restore!");
                        console.log('error');
                    }
                });
        });
        /** Restore Board List */
        $("#BoardListTrashedTable").on("click", ".board-list-force-del", function () {
            swal({
                title: "Are you sure?",
                text: "want to permanently delete !",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var delId = $(this).data("id");
                        var url = '{{ route('admin.board.list.force.delete','/')}}/' + delId;
                        AjaxRequestGet(url, {'board_list_id': delId}, 'Board List permanent deleted successfully!', false, '{{ route('admin.board.list.trashed')}}');
                    } else {
                        swal("Your Board List not Delete!");
                        console.log('error');
                    }
                });
        });

    });

    // Restore card
    $("#BoardListTrashedTable").on("click", ".restoreButton", function () {

        var restoreId = $(this).data("id");
        console.log(restoreId);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "Get",
            url: '{{ route('admin.board.list.restore','/')}}/' + restoreId,
            success: function (data) {
                console.log("tet");
                swal("Good job!", "Record successfully Restore!", "success");
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            },
            error: function (data) {
                console.log('Error:', data);
                swal("Error!", "Request Fail!", "error");
            }
        });
    });


    // Restore card
    $("#BoardListTrashedTable").on("click", ".board-list-force-del", function () {

        swal({
            title: "Are you sure?",
            text: "want to permanently delete !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    var delId = $(this).data("id");
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        type: "Get",
                        url: '{{ route('admin.board.list.force.delete','/')}}/' + delId,
                        success: function (data) {
                            swal("Good job! Record deleted successfully!", {
                                icon: "success",
                            });
                            setTimeout(function () {
                                window.location.reload();
                            }, 2000);
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                } else {
                    swal("Your card not Delete!");
                    console.log('error');
                }
            });
    });
</script>
