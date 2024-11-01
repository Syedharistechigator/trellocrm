<script>
    /** Create form*/
    $('#ip_address_create_form').on('submit', function (e) {
        e.preventDefault();
        AjaxRequestPost('{{ route('admin.ip.address.store') }}', $(this).serialize(), 'Ip address created successfully!', true,'{{ route('admin.ip.address.index') }}');
    });
    /** Update form*/
    $('#ip_address_update_form').on('submit', function (e) {
        e.preventDefault();
        var id = $('#hdn').val();
        var url = '{{ route('admin.ip.address.update','/')}}/' + id;
        AjaxRequestPost(url, $(this).serialize(), 'Ip address updated successfully!');

    });
    /** Change Status */
    if ($('#IpAddressTable').length || $('#ip_address_update_form').length) {
        $(document).on("change", ".change-status", function () {
            var status = $(this).prop('checked') === true ? 1 : 0;
            var id = $(this).data('id');
            AjaxRequestPost('{{ route('admin.ip.address.change.status') }}', {
                'status': status,
                'ip_address_id': id
            }, 'Status changed successfully!', false);
        });
    }
    /** Delete*/
    $("#IpAddressTable").on("click", ".delButton", function () {
        swal({
            title: "Are you sure?",
            text: "Once deleted, not be able to recover this Ip Address!",
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
                    var url = '{{ route('admin.ip.address.destroy','/')}}/' + id;

                    AjaxRequestGet(url, null, 'Poof! Your Ip Address has been deleted!', false, '{{ route('admin.ip.address.index')}}');
                } else {
                    swal("Your Ip Address is safe!", {icon: "error", buttons: false, timer: 1000});
                }
            });
    });


    /** Trashed Blade*/
    $("#IpAddressTrashedTable").on("click", ".restoreButton", function () {
        var restoreId = $(this).data("id");
        var url = '{{ route('admin.ip.address.restore','/')}}/' + restoreId;
        AjaxRequestGet(url, null, 'Ip Address Successfully restored!', false, '{{ route('admin.ip.address.trashed')}}');
    });

    /**Restore All Ip Address*/
    $("#restoreAll").on("click", ".restoreAllButton", function () {

        swal({
            title: "Are you sure?",
            text: "want to Restore All Ip Address !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    AjaxRequestGet('{{ route('admin.ip.address.restore.all')}}', null, 'Restore all Ip Addresses successfully!', false, '{{ route('admin.ip.address.trashed')}}');
                } else {
                    swal("Your Ip Addresses not restore!");
                    console.log('error');
                }
            });
    });
    /** Restore Ip Address */
    $("#IpAddressTrashedTable").on("click", ".ip-address-force-del", function () {
        swal({
            title: "Are you sure?",
            text: "want to permanently delete !",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
            .then((willDelete) => {
                if (willDelete) {
                    var delId = $(this).data("id");
                    var url = '{{ route('admin.ip.address.force.delete','/')}}/' + delId;
                    AjaxRequestGet(url, {'ip_address_id': delId}, 'Ip Address permanent deleted successfully!', false, '{{ route('admin.ip.address.trashed')}}');
                } else {
                    swal("Your Ip Addresses not Delete!");
                    console.log('error');
                }
            });
    });

</script>
