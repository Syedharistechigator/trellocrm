<script>
    /** => Developer Michael Update <= **/

    function setDefaultPaymentUrl() {
        $('#payment-url').val('https://secure.expigate.com/api/transact.php');
    }
    $(document).ready(function () {
        if (!$('#payment-url').val()) {
            setDefaultPaymentUrl();
        }
        $('#merchant_type').change(function () {
            if ($(this).val() === '1') {
                $('#payment-url').val('https://merchantstronghold.transactiongateway.com/api/transact.php');
            } else {
                setDefaultPaymentUrl();
            }
        });
        var loading_div = $('.loading_div')
        /** Record Create/Update Function */
        $('form').on('submit', function (e) {
            e.preventDefault();
            let [url, msg] = $(this).attr('id') === "expigate_update_form" ? ['{{ route('admin.payment.method.expigate.update','/')}}/' + $('#hdn').val(), 'Record updated successfully'] : ($(this).attr('id') === "expigate_create_form"  ? ['{{ route('admin.payment.method.expigate.store') }}', 'Record created successfully'] : []);
            if (url === null) {
                return false;
            }
            AjaxRequestPostPromise(url, new FormData(this), msg, false, '{{route("admin.payment.method.expigate.index")}}', true, true, false).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        $(".sandbox-mode , .change-status").on("change", function (e) {
            e.preventDefault();
            var [url, msg, field, value,toastType] = $(this).hasClass('sandbox-mode') ? ['{{ route('admin.payment.method.expigate.change.mode') }}', ($(this).prop('checked') === true ? 'Sandbox Mode enabled successfully!' : 'Production Mode enabled successfully!'), 'mode', ($(this).prop('checked') === true ? 1 : 0 ) , $(this).prop('checked') === true ? 'error':'success'] : ($(this).hasClass('change-status') ? ['{{ route('admin.payment.method.expigate.change.status') }}', ($(this).prop('checked') === true ? 'Payment Method enabled successfully!' : 'Payment Method disabled successfully!'), 'status', ($(this).prop('checked') === true ? 1 : 0),$(this).prop('checked') === true ? 'success':'error'] : []);
            if (url === null) {
                return false;
            }
            AjaxRequestGetPromise(url, {expigate_id: $(this).data('id'),[field]: value}, msg, false, null, false, true, true,false,toastType).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
    });


    {{--$(document).ready(function () {--}}
    {{--    --}}

    {{--    $('#expigate_create_form').on('submit', function (e) {--}}
    {{--        e.preventDefault();--}}
    {{--        console.log('test');--}}
    {{--        $.ajaxSetup({--}}
    {{--            headers: {--}}
    {{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--            }--}}
    {{--        });--}}
    {{--        $.ajax({--}}
    {{--            url: '{{route('admin.payment.method.expigate.store')}}',--}}
    {{--            method: 'post',--}}
    {{--            data: $(this).serialize(),--}}
    {{--            success: function (result) {--}}
    {{--                console.log(result);--}}
    {{--                swal("Good job!", "Successfully Create!", "success");--}}

    {{--                window.location = '{{route("admin.payment.method.expigate.index")}}';--}}
    {{--            },--}}
    {{--            error: function () {--}}
    {{--                swal("Error!", "Request Fail!", "error");--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}
    {{--    /** Update Expigate payment method */--}}
    {{--    $('#expigate_update_form').on('submit', function (e) {--}}
    {{--        e.preventDefault();--}}
    {{--        console.log('test');--}}
    {{--        var mid = $('#hdn').val();--}}

    {{--        $.ajaxSetup({--}}
    {{--            headers: {--}}
    {{--                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--            }--}}
    {{--        });--}}

    {{--        $.ajax({--}}
    {{--            url: '{{ route('admin.payment.method.expigate.update','/')}}/' + mid,--}}
    {{--            method: 'post',--}}
    {{--            data: $(this).serialize(), // get all form field value in serialize form--}}
    {{--            success: function (result) {--}}
    {{--                console.log(result);--}}
    {{--                swal("Good job!", "Successfully Updated!", "success");--}}
    {{--                setInterval('location.reload()', 2000);--}}
    {{--            },--}}
    {{--            error: function () {--}}
    {{--                swal("Error!", "Request Fail!", "error");--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}
    {{--    /** Sandbox mode on edit*/--}}
    {{--    $(".sandbox-mode").on("change", function () {--}}
    {{--        var mode = $(this).prop('checked') == true ? 1 : 0;--}}
    {{--        var expigate_id = $(this).data('id');--}}

    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            dataType: "json",--}}
    {{--            url: '{{route('admin.payment.method.expigate.change.mode')}}',--}}
    {{--            data: {'mode': mode, 'expigate_id': expigate_id},--}}
    {{--            success: function (data) {--}}
    {{--                swal("Good job!", "Sandbox Mode change successfully!", "success");--}}
    {{--                console.log(data.success)--}}
    {{--            },--}}
    {{--            error: function () {--}}
    {{--                swal("Error!", "Request Fail!", "error");--}}
    {{--            }--}}
    {{--        });--}}

    {{--    });--}}

    {{--    /** Enable Payment Method */--}}
    {{--    $(document).on("change", ".change-status", function () {--}}
    {{--        var status = $(this).prop('checked') == true ? 1 : 0;--}}
    {{--        var expigate_id = $(this).data('id');--}}
    {{--        $.ajax({--}}
    {{--            type: "GET",--}}
    {{--            dataType: "json",--}}
    {{--            url: '{{route('admin.payment.method.expigate.change.status')}}',--}}
    {{--            data: {'status': status, 'expigate_id': expigate_id},--}}
    {{--            success: function (data) {--}}
    {{--                swal("Good job!", "Status change successfully!", "success");--}}
    {{--                console.log(data.success)--}}
    {{--            },--}}
    {{--            error: function () {--}}
    {{--                swal("Error!", "Request Fail!", "error");--}}
    {{--            }--}}
    {{--        });--}}
    {{--    });--}}

    {{--});--}}
</script>
