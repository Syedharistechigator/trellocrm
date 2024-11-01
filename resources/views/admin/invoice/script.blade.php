<script>
    // search Client Invoices
    {{--$('#team').on('change', function () {--}}
    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    var teamKey = $(this).val();--}}


    {{--    $.ajax({--}}
    {{--        url: "{{ route('teamInvoices') }}",--}}
    {{--        method: 'POST',--}}
    {{--        data: {search: teamKey},--}}
    {{--        success: function (result) {--}}
    {{--            $("#InvoiceTable").html(result);--}}
    {{--            $('#InvoiceTable').DataTable({--}}
    {{--                "destroy": true, //use for reinitialize datatable--}}
    {{--            });--}}

    {{--            $(".table-responsive + nav").removeClass('d-flex').addClass('d-none');--}}
    {{--        }--}}
    {{--    });--}}
    {{--}); --}}
    {{--$('#brand').on('change', function () {--}}

    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    var brandKey = $(this).val();--}}

    {{--    $.ajax({--}}
    {{--        url: "{{ route('brandInvoicess') }}",--}}
    {{--        method: 'POST',--}}
    {{--        data: {search: brandKey},--}}
    {{--        success: function (result) {--}}
    {{--            $("#InvoiceTable").html(result);--}}
    {{--            $('#InvoiceTable').DataTable({--}}
    {{--                "destroy": true, //use for reinitialize datatable--}}
    {{--            });--}}

    {{--            $(".table-responsive + nav").removeClass('d-flex').addClass('d-none');--}}
    {{--        }--}}
    {{--    });--}}

    {{--});--}}
    var loading_div = $('.loading_div');

    function calculateAmountWithMerchant() {
        let amount = parseFloat($('#amount').val()) || 0;
        let amount_with_merchantFee = 0;
        let merchantFee = parseFloat($('#merchant_handling_fee').val()) || 0;
        let is_merchant_handling_fee = $('#is_merchant_handling_fee').val();
        if (amount > 0) {
            if (is_merchant_handling_fee === "1") {
                amount_with_merchantFee = amount - merchantFee;
            } else {
                amount_with_merchantFee = amount + merchantFee;
            }
            $('#amount').val(amount_with_merchantFee.toFixed(2));
        } else {
            $('#amount').val(0);
        }
        calculateTotalAmount();
    }

    function calculateTotalAmount() {
        let amount = parseFloat($('#amount').val()) || 0;
        let amount_with_merchantFee = 0;
        let merchantFee = parseFloat($('#merchant_handling_fee').val()) || 0;
        if ($('#is_merchant_handling_fee').is(":checked")) {
            amount_with_merchantFee = amount + merchantFee;
        } else {
            amount_with_merchantFee = amount;
        }
        let tax = $('#tax').val();
        let total = Math.round(((amount_with_merchantFee * tax) / 100));
        let totaltax = Number(total) + Number(amount_with_merchantFee);

        $('#tax_amount').val(total);
        $('#total_amount').val(totaltax);

    }

    $(document).ready(function () {
        $(".val-class").on('click', function () {
            $(this).val(this.checked ? 1 : 0);
        })
        // $('#split_merchant_handling_fee').change(function () {
        //     if ($(this).is(':checked')) {
        //         if (!$('#is_merchant_handling_fee').is(":checked")) {
        //             $('#is_merchant_handling_fee').trigger('click');
        //         }
        //     }
        // });
        // $('#is_merchant_handling_fee').change(function () {
        //     if (!$(this).is(':checked')) {
        //         if ($('#split_merchant_handling_fee').is(":checked")) {
        //             $('#split_merchant_handling_fee').trigger('click');
        //         }
        //     }
        // });
        // $('#split_tax').change(function () {
        //     if ($(this).is(':checked')) {
        //         if (!$('#taxable').is(":checked")) {
        //             $('#taxable').trigger('click');
        //         }
        //     }
        // });
        // $('#taxable').change(function () {
        //     if (!$(this).is(':checked')) {
        //         if ($('#split_tax').is(":checked")) {
        //             $('#split_tax').trigger('click');
        //         }
        //     }
        // });

        $('#is_merchant_handling_fee').on('click', function () {
            if ($(this).is(":checked")) {
                this.value = 1;
                $('.merchant-handling-fee-div').show();
            } else {
                this.value = 0;
                $('.merchant-handling-fee-div').hide();
            }
            calculateAmountWithMerchant();
        });

        let previousMerchantFee = parseFloat($('#merchant_handling_fee').val()) || 20.00;

        $("#merchant_handling_fee").keyup(function () {
            let amount = parseFloat($('#amount').val()) || 0;
            let currentMerchantFee = parseFloat($(this).val()) || 0;
            if ($('#is_merchant_handling_fee').is(":checked")) {
                amount += previousMerchantFee;
                amount -= currentMerchantFee;
            }
            $('#amount').val(amount.toFixed(2));
            previousMerchantFee = currentMerchantFee;
        });


        $('#create-invoice-show-modal, .editInvoice').click(function () {
            $('#team, #brand, #date-range').prop('disabled', true);

            if ($('#is_merchant_handling_fee').is(":checked")) {
                $('#is_merchant_handling_fee').trigger('click');
            }
            $("#total_amount").val(0);
        });
        $('#invoiceModal, #editInvoiceModal').on('hidden.bs.modal', function () {
            $('#team, #brand, #date-range').prop('disabled', false);
        });

        function getParam() {
            window.location.href = "{{ route('admin.invoices.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&teamKey=" + encodeURIComponent($('#team').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $('#team, #brand').on('change', getParam);


        $("#InvoiceTable").on("click", ".MerchantPaymentBtn", function () {
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
                        text: "Yes!"
                    }
                },
                dangerMode: false,
            })
                .then((confirmed) => {
                    if (confirmed) {
                        const url = '{{ route('MerchantFeeAndTaxPayment') }}'
                        AjaxRequestGetPromise(url, {invoiceid:$(this).data('id')}, null, false, null, false, true, true, false).then(res => {
                            console.log(res);
                            location.reload();
                        }).catch(error => {
                            console.log(error);
                        }).finally(() => {
                            loading_div.css('display', 'none');
                        })
                    } else {
                        swal("May be next time!", {buttons: false, timer: 1000});
                    }
                });
        });
    });

    // Authorize Payment
    $("#InvoiceTable").on("click", ".AuthorizePaymentButton", function () {
        swal({
            title: "Are you sure?",
            text: "Once captured, not be able to capture again!",
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
                    text: "Yes!"
                }
            },
            dangerMode: false,
        })
            .then((confirmed) => {
                if (confirmed) {
                    const url = '{{ route('api.authorize.payment.capture.authorized.invoice') }}'
                    const formData = new FormData();
                    formData.append("id", $(this).data('id'))
                    formData.append("key", $(this).data('key'))
                    AjaxRequestPostPromise(url, formData, null, false, null, false, true, true, false).then(res => {
                        $(this).remove();
                        let tr = $('#invoice-tr-' + $(this).data('id'));
                        tr.find('.status-span').html('<span class="badge badge-success rounded-pill">Paid</span>');
                        tr.find('.td-due-date span').removeClass('badge-danger xtext-danger').addClass('badge-success xtext-success');
                        tr.find('.upSalePayment, .failedCardUpSalePayment').remove();
                    }).catch(error => {
                    }).finally(() => {
                        loading_div.css('display', 'none');
                    })
                } else {
                    swal("May be next time!", {buttons: false, timer: 1000});
                }
            });
    });

    // Delete Invoice
    $("#InvoiceTable").on("click", ".delButton", function () {
        swal({
            title: "Are you sure?",
            text: "Once deleted, not be able to recover this Invoice!",
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
                        url: "invoiceadmin/" + cid,
                        success: function (data) {
                            swal("Poof! Your Lead has been deleted!", {
                                icon: "success",
                            });
                            //setInterval('location.reload()', 2000);        // Using .reload() method.
                            setTimeout(function () {
                                window.location.href = '{{route("admin.invoices.index")}}';
                            }, 2000);
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


    $('#brand_key').on('change', function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var brand = $(this).val();
        let cxmTeamKey = $(this).find(':selected').attr('data-cxm-team-key');
        console.log(brand + ' TK ' + cxmTeamKey);

        $('#team_hnd').val(cxmTeamKey);

        $.ajax({
            url: "{{ route('brandteamAgent') }}",
            method: 'POST',
            data: {search: cxmTeamKey},
            success: function (data) {
                var len = data.length;
                $("#agent_id").empty();
                $("#agent_id").append('<option class="bs-title-option" value="">Select Agent</option>');
                for (var i = 0; i < len; i++) {
                    var id = data[i]['id'];
                    var name = data[i]['name'];
                    $("#agent_id").append('<option value="' + id + '">' + name + '</option>');
                }
                $('#agent_id').selectpicker('refresh');

            }
        });
    });

    $('#taxable').on('click', function () {
        if ($(this).is(":checked")) {
            this.value = 1;
            $("#taxField").show();
            $("#totalAmount").show();
            $('#taxField').prop('required', true);
        } else {
            this.value = 0;
            $("#taxField").hide();
            // $("#totalAmount").hide();
            $("#tax").val('');
            // $("#total_amount").val('');
            $('#taxField').prop('required', false);
        }
        calculateTotalAmount();
    });

    $("#tax, #amount").keyup(function () {
        var amount = parseFloat($('#amount').val()) || 0;
        if ($('#is_merchant_handling_fee').is(":checked")) {
            let merchantFee = parseFloat($('#merchant_handling_fee').val()) || 20.00;
            amount += merchantFee;
        }
        var tax = $('#tax').val();
        var total = Math.round(((amount * tax) / 100));
        var totaltax = Number(total) + Number(amount);

        $('#tax_amount').val(total);
        $('#total_amount').val(totaltax);
    });


    function createInvoice() {
        let cxmAmount = $('#amount').val();
        let cxmTotalAmount = $('#total_amount').val();
        let cxmGrossTotalAmount = 0;
        cxmGrossTotalAmount = cxmTotalAmount ? cxmTotalAmount : cxmAmount;

        cxmGrossTotalAmountInt = parseInt(cxmGrossTotalAmount);
        cxmPaymentLimitInt = parseInt("{{env('PAYMENT_LIMIT')}}");

        if (cxmGrossTotalAmountInt <= cxmPaymentLimitInt) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('adminStoreInvoice') }}",
                method: 'POST',
                data: $('#admin_direct-invoice-Form').serialize(), // get all form field value in serialize form
                success: function (data) {
                    $("#admin_direct-invoice-Form")[0].reset();

                    $("#invoiceModal").modal('hide');

                    swal("Good job!", "Invoice successfully Created!", "success");
                    setTimeout(function () {
                        window.location.href = '{{route("admin.invoices.index")}}';
                    }, 2000);
                },
                error: function (data) {
                    var errorMessage = "An unknown error occurred.";
                    if (data.responseJSON) {
                        if (data.responseJSON.errors) {
                            errorMessage = Object.values(data.responseJSON.errors)[0][0];
                        } else if (data.responseJSON.error) {
                            errorMessage = data.responseJSON.error;
                        } else if (data.responseJSON.message) {
                            errorMessage = data.responseJSON.message;
                        }
                    }
                    swal('Error', errorMessage, 'error');
                }
            });
        } else {
            swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
        }
    }

    //Create Invoice
    $('#admin_direct-invoice-Form').on('submit', function (e) {
        e.preventDefault();
        var i;
        var server_name = "{{$_SERVER['SERVER_NAME']}}";

        // if (server_name == 'development.tgcrm.net' || server_name == '127.0.0.1') {
        //     for (i = 1; i <= 10; i++) {
        //         let amount = parseInt($('#amount').val());
        //         let totalAmount = parseInt($('#total_amount').val()) ? parseInt($('#total_amount').val()) : amount;
        //         if(i > 1){
        //             amount ++;
        //             totalAmount ++;
        //         }
        //         $('#amount').val(amount);
        //         $('#total_amount').val(totalAmount);
        //         createInvoice();
        //     }
        // } else {
        createInvoice();
        // }
    });

    $("#InvoiceTable").on("click", ".copy-url", function () {
        var copyText = $(this).attr('id');
        $(this).css('background-color', '#f00');

        /* Select the text field */

        let $cxmTemp = $("<input>");
        $(this).append($cxmTemp);
        $cxmTemp.val(copyText).select();
        document.execCommand("copy");
        $cxmTemp.remove();

        /* Alert the copied text */
        // alert("Copied the text: " + copyText.value);
        swal("Good job!", "URL Successfully Copied!", "success");
    });


    //edit invoice
    $("#InvoiceTable").on("click", ".editInvoice", function () {
        var invoice_id = $(this).data('id');
        document.getElementById("invoice_hdn").value = invoice_id;

        $.ajax({
            type: "GET",
            url: "invoiceadmin/" + invoice_id + '/edit',
            success: function (data) {
                $('#edit_amount').val(data.final_amount);
                $('#edit_due_date').text(data.due_date);
                $('#edit_invoice_description').text(data.invoice_descriptione);
                $('#edit_tax').val(data.tax_percentage);
                $('#edit_total_amount').val(data.total_amount);
                $('#edit_brand_key').selectpicker('val', data.brand_key);
                $('#edit_agent_id').selectpicker('val', data.agent_id);
                $('#sales_type').selectpicker('val', data.sales_type);
                $('#sales_status').selectpicker('val', data.status);
                $('#edit_cur_symbol').selectpicker('val', data.cur_symbol);
                $('#edit_payment_gateway').selectpicker('val', data.payment_gateway);
                // $('#edit_is_split').selectpicker('val', data.is_split);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });
    /**Split Payments*/
    $("#InvoiceTable").on("click", ".viewPaymentInvoice", function () {
        $('#first-payment').text('');
        $('#second-payment').text('');
        $('#third-payment').text('');

        var invoice_id = $(this).data('id');
        $.ajax({
            type: "GET",
            url: "{{ route('view_payment_invoice') }}" + "/" + invoice_id,
            success: function (data) {
                $('#first-payment').text(data.first_payment);
                $('#second-payment').text(data.second_payment);
                $('#third-payment').text(data.third_payment);
            },
            error: function (data) {
                console.log('Error:', data);
                console.log('Error', data.responseJSON.error);
            }

        });
    });

    //update Invoice
    $('#invoice_update_form').on('submit', function (e) {
        e.preventDefault();
        let cxmAmount = $('#edit_amount').val();
        let cxmTotalAmount = $('#edit_total_amount').val();
        let cxmGrossTotalAmount = 0;
        cxmGrossTotalAmount = cxmTotalAmount ? cxmTotalAmount : cxmAmount;

        cxmGrossTotalAmountInt = parseInt(cxmGrossTotalAmount);
        cxmPaymentLimitInt = parseInt("{{env('PAYMENT_LIMIT')}}");

        if (cxmGrossTotalAmountInt <= cxmPaymentLimitInt) {

            var bid = $('#invoice_hdn').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            {{--//{{ route('changeleadStatus') }}--}}

            $.ajax({
                url: "/admin/invoiceadmin/" + bid,
                method: 'post',
                data: $(this).serialize(),
                success: function (result) {
                    $("#editInvoiceModal").modal('hide');
                    swal("Good job!", "Invoice successfully Updated!", "success");
                    setTimeout(function () {
                        window.location.href = '{{route("admin.invoices.index")}}';
                    }, 2000);
                },
                error: function (data) {
                    var errorMessage = "An unknown error occurred.";
                    if (data.responseJSON) {
                        if (data.responseJSON.errors) {
                            errorMessage = Object.values(data.responseJSON.errors)[0][0];
                        } else if (data.responseJSON.error) {
                            errorMessage = data.responseJSON.error;
                        } else if (data.responseJSON.message) {
                            errorMessage = data.responseJSON.message;
                        }
                    }
                    swal('Error', errorMessage, 'error');
                }
            });

        } else {
            swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
        }
    });

    function isJSON(str) {
        try {
            JSON.parse(str);
            return true;
        } catch (e) {
            return false;
        }
    }

    $("#InvoiceTable").on("click", ".upSalePayment", function () {
        var invoice_id = $(this).data('id');
        var clientId = $(this).attr('data-cxm-client-id');
        $("#invoice_key").val(invoice_id);
        var url = '{{ route('admin.client.card.info.active.status','/')}}/' + clientId;

        $.ajax({
            type: "GET",
            datatype: 'JSON',
            url: url,
            success: function (data) {
                $("#client_card").empty();
                if (isJSON(data)) {
                    $.each(JSON.parse(data), function (i, v) {
                        // console.log(i+'|'+v.id);
                        $("#client_card").append("<option value='" + v.id + "'>" + v.card_type + ' - ' + v.card4Digit + (v.invoice_id ? ' / Invoice : ' + v.invoice_id : '') + "</option>");
                    });
                } else {
                    $.each(data, function (i, v) {
                        $("#client_card").append("<option value='" + v.id + "'>" + v.card_type + ' - ' + v.card4Digit + (v.invoice_id ? ' / Invoice : ' + v.invoice_id : '') + "</option>");
                    });
                }

            }
        });


    });

    @if(auth()->guard('admin')->user()->type === 'super')
    $("#InvoiceTable").on("click", ".failedCardUpSalePayment", function () {
        var invoice_id = $(this).data('id');
        var clientId = $(this).attr('data-cxm-client-id');
        var status = 0;
        $("#failed_card_invoice_key").val(invoice_id);
        var url = '{{ route('admin.client.card.info.inactive.status','/')}}/' + clientId;

        $("#failed_card_client_card").empty();
        $.ajax({
            type: "GET",
            datatype: 'JSON',
            url: url,
            success: function (data) {
                if (isJSON(data)) {
                    $.each(JSON.parse(data.cards), function (i, v) {
                        $("#failed_card_client_card").append("<option value='" + v.id + "'>" +
                            v.card_type + ' - ' + v.card4Digit +
                            (v.invoice_id ? ' / Invoice : ' + v.invoice_id : '') +
                            (v.updated_at ? ' / TIME : ' + v.updated_at : '') +
                            (v.card_status === 1 ? ' / Success ' : ' / Failed') +
                            "</option>");
                    });
                } else {
                    $.each(data.cards, function (i, v) {
                        $("#failed_card_client_card").append("<option value='" + v.id + "'>" +
                            v.card_type + ' - ' + v.card4Digit +
                            (v.invoice_id ? ' / Invoice : ' + v.invoice_id : '') +
                            (v.updated_at ? ' / TIME : ' + v.updated_at : '') +
                            (v.card_status === 1 ? ' / Success ' : ' / Failed') +
                            "</option>");
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });


    });
    var authorizeMerchants = {!! app('dm_payment_method_authorize')->mapWithKeys(function($item) {return [$item->id => ['merchant' => $item->merchant, 'status' => $item->status, 'capacity' => $item->capacity, 'cap_usage' => $item->cap_usage]];}) !!};
    var expigateMerchants = {!! app('dm_payment_method_expigate')->mapWithKeys(function($item) {return [$item->id => ['merchant' => $item->merchant, 'status' => $item->status, 'capacity' => $item->capacity, 'cap_usage' => $item->cap_usage]];}) !!};

    $('input[name="merchant_type"]').change(function () {
        var mainMerchant = $(this).val();
        if (mainMerchant === 'authorize') {
            populateMerchantList(authorizeMerchants);
        } else if (mainMerchant === 'expigate') {
            populateMerchantList(expigateMerchants);
        } else {
            $('#merchant_list_dropdown').hide();
        }
    });

    function populateMerchantList(merchants) {
        var dropdown = $('#failed_card_merchant_list');
        dropdown.empty();
        $.each(merchants, function (id, merchant) {
            var statusSymbol = merchant.status == 1 ? "✓" : "✗";
            var remaining = merchant.capacity - merchant.cap_usage;
            var remainingSymbol = getRemainingSymbol(remaining, merchant.capacity);
            let percentage = Math.round((remaining / merchant.capacity) * 100);

            dropdown.append($('<option></option>').attr('value', id).text(merchant.merchant + " (" + statusSymbol + ", " + percentage + "% / " + (merchant.capacity - merchant.cap_usage).toFixed(2) + ")"));
        });
        $('#merchant_list_dropdown').show();
    }

    function getRemainingSymbol(remaining, capacity) {
        if (remaining < 0) {
            return "⛔";
        } else if (remaining === 0) {
            return "🔴";
        } else if (remaining >= capacity * 1.00) {
            return "🟢⚡⚡";
        } else if (remaining >= capacity * 0.75) {
            return "🟢⚡";
        } else if (remaining > capacity * 0.50) {
            return "🟢";
        } else if (remaining > capacity * 0.25) {
            return "🟡";
        } else if (remaining > capacity * 0.01) {
            return "🟠";
        } else {
            return "🔴";
        }
    }

    $('#admin-failed-card-upsale-payment-Form').on('submit', function (e) {
        e.preventDefault();

        swal({
            title: "Are you sure?",
            text: "You want to Pay this Invoice!",
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
                    text: "Yes, Paid!"
                }
            },
            dangerMode: true,
        })
            .then((willPay) => {
                if (willPay) {
                    $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('admin_upsale_direct_payment') }}",
                        method: 'POST',
                        data: $(this).serialize(), // get all form field value in serialize form
                        success: function (res) {
                            console.log(res);
                            console.log('_transactionid_', res);
                            if (res && (res.error || res.errors)) {
                                var errorData = res.error || res.errors;

                                var errorMessage;
                                if (errorData.card_name) errorMessage = errorData.card_name[0];
                                else if (errorData.card_number) errorMessage = errorData.card_number[0];
                                else if (errorData.card_exp_month) errorMessage = errorData.card_exp_month[0];
                                else if (errorData.card_exp_year) errorMessage = errorData.card_exp_year[0];
                                else if (errorData.card_cvv) errorMessage = errorData.card_cvv[0];
                                else errorMessage = errorData.error || errorData.errors || 'An unknown error occurred.';

                                swal('error', errorMessage, 'error');
                                $('.page-loader-wrapper').css('display', 'none');
                                $("#failedCardUpSalePaymentModal").modal('hide');
                                return false;
                            }

                            $("#admin-upsale-payment-Form")[0].reset();
                            $('.page-loader-wrapper').css('display', 'none');
                            $("#failedCardUpSalePaymentModal").modal('hide');


                            if (res.payment_gateway == 'none') {
                                swal('error', 'Please Contact Support or try again later.', "error");

                                $('.page-loader-wrapper').css('display', 'none');
                                $("#failedCardUpSalePaymentModal").modal('hide');
                                return false;
                            }


                            if (res.payment_gateway == 'authorize') {
                                if (res.code == 1) {
                                    swal('success', 'Payment has been done Successfully.', "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', res.message);
                                }
                            }

                            if (res.payment_gateway == 'expigate') {
                                if (res.message.toLowerCase() == 'success' || res.message.toLowerCase() == 'approved') {
                                    swal('success', 'Payment has been done Successfully.', "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', res.message);
                                }
                            }

                            if (res.payment_gateway == 'payarc') {
                                if (res.message.toLowerCase() == 'success' || res.message.toLowerCase() == 'approved') {
                                    swal('success', 'Payment has been done Successfully.', "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', res.message);
                                }
                            }

                        },
                        error: function (error) {
                            console.log(error);
                            var errors = error.responseJSON?.errors || {};
                            console.log('e.errors', errors);

                            var errorMessage =
                                errors.card_name ? errors.card_name[0] :
                                    errors.card_number ? errors.card_number[0] :
                                        errors.card_exp_month ? errors.card_exp_month[0] :
                                            errors.card_exp_year ? errors.card_exp_year[0] :
                                                errors.card_cvv ? errors.card_cvv[0] :
                                                    errors.errors ? errors.errors :
                                                        errors || 'An unknown error occurred.';

                            swal('error', errorMessage, 'error');
                            // swal("Errors!", "Request Fail!", "error");
                            $('.page-loader-wrapper').css('display', 'none');
                            $("#failedCardUpSalePaymentModal").modal('hide');
                        }
                    });
                } else {
                    swal("Please let us know when you want to charge amount.!", {buttons: false, timer: 1000});
                }
            });
    });
    @endif
    // //Create upsale Payment
    // $('#admin-upsale-payment-Form').on('submit', function(e){
    //      e.preventDefault();
    //     console.log('Upsale Form');

    //     swal({
    //         title: "Are you sure?",
    //         text: "You want to Pay this Invoice!",
    //         icon: "warning",
    //         buttons: {
    //             cancel: {
    //                 text: "No",
    //                 value: null,
    //                 visible: true,
    //                 className: "btn-warning",
    //                 closeModal: true,
    //             },
    //             confirm: {
    //                 text: "Yes, Paid!"
    //             }
    //         },
    //         dangerMode: true,
    //     })
    //     .then((willPay) => {
    //         if (willPay) {
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //                 }
    //             });

    //             $.ajax({
    //                 url: "{{ route('adminUsalePayment') }}",
    //                 method:'POST',
    //                 data: $(this).serialize(), // get all form field value in serialize form
    //                 success: function(data){
    //                     $("#admin-upsale-payment-Form")[0].reset();
    //                     console.log('Test:'+data);
    //                     console.log("code: " + data.code);
    //                     $("#upsalePaymentModal").modal('hide');


    //                 },
    //                 error: function(){
    //                     swal("Errors!", "Request Fail!", "error");
    //                 }
    //             });
    //         } else {
    //             swal("Your brand is safe!", {buttons: false, timer: 1000});
    //         }
    //     });
    // });

    $('#admin-upsale-payment-Form').on('submit', function (e) {
        e.preventDefault();

        swal({
            title: "Are you sure?",
            text: "You want to Pay this Invoice!",
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
                    text: "Yes, Paid!"
                }
            },
            dangerMode: true,
        })
            .then((willPay) => {
                if (willPay) {
                    $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: "{{ route('admin_upsale_multi_payment') }}",
                        method: 'POST',
                        data: $(this).serialize(), // get all form field value in serialize form
                        success: function (res) {
                            console.log(res);
                            console.log('_transactionid_', res);
                            if (res && (res.error || res.errors)) {
                                var errorData = res.error || res.errors;

                                var errorMessage;
                                if (errorData.card_name) errorMessage = errorData.card_name[0];
                                else if (errorData.card_number) errorMessage = errorData.card_number[0];
                                else if (errorData.card_exp_month) errorMessage = errorData.card_exp_month[0];
                                else if (errorData.card_exp_year) errorMessage = errorData.card_exp_year[0];
                                else if (errorData.card_cvv) errorMessage = errorData.card_cvv[0];
                                else errorMessage = errorData.error || errorData.errors || 'An unknown error occurred.';

                                swal('error', errorMessage, 'error');
                                $('.page-loader-wrapper').css('display', 'none');
                                $("#upsalePaymentModal").modal('hide');
                                return false;
                            }

                            $("#admin-upsale-payment-Form")[0].reset();
                            $('.page-loader-wrapper').css('display', 'none');
                            $("#upsalePaymentModal").modal('hide');


                            var t = res.response
                            var type_message = t.type_message ?? 'Payment has been done Successfully.';

                            if (res.payment_gateway == 'none') {
                                swal('error', 'Please Contact Support or try again later.', "error");

                                $('.page-loader-wrapper').css('display', 'none');
                                $("#upsalePaymentModal").modal('hide');
                                return false;
                            }


                            if (t.payment_gateway == 'authorize') {
                                if (t.code == 1) {
                                    swal('success', type_message, "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', t.message);
                                }
                            }

                            if (t.payment_gateway == 'expigate') {
                                if (t.message.toLowerCase() == 'success' || t.message.toLowerCase() == 'approved') {
                                    swal('success', type_message, "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', t.message);
                                }
                            }

                            if (t.payment_gateway == 'payarc') {
                                if (t.message.toLowerCase() == 'success' || t.message.toLowerCase() == 'approved') {
                                    swal('success', type_message, "success");
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    swal('error', t.message);
                                }
                            }

                        },
                        error: function (error) {
                            console.log(error);
                            var errors = error.responseJSON?.errors || {};
                            console.log('e.errors', errors);

                            var errorMessage =
                                errors.card_name ? errors.card_name[0] :
                                    errors.card_number ? errors.card_number[0] :
                                        errors.card_exp_month ? errors.card_exp_month[0] :
                                            errors.card_exp_year ? errors.card_exp_year[0] :
                                                errors.card_cvv ? errors.card_cvv[0] :
                                                    errors.errors ? errors.errors :
                                                        errors || 'An unknown error occurred.';

                            swal('error', errorMessage, 'error');
                            // swal("Errors!", "Request Fail!", "error");
                            $('.page-loader-wrapper').css('display', 'none');
                            $("#upsalePaymentModal").modal('hide');
                        }
                    });
                } else {
                    swal("Please let us know when you want to charge amount.!", {buttons: false, timer: 1000});
                }
            });
    });
</script>
