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
        $('#ThirdPartyRoleTable').DataTable().destroy();
        $('#ThirdPartyRoleTable').DataTable({
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
        $('#create-third-party-role-modal-btn, .editThirdPartyRole').click(function () {
            $('[type=search],select[name="ThirdPartyRoleTable_length"]').prop('disabled', true);
        });
        $('#createThirdPartyRoleModal, #editThirdPartyRoleModal').on('hidden.bs.modal', function () {
            $('[type=search],select[name="ThirdPartyRoleTable_length"]').prop('disabled', false);
        });
        $('#create-third-party-role-modal-btn').on('click', function () {
            $('#create_form')[0].reset();
            $('#team_key').val('').selectpicker('refresh');
            $('#agent_id').val('').selectpicker('refresh');
            $('#client_id').val('').selectpicker('refresh');
            $('#invoice_id').val('').selectpicker('refresh');
            $('#merchant_type').val('4').selectpicker('refresh');
            $('#payment_status').val('0').selectpicker('refresh');
            $('#order_status').val('Order Placed').selectpicker('refresh');
        });

        function createRowHtml(data) {
            let invoice_td = ''
            if (data.invoice_id) {
                invoice_td = `
                            <a class="text-warning invoice-trigger" data-invoice-num="${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}" href="#${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}">${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}</a>
                            <div class="">
                                <span class="badge badge-info rounded-pill">${data.invoice_id}</span>
                            </div>`;
            }
            return `<tr id="tr-${data.id}">
                        <td class="align-middle">${data.id}</td>
                        <td class="align-middle">
                            ${invoice_td}
                        </td>
                        <td class="align-middle">${data.get_team && data.get_team.name ? data.get_team.name : ""}</td>
                        <td class="align-middle">${data.get_client && data.get_client.name ? data.get_client.name : ""}</td>
                        <td class="align-middle">${data.order_id}</td>
                        <td class="align-middle">${convertValue(data.order_status, options.order_status)}</td>
                        <td class="align-middle td-make-desc-short" title="${data.description ? data.description : ""}">${data.description ? data.description.substring(0, 20) + (data.description.length > 20 ? '...' : '') : ""}</td>
                        <td class="align-middle">${data.amount ? '$' + parseFloat(data.amount).toFixed(2) : ""}</td>
                        <td class="align-middle">${data.formatted_created_at ?? ''}</td>
                        <td class="align-middle">${convertValue(data.merchant_type, options.merchant_type)}</td>
                        <td class="align-middle">${convertValue(data.payment_status, options.payment_status)}</td>
                        <td class="align-middle text-nowrap">
                            <button data-id="${data.id}" title="Edit" class="btn btn-info btn-sm btn-round editThirdPartyRole" data-toggle="modal" data-target="#editThirdPartyRoleModal">
                                                    <i class="zmdi zmdi-edit"></i>
                            </button>
                        </td>
                    </tr>`;
        }

        function createRowData(data) {
            let invoice_td = ''
            if (data.invoice_id) {
                invoice_td = `<a class="text-warning invoice-trigger" data-invoice-num="${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}" href="#${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}">${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}</a>
                    <div class="">
                        <span class="badge badge-info rounded-pill">${data.invoice_id}</span>
                    </div>`;
            }
            return [
                data.id,
                invoice_td,
                data.get_team && data.get_team.name ? data.get_team.name : "",
                data.get_client && data.get_client.name ? data.get_client.name : "",
                data.order_id ? data.order_id : "",
                convertValue(data.order_status, options.order_status),
                `<td class="align-middle td-make-desc-short" title="${data.description ? data.description : ""}">${data.description ? data.description.substring(0, 20) + (data.description.length > 20 ? '...' : '') : ""}</td>`,
                `${data.amount ? '$' + parseFloat(data.amount).toFixed(2) : ""}`,
                data.formatted_created_at ?? '',
                convertValue(data.merchant_type, options.merchant_type),
                convertValue(data.payment_status, options.payment_status),
                `
                    <button data-id="${data.id}" title="Edit" class="btn btn-info btn-sm btn-round editThirdPartyRole" data-toggle="modal" data-target="#editThirdPartyRoleModal">
                        <i class="zmdi zmdi-edit"></i>
                    </button>
                `
            ];
        }

        function convertValue(value, options) {
            return options[value] || 'None';
        }

        const options = {
            merchant_type: {1: 'Authorize', 2: 'Expigate', 3: 'PayArc', 4: 'Paypal', 21: 'Master Card 0079'},
            payment_status: {0: 'Pending', 1: 'In Review', 2: 'Completed'},
            order_status: {
                'Order Placed': 'Order Placed',
                'Shipped': 'Shipped',
                'Delivered': 'Delivered',
                'On Hold': 'On Hold'
            }
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
            let url = $(this).attr('id') === "update_form" ? '{{ route('user.third.party.role.update','/')}}/' + $('#editThirdPartyRoleModal').attr('data-id') : ($(this).attr('id') === "create_form" ? '{{ route('user.third.party.role.store') }}' : null);
            if (url === null || ($(this).attr('id') === "update_form" && $('#editThirdPartyRoleModal').attr('data-id') === '')) {
                return false;
            }
            AjaxRequestPostPromise(url, new FormData(this), null, false, null, false, true, true).then((res) => {
                if (res.success && res.status && res.status === 1) {

                    let table = $('#ThirdPartyRoleTable').DataTable();
                    if (table) {
                        if ($(this).attr('id') === "create_form") {
                            table.row.add($(createRowHtml(res.data))).invalidate().draw(false)
                            /** For change specific value */
                            // $('#team_key').val('').selectpicker('refresh').trigger('change', '');
                            $('#team_key').val('').selectpicker('refresh');
                            $('#agent_id').val('').selectpicker('refresh');
                            $('#client_id').val('').selectpicker('refresh');
                            $('#order_status').val('Order Placed').selectpicker('refresh');
                            $('#invoice_id').val('').selectpicker('refresh');
                            $('#merchant_type').val('4').selectpicker('refresh');
                            $('#payment_status').val('0').selectpicker('refresh');
                        } else if ($(this).attr('id') === "update_form") {
                            if (res.data) {
                                let rowData = createRowData(res.data);
                                let rowIndex = table.row('#tr-' + res.data.id).index();
                                table.row(rowIndex).data(rowData).draw(false);
                                $('#edit_team_key').val(res.data.team_key ? res.data.team_key : "").selectpicker('refresh');
                                $('#edit_agent_id').val(res.data.agent_id ? res.data.agent_id : "").selectpicker('refresh');
                                $('#edit_client_id').val(res.data.client_id ? res.data.client_id : "").selectpicker('refresh');
                                $('#edit_order_status').val(res.data.order_status ? res.data.order_status : "").selectpicker('refresh');
                                $('#edit_invoice_id').val(res.data.invoice_id ? res.data.invoice_id : "").selectpicker('refresh');
                                $('#edit_merchant_type').val(res.data.merchant_type ? res.data.merchant_type : "").selectpicker('refresh');
                                $('#edit_payment_status').val(res.data.payment_status ? res.data.payment_status : "").selectpicker('refresh');
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
            }).finally(() => {
                $(".modal").modal("hide")
                loading_div.css('display', 'none');
            })
        });

        function update_select(column, array, default_text) {
            column.empty().selectpicker('refresh').append(`<option class="" value="" disabled>${default_text}</option>`);
            array.forEach(function (item) {
                var value = ((column.attr('id') === 'invoice_id') || column.attr('id') === 'edit_invoice_id') ? item.invoice_key : item.id;
                column.append('<option value="' + value + '">' + item.data + '</option>');
            })
            column.val('').selectpicker('refresh');
            if (((column.attr('id') !== 'invoice_id') && column.attr('id') !== 'edit_invoice_id')) {
                column.val('').attr('required', true).prop('required', true);
            } else {
                column.val('').attr('required', false).prop('required', false);
            }
        }

        /** On Change Team Show Agent List And Clients & On Change Client Show Paid Invoice List */
        $('#team_key , #client_id').on('change', function () {
            let team_key = $('#team_key').val();
            let client_id = $('#client_id').val();
            let url = $(this).attr('id') === "team_key" ? '{{ route('user.third.party.role.team.agents.clients','/')}}/' + team_key : ($(this).attr('id') === "client_id" ? '{{ route('user.third.party.client.paid.invoices', '/') }}/' + team_key + '/' + client_id : null);
            $('#invoice_id').empty().selectpicker('refresh').append(`<option class="" value="" disabled>Select Invoice</option>`);

            AjaxRequestGetPromise(url, null, null, false, '{{route("user.third.party.role.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    if ($(this).attr('id') === 'team_key') {
                        update_select($('#agent_id'), res.users, 'Select Team for Agent List');
                        update_select($('#client_id'), res.clients, 'Select Client');
                    } else if ($(this).attr('id') === 'client_id') {
                        update_select($('#invoice_id'), res.invoices, 'Select Invoice');
                    }
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
        /** For Edit :  On Change Team Show Agent List And Clients & On Change Client Show Paid Invoice List */
        $('#edit_team_key , #edit_client_id').on('change', function () {
            let team_key = $('#edit_team_key').val();
            let client_id = $('#edit_client_id').val();
            let url = $(this).attr('id') === "edit_team_key" ? '{{ route('user.third.party.role.team.agents.clients','/')}}/' + team_key : ($(this).attr('id') === "edit_client_id" ? '{{ route('user.third.party.client.paid.invoices', '/') }}/' + team_key + '/' + client_id : null);
            $('#edit_invoice_id').empty().selectpicker('refresh').append(`<option class="" value="" disabled>Select Invoice</option>`);
            AjaxRequestGetPromise(url, null, null, false, '{{route("user.third.party.role.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    if ($(this).attr('id') === 'edit_team_key') {
                        update_select($('#edit_agent_id'), res.users, 'Select Team for Agent List');
                        update_select($('#edit_client_id'), res.clients, 'Select Client');
                    } else if ($(this).attr('id') === 'edit_client_id') {
                        update_select($('#edit_invoice_id'), res.invoices, 'Select Invoice');
                    }
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        /** On Click Edit Show Edit Details */
        $(document).on('click', '.editThirdPartyRole', function (e) {
            e.preventDefault();
            $('#update_form')[0].reset();
            let url = '{{ route('user.third.party.role.edit','/')}}/' + $(this).attr('data-id');

            AjaxRequestGetPromise(url, null, null, false, '{{route("user.third.party.role.index")}}', false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    $("#editThirdPartyRoleModal").attr('data-id', res.data.id);
                    update_select($('#edit_agent_id'), res.users, 'Select Team for Agent List');
                    update_select($('#edit_client_id'), res.clients, 'Select Client');
                    update_select($('#edit_invoice_id'), res.invoices, 'Select Invoice');

                    $('#edit_team_key').selectpicker('val', res.data.team_key);
                    $('#edit_agent_id').selectpicker('val', res.data.agent_id);
                    $('#edit_client_id').selectpicker('val', res.data.client_id);
                    $('#edit_invoice_id').selectpicker('val', res.data.invoice_id);
                    $('#edit_order_id').val(res.data.order_id);
                    $('#edit_order_status').selectpicker('val', res.data.order_status);
                    $('#edit_description').text(res.data.description);
                    $('#edit_amount').val(res.data.amount);
                    $('#edit_merchant_type').selectpicker('val', res.data.merchant_type);
                    $('#edit_payment_status').selectpicker('val', res.data.payment_status);
                    $('#edit_transaction_id').val(res.data.transaction_id);
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
    })
    ;
</script>
