<script>
    $(document).ready(function () {
        function getParam() {
            window.location.href = "{{ route('user.payments.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val());
        }

        $('#cxmRefundModal,#paymentModal').click(function () {
            $('#brand, #date-range, [type=search]').prop('disabled', true);
        });
        $('#cxmRefundModal,#paymentModal').on('hidden.bs.modal', function () {
            $('#brand, #date-range, [type=search]').prop('disabled', false);
        });

        $('#brand').on('change', getParam);

        $('#PaymentTable').on('click', '.cxm-btn-refund', function () {
            swal({
                title: "Are you want to refund?",
                text: "Press Yes, if you want to refund your payment!",
                icon: "warning",
                buttons: {
                    cancel: {
                        text: "Cancel",
                        value: null,
                        visible: true,
                        className: "btn-warning",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Yes, refund!"
                    }
                },
                dangerMode: true,
            })
                .then((cxmRefund) => {
                    if (cxmRefund) {
                        $('#cxmRefundModal').modal('show');
                        console.log('Test Refund');
                        var payment_id = $(this).data('id');
                        console.log(payment_id);
                        $.ajax({
                            type: "GET",
                            url: "{{url('paymentdetail/')}}/" + payment_id,
                            success: function (data) {
                                console.log(data);
                                $('#payment_id').val(payment_id);
                                $('#team_key').val(data.team_key);
                                $('#brand_key').val(data.brand_key);
                                $('#invoice_id').val(data.invoice_id);
                                $('#client_id').val(data.clientid);
                                $('#agent_id').val(data.agent_id);
                                $('#auth_transaction_id').val(data.authorizenet_transaction_id);
                                $('#amount').val(data.amount);
                                $('#cxm_card').val("{{cxmEncrypt("+data.card_number+", $privateKey)}}");
                            },
                            error: function (data) {
                                console.log('Error:', data);
                            }
                        });

                    } else {
                        swal('Thank You', 'Thank you for your patience!', 'success', {buttons: false, timer: 2000});
                    }
                });
        });

        //Create refund
        $('#refund_form').on('submit', function (e) {
            e.preventDefault();
            console.log('tet');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('PaymentRefund') }}",
                method: 'POST',
                data: $(this).serialize(), // get all form field value in serialize form
                success: function (data) {
                    $("#refund_form")[0].reset();
                    console.log(data);
                    $("#cxmRefundModal").modal('hide');

                    swal("Good job!", "Successfully Payment Refund Request to QA!", "success");
                    setTimeout(function () {
                        window.location = '{{url("/userpayment")}}';
                    }, 2000);
                },
                error: function () {
                    swal("Errors!", "Request Fail!", "error");
                }
            });
        });

        @if(Auth::user()->type == 'lead' && str_contains(request()->server('SERVER_NAME'), 'uspto-filing') != false)
        /** Add Payment record from user side */
        $('#direct-payment-Form').on('submit', function (e) {
            e.preventDefault();
            $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
            console.log('Direct Payment Form');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('createPayment') }}",
                method: 'POST',
                data: $(this).serialize(), // get all form field value in serialize form
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    $("#direct-payment-Form")[0].reset();
                    console.log(data);
                    $("#paymentModal").modal('hide');

                    swal("Good job!", "Payment successfully Created!", "success");
                    setTimeout('location.reload()', 1000);
                },
                error: function () {
                    $('.page-loader-wrapper').css('display', 'none');
                    swal("Errors!", "Request Fail!", "error");
                }
            });
        });
        @endif
        $('.dropify').dropify();


        // Upload Project File
        $('#payment_varified_form').on('submit', function (e) {
            e.preventDefault();
            $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
            var form_data = new FormData(this);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: "{{ route('complianceVarified') }}",
                method: 'POST',
                dataType: "JSON",
                data: form_data,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    $("#payment_varified_form")[0].reset();
                    console.log(data);

                    $("#varifiedModal").modal('hide');

                    swal("Good job!", "Your file upload successfully!", "success")
                        .then(() => {
                            location.reload();
                        });
                },
                error: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    console.log(data);
                    swal("Errors!", "Request Fail!", "error", {buttons: false, timer: 2000});
                }
            });
        });

        /** Delete*/
        $("#PaymentTable").on("click", ".delButton", function () {
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
                        var url = '{{ route('user.payment.destroy','/')}}/' + id;

                        var res = AjaxRequestGet(url, null, 'Poof! Your record has been deleted!', false, null);
                        if (res && res.success) {
                            $('#PaymentTable').DataTable().row($("#tr-" + id)).remove().draw(false);
                        }
                    } else {
                        swal("Your record is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });

    });
</script>
