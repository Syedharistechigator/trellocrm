<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    var paymentApprovals = {!! json_encode($paymentApprovals) !!};

    $(document).ready(function () {
        $('#team, #brand').on('change', getParam);
    });

    function getParam() {
        window.location.href = "{{ route('admin.wire.payments.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
    }

    $(function () {
        ['#team', '#brand'].forEach(function (selector) {
            var parentId = $(selector).attr('id');
            $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
        });

        $(document).ready(function () {
            var loading_div = $('.loading_div')


            function getRandomInt(min, max) {
                min = Math.ceil(min);
                max = Math.floor(max);
                return Math.floor(Math.random() * (max - min + 1)) + min;
            }

            $('[type=search],.bs-searchbox input[type=text]').each(function () {
                var randomNumber = getRandomInt(111111111, 999999999);
                $(this).attr('id', "dt-search-box-" + randomNumber);
            });
            $('#WirePaymentTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true,
                initComplete: function () {
                    $('#WirePaymentTable_filter input').attr('id', 'WirePaymentTable_searchInput');
                }
            });

            var dateRangePicker = $(".cxm-date-range-picker");
            var initialStartDate = moment("{{ $fromDate }}", 'YYYY-MM-DD');
            var initialEndDate = moment("{{ $toDate }}", 'YYYY-MM-DD');
            var initialDateRange = initialStartDate.format('YYYY-MM-DD') + ' - ' + initialEndDate.format('YYYY-MM-DD');
            dateRangePicker.daterangepicker({
                opens: "left",
                locale: {
                    format: 'YYYY-MM-DD'
                },
                ranges: {
                    'Last 245 Days': [moment().subtract(244, 'days'), moment()],
                    'Last 3 Years': [moment().subtract(3, 'years').add(1, 'day'), moment()]
                },
                startDate: initialStartDate, // Set the initial start date
                endDate: initialEndDate,     // Set the initial end date
            });
            dateRangePicker.on('apply.daterangepicker', getParam);
            dateRangePicker.val(initialDateRange);

            $('#team, #brand').on('change', getParam);
            $('#create-wire-payment-modal-btn').click(function () {
                $('#team, #brand, #date-range, [type=search], select[name="WirePaymentTable_length"]').prop('disabled', true);
            });
            $('#createWirePaymentModal').on('hidden.bs.modal', function () {
                $('#team, #brand, #date-range, [type=search], select[name="WirePaymentTable_length"]').prop('disabled', false);
            });

            $('#create-wire-payment-modal-btn').on('click', function () {
                $('#create_form')[0].reset();
                $('#brand_key').val('').selectpicker('refresh');
                $('#agent_id').val('').selectpicker('refresh');
                $('#sales_type').val('').selectpicker('refresh');
                $("#screenshots").val('');
            });

            function convertValue(value, options) {
                return options[value] || 'None';
            }

            const options = {
                payment_approval: {
                    'Approved': '<span class="badge badge-success rounded-pill">Approved</span>',
                    'Not Approved': '<span class="badge badge-danger rounded-pill">Not Approved</span>',
                    'Pending': '<span class="badge badge-warning rounded-pill">Pending</span>',
                },
            };
            /** Payment Approval Modal => Adding id*/
            $(document).on('click', '.paymentApproval', function (e) {
                e.preventDefault();
                $('#PaymentApprovalModal').attr('data-id', $(this).attr('data-id'));
                $("#paymentApprovalSaveBtn").show();
                var id = $(this).attr('data-id');
                if (!id) {
                    createToast('error', 'need to reload');
                    return false;
                }
                if (!paymentApprovals.hasOwnProperty(id)) {
                    createToast('error', 'need to reload');
                    return false;
                }
                var paymentApprovalStatus = paymentApprovals[id];
                $('#payment_approval').val(paymentApprovalStatus).selectpicker('refresh');
            });

            /** Submit Payament Approval */
            $('#update-payment-approval-form').on('submit', function (e) {
                e.preventDefault();
                var id = $('#PaymentApprovalModal').attr('data-id');
                if (!id) {
                    createToast('error', 'need to reload');
                    return;
                }
                var formData = new FormData(this);
                formData.append('paymentApprovals', JSON.stringify(paymentApprovals));
                var url = '{{ route('admin.wire.payment.change.approval.status','/')}}/' + id;
                AjaxRequestPostPromise(url, formData, null, false, null, false, true, true)
                    .then((res) => {
                            if (res.success && res.status && res.status === 1 && res.data) {
                                if (res.paymentApprovals) {
                                    paymentApprovals = res.paymentApprovals;
                                }
                                $(`#tr-${res.data.id} #payment-approval-${res.data.id}`).html(convertValue(res.data.payment_approval, options.payment_approval));
                                $(`#tr-${res.data.id} #status-change-${res.data.id}`).html(res.data.approval_actor && res.data.approval_actor.name ? res.data.approval_actor.name : "" );
                                $(`#wire-payment-id-${res.data.id}`).remove();
                            } else {
                                createToast('error', 'Failed to update payment status');
                            }
                            loading_div.css('display', 'none');
                        }
                    )
                    .catch((error) => {
                        loading_div.css('display', 'none');
                    })
                    .finally(() => {
                        $(".modal").modal('hide');
                        loading_div.css('display', 'none');
                    })
            });

            function createRowHtmlAttachmentsModal(data, index) {
                return `<tr id="tr-${index + 1}">
                            <td class="align-middle text-center">${index + 1}</td>
                            <td class="align-middle">
                            <a title="View Attachment" href="{{ asset('assets/images/wire-payments/') }}/${data.mime_type}/${data.file_name}" target="_blank"><img class="width-100px" src="{{ asset('assets/images/wire-payments/') }}/${data.mime_type}/${data.file_name}" target="_blank"/></a></td>
                            <td class="align-middle">${data.original_name}</td>
                            <td class="align-middle">${data.extension}</td>
                            <td class="align-middle">${data.file_size}</td>
                            <td class="align-middle"><a title="View Attachment" href="{{ asset('assets/images/wire-payments/') }}/${data.mime_type}/${data.file_name}" target="_blank" class="btn btn-warning btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a></td>
                        </tr>`;
            }

            /** Show attachments modal */
            $(document).on('click', '.viewAttachments', function () {
                loading_div.css('display', 'flex');
                var url = '{{ route('admin.wire.payment.view.attachment','/')}}/' + $(this).data('id');
                AjaxRequestGetPromise(url, null, null, false, null, false, false, false, false)
                    .then((res) => {
                            if (res.success && res.status && res.status === 1) {
                                let table = $('#attachmentsTable').DataTable();
                                $('#attachmentsTable_filter label input').attr('id', 'dt-attachment-search-box');
                                table.clear().draw();
                                res.data.forEach(function (attachment, index) {
                                    table.row.add($(createRowHtmlAttachmentsModal(attachment, index))).invalidate().draw(false)
                                });
                                $('#attachmentsModal').modal('show');
                            } else {
                                createToast('error', 'Failed to fetch attachments');
                            }
                            loading_div.css('display', 'none');
                        }
                    )
                    .catch((error) => {
                        loading_div.css('display', 'none');
                    })
                    .finally(() => {
                        loading_div.css('display', 'none');
                    })
            });

            function update_select(column, array, default_text) {
                column.empty().selectpicker('refresh').append(`<option class="" value="" disabled>${default_text}</option>`);
                array.forEach(function (item) {
                    column.append('<option value="' + item.id + '">' + item.data + '</option>');
                })
                column.val('').selectpicker('refresh');
                column.val('').attr('required', true).prop('required', true);
            }

            /** On Change Brand Show Agent List */
            $('#brand_key').on('change', function () {
                let brand = $('#brand_key');
                let brand_key = brand.val() ? brand.val() : 0;
                let team_key = brand.find(':selected').attr('data-team-key');

                if (!brand_key || !team_key) {
                    createToast('error', 'Please select a valid brand');
                    return false;
                }
                $('#team_hnd').val(team_key);

                let url = '{{ route('admin.wire.payments.brand.agents','/')}}/' + team_key;
                $('#agent_id').empty().selectpicker('refresh');
                AjaxRequestGetPromise(url, null, null, false, null, false, true, false).then((res) => {
                    if (res.status && res.status === 1 && res.success) {
                        update_select($('#agent_id'), res.agents, 'Select Agent');
                    }
                }).catch((error) => {
                    console.log(error);
                }).finally(() => {
                    loading_div.css('display', 'none');
                })
            });

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

            function createRowHtml(data) {
                let logoSrc = `{{config('app.home_name') =='Uspto' ? 'assets/images/uspto-colored.png' : 'assets/images/logo-colored.png'}}`;
                let actionButtonsHtml = '';
                if (data.screenshot) {
                    let screenshotsArray = JSON.parse(data.screenshot);
                    if (Array.isArray(screenshotsArray) && screenshotsArray.length > 0) {
                        actionButtonsHtml += `<button data-id="${data.id}" id="attachment-id-${data.id}" title="View Attachment" class="btn btn-warning btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                                  </button>`;
                    }
                }

                if (data.payment_approval && data.payment_approval === 'Pending') {
                    actionButtonsHtml += `<button data-id="${data.id}" id="wire-payment-id-${data.id}" title="Change Payment Approval Status" class="btn btn-warning btn-sm btn-round paymentApproval" data-toggle="modal" data-target="#PaymentApprovalModal">
                                <i class="zmdi zmdi-refresh-sync"></i>
                              </button>`;
                }
                return `<tr id="tr-${data.id}">
                            <td class="align-middle text-center">${data.id}</td>
                            <td class="align-middle text-center text-nowrap">
                                <span class="brand-icon">
                                    <object data="${data.get_brand && data.get_brand.logo ? data.get_brand.logo : ""}">
                                        <img src="${logoSrc}" alt="${data.get_brand && data.get_brand.name ? data.get_brand.name : ""}" loading="lazy">
                                    </object>
                                </span>
                                <br>${data.get_brand && data.get_brand.name ? data.get_brand.name : ""}<br>${data.brand_key}
                            </td>
                            <td class="align-middle">${data.get_agent && data.get_agent.name ? data.get_agent.name : ""}</td>
                            <td class="align-middle text-nowrap">${data.client_name}<br>${data.client_email}</td>
                            <td class="align-middle text-nowrap">${data.sales_type ?? ""}</td>
                            <td class="align-middle text-nowrap">${data.amount ? '$' + parseFloat(data.amount).toFixed(2) : ""}</td>
                            <td class="align-middle td-make-desc-short" title="${data.description ? data.description : ""}">${data.description ? data.description.substring(0, 20) + (data.description.length > 20 ? '...' : '') : ""}</td>
                            <td class="align-middle text-nowrap">${data.due_date ?? ""}</td>
                            <td class="align-middle text-nowrap">${data.transaction_id ?? ""}</td>
                            <td class="text-center align-middle td-payment-approval" id="payment-approval-${data.id}">${convertValue(data.payment_approval, options.payment_approval)}</td>
                            <td class="align-middle text-nowrap text-center td-status-change" id="status-change-${data.id}">${data.actor && data.actor.name ? data.actor.name : ""}</td>
                            <td class="align-middle text-nowrap text-center">${data.approval_actor && data.approval_actor.name ? data.approval_actor.name : ""}</td>
                            <td class="text-center align-middle">${actionButtonsHtml}</td>
                        </tr>`;
            }
            function logFormData(formData) {
                for (var pair of formData.entries()) {
                    console.log(pair[0]+ ': ' + pair[1]);
                }
            }
            /** Record Create/Update Function */
            $('form').on('submit', function (e) {
                e.preventDefault();
                let url = $(this).attr('id') === "update_form" ? null : ($(this).attr('id') === "create_form" ? '{{ route('admin.wire.payment.store') }}' : null);
                if (url === null) {
                    return false;
                }
                var formData = new FormData(this);
                var screenshots = $('#screenshots')[0].files;
                if (screenshots.length > 0) {
                    let isValid = true;

                    for (var i = 0; i < screenshots.length; i++) {
                        if (!screenshots[i].type.startsWith('image/')) {
                            createToast('error',`Please upload a valid image file. Invalid file: ${screenshots[i].name}`);
                            isValid = false;
                            return false;
                        }
                    }

                    if (isValid) {
                        for (var i = 0; i < screenshots.length; i++) {
                            formData.append('screenshots[]', screenshots[i]);
                        }
                    }
                }
                formData.append('paymentApprovals', JSON.stringify(paymentApprovals));
                AjaxRequestPostPromise(url, formData, null, false, null, false, true, true).then((res) => {
                    if (res.success && res.status && res.status === 1) {

                        let table = $('#WirePaymentTable').DataTable();
                        if (table) {
                            /** Update payment approval statuses without reloading the page */
                            if (res.paymentApprovals) {
                                paymentApprovals = res.paymentApprovals;
                                Object.keys(paymentApprovals).forEach(id => {
                                    let $row = $(`#tr-${id}`);
                                    if ($row.length) {
                                        $row.find('.td-payment-approval').html(convertValue(paymentApprovals[id], options.payment_approval));
                                    }
                                });
                            }
                            if (res.data) {
                                if ($(this).attr('id') === "create_form") {
                                    table.row.add($(createRowHtml(res.data))).invalidate().draw(false)
                                }
                                highlightRow('tr-' + res.data.id);
                            }

                            /** For change specific value */
                            $('#brand_key').val('').selectpicker('refresh');
                            $('#agent_id').val('').selectpicker('refresh');
                            $('#sales_type').val('').selectpicker('refresh');
                        }
                    } else {
                        createToast('error', 'Failed to submitted record');
                    }
                }).catch((error) => {
                    if (error.responseJSON && error.responseJSON.paymentApprovals) {
                        paymentApprovals = error.responseJSON.paymentApprovals;
                        Object.keys(paymentApprovals).forEach(id => {
                            let $row = $(`#tr-${id}`);
                            if ($row.length) {
                                $row.find('.td-payment-approval').html(convertValue(paymentApprovals[id], options.payment_approval));
                            }
                        });
                    }
                    console.log(error);
                }).finally((final) => {
                    console.log(final);
                    $(".modal").modal("hide")
                    loading_div.css('display', 'none');
                })
            });

        });
    });
</script>
