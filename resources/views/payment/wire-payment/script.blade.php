<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    /** => Developer Michael Update <= **/
    var paymentApprovals = {!! json_encode($paymentApprovals) !!};
    $(document).ready(function () {
        var loading_div = $('.loading_div')

        function getParam() {
            window.location.href = "{{ route('user.wire.payments.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        function getRandomInt(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        $('[type=search],.bs-searchbox input[type=text]').each(function () {
            var randomNumber = getRandomInt(111111111, 999999999);
            $(this).attr('id', "dt-search-box-" + randomNumber);
        });
        $('#WirePaymentTable').DataTable().destroy();
        $('#WirePaymentTable').DataTable({
            dom: 'lBfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            order: [[0, 'desc']],
            scrollX: false,
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

        $('#brand').on('change', getParam);


        $('#create-wire-payment-modal-btn').click(function () {
            $('#brand, #date-range, [type=search], select[name="WirePaymentTable_length"]').prop('disabled', true);
        });
        $('#createWirePaymentModal').on('hidden.bs.modal', function () {
            $('#brand, #date-range, [type=search], select[name="WirePaymentTable_length"]').prop('disabled', false);
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

        function createRowHtml(data) {
            let logoSrc = `{{config('app.home_name') =='Uspto' ? 'assets/images/uspto-colored.png' : 'assets/images/logo-colored.png'}}`;
            return `<tr id="tr-${data.id}">
                        <td class="align-middle text-center">${data.id}</td>
                        <td class="align-middle text-nowrap">
                            <span class="brand-icon">
                                <object data="${data.get_brand && data.get_brand.logo ? data.get_brand.logo : ""}">
                                    <img src="${logoSrc}" alt="${data.get_brand && data.get_brand.name ? data.get_brand.name : ""}" loading="lazy">
                                </object>
                            </span>
                            <br>${data.get_brand && data.get_brand.name ? data.get_brand.name : ""}<br>${data.brand_key}
                        </td>
                        <td class="align-middle">${data.get_agent && data.get_agent.name ? data.get_agent.name : ""}</td>
                        <td class="align-middle text-nowrap">${data.client_name}<br>${data.client_email}</td>
                        <td class="align-middle text-nowrap">${data.sales_type}</td>
                        <td class="align-middle text-nowrap">${data.amount ? '$' + parseFloat(data.amount).toFixed(2) : ""}</td>
                        <td class="align-middle td-make-desc-short" title="${data.description ? data.description : ""}">${data.description ? data.description.substring(0, 20) + (data.description.length > 20 ? '...' : '') : ""}</td>
                        <td class="align-middle text-nowrap">${data.due_date}</td>
                        <td class="align-middle text-nowrap">${data.transaction_id ?? ""}</td>
                        <td class="text-center align-middle td-payment-approval">${convertValue(data.payment_approval, options.payment_approval)}</td>
                    </tr>`;
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
            let url = $(this).attr('id') === "update_form" ? null : ($(this).attr('id') === "create_form" ? '{{ route('user.wire.payment.store') }}' : null);
            if (url === null) {
                return false;
            }
            var formData = new FormData(this);
            var screenshots = $('#screenshots')[0].files;
            if (screenshots.length > 0) {
                let isValid = true;

                for (var i = 0; i < screenshots.length; i++) {
                    if (!screenshots[i].type.startsWith('image/')) {
                        createToast('error', `Please upload a valid image file. Invalid file: ${screenshots[i].name}`);
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

                        // updateAllCreationTimes();
                        //
                        // setInterval(() => {
                        //     updateAllCreationTimes();
                        // }, 100);
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
    });
</script>
