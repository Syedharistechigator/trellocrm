<script>
    /** => Developer Michael Update <= **/
    $(document).ready(function () {
        var loading_div = $('.loading_div')
        /** Record Create/Update Function */
        $('form').on('submit', function (e) {
            e.preventDefault();
            let [url, msg] = $(this).attr('id') === "update_form" ? ['{{ route('admin.payment.method.authorize.update','/')}}/' + $('#hdn').val(), 'Record updated successfully'] : ($(this).attr('id') === "create_form" ? ['{{ route('admin.payment.method.authorize.store') }}', 'Record created successfully'] : []);
            if (url === null) {
                return false;
            }
            AjaxRequestPostPromise(url, new FormData(this), msg, false, '{{route("admin.payment.method.authorize.index")}}', true, true, false).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        $(".sandbox-mode , .change-status , .authorization-mode").on("change", function (e) {
            e.preventDefault();
            var [url, msg, field, value, toastType] = $(this).hasClass('sandbox-mode') ? ['{{ route('admin.payment.method.authorize.change.mode') }}', ($(this).prop('checked') === true ? 'Sandbox Mode enabled successfully!' : 'Production Mode enabled successfully!'), 'mode', ($(this).prop('checked') === true ? 1 : 0), $(this).prop('checked') === true ? 'error' : 'success'] : ($(this).hasClass('change-status') ? ['{{ route('admin.payment.method.authorize.change.status') }}', ($(this).prop('checked') === true ? 'Payment Method enabled successfully!' : 'Payment Method disabled successfully!'), 'status', ($(this).prop('checked') === true ? 1 : 0), $(this).prop('checked') === true ? 'success' : 'error'] : ($(this).hasClass('authorization-mode') ? ['{{ route('admin.payment.method.authorize.authorization') }}', ($(this).prop('checked') === true ? 'Payment Method authorization enabled successfully!' : 'Payment Method authorization disabled successfully!'), 'authorization', ($(this).prop('checked') === true ? 1 : 0), $(this).prop('checked') === true ? 'success' : 'error'] : []));
            if (url === null) {
                return false;
            }
            AjaxRequestGetPromise(url, {
                method_id: $(this).data('id'),
                [field]: value
            }, msg, false, null, false, true, true, false, toastType).catch(() => {
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
    });

    {{--// create payment methods--}}
    {{--$('#create_form').on('submit', function(e){--}}
    {{--    e.preventDefault();--}}

    {{--    console.log('test');--}}


    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    $.ajax({--}}
    {{--        url: "{{ route('paymentmethod.store') }}",--}}

    {{--        method:'post',--}}
    {{--        data: $(this).serialize(), // get all form field value in serialize form--}}
    {{--        success: function(result){--}}
    {{--            console.log(result);--}}
    {{--            swal("Good job!", "Successfully Create!", "success");--}}

    {{--            window.location='{{url("admin/paymentmethod")}}';--}}
    {{--        },--}}
    {{--        error: function(){--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}
    {{--//update Brand--}}
    {{--$('#update_form').on('submit', function(e){--}}
    {{--    e.preventDefault();--}}

    {{--    console.log('test');--}}
    {{--    var mid = $('#hdn').val();--}}

    {{--    $.ajaxSetup({--}}
    {{--        headers: {--}}
    {{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
    {{--        }--}}
    {{--    });--}}

    {{--    $.ajax({--}}
    {{--        url: "/admin/paymentmethod/"+mid,--}}
    {{--        method:'post',--}}
    {{--        data: $(this).serialize(), // get all form field value in serialize form--}}
    {{--        success: function(result){--}}
    {{--            console.log(result);--}}
    {{--            swal("Good job!", "Successfully Updated!", "success");--}}
    {{--            setInterval('location.reload()', 2000);--}}
    {{--        },--}}
    {{--        error: function(){--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}
    {{--});--}}

    {{--// Sandbox Mode--}}
    {{--$(".sandbox-mode").on("change", function () {--}}

    {{--    console.log("change");--}}

    {{--    var mode = $(this).prop('checked') === true ? 1 : 0;--}}
    {{--    var method_id = $(this).data('id');--}}

    {{--    $.ajax({--}}
    {{--        type: "GET",--}}
    {{--        dataType: "json",--}}
    {{--        url: `{{route('admin.payment.method.authorize.change.mode')}}`,--}}
    {{--        data: {'mode': mode, 'method_id': method_id},--}}
    {{--        success: function (data) {--}}
    {{--            swal("Good job!", "Sandbox Mode change successfully!", "success");--}}
    {{--            console.log(data.success)--}}
    {{--        },--}}
    {{--        error: function () {--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}

    {{--});--}}

    {{--// Enable Payment Method--}}
    {{--$(document).on("change", ".change-status", function () {--}}

    {{--    console.log("change");--}}

    {{--    var status = $(this).prop('checked') === true ? 1 : 0;--}}
    {{--    var method_id = $(this).data('id');--}}

    {{--    $.ajax({--}}
    {{--        type: "GET",--}}
    {{--        dataType: "json",--}}
    {{--        url: `{{route('admin.payment.method.authorize.change.status')}}`,--}}
    {{--        data: {'status': status, 'method_id': method_id},--}}
    {{--        success: function (data) {--}}
    {{--            swal("Good job!", "Status change successfully!", "success");--}}
    {{--            console.log(data.success)--}}
    {{--        },--}}
    {{--        error: function () {--}}
    {{--            swal("Error!", "Request Fail!", "error");--}}
    {{--        }--}}
    {{--    });--}}

    // });
</script>
