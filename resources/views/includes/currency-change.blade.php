<script>
    $(document).ready(function () {
        updateTaxFieldRequired();
        $('#cur_symbol').on('change', function () {
            var cur_symbol = $('#cur_symbol option:selected').val();

            // console.log(cur_symbol);

            if (cur_symbol == 'USD') {
                $('.cxm-currency-symbol-icon').html('$');

            } else if (cur_symbol == 'EUR') {
                $('.cxm-currency-symbol-icon').html('€');

            } else if (cur_symbol == 'GBP') {
                $('.cxm-currency-symbol-icon').html('£');
            } else if (cur_symbol == 'AUD') {
                $('.cxm-currency-symbol-icon').html('A$');

            } else if (cur_symbol == 'CAD') {
                $('.cxm-currency-symbol-icon').html('C$');
            }
        });

        $('#edit_cur_symbol').on('change', function () {
            var edit_cur_symbol = $('#edit_cur_symbol option:selected').val();

            if (edit_cur_symbol == 'USD') {
                $('.cxm-currency-symbol-icon').html('$');

            } else if (edit_cur_symbol == 'EUR') {
                $('.cxm-currency-symbol-icon').html('€');

            } else if (edit_cur_symbol == 'GBP') {
                $('.cxm-currency-symbol-icon').html('£');
            } else if (edit_cur_symbol == 'AUD') {
                $('.cxm-currency-symbol-icon').html('A$');

            } else if (edit_cur_symbol == 'CAD') {
                $('.cxm-currency-symbol-icon').html('C$');
            }
        });

        // $('#taxable').on('click', function () {
        //     if ($(this).is(":checked")) {
        //         //this.value = this.checked ? 1 : 0;
        //         this.value = 1;
        //         $("#taxField").show();
        //         $("#totalAmount").show();
        //         $('#tax').prop('required', true);
        //     } else {
        //         this.value = 0;
        //         $("#taxField").hide();
        //         // $("#totalAmount").hide();
        //         $("#tax").val('');
        //         $("#total_amount").val('');
        //         $('#tax').prop('required', false);
        //     }
        // });

        function updateTaxFieldRequired() {
            var edit_taxable = document.getElementById('edit_taxable');
            var editTax = document.getElementById('edit_tax');

            if (edit_taxable && editTax) {
                if (edit_taxable.value == 1) {
                    editTax.setAttribute('required', 'required');
                } else {
                    editTax.removeAttribute('required');
                }
            }
        }

        $('#edit_taxable').on('click', function () {
            if ($(this).is(":checked")) {
                this.value = 1;
                $("#edit_taxField").show();
                $("#edit_totalAmount").show();
                $('#edit_tax').prop('required', true);
            } else {
                this.value = 0;
                $("#edit_taxField").hide();
                $("#edit_totalAmount").hide();
                $("#edit_tax").val('');
                $("#edit_total_amount").val('');
                $('#edit_tax').prop('required', false);
            }
        });

        $("#tax, #amount").keyup(function () {
            // var amount = $('#amount').val();

            var amount = parseFloat($('#amount').val());
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

        $("#edit_tax, #edit_amount").keyup(function () {
            var edit_amount = $('#edit_amount').val();
            var edit_tax = $('#edit_tax').val();
            var edit_total = Math.round(((edit_amount * edit_tax) / 100));
            var edit_totaltax = Number(edit_total) + Number(edit_amount);

            $('#edit_tax_amount').val(edit_total);
            $('#edit_total_amount').val(edit_totaltax);
        });
    });
</script>
