<script>
    /** => Developer Michael Update <= **/
    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    let configMonths = {!! json_encode(config('app.months')) !!};

    $('[type=search],.bs-searchbox input[type=text]').each(function () {
        var randomNumber = getRandomInt(111111111, 999999999);
        $(this).attr('id', "dt-search-box-" + randomNumber);
    });

    function getParam() {
        let params = [];
        let teamKey = $('#search-team').val();
        if (teamKey && teamKey > 0) {
            params.push('teamKey=' + encodeURIComponent(teamKey));
        }

        let month = $('#search-month').val();
        if (month && configMonths.includes(month)) {
            params.push('month=' + encodeURIComponent(month));
        }
        let year = $('#search-year').val();
        if (year && year >= 2021 && year <= 2030 && !isNaN(year)) {
            params.push('year=' + encodeURIComponent(year));
        }
        window.location.href = params.length > 0 ? "{{ route('admin.team.carry-forward.index') }}?" + params.join('&') : "{{ route('admin.team.carry-forward.index') }}";
    }


    $(document).ready(function () {
        $('#search-team, #search-month, #search-year').on('change', getParam);

        var loading_div = $('.loading_div')
        var TableId = $('table').first().attr('id');
        if (TableId) {
            if (TableId === "CarryForwardTable") {
                $('#CarryForwardTable').DataTable().destroy();
                $('#CarryForwardTable').DataTable({
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


        $('#create-carry-forward-modal-btn, .editCarryForward').click(function () {
            $('#search-team, #search-month, #search-year ,[type=search],select[name="CarryForwardTable_length"]').prop('disabled', true);
        });
        $('#createCarryForwardModal, #editCarryForwardModal').on('hidden.bs.modal', function () {
            $('#search-team, #search-month, #search-year ,[type=search],select[name="CarryForwardTable_length"]').prop('disabled', false);
        });
        $('#create-carry-forward-modal-btn').on('click', function () {
            $('#create_form')[0].reset();
            $('#team_key').val('').selectpicker('refresh');
            $('#month').val(new Date().getMonth() + 1).selectpicker('refresh');
            $('#year').val(new Date().getFullYear()).selectpicker('refresh');
        });

        function createRowHtml(data) {
            return `<tr id="tr-${data.id}">
                <td class="align-middle">${data.id}</td>
                <td class="align-middle">${data.get_team ? data.get_team.name : ""}</td>
                <td class="align-middle">${data.amount ? "$" + parseFloat(data.amount).toFixed(2) : ""}</td>
                <td class="align-middle">${convertValue(data.month, options.month)}</td>
                <td class="align-middle">${data.year}</td>
                <td class="align-middle text-nowrap">
                    <button data-id="${data.id}" title="Edit" class="btn btn-warning btn-sm btn-round editCarryForward" data-toggle="modal" data-target="#editCarryForwardModal">
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
                data.get_team ? data.get_team.name : "",
                data.amount ? "$" + parseFloat(data.amount).toFixed(2) : "",
                convertValue(data.month, options.month),
                data.year,
                `
            <button data-id="${data.id}" title="Edit" class="btn btn-warning btn-sm btn-round editCarryForward" data-toggle="modal" data-target="#editCarryForwardModal">
                <i class="zmdi zmdi-edit"></i>
            </button>
            <a title="Delete" data-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton">
                <i class="zmdi zmdi-delete"></i>
            </a>
        `
            ];
        }

        function convertValue(value, options) {
            return options[value] || 'None';
        }

        const options = {
            month: {
                1: 'January',
                2: 'February',
                3: 'March',
                4: 'April',
                5: 'May',
                6: 'June',
                7: 'July',
                8: 'August',
                9: 'September',
                10: 'October',
                11: 'November',
                12: 'December',
            },
        };

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
            let url = $(this).attr('id') === "update_form" ? '{{ route('admin.team.carry-forward.update','/')}}/' + $('#editCarryForwardModal').attr('data-id') : ($(this).attr('id') === "create_form" ? '{{ route('admin.team.carry-forward.store') }}' : null);
            if (url === null || ($(this).attr('id') === "update_form" && $('#editCarryForwardModal').attr('data-id') === '')) {
                return false;
            }
            AjaxRequestPostPromise(url, new FormData(this), null, false, null, false, true, true).then((res) => {
                if (res.success && res.status && res.status === 1) {

                    let table = $('#CarryForwardTable').DataTable();
                    if (table) {
                        if ($(this).attr('id') === "create_form") {
                            table.row.add($(createRowHtml(res.data))).invalidate().draw(false)
                            /** For change specific value */
                            $('#team_key').val('').selectpicker('refresh');
                            $('#month').val('').selectpicker('refresh');
                            $('#year').val('').selectpicker('refresh');
                        } else if ($(this).attr('id') === "update_form") {
                            if (res.data) {
                                let rowData = createRowData(res.data);
                                let rowIndex = table.row('#tr-' + res.data.id).index();
                                table.row(rowIndex).data(rowData).draw(false);
                                $('#edit_team_key').val(res.data.team_key ? res.data.team_key : "").selectpicker('refresh');
                                $('#edit_amount').val(res.data.amount ? res.data.amount : "");
                                $('#edit_month').val(res.data.month ? res.data.month : "").selectpicker('refresh');
                                $('#edit_year').val(res.data.year ? res.data.year : "").selectpicker('refresh');
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
                if (error.status == 422 && error.responseJSON.errors && error.responseJSON.errors.year && error.responseJSON.errors.year[0] === "Carry Forward for this team already exists for the specified month and year.") {
                    var table = $('#CarryForwardTable').DataTable();
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
        $(document).on('click', '.editCarryForward', function (e) {
            e.preventDefault();
            $('#update_form')[0].reset();
            $('#edit_team_key').val('').selectpicker('refresh');
            $('#edit_month').val('').selectpicker('refresh');
            $('#edit_year').val('').selectpicker('refresh');

            let url = '{{ route('admin.team.carry-forward.edit','/')}}/' + $(this).attr('data-id');

            AjaxRequestGetPromise(url, null, null, false, '{{route("admin.team.carry-forward.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    $("#editCarryForwardModal").attr('data-id', res.data.id);
                    $('#edit_team_key').val(res.data.team_key ? res.data.team_key : "").selectpicker('refresh');
                    $('#edit_amount').val(res.data.amount ? res.data.amount : "");
                    $('#edit_month').val(res.data.month ? res.data.month : "").selectpicker('refresh');
                    $('#edit_year').val(res.data.year ? res.data.year : "").selectpicker('refresh');
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        /** Delete*/
        $("#CarryForwardTable").on("click", ".delButton", function () {
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
                        var url = '{{ route('admin.team.carry-forward.destroy','/')}}/' + id;

                        var res = AjaxRequestGet(url, null, 'Poof! Your record has been deleted!', false, null);
                        if (res && res.success) {
                            $('#CarryForwardTable').DataTable().row($("#tr-" + id)).remove().draw(false);
                        }
                    } else {
                        swal("Your record is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });
    })
    ;
</script>