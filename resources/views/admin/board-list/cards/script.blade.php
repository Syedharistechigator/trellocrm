<script>
    $(document).ready(function () {
        var loading_div = $('.loading_div');
        /** Update form*/
        $('#board_list_card_update_form').on('submit', function (e) {
            e.preventDefault();
            let id = `{{$board_list_card->id??""}}`;
            if (!id) {
                createToast('error', 'Record not found redirect to index.');
                window.location.href(`{{route('admin.board.list.cards.index')}}`);
            }
            let url = `{{ route('admin.board.list.cards.update', ':id') }}`.replace(':id', id);
            AjaxRequestPostPromise(url, new FormData(this), null, true, null, true, true, false).then((res) => {
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });

        function update_select(column, array, default_text) {
            column.empty().selectpicker('refresh').append(`<option class="" value="" disabled>${default_text}</option>`);
            array.forEach(function (item) {
                column.append('<option value="' + item.id + '">' + item.data + '</option>');
            })
            column.val('').selectpicker('refresh');
            column.val('').attr('required', true).prop('required', true);
        }

        /** On Change Department Update Board List */
        $('#department').on('change', function () {
            let department = $('#department').val() ? $('#department').val() : 0;
            let selected_board_list_id = `{{$board_list_card->board_list_id??""}}`
            let url = '{{ route('admin.department.get.board.lists','/')}}/' + department;
            $('#board-list').empty().selectpicker('refresh').append(`<option class="" value="" disabled>Select Board List</option>`);

            AjaxRequestGetPromise(url, null, null, false, null, false, true, false).then((res) => {
                if (res.status && res.status === 1 && res.success) {
                    update_select($('#board-list'), res.board_lists, 'Select Board List');
                    res.board_lists.some(function (board_list) {
                        if (board_list.id == selected_board_list_id) {
                            $('#board-list').val(selected_board_list_id).selectpicker('refresh');
                            return true;
                        }
                    });
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {
                loading_div.css('display', 'none');
            })
        });
    });
</script>
