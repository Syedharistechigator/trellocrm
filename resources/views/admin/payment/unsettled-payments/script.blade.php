<script src="{{ asset('assets/plugins/momentjs/moment.js') }}"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(document).ready(function () {
        $('#team, #brand').on('change', getParam);
    });

    function getParam() {
        window.location.href = "{{ route('admin.payment.unsettled.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
    }

    $(function () {
        ['#team', '#brand'].forEach(function (selector) {
            var parentId = $(selector).attr('id');
            $(selector).siblings('.dropdown-menu').find('input[type="text"]').attr('id', parentId + '-search');
        });

        $(document).ready(function () {
            var loading_div = $('.loading_div')
            $('#UnsettledPaymentTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']],
                scrollX: true,
                initComplete: function () {
                    $('#UnsettledPaymentTable_filter input').attr('id', 'UnsettledPaymentTable_searchInput');
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

            $(document).on('click', '.settlePayment', function () {
                var id = $(this).attr('data-id');
                let url = '{{ route('admin.check.payment.status','/')}}/' + id;
                AjaxRequestGetPromise(url, null, null, false, null, false, true, true).then((res) => {
                    if (res.status && res.status === 1 && res.success) {
                        $('#UnsettledPaymentTable').DataTable().row($("#tr-" + id)).remove().draw(false);
                        console.log(res.success);
                    }else{
                        let badgeClass = '';
                        let badgeText = res.event.charAt(0).toUpperCase() + res.event.slice(1);
                        if (res.event === 'captured pending settlement') {
                            badgeClass = 'badge-warning';
                        } else if (res.event === 'voided') {
                            badgeClass = 'badge-danger';
                        } else {
                            badgeClass = 'badge-primary';
                        }
                        $("#tr-" + id).find('.settlement-event').html(`<span class="badge ${badgeClass} rounded-pill">${badgeText}</span>`);
                    }
                }).catch((error) => {
                    console.log(error);
                }).finally(() => {
                    loading_div.css('display', 'none');
                })
            });
        });
    });
</script>
