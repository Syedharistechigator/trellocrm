<script>
    $(document).ready(function () {
        var loading_div = $('.loading_div');
        $('#create-customer-sheet-modal, .editCustomerSheet').click(function () {
            $('[type=search],select[name="CustomerSheetTable_length"]').prop('disabled', true);
        });
        $('#customerSheetModal, #editCustomerSheetModal').on('hidden.bs.modal', function () {
            $('[type=search],select[name="CustomerSheetTable_length"]').prop('disabled', false);
        });
        @if(auth()->user()->type == 'tm-user' || auth()->user()->type == 'tm-client')

        function createRowData(data,attachments) {
            let viewAttachmentButton = '';
            if (attachments && attachments.length > 0) {
                viewAttachmentButton = `<button data-id="${data.id}" id="attachment-id-${data.id}" title="View Attachment" class="btn btn-info btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                                </button>`;
            }
            return [
                data.id,
                data.customer_id,
                data.customer_name,
                data.customer_email,
                data.customer_phone,
                data.order_date,
                convertValue(data.order_type, options.orderType),
                convertValue(data.filling, options.filling),
                '$ ' + data.amount_charged,
                convertValue(data.order_status, options.orderStatus),
                convertValue(data.communication, options.communication),
                data.project_assigned,
                `${viewAttachmentButton}
                <button data-id="${data.id}" id="add-attachment-id-${data.id}" title="Add Attachment" class="btn btn-info btn-sm btn-round addAttachments" data-toggle="modal" data-target="#addAttachmentsModal">
                                                    <i class="zmdi zmdi-plus-circle"></i></button>
                <button data-id="${data.id}" title="Edit" class="btn btn-info btn-sm btn-round editCustomerSheet" data-toggle="modal" data-target="#editCustomerSheetModal">
                    <i class="zmdi zmdi-edit"></i>
                </button>
                <a title="Delete" data-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton" style="color: white;"><i class="zmdi zmdi-delete"></i></a>`
            ];
        }

        function convertValue(value, options) {
            return options[value] || 'None';
        }

        const options = {
            orderType: {1: 'Copyright', 2: 'Trademark', 3: 'Attestation'},
            filling: {1: 'Logo', 2: 'Slogan', 3: 'Business Name'},
            @if(auth()->user()->type == 'tm-user')
            orderStatus: {
                1: '<span class="badge bg-grey rounded-pill">requested</span>',
                2: '<span class="badge bg-amber rounded-pill">applied</span>',
                3: '<span class="badge bg-amber rounded-pill">received</span>',
                4: '<span class="badge bg-danger rounded-pill">rejected</span>',
                5: '<span class="badge bg-red rounded-pill">objection</span>',
                6: '<span class="badge badge-success rounded-pill">approved</span>',
                7: '<span class="badge bg-red rounded-pill">delivered</span>',
            },
            communication: {
                1: 'out-of-reached',
                2: 'skeptic',
                3: 'satisfied',
                4: 'refunded',
                5: 'refund requested',
                6: 'do-not-call',
                7: 'not-interested',
            },
            @endif
        };

        function createRowHtml(data,attachments) {
            let viewAttachmentButton = '';
            if (attachments && attachments.length > 0) {
                viewAttachmentButton = `<button data-id="${data.id}" id="attachment-id-${data.id}" title="View Attachment" class="btn btn-info btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal">
                                    <i class="zmdi zmdi-attachment-alt"></i>
                                </button>`;
            }
            return `<tr id="tr-${data.id}">
            <td class="align-middle">${data.id}</td>
            <td class="align-middle">${data.customer_id}</td>
            <td class="align-middle">${data.customer_name}</td>
            <td class="align-middle">${data.customer_email}</td>
            <td class="align-middle">${data.customer_phone}</td>
            <td class="align-middle text-nowrap">${data.order_date}</td>
            <td class="align-middle">${convertValue(data.order_type, options.orderType)}</td>
            <td class="align-middle">${convertValue(data.filling, options.filling)}</td>
            <td class="align-middle text-center">$ ${data.amount_charged}</td>
            @if(Auth::user()->type == 'tm-user')
                <td class="align-middle text-center">${convertValue(data.order_status, options.orderStatus)}</td>
                <td class="align-middle">${convertValue(data.communication, options.communication)}</td>
                <td class="align-middle">${data.project_assigned}</td>
            @endif
            <td class="align-middle text-nowrap" id="action-btn-${data.id}">
                ${viewAttachmentButton}
                @if(Auth::user()->type == 'tm-user')
                    <button data-id="${data.id}" id="add-attachment-id-${data.id}" title="Add Attachment" class="btn btn-info btn-sm btn-round addAttachments" data-toggle="modal" data-target="#addAttachmentsModal">
                                                        <i class="zmdi zmdi-plus-circle"></i></button>
                    <button data-id="${data.id}" title="Edit" class="btn btn-info btn-sm btn-round editCustomerSheet" data-toggle="modal" data-target="#editCustomerSheetModal">
                        <i class="zmdi zmdi-edit"></i>
                    </button>
                    <a title="Delete" data-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round delButton"><i class="zmdi zmdi-delete"></i></a>
               @endif
            </td>
        </tr>
    `;
        }

        $('#create-customer-sheet-modal').on('click', function () {
            $('#create-customer-sheet-form')[0].reset();
        });
        /** Create Customer Sheet */
        $('#create-customer-sheet-form').on('submit', function (e) {
            @php
                [$prefix, $labelPrefix] = (Auth::user()->type === 'tm-client') ? ["", ""] : ["customer_", "Customer "];
            @endphp
            e.preventDefault();
            var url = '{{ route('user.customer.sheet.store') }}';
            var formData = new FormData();
            formData.append("{{$prefix}}name", $('#{{$prefix}}name').val())
            formData.append("{{$prefix}}email", $('#{{$prefix}}email').val())
            formData.append("{{$prefix}}phone", $('#{{$prefix}}phone').val())
            formData.append("order_date", $('#order_date').val())
            formData.append("order_type", $('#order_type').val())
            formData.append("filling", $('#filling').val())
            formData.append("amount_charged", $('#amount_charged').val())
            @if(Auth::user()->type == 'tm-user')
            formData.append("order_status", $('#order_status').val())
            formData.append("communication", $('#communication').val())
            formData.append("project_assigned", $('#project_assigned').val())
            @endif
            var attachments = $('#cs-attachments')[0].files;
            if (attachments.length > 0) {
                for (var i = 0; i < attachments.length; i++) {
                    formData.append('attachments[]', attachments[i]);
                }
            }

            loading_div.css('display', 'flex');
            var response = AjaxRequestPostPromise(url, formData, null, false, null, false, false, true)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            createToast('success', 'Record created successfully');
                            $('#CustomerSheetTable').DataTable().row.add($(createRowHtml(res.data,res.attachments))).invalidate().draw(false)
                            $("#customerSheetModal").modal('hide');
                            $('#create-customer-sheet-form')[0].reset();
                        } else {
                            createToast('error', 'Failed to create record');
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

        @endif
        @if(auth()->user()->type == 'tm-user')
        /** Edit Customer Sheet */
        $("#CustomerSheetTable").on("click", ".editCustomerSheet", function () {
            $('#update-customer-sheet-form')[0].reset();
            loading_div.css('display', 'flex');
            var url = '{{ route('user.customer.sheet.edit','/')}}/' + $(this).data('id');
            var response = AjaxRequestGetPromise(url, null, null, false, null, false, false, true, false)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            $('#editCustomerSheetModal').attr('data-id', res.data.id);
                            $("#edit_customer_name").val(res.data.customer_name);
                            $("#edit_customer_email").val(res.data.customer_email);
                            $("#edit_customer_phone").val(res.data.customer_phone);
                            $("#edit_order_date").text(res.data.order_date);
                            $("#edit_order_type").selectpicker('val', res.data.order_type);
                            $("#edit_filling").selectpicker('val', res.data.filling);
                            $("#edit_amount_charged").val(res.data.amount_charged);
                            $("#edit_order_status").selectpicker('val', res.data.order_status);
                            $("#edit_communication").selectpicker('val', res.data.communication);
                            $("#edit_project_assigned").val(res.data.project_assigned);
                        } else {
                            createToast('error', 'Failed to fetch record');
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

        /** Update Customer Sheet */
        $('#editCustomerSheetModal').on('submit', function (e) {
            e.preventDefault();

            var url = '{{ route('user.customer.sheet.update','/')}}/' + $('#editCustomerSheetModal').attr('data-id');
            var formData = new FormData();
            formData.append("customer_name", $('#edit_customer_name').val())
            formData.append("customer_email", $('#edit_customer_email').val())
            formData.append("customer_phone", $('#edit_customer_phone').val())
            formData.append("order_date", $('#edit_order_date').val())
            formData.append("order_type", $('#edit_order_type').val())
            formData.append("filling", $('#edit_filling').val())
            formData.append("amount_charged", $('#edit_amount_charged').val())
            formData.append("order_status", $('#edit_order_status').val())
            formData.append("communication", $('#edit_communication').val())
            formData.append("project_assigned", $('#edit_project_assigned').val())

            loading_div.css('display', 'flex');
            var response = AjaxRequestPostPromise(url, formData, null, false, null, false, false, true)
                .then((res) => {
                        if (res.status && res.status === 1) {

                            createToast('success', 'Record updated successfully');

                            var table = $('#CustomerSheetTable').DataTable();
                            var rowData = createRowData(res.data,res.attachments);
                            var rowIndex = table.row('#tr-' + res.data.id).index();
                            table.row(rowIndex).data(rowData).draw(false);
                            $('#editCustomerSheetModal').modal('hide');
                        } else {
                            createToast('error', 'Failed to update record');
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



        /** Add Attachments modal */
        $(document).on('click', '.addAttachments', function (e) {
            e.preventDefault();
            $('#addAttachmentsModal').attr('data-id', $(this).attr('data-id'));
            $("#addMoreAttachmentSaveBtn").show();
        });

        /** Submit More Attachments */
        $('#add-customer-sheet-attachment-form').on('submit', function (e) {
            e.preventDefault();
            var customerId  =  $('#addAttachmentsModal').attr('data-id');
            if(!customerId ){
                createToast('error','need to reload');
                return;
            }
            var url = '{{ route('user.customer.sheet.add.attachment','/')}}/' + customerId ;

            var formData = new FormData();

            var attachments = $('#add_more_attachments')[0].files;
            if (attachments.length > 0) {
                for (var i = 0; i < attachments.length; i++) {
                    formData.append('attachments[]', attachments[i]);
                }
            }else{
                createToast('error', 'No Attachments found. PLEASE ADD SOME.');
                return false;
            }

            loading_div.css('display', 'flex');
            var response = AjaxRequestPostPromise(url, formData, null, false, null, false, false, true,false)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            createToast('success', 'Attachment added successfully');
                            $("#addAttachmentsModal").modal('hide');

                            var viewAttachmentButtonExists = $('#attachment-id-' + customerId).length > 0;
                            if (!viewAttachmentButtonExists) {
                                $('#action-btn-' + customerId).prepend('<button data-id="' + customerId + '" id="attachment-id-' + customerId + '" title="View Attachment" class="btn btn-info btn-sm btn-round viewAttachments" data-toggle="modal" data-target="#attachmentsModal"><i class="zmdi zmdi-attachment-alt"></i></button>');
                            }else{
                                $('#attachment-id-' + customerId).show();
                            }
                            $('#add-customer-sheet-attachment-form')[0].reset();

                        } else {
                            createToast('error', 'Failed to add attachment');
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



        /** Delete Attachment */
        $("#attachmentsTable").on("click", ".deleteAttachment", function () {
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
                        var attachment_id = $(this).data("attachment-id");
                        var url = '{{ route('user.customer.sheet.attachment.destroy','/')}}/' + attachment_id;
                        var res = AjaxRequestGet(url, null, 'Poof! Your record has been deleted!', false, null,true);
                        if (res && res.success) {
                            $('#attachmentsTable').DataTable().row($("#tr-attachment-" + attachment_id)).remove().draw(false);

                            if ($('#attachmentsTable').DataTable().rows().count() === 0) {
                                $("#attachment-id-"+id).hide();
                                setTimeout(function () {
                                    $("#attachmentsModal").modal('hide');
                                }, 2000);
                            }
                        }
                    } else {
                        swal("Your record is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });

        /** Delete*/
        $("#CustomerSheetTable").on("click", ".delButton", function () {
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
                        var url = '{{ route('user.customer.sheet.destroy','/')}}/' + id;

                        var res = AjaxRequestGet(url, null, 'Poof! Your record has been deleted!', false, null);
                        if (res && res.success) {
                            $('#CustomerSheetTable').DataTable().row($("#tr-" + id)).remove().draw(false);
                        }
                    } else {
                        swal("Your record is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });

        @endif

        function populateAttachmentsModal(data,index) {
            @if(auth()->user()->type != 'tm-user')
            var btn = '';
            @else
            var btn = `<a title="Delete" data-id="${data.customer_sheet_id}" data-attachment-id="${data.id}" data-type="confirm" href="javascript:void(0);" class="btn btn-danger btn-sm btn-round deleteAttachment"><i class="zmdi zmdi-delete"></i></a>`;
            @endif

            var viewLink = "{{ asset('assets/') }}/" + (data.mime_type.includes('image') ? 'images' : 'files') + "/customer-sheet/" + data.mime_type + "/" + data.file_name;

            return [
                index + 1,
                data.original_name,
                data.extension,
                data.file_size,
                `
                {{--<a title="View" href="{{ asset("assets/images/customer-sheet/").'/' }}${data.mime_type}/${data.file_name}" target="_blank" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>--}}
                <a title="View" href="${viewLink}" target="_blank" rel="noopener noreferrer" class="btn btn-info btn-sm btn-round"><i class="zmdi zmdi-open-in-new"></i></a>
                ${btn}`,
            ];
        }
        /** Show attachments modal */
        $(document).on('click', '.viewAttachments', function () {
            loading_div.css('display', 'flex');
            var url = '{{ route('user.customer.sheet.view.attachment','/')}}/' + $(this).data('id');
            var response = AjaxRequestGetPromise(url, null, null, false, null, false, false, true, false)
                .then((res) => {
                        if (res.status && res.status === 1) {
                            $('#attachmentsTable').DataTable().destroy();
                            var table = $('#attachmentsTable').DataTable({"order": [[0, "asc"]]});
                            $ ('#attachmentsTable_filter label input').attr ('id', 'dt-attachment-search-box');
                            table.clear().draw();
                            res.data.forEach(function (attachment,index) {
                                if(attachment.id) {
                                    var rowData = populateAttachmentsModal(attachment, index);
                                    table.row.add(rowData).node().id = 'tr-attachment-' + attachment.id;
                                    table.draw(false);
                                }
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

    });
</script>
