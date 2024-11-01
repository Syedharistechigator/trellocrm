<script>

    /** Update Token Status Or Icon */
    var tokenElements = document.querySelectorAll('.token_status');
    tokenElements.forEach(function(tokenElement) {
        var expirationTimestamp = parseInt(tokenElement.getAttribute('data-expiration'));
        updateTokenStatus(expirationTimestamp, tokenElement.id);
    });
    function updateTokenStatus(expirationTimestamp, elementId) {
        var currentTime = Math.floor(Date.now() / 1000); // Current time in seconds
        var statusElement = document.getElementById(elementId); // Get the token status element

        if (currentTime > expirationTimestamp) {
            statusElement.classList.add('zmdi-close-circle', 'text-danger');
        } else {
            statusElement.classList.add('zmdi-check-circle', 'text-success');
        }
    }

    $(document).ready(function () {
        /** Status Value*/
        function getStatus(formData) {
            var statusValue = $('.change-status').prop('checked') ? 1 : 0;
            if (formData.includes('status=')) {
                formData = formData.replace(/(status=)[^&]+/, '$1' + statusValue);
            } else {
                formData += '&status=' + statusValue;
            }
            return formData;
        }

        function checkField(input, dangerMessage) {
            var parent_id = $('#parent_id');
            if (parent_id.val() < 1 || parent_id.val() === null) {
                parent_id.prop('required', true);

                if (input.val() === '') {
                    input.prop('required', true);
                    dangerMessage.show();
                } else {
                    input.prop('required', false);
                    dangerMessage.hide();
                }
            } else {
                dangerMessage.hide();
                parent_id.prop('required', false);
                input.prop('required', false);
            }
        }

        function check_parent_id() {
            checkField($('#client_id'), $('#client_id_message .text-danger'));
            checkField($('#client_secret'), $('#client_secret_message .text-danger'));
        }

        function check_client_id() {
            checkField($('#client_id'), $('#client_id_message .text-danger'));
        }

        function check_client_secret() {
            checkField($('#client_secret'), $('#client_secret_message .text-danger'));
        }

        /** Call the function when the page loads or when the parent_id changes */
        check_parent_id();

        /** Call the functions when the corresponding fields change */
        $('#parent_id').on('change', function () {
            check_parent_id();
            check_client_id();
            check_client_secret();
        });
        $('#client_id').on('input', function () {
            check_client_id();
        });
        $('#client_secret').on('input', function () {
            check_client_secret();
        });
        /** On Submit Form*/
        $('.zc-submit').on('click', function () {

            var brand_key = $('#brand_key');
            if (brand_key.val() === '') {
                brand_key.prop('required', true);
            } else {
                brand_key.prop('required', false);
            }

            var email = $('#email');
            if (email.val() === '') {
                email.prop('required', true);
            } else {
                email.prop('required', false);
            }
            check_parent_id();

            var provider = $('#provider');
            if (provider.val() === '') {
                provider.prop('required', true);
            } else {
                provider.prop('required', false);
            }
        });

        /** Create form*/
        $('#zoom_configuration_create_form').on('submit', function (e) {
            e.preventDefault();
            var formData = $(this).serialize();
            formData = getStatus(formData);
            AjaxRequestPost('{{ route('admin.zoom.configuration.store') }}', formData, 'Zoom Configuration created successfully!', true, '{{ route('admin.zoom.configuration.index') }}');
        });
        /** Update form*/
        $('#zoom_configuration_update_form').on('submit', function (e) {
            e.preventDefault();
            var id = $('#hdn').val();
            var url = '{{ route('admin.zoom.configuration.update','/')}}/' + id;
            var formData = $(this).serialize();
            formData = getStatus(formData);
            AjaxRequestPost(url, formData, 'Zoom Configuration updated successfully!', true, '{{ route('admin.zoom.configuration.index') }}');
        });
        /** Change Status */
        if ($('#ZoomConfigurationTable').length || $('#zoom_configuration_update_form').length) {
            $(".change-status").on("change", function () {
                var status = $(this).prop('checked') === true ? 1 : 0;
                var id = $(this).data('id');
                AjaxRequestPost('{{ route('admin.zoom.configuration.change.status') }}', {
                    'status': status,
                    'id': id
                }, 'Status changed successfully!', false);
            });
        }
        /** Delete*/
        $("#ZoomConfigurationTable").on("click", ".delButton", function () {
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
                        var url = '{{ route('admin.zoom.configuration.destroy','/')}}/' + id;

                        var res = AjaxRequestGet(url, null, 'Poof! Your Zoom Configuration has been deleted!', false, null);
                        if (res && res.success) {
                            $("#tr-" + id).remove();
                            if ($("#ZoomConfigurationTable tbody tr").length === 0) {
                                $("#ZoomConfigurationTable tbody").html('<tr><td colspan="12" class="text-center">No data available in table</td></tr>');
                            }
                        }
                    } else {
                        swal("Your Zoom Configuration is safe!", {icon: "success", buttons: false, timer: 1000});
                    }
                });
        });


        /** Trashed Blade*/
        $("#ZoomConfigurationTrashedTable").on("click", ".restoreButton", function () {
            var restoreId = $(this).data("id");
            var url = '{{ route('admin.zoom.configuration.restore','/')}}/' + restoreId;
            var res = AjaxRequestGet(url, null, 'Zoom Configuration Successfully restored!', false, null);
            if (res && res.success) {
                $("#ZoomConfigurationTrashedTable #tr-" + restoreId).remove();
                if ($("#ZoomConfigurationTrashedTable tbody tr").length === 0) {
                    $("#ZoomConfigurationTrashedTable tbody").html('<tr><td colspan="12" class="text-center">No data available in table</td></tr>');
                }
            }
        });

        /**Restore All Zoom Configuration*/
        $("#restoreAll").on("click", ".restoreAllButton", function () {

            swal({
                title: "Are you sure?",
                text: "want to Restore All Zoom Configuration !",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) {
                        var res = AjaxRequestGet('{{ route('admin.zoom.configuration.restore.all')}}', null, 'Restore all Zoom Configurations successfully!', false, null);
                        if (res && res.success) {
                            $("#ZoomConfigurationTrashedTable tbody").empty();
                            $("#ZoomConfigurationTrashedTable tbody").html('<tr><td colspan="12" class="text-center">No data available in table</td></tr>');
                        }
                    } else {
                        swal("Your Zoom Configurations not restore!");
                        console.log('error');
                    }
                });
        });
        /** Restore Zoom Configuration */
        $("#ZoomConfigurationTrashedTable").on("click", ".zc-force-del", function () {
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
                        var url = '{{ route('admin.zoom.configuration.force.delete','/')}}/' + delId;
                        var res = AjaxRequestGet(url, {'zoom_configuration_id': delId}, 'Record permanent deleted successfully!', false, null);
                        if (res && res.success) {
                            $("#ZoomConfigurationTrashedTable #tr-" + delId).remove();
                            if ($("#ZoomConfigurationTrashedTable tbody tr").length === 0) {
                                $("#ZoomConfigurationTrashedTable tbody").html('<tr><td colspan="12" class="text-center">No data available in table</td></tr>');
                            }
                        }
                    } else {
                        swal("Your Zoom Configurations not Delete!");
                        console.log('error');
                    }
                });
        });

        /** Update Expires In*/
        if ($('#zoom_configuration_update_form').length > 0) {
            var expirationTimestamp = {{isset($email) && isset(json_decode($email->access_token, true)['expires_at']) ? json_decode($email->access_token, true)['expires_at'] : 0 }};
            setInterval(function () {
                var currentTime = Math.floor(Date.now() / 1000);
                var secondsLeft = expirationTimestamp - currentTime;
                var inputField = document.getElementById("expires_in");
                if (secondsLeft > 0) {
                    inputField.value = secondsLeft + " seconds left";
                } else {
                    inputField.value = "Expired";
                }
            }, 1000);
        }
    });
</script>
