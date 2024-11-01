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

        var TableId = $('table').first().attr('id');
        if (TableId) {
            if (TableId === "ThirdPartyRoleTable") {
                $('#ThirdPartyRoleTable').DataTable().destroy();
                $('#ThirdPartyRoleTable').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                    pageLength: 5,
                    order: [[0, 'desc']],
                    scrollX: false,
                });
                $('[type=search],.bs-searchbox input[type=text]').each(function () {
                    var randomNumber = getRandomInt(111111111, 999999999);
                    $(this).attr('id', "dt-search-box-" + randomNumber);
                });

            } else if (TableId === "ClientTable") {
                $('#ClientTable').DataTable().destroy();
                $('#ClientTable').DataTable({
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
            }
        }

        function createRowHtml(data, key) {
            let invoice_td = ''
            if (data.invoice_id) {
                invoice_td = `
                            <a class="text-warning invoice-trigger" data-invoice-num="${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}" href="#${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}">${data.get_invoice && data.get_invoice.invoice_num ? data.get_invoice.invoice_num : ""}</a>
                            <div class="">
                                <span class="badge badge-info rounded-pill">${data.invoice_id}</span>
                            </div>`;
            }
            return `<tr id="tr-${data.id}">
                        <td class="align-middle">${key}</td>
                        <td class="align-middle">
                            ${invoice_td}
                        </td>
                        <td class="align-middle">${data.order_id ?? ""}</td>
                        <td class="align-middle">${data.order_status ? convertValue(data.order_status, options.order_status) : ""}</td>
                        <td class="align-middle td-make-desc-short" title="${data.description ? data.description : ""}">${data.description ? data.description.substring(0, 20) + (data.description.length > 20 ? '...' : '') : ""}</td>
                        <td class="align-middle">${data.amount ? '$' + data.amount : ""}</td>
                        <td class="align-middle">${data.transaction_id ?? ''}</td>
                        <td class="align-middle">${convertValue(data.merchant_type, options.merchant_type)}</td>
                        <td class="align-middle">${convertValue(data.payment_status, options.payment_status)}</td>
                    </tr>`;
        }

        function convertValue(value, options) {
            return options[value] || 'None';
        }

        const options = {
            merchant_type: {1: 'Authorize', 2: 'Expigate', 3: 'PayArc', 4: 'Paypal'},
            payment_status: {0: 'Pending', 1: 'In Review', 2: 'Completed'},
            order_status: {
                'Order Placed': 'Order Placed',
                'Shipped': 'Shipped',
                'Delivered': 'Delivered',
                'On Hold': 'On Hold'
            }
        };
        @if(isset($client))
        /** On Change Team Show Agent List And Clients & On Change Client Show Paid Invoice List */
        $('#client-spending-btn').on('click', function () {
            spending_request();
        });
        @endif

        @if(auth()->user()->type == 'third-party-user')
        spending_request();
        @endif

        function spending_request() {
            // window.location.hash = 'client-spending';
            if (!$("#loader_row")) {
                $('#ThirdPartyRoleTable').DataTable().row.add($(`<tr id="loader_row"><td></td><td></td><td></td><td></td><td>Loading...</td><td></td><td></td><td></td><td></td></tr>`)).invalidate().draw(false);
            } else {
                $("#loader_row").show();
            }
            var url = `{{route('user.client.spending', [$client->id ?? null])}}`;
            AjaxRequestGetPromise(url, null, null, false, `{{route('client.index')}}`, false, false, true).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    if ($("#loader_row")) {
                        $("#loader_row").hide();
                        $('#ThirdPartyRoleTable').DataTable().row($("#loader_row")).remove().draw(false);
                    }
                    let table = $('#ThirdPartyRoleTable').DataTable();
                    if (table) {
                        table.clear().draw(false)
                        res.data.forEach(function (val, key) {
                            table.row.add($(createRowHtml(val, key + 1))).invalidate().draw(false);
                        })
                    }
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                if ($("#loader_row")) {
                    $("#loader_row").hide();
                }
                loading_div.css('display', 'none');
            })
        }
    });


    $("#projectTable").on("click", ".statusChange", function () {
        console.log('test');
        var project_id = $(this).data('id');
        document.getElementById("status_hdn").value = project_id;
        console.log(project_id);
    });

    //Assing Brands to Team
    $('#changeProjectStatus').on('click', function (e) {
        e.preventDefault();

        var project_id = $('#status_hdn').val();
        var ProjectStatus = $('#project-status').val();

        console.log(project_id);
        console.log(ProjectStatus);

        if (ProjectStatus != '') {
            console.log(ProjectStatus);
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "{{ route('changeClientProjectStatus') }}",
                data: {'project_id': project_id, 'projectStatus': ProjectStatus},

                success: function (data) {
                    $("#changeStatusModal").modal('hide');
                    swal("Good job!", "Status change successfully!", "success");
                    console.log(data);
                    setInterval('location.reload()', 2000);        // Using .reload() method.
                },
                error: function (data) {
                    console.log(data);
                    swal("Error!", "Request Fail!", "error");
                }
            });
        } else {
            swal("Error!", "Select Brand", "error");
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
                        url: "{{url('sendinvoice/')}}/" + id,
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

    $("#InvoiceTable").on("click", ".copy-url", function () {
        var copyText = $(this).attr('id');
        $(this).css('background-color', '#f00');

        console.log(copyText);
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
        console.log('Edit Invoice');
        var invoice_id = $(this).data('id');
        document.getElementById("invoice_hdn").value = invoice_id;

        $.ajax({
            type: "GET",
            url: "{{url('invoice/')}}/" + invoice_id + '/edit',
            //url: "invoice/"+invoice_id+'/edit',
            success: function (data) {
                console.log(data);

                var cxmToDay = (data.due_date ? new Date(data.due_date) : new Date());
                let cxmDueDate = cxmToDay.getFullYear() + '-' + (cxmToDay.getMonth() + 1) + '-' + cxmToDay.getDate();
                console.log(cxmDueDate);

                $('#edit_amount').val(data.final_amount);
                $('#edit_due_date').val(cxmDueDate);
                // $('#edit_brand_key').val(data.brand_key).prop('selected', true);
                // $('#edit_agent_id').val(data.agent_id).prop('selected', true);
                // $('#sales_type').val(data.sales_type).prop('selected', true);

                $('#edit_brand_key').selectpicker('val', data.brand_key);
                $('#edit_agent_id').selectpicker('val', data.agent_id);
                $('#edit_sales_type').selectpicker('val', data.sales_type);
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });

    //update Invoice
    $('#invoice_update_form').on('submit', function (e) {
        e.preventDefault();

        var bid = $('#invoice_hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            //url: "/invoice/"+bid,
            url: "{{url('invoice/')}}/" + bid,
            method: 'post',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (result) {
                console.log(result);
                $("#editInvoiceModal").modal('hide');
                swal("Good job!", "Brand successfully Updated!", "success");
                setInterval('location.reload()', 1000);
            },
            error: function () {
                swal("Error!", "Request Fail!", "error");
            }
        });
    });

    //edit Project
    $("#projectTable").on("click", ".editproject", function () {
        console.log('Edit project');
        var project_id = $(this).data('id');
        document.getElementById("project_hdn").value = project_id;
        console.log(project_id);
        $('#edit_project_brand_key').val([]).selectpicker('refresh')

        let url = "{{ auth()->guard('admin')->check() ? url('admin/adminproject/') : url('project/') }}/" + project_id + '/edit';

        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                console.log(data);

                var cxmToDay = (data.project_date_start ? new Date(data.project_date_start) : new Date());
                let cxmTodayDate = cxmToDay.getFullYear() + '-' + (cxmToDay.getMonth() + 1) + '-' + cxmToDay.getDate();
                $('#edit_project_brand_key').val(data.brand_key).selectpicker('refresh');
                $('#edit_project_agent_id').val(data.agent_id).prop('selected', true);
                $('#edit_project_logo_category').val(data.category_id).prop('selected', true);
                $('#edit_project_title').val(data.project_title);
                $('#edit_project_description').val(data.project_description);
                // $('#edit_project_start_date').val(data.project_date_start);
                $('#edit_project_start_date').val('2022-11-29');
                $('#edit_project_due_date').val(cxmTodayDate);
                $('#edit_project_cost').val(data.project_cost);

            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
    });


    //Create Client Invoice
    $('#create-client-invoice').on('submit', function (e) {
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
                url: "{{ route('createClientInvoice') }}",
                method: 'POST',
                data: $(this).serialize(), // get all form field value in serialize form
                success: function (data) {
                    $('.page-loader-wrapper').css('display', 'none');
                    $("#create-client-invoice")[0].reset();
                    console.log(data);
                    $("#cxmInvoiceModal").modal('hide');
                    swal("Good job!", "Invoice successfully Created!", "success");
                    setInterval('location.reload()', 1000);
                },
                error: function () {
                    $('.page-loader-wrapper').css('display', 'none');
                    swal("Errors!", "Request Fail!", "error");
                }
            });
        } else {
            $('.page-loader-wrapper').css('display', 'none');
            swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
        }


    });

    //Create Client project
    $('#create_client_project').on('submit', function (e) {
        e.preventDefault();
        console.log('tet');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('createClientProject') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                $("#create_client_project")[0].reset();
                console.log(data);
                $("#cxmProjectModal").modal('hide');

                swal("Good job!", "Project successfully Created!", "success");
                setInterval('location.reload()', 1000);
            },
            error: function () {
                $('.page-loader-wrapper').css('display', 'none');
                swal("Errors!", "Request Fail!", "error");
            }
        });
    });

    //Create Client Direct payment
    $('#create-client-payment').on('submit', function (e) {
        e.preventDefault();
        console.log('test');
        $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ route('createProjectPayment') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                $('.page-loader-wrapper').css('display', 'none');
                $("#create-client-payment")[0].reset();
                console.log(data);
                $("#cxmPaymentModal").modal('hide');

                swal("Good job!", "Invoice successfully Created!", "success");
                setInterval('location.reload()', 1000);
            },
            error: function () {
                swal("Errors!", "Request Fail!", "error");
            }
        });
    });


    //Create Client
    $('#client-Form').on('submit', function (e) {
        e.preventDefault();
        console.log('create Client');
        $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('client.store') }}",
            method: 'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function (data) {
                $('.page-loader-wrapper').css('display', 'none');
                $("#client-Form")[0].reset();
                $("#clientModal").modal('hide');

                swal(data.title, data.message, data.status);
                setTimeout(function () {
                    window.location = '{{url("client")}}';
                }, 2000);
            },
            error: function () {
                $('.page-loader-wrapper').css('display', 'none');
                swal("Errors!", "Request Fail!", "error");
            }
        });
    });

    $('#projects').on('change', function () {
        console.log('New Project');
        var projectValue = $(this).val();
        console.log(projectValue);
        if (projectValue == 'new') {
            console.log('New client');

            //document.getElementById('projects').value= null;

            $('#projectTitle, #projectTileBlock').show();

            $('#projectTitle').attr('required', true);
            // $('#projectTitle').val('');
        } else {
            $('#projectTitle, #projectTileBlock').hide();
            $('#projectTitle').removeAttr('required');
        }

    });
</script>
