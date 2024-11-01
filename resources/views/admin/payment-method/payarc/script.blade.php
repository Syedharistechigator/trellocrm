<script>
    /** => Developer Michael Update <= **/
    $(document).ready(function () {
        var loading_div = $('.loading_div')
        /** Record Create/Update Function */
        $('form').on('submit', function (e) {
            e.preventDefault();
            let [url, msg] = $(this).attr('id') === "payarc_update_form" ? ['{{ route('admin.payment.method.payarc.update','/')}}/' + $('#hdn').val(), 'Record updated successfully'] : ($(this).attr('id') === "payarc_create_form"  ? ['{{ route('admin.payment.method.payarc.store') }}', 'Record created successfully'] : []);
            if (url === null) {
                return false;
            }
            AjaxRequestPostPromise(url, new FormData(this), msg, false, '{{route("admin.payment.method.payarc.index")}}', true, true, false).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        $(".sandbox-mode , .change-status").on("change", function (e) {
            e.preventDefault();
            var [url, msg, field, value,toastType] = $(this).hasClass('sandbox-mode') ? ['{{ route('admin.payment.method.payarc.change.mode') }}', ($(this).prop('checked') === true ? 'Sandbox Mode enabled successfully!' : 'Production Mode enabled successfully!'), 'mode', ($(this).prop('checked') === true ? 1 : 0 ) , $(this).prop('checked') === true ? 'error':'success'] : ($(this).hasClass('change-status') ? ['{{ route('admin.payment.method.payarc.change.status') }}', ($(this).prop('checked') === true ? 'Payment Method enabled successfully!' : 'Payment Method disabled successfully!'), 'status', ($(this).prop('checked') === true ? 1 : 0),$(this).prop('checked') === true ? 'success':'error'] : []);
            if (url === null) {
                return false;
            }
            AjaxRequestGetPromise(url, {payarc_id: $(this).data('id'),[field]: value}, msg, false, null, false, true, true,false,toastType).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
    });

    {{--/** Create PayArc payment methods */--}}
    {{--$('#payarc_create_form').on('submit', function (e) {--}}
    {{--    e.preventDefault();--}}
    {{--    console.log('test');--}}
    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}
    {{--    $.ajax({--}}
    {{--        url: '{{route('admin.payment.method.payarc.store')}}',--}}
    {{--        method: 'post',--}}
    {{--        data: $(this).serialize(), // get all form field value in serialize form--}}
    {{--        success: function (result) {--}}
    {{--            console.log(result);--}}
    {{--            swal("Good job!", "Successfully Create!", "success");--}}

    {{--            window.location = '{{route("admin.payment.method.payarc.index")}}';--}}
    {{--        },--}}
    {{--        error: function () {--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}
    {{--/** Update PayArc payment method */--}}
    {{--$('#payarc_update_form').on('submit', function (e) {--}}
    {{--    e.preventDefault();--}}
    {{--    console.log('test');--}}
    {{--    var mid = $('#hdn').val();--}}

    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    $.ajax({--}}
    {{--        url: '{{ route('admin.payment.method.payarc.update','/')}}/'+ mid,--}}
    {{--        method: 'post',--}}
    {{--        data: $(this).serialize(), // get all form field value in serialize form--}}
    {{--        success: function (result) {--}}
    {{--            console.log(result);--}}
    {{--            swal("Good job!", "Successfully Updated!", "success");--}}
    {{--            setInterval('location.reload()', 2000);--}}
    {{--        },--}}
    {{--        error: function () {--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}
    {{--/** Sandbox mode on edit*/--}}
    {{--$(".sandbox-mode").on("change", function () {--}}
    {{--    var mode = $(this).prop('checked') == true ? 1 : 0;--}}
    {{--    var payarc_id = $(this).data('id');--}}

    {{--    $.ajax({--}}
    {{--        type: "GET",--}}
    {{--        dataType: "json",--}}
    {{--        url: '{{route('admin.payment.method.payarc.change.mode')}}',--}}
    {{--        data: {'mode': mode, 'payarc_id': payarc_id},--}}
    {{--        success: function (data) {--}}
    {{--            swal("Good job!", "Sandbox Mode change successfully!", "success");--}}
    {{--            console.log(data.success)--}}
    {{--        },--}}
    {{--        error: function (data) {--}}
    {{--            $('.page-loader-wrapper').css('display', 'none');--}}
    {{--            var errorMessage = "An unknown error occurred.";--}}
    {{--            if (data.responseJSON) {--}}
    {{--                if (data.responseJSON.errors) {--}}
    {{--                    errorMessage = Object.values(data.responseJSON.errors)[0][0];--}}
    {{--                } else if (data.responseJSON.error) {--}}
    {{--                    errorMessage = data.responseJSON.error;--}}
    {{--                } else if (data.responseJSON.message) {--}}
    {{--                    errorMessage = data.responseJSON.message;--}}
    {{--                }--}}
    {{--            }--}}
    {{--            swal('Error', errorMessage, 'error');--}}
    {{--        }--}}
    {{--    });--}}

    {{--});--}}

    {{--/** Enable Payment Method */--}}
    {{--$(document).on("change", ".change-status", function () {--}}
    {{--    var status = $(this).prop('checked') == true ? 1 : 0;--}}
    {{--    var payarc_id = $(this).data('id');--}}
    {{--    $.ajax({--}}
    {{--        type: "GET",--}}
    {{--        dataType: "json",--}}
    {{--        url: '{{route('admin.payment.method.payarc.change.status')}}',--}}
    {{--        data: {'status': status, 'payarc_id': payarc_id},--}}
    {{--        success: function (data) {--}}
    {{--            swal("Good job!", "Status change successfully!", "success");--}}
    {{--            console.log(data.success)--}}
    {{--        },--}}
    {{--        error: function () {--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}
</script>
