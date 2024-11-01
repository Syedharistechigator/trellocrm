<script>

//var placementFrom = 'top';
//var placementAlign = 'center';
//var animateEnter = "";
//var animateExit = "";
//var colorName = 'alert-success';
//var notifText = 'Status change successfully.';
//showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);


//Create card
$('#card_form').on('submit', function(e){
	 e.preventDefault();
    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //console.log($(this).serialize());
        $.ajax({
            url: "{{ route('card.store') }}",
            datatype: 'json',
            method:'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(data){
                $("#card_form")[0].reset();
                $('.page-loader-wrapper').css('display', 'none');
                swal("Good job!", "card successfully Created!", "success");
            },
            error: function(){
            $('.page-loader-wrapper').css('display', 'none');
            swal("Error!", "Request Fail!", "error");
            }
        });
});

// Delete card
$("#cardTable").on("click", ".delButton", function(){
    swal({
        title: "Are you sure?",
        text: "Once deleted, not be able to recover this card!",
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
           var cid = $(this).data("id");
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

           $.ajax({
                type: "DELETE",
                url: "card/"+cid,
                success: function (data) {
                    swal("Poof! Your card has been deleted!", {
                        icon: "success",
                    });
                    setTimeout(function(){
                            window.location='{{url("/admin/card")}}';
                    }, 2000);        // Using .reload() method.
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        } else {
            swal("Your card is safe!", {buttons: false, timer: 1000});
        }
    });
});


// Restore card
$("#cardTable").on("click", ".card_restoreButton", function(){

   var restoreId = $(this).data("id");
   console.log(restoreId);

   $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

   $.ajax({
        type: "Get",
        url: "card_restore/"+restoreId,
        success: function (data) {
           console.log("tet");
           swal("Good job!", "card successfully Restore!", "success");
           setTimeout(function(){
                            window.location='{{url("/admin/card")}}';
                    }, 2000);         // Using .reload() method.
        },
        error: function (data) {
            console.log('Error:', data);
            swal("Error!", "Request Fail!", "error");
        }
    });
});




// publish & unpublish function
$("#cardTable").on("change", ".toggle-class", function(){
    var status = $(this).prop('checked') == true ? 1 : 0;
    var card_id = $(this).data('id');

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/card_changeStatus',
        data: {'status': status, 'card_id': card_id},
        success: function(data){
            swal("Good job!", "Status change successfully!", "success");
            console.log(data.success)
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
    });
});


//update card
$('#card_update_form').on('submit', function(e){
    e.preventDefault();

        var bid = $('#hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: "/admin/card/"+bid,
        method:'post',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(result){
            console.log(result);
            swal("Good job!", "card successfully Updated!", "success");
            setTimeout(function(){
                window.location='{{url("admin/card")}}';
            }, 2000);
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
        });
});


// Restore card
$("#cardTable").on("click", ".card_forcedel", function(){

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
           $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

           $.ajax({
                    type: "Get",
                    url: "card_forcedelete/"+delId,
                    success: function (data) {
                        swal("Good job! card deleted successfully!", {
                            icon: "success",
                        });
                         setTimeout(function(){
                            window.location='{{url("/admin/card")}}';
                    }, 2000); ;
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        } else {
          swal("Your card not Delete!");
          console.log('error');
        }
    });
});
</script>
