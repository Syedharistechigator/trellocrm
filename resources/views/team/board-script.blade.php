<!-- <script
  src="https://code.jquery.com/jquery-3.7.1.js"
  integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
  crossorigin="anonymous"></script> -->
<!-- <script src="{{ asset('assets/js/board/jquery.min.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js" referrerpolicy="no-referrer"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="{{ asset('assets/js/board/popper.js') }}"></script>
<script src="{{ asset('assets/js/board/tooltip.js') }}"></script>
<script src="{{ asset('assets/js/board/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/board/jquery.nicescroll.min.js') }}"></script>
<script src="{{ asset('assets/js/board/moment.min.js') }}"></script>
<script src="{{ asset('assets/js/board/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/board/daterangepicker.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js" referrerpolicy="no-referrer"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

  <script src="{{asset('assets/js/board/tasks.js')}}"></script>
  <script>
    base_url = "{{url('')}}"+"/";
    csrfName = 'csrf_token_name';
    csrfHash = 'bd1d148b8c8456563293928ff16d77a8';
</script>
<script>
    $(document).ready(function() {
        $('.cxm-btn-create').on('click', function() {
            var cartId = $(this).data('cartid');
            var sort_tasks = $(this).data('cartsort');
            $('#task_form input[name="card_id"]').val(cartId);
            $('#task_form input[name="sort_tasks"]').val(sort_tasks);
        });
    });
    $('#task_form').on('submit', function(e){
     e.preventDefault();
    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //console.log($(this).serialize());
        $.ajax({
            url: "{{ route('task.store') }}",
            datatype: 'json',
            method:'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(data){
                $("#task_form")[0].reset();
                $('.page-loader-wrapper').css('display', 'none');
                swal("Good job!", "Task successfully Created!", "success");
                $('#cxmDetailModal').modal('hide');
                setTimeout(function() {
                location.reload();
                }, 3000);
            },
            error: function(){
            $('.page-loader-wrapper').css('display', 'none');
            swal("Error!", "Request Fail!", "error");
            }
        });
});
    </script>
<!-- <script>
      jQuery(window).on("load", function(){
          jQuery("body a").attr("href","javascript:;");
      });
</script> -->