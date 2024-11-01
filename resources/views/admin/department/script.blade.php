<script>
    /** => Developer Michael Update <= **/
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
            if (TableId === "DepartmentTable") {
                $('#DepartmentTable').DataTable().destroy();
                $('#DepartmentTable').DataTable({
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


        $('#create-department-modal-btn, .editDepartment').click(function () {
            $('#search-team, #search-month, #search-year ,[type=search],select[name="DepartmentTable_length"]').prop('disabled', true);
        });
        $('#createDepartmentModal, #editDepartmentModal').on('hidden.bs.modal', function () {
            $('#search-team, #search-month, #search-year ,[type=search],select[name="DepartmentTable_length"]').prop('disabled', false);
        });
        $('#create-department-modal-btn').on('click', function () {
            $('#create_form')[0].reset();
            $('#status').val(1).selectpicker('refresh');
        });

        function createRowHtml(data) {
            return `<tr id="tr-${data.id}">
                <td class="align-middle">${data.id}</td>
                <td class="align-middle">${data.name}</td>
                <td class="align-middle">${data.order}</td>
                <td class="align-middle">
                    <div class="custom-control custom-switch">
                        <input data-id="${data.id}" type="checkbox"
                               class="custom-control-input change-status"
                               id="customSwitch${data.id}" ${data.status && data.status == 0 ? '' : 'checked'}>
                        <label class="custom-control-label" for="customSwitch${data.id}"></label>
                    </div>
                </td>
                <td class="align-middle text-nowrap">
                    <button data-id="${data.id}" title="Edit" class="btn btn-warning btn-sm btn-round editDepartment" data-toggle="modal" data-target="#editDepartmentModal">
                        <i class="zmdi zmdi-edit"></i>
                    </button>
                    <a title="Delete" data-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton">
                        <i class="zmdi zmdi-delete"></i>
                    </a>
                </td>
            </tr>`;
        }

        function createRowData(data) {
            return [
                data.id,
                data.name,
                data.order,
                `
                    <div class="custom-control custom-switch">
                        <input data-id="${data.id}" type="checkbox"
                               class="custom-control-input change-status"
                               id="customSwitch${data.id}" ${data.status && data.status == 0 ? '' : 'checked'}>
                        <label class="custom-control-label" for="customSwitch${data.id}"></label>
                    </div>
                `,
                `
                    <button data-id="${data.id}" title="Edit" class="btn btn-warning btn-sm btn-round editDepartment" data-toggle="modal" data-target="#editDepartmentModal">
                        <i class="zmdi zmdi-edit"></i>
                    </button>
                    <a title="Delete" data-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton">
                        <i class="zmdi zmdi-delete"></i>
                    </a>
                `
            ];
        }

        function highlightRow(rowId, opacity = 0.6) {
            if (opacity >= 0) {
                setTimeout(() => {
                    $(`#${rowId}`).css('background-color', `rgba(0, 200, 220, ${opacity})`);
                    highlightRow(rowId, opacity - 0.05);
                }, 100);
            } else {
                $(`#${rowId}`).removeClass('highlight');
            }
        }

        /** Record Create/Update Function */
        $('form').on('submit', function (e) {
            e.preventDefault();
            let url = $(this).attr('id') === "update_form" ? '{{ route('admin.department.update','/')}}/' + $('#editDepartmentModal').attr('data-id') : ($(this).attr('id') === "create_form" ? '{{ route('admin.department.store') }}' : null);
            if (url === null || ($(this).attr('id') === "update_form" && $('#editDepartmentModal').attr('data-id') === '')) {
                return false;
            }

            AjaxRequestPostPromise(url, new FormData(this), null, false, null, false, true, true).then((res) => {
                if (res.success && res.status && res.status === 1) {
                    if (res.order_change) {
                        window.location.reload();
                    }
                    let table = $('#DepartmentTable').DataTable();
                    if (table) {
                        if ($(this).attr('id') === "create_form") {
                            table.row.add($(createRowHtml(res.data))).invalidate().draw(false)
                            $('#status').val(1).selectpicker('refresh');
                        } else if ($(this).attr('id') === "update_form") {
                            if (res.data) {
                                let rowData = createRowData(res.data);
                                let rowIndex = table.row('#tr-' + res.data.id).index();
                                table.row(rowIndex).data(rowData).draw(false);
                                $('#edit_name').val(res.data.name ? res.data.name : "");
                                $('#edit_order').val(res.data.order ? res.data.order : "");
                                if (typeof res.data.status !== 'undefined') {
                                    $('#edit_status').val(res.data.status).selectpicker('refresh');
                                } else {
                                    $('#edit_status').val(0).selectpicker('refresh');
                                }
                            } else {
                                createToast('error', 'Failed to submitted record');
                            }
                        }
                        highlightRow('tr-' + res.data.id);
                    }
                } else {
                    createToast('error', 'Failed to submitted record');
                }
            }).catch((error) => {
                console.log(error);
                if (error.status == 422 && error.responseJSON.errors && error.responseJSON.errors.year && error.responseJSON.errors.year[0] === "Department for this team already exists for the specified month and year.") {
                    var table = $('#DepartmentTable').DataTable();
                    var conflictingId = error.responseJSON.data.id;
                    var rowIndex = table.row("#tr-" + conflictingId).index();
                    var pageLength = table.page.len();
                    var targetPage = Math.floor(rowIndex / pageLength);

                    table.page(targetPage).draw(false)
                    highlightRow(`tr-${conflictingId}`);
                }
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });

        /** On Click Edit Show Edit Details */
        $(document).on('click', '.editDepartment', function (e) {
            e.preventDefault();
            $('#update_form')[0].reset();
            $('#update_form').find('input, textarea, button').prop('disabled', true);
            $('#edit_status').val(1).selectpicker('refresh');
            let url = '{{ route('admin.department.edit','/')}}/' + $(this).attr('data-id');

            AjaxRequestGetPromise(url, null, null, false, '{{route("admin.department.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    $("#editDepartmentModal").attr('data-id', res.data.id);
                    $('#edit_name').val(res.data.name ? res.data.name : "");
                    $('#edit_order').val(res.data.order || res.data.order == 0 ? res.data.order : "");
                    if (typeof res.data.status !== 'undefined') {
                        $('#edit_status').val(res.data.status).selectpicker('refresh');
                    } else {
                        $('#edit_status').val(0).selectpicker('refresh');
                    }
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
                $('#update_form').find('input, textarea, button').prop('disabled', false);
            })
        });

        /** Delete*/
        $("#DepartmentTable").on("click", ".delButton", function () {
            swal({
                title: "Are you sure?",
                text: "Once deleted, not be able to recover this Record!",
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
                        var url = '{{ route('admin.department.destroy','/')}}/' + id;
                            AjaxRequestGetPromise(url, null, 'Poof! Your record has been deleted!', false, null, false, true, true).then((res) => {
                            if (res && res.success) {
                                $('#DepartmentTable').DataTable().row($("#tr-" + id)).remove().draw(false);
                            }
                        }).catch((error) => {
                            console.log(error);
                        }).finally(() => {
                            loading_div.css('display', 'none');
                        })
                    } else {
                        swal("Your record is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });

        /** Change Status */
        if ($('#DepartmentTable').length || $('#update_form').length) {
            $(document).on("change", ".change-status", function () {
                var formData = new FormData();
                formData.append("id", $(this).data('id'))
                formData.append("status", $(this).prop('checked') === true ? 1 : 0)
                AjaxRequestPostPromise('{{ route('admin.department.change.status') }}', formData, 'Status changed successfully!', false, null, false, true, true, false, 'success');
            });
        }
    });
</script>
