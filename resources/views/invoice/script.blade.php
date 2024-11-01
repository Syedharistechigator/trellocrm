<script>
    $(document).ready(function () {
        var loading_div = $('.loading_div')

        $('#client, #projects').attr('required', false);

        $('#create-invoice-show-modal, .editInvoice').click(function () {
            $('#brand, #date-range, [type=search]').prop('disabled', true);
        });
        $('#invoiceModal, #editInvoiceModal').on('hidden.bs.modal', function () {
            $('#brand, #date-range, [type=search]').prop('disabled', false);
        });

        function getParam() {
            window.location.href = "{{ route('user.invoices.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $('#brand').on('change', getParam);


        /** Copy Invoice Payment Url */
        $("#InvoiceTable").on("click", ".copy-url", function () {
            var copyText = $(this).attr('id');
            $(this).css('background-color', '#f00');
            let $cxmTemp = $("<input>");
            $(this).append($cxmTemp);
            $cxmTemp.val(copyText).select();
            document.execCommand("copy");
            $cxmTemp.remove();
            swal("Good job!", "URL Successfully Copied!", "success");
        });

        function update_select(column, array, default_text) {
            column.empty().selectpicker('refresh').append(`<option class="" value="" disabled>${default_text}</option>`);
            array.forEach(function (item) {
                column.append('<option value="' + item.id + '">' + item.data + '</option>');
            })
            column.val('').selectpicker('refresh');
            column.val('').attr('required', true).prop('required', true);
        }

        /** On Change Team Show Brand List |OR| On Change Brand Show Agent List */
        $('#team_hnd,#brand_key').on('change', function () {
            let id = $(this).attr('id');
            let value = $(this).val();
            if (!value) {
                createToast('error', 'Please select a valid option');
                return false;
            }
            $('#agent_id').empty().selectpicker('refresh');
            let url;
            if (id === 'team_hnd') {
                $('#brand_key').empty().selectpicker('refresh');
                url = '{{ route('user.team.brands', '/') }}/' + value;
            } else if (id === 'brand_key') {
                let teamKey = $('#team_hnd').val();
                if (!teamKey) {
                    createToast('error', 'Please select a valid team');
                    return false;
                }
                url = '{{ route('teamAgent','/')}}/' + value + '/' + teamKey;
            }

            AjaxRequestGetPromise(url, null, null, false, null, false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    if (id === 'team_hnd') {
                        update_select($('#brand_key'), res.brands, 'Select Brand');
                    } else if (id === 'brand_key') {
                        update_select($('#agent_id'), res.users, 'Select Agent');
                    }
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            });
        });

        /** On Change Brand Show Agent List */
        {{--$('#brand_key').on('change', function () {--}}
        {{--    var brand = $(this).val();--}}
        {{--    let cxmTeamKey = $(this).find(':selected').attr('data-cxm-team-key');--}}
        {{--    if (!cxmTeamKey) {--}}
        {{--        cxmTeamKey = '{{auth()->user()->type == 'ppc' ? auth()->user()->team_key : null}}';--}}
        {{--    }--}}
        {{--    $('#team_hnd').val(cxmTeamKey);--}}
        {{--    var url = '{{ route('teamAgent','/')}}/' + brand + '/' + cxmTeamKey;--}}

        {{--    $.ajax({--}}
        {{--        type: "GET",--}}
        {{--        url: url,--}}
        {{--        success: function (data) {--}}
        {{--            console.log(data);--}}
        {{--            var len = data.user.length;--}}
        {{--            $("#agent_id").empty();--}}

        {{--            $('#agent_id options').remove();--}}
        {{--            $('#agent_id').selectpicker('refresh');--}}
        {{--            $("#agent_id").append('<option class="bs-title-option" value="">Select Agent</option><option value="1">Default User</option>');--}}
        {{--            for (var i = 0; i < len; i++) {--}}
        {{--                var id = data.user[i]['id'];--}}
        {{--                var name = data.user[i]['name'];--}}
        {{--                $("#agent_id").append('<option value="' + id + '">' + name + '</option>');--}}
        {{--            }--}}
        {{--            $('#agent_id').val('');--}}

        {{--            $('#agent_id').attr('required', true);--}}
        {{--            $('#agent_id').prop('required', true);--}}
        {{--            $('#agent_id').selectpicker('refresh');--}}
        {{--        }--}}
        {{--    });--}}
        {{--});--}}

        /** On Change Sales Type Show / Hide Client And Project */
        $('#type').on('change', function () {
            var salesType = $(this).val();

            if (salesType == 'Upsale' || salesType == 'Recurring') {
                $('#showClient, #showProject').show();
                $('#showName, #showEmail, #showPhone').hide();
                $('#name, #phone, #email, #projectTitle, #projectTileBlock').hide();

                $('#name, #phone, #email, #projectTitle').removeAttr('required');

                $('#client, #projects').attr('required', true);
                $('#client, #projects').prop('required', true);
                $('#client, #projects').selectpicker('val', '');


                $('#projects option').remove();
                $("#projects").append("<option disabled>Select Client for project</option>");
                $('#projects').val('');
                $('#projects').selectpicker('refresh');


            } else {
                $('#showName, #showEmail, #showPhone').show();
                $('#name, #phone, #email, #projectTitle, #projectTileBlock').show();
                $('#client, #projects').attr('required', false);
                $('#client, #projects').prop('required', false);

                $('#showClient, #showProject').hide();

                $('#client, #projects').removeAttr('required');

                $('#name, #phone, #email, #projectTitle').attr('required', true);
                $('#name, #phone, #email, #projectTitle').val('');
            }
        });

        /** On Change Project Type Show / Hide New Or Existing Project */
        $('#projects').on('change', function () {
            var projectValue = $(this).val();
            if (projectValue == 'new') {
                $('#projectTitle').attr('required', true);
                $('#projectTitle').prop('required', true);
                $('#projectTileBlock, #projectTitle').show();
            } else {
                $('#projectTitle').removeAttr('required', false);
                $('#projectTitle').prop('required', false);
                $('#projectTileBlock, #projectTitle').hide();
            }
        });

        /** On Change Client DropDown Check If Client Is New Show Project Input And Hide Project DropDown Else Hide Project Input And Show Project DropDown*/
        $('#client').on('change', function () {
            $('#projectTileBlock, #projectTitle').hide();
            $('#projectTitle').prop('required', false);

            var clientValue = $(this).val();

            if (clientValue == 'new') {

                document.getElementById('client').value = null;
                $('#showName, #showEmail, #showPhone').show();
                $('#name, #phone, #email,#projectTileBlock, #projectTitle').show();
                $('#showProject').hide();

                $('#client, #projects').removeAttr('required');
                $('#name, #phone, #email, #projectTitle').attr('required', true);


                $('#name, #phone, #email, #projectTitle').val('');
            } else {
                $('#showName, #showEmail, #showPhone').hide();
                $('#name, #phone, #email, #projectTitle, #projectTileBlock').hide();
                $('#client, #projects').prop('required', true);

                $('#showProject').show();
                $('#name, #phone, #email, #projectTitle').attr('required', false);

            }

        });

        /** On Change Client DropDown Update Project DropDown Options*/
        $('#client').on('change', function () {
            var client = $(this).val();
            $.ajax({
                type: "GET",
                url: "clientproject/" + client,
                success: function (data) {
                    var len = data.length;
                    $("#projects").empty();
                    $("#projects").append("<option value='new'><b>Create New Project</b></option>");
                    for (var i = 0; i < len; i++) {
                        var id = data[i]['id'];
                        var name = data[i]['project_title'];
                        $("#projects").append("<option value='" + id + "'>" + name + "</option>");
                    }
                    $('#projects').selectpicker('refresh');
                }
            });
        });

        //Create Invoice
        $('#direct-invoice-Form').on('submit', function (e) {
            e.preventDefault();
            $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});

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
                    url: "{{ route('storeInvoice') }}",
                    method: 'POST',
                    data: $(this).serialize(), // get all form field value in serialize form
                    success: function (data) {
                        $("#direct-invoice-Form")[0].reset();
                        $("#invoiceModal").modal('hide');
                        $('.page-loader-wrapper').css('display', 'none');

                        swal("Good job!", "Invoice successfully Created!", "success");
                        setTimeout(function () {
                            window.location = '{{url("/invoices")}}';
                        }, 2000);

                    },
                    error: function (data) {
                        $('.page-loader-wrapper').css('display', 'none');
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

                $('.page-loader-wrapper').css('display', 'none');
                swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
            }
        });

        // Send Email
        $("#InvoiceTable").on("click", ".sendEmail", function () {
            swal({
                title: "Email To Client",
                text: "Are you sure?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var id = $(this).data("id");

                        $.ajax({
                            type: "GET",
                            url: "sendinvoice/" + id,
                            success: function (data) {
                                swal("Good job!", "Send Email Successfully!", "success");
                            },
                            error: function (data) {
                                swal("Errors!", "Request Fail!", "error");
                            }
                        });
                    }
                });
        });

        //edit invoice
        $("#InvoiceTable").on("click", ".editInvoice", function () {
            var invoice_id = $(this).data('id');
            document.getElementById("invoice_hdn").value = invoice_id;

            $.ajax({
                type: "GET",
                url: "invoice/" + invoice_id + '/edit',
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
                    $('#edit_is_split').selectpicker('val', data.is_split);
                },
                error: function (data) {
                    console.log('Error:', data);
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
            // console.log(cxmAmount + ' | ' + cxmTotalAmount + ' <|> ' + cxmGrossTotalAmount);

            cxmGrossTotalAmountInt = parseInt(cxmGrossTotalAmount);
            cxmPaymentLimitInt = parseInt("{{env('PAYMENT_LIMIT')}}");

            if (cxmGrossTotalAmountInt <= cxmPaymentLimitInt) {

                var bid = $('#invoice_hdn').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "/invoice/" + bid,
                    method: 'post',
                    data: $(this).serialize(), // get all form field value in serialize form
                    success: function (result) {
                        $("#editInvoiceModal").modal('hide');
                        swal("Good job!", "Invoice successfully Updated!", "success");
                        setTimeout(function () {
                            window.location = '{{url("invoice")}}';
                        }, 2000);
                    },
                    error: function () {
                        swal("Error!", "Request Fail!", "error");
                    }
                });

            } else {
                swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
            }
        });

        // publish Invoice
        $("#InvoiceTable").on("click", ".publishInvoice", function () {

            swal({
                title: "Publish Invoice",
                text: "Are you sure?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var id = $(this).data("id");

                        $.ajax({
                            type: "GET",
                            url: "publishinvoice/" + id,
                            success: function (data) {
                                swal("Good job!", "Publish Invoice Successfully!", "success");
                                setInterval('location.reload()', 1000);
                            },
                            error: function (data) {
                                swal("Errors!", "Request Fail!", "error");
                            }
                        });
                    }
                });
        });

        function isJSON(str) {
            try {
                JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        }

        // upsale button
        $("#InvoiceTable").on("click", ".upSalePayment", function () {
            var invoice_id = $(this).data('id');
            var clientId = $(this).attr('data-cxm-client-id');
            $("#invoice_key").val(invoice_id);
            // console.log(invoice_id + ' : ' + clientId);
            {{--console.log('{{Config::get('app.privateKey')}}');--}}


            $.ajax({
                type: "GET",
                datatype: 'JSON',
                url: "client_card_info/" + clientId,
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

        // submit upsale
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
                            {{--url: "{{ route('UserUpsalePayment') }}",--}}
                            url: "{{ route('user_upsale_multi_payment') }}",
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

                                if (res.payment_gateway == 'none') {
                                    swal('error', 'Please Contact Support or try again later.', "error");

                                    $('.page-loader-wrapper').css('display', 'none');
                                    $("#upsalePaymentModal").modal('hide');
                                    return false;
                                }


                                if (t.payment_gateway == 'authorize') {
                                    if (t.code == 1) {
                                        swal('success', 'Payment has been done Successfully.', "success");
                                        setTimeout(function () {
                                            window.location.reload();
                                        }, 2000);
                                    } else {
                                        swal('error', t.message);
                                    }
                                }

                                if (t.payment_gateway == 'expigate') {
                                    if (t.message.toLowerCase() == 'success' || t.message.toLowerCase() == 'approved') {
                                        swal('success', 'Payment has been done Successfully.', "success");
                                        setTimeout(function () {
                                            window.location.reload();
                                        }, 2000);
                                    } else {
                                        swal('error', t.message);
                                    }
                                }

                                if (t.payment_gateway == 'payarc') {
                                    if (t.message.toLowerCase() == 'success' || t.message.toLowerCase() == 'approved') {
                                        swal('success', 'Payment has been done Successfully.', "success");
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
    });
</script>
