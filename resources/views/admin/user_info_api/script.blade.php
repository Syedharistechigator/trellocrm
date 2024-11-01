<script>

//var placementFrom = 'top';
//var placementAlign = 'center';
//var animateEnter = "";
//var animateExit = "";
//var colorName = 'alert-success';
//var notifText = 'Status change successfully.';
//showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);


//Create user_info_api
$('#user_info_api_form').on('submit', function(e){
	 e.preventDefault();
    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //console.log($(this).serialize());
        $.ajax({
            url: "{{ route('user_info_api.store') }}",
            datatype: 'json',
            method:'POST',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(data){
                $("#user_info_api_form")[0].reset();
                $('.page-loader-wrapper').css('display', 'none');
                swal("Good job!", "User Info API successfully Created!", "success");
            },
            error: function(){
            $('.page-loader-wrapper').css('display', 'none');
            swal("Error!", "Request Fail!", "error");
            }
        });
});

// Delete user_info_api
$("#user_info_apiTable").on("click", ".delButton", function(){
    swal({
        title: "Are you sure?",
        text: "Once deleted, not be able to recover this user info api!",
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
                url: "user_info_api/"+cid,
                success: function (data) {
                    swal("Poof! Your User Info API has been deleted!", {
                        icon: "success",
                    });
                    setTimeout(function(){
                            window.location='{{url("/admin/user_info_api")}}';
                    }, 2000);        // Using .reload() method.
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        } else {
            swal("Your User Info API is safe!", {buttons: false, timer: 1000});
        }
    });
});


// Restore user_info_api
$("#user_info_apiTable").on("click", ".user_info_api_restoreButton", function(){

   var restoreId = $(this).data("id");
   console.log(restoreId);

   $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

   $.ajax({
        type: "Get",
        url: "user_info_api_restore/"+restoreId,
        success: function (data) {
           console.log("tet");
           swal("Good job!", "User Info API successfully Restore!", "success");
           setTimeout(function(){
                            window.location='{{url("/admin/user_info_api")}}';
                    }, 2000);         // Using .reload() method.
        },
        error: function (data) {
            console.log('Error:', data);
            swal("Error!", "Request Fail!", "error");
        }
    });
});




// publish & unpublish function
$("#user_info_apiTable").on("change", ".toggle-class", function(){
    var status = $(this).prop('checked') == true ? 1 : 0;
    var user_info_api_id = $(this).data('id');

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/user_info_api_changeStatus',
        data: {'status': status, 'user_info_api_id': user_info_api_id},
        success: function(data){
            swal("Good job!", "Status change successfully!", "success");
            console.log(data.success)
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
    });
});


//update user_info_api
$('#user_info_api_update_form').on('submit', function(e){
    e.preventDefault();

        var bid = $('#hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: "/admin/user_info_api/"+bid,
        method:'post',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(result){
            console.log(result);
            swal("Good job!", "User Info API successfully Updated!", "success");
            setTimeout(function(){
                window.location='{{url("admin/user_info_api")}}';
            }, 2000);
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
        });
});


// Restore user_info_api
$("#user_info_apiTable").on("click", ".user_info_api_forcedel", function(){

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
                    url: "user_info_api_forcedelete/"+delId,
                    success: function (data) {
                        swal("Good job! User Info API deleted successfully!", {
                            icon: "success",
                        });
                         setTimeout(function(){
                            window.location='{{url("/admin/user_info_api")}}';
                    }, 2000); ;
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        } else {
          swal("Your User Info API not Delete!");
          console.log('error');
        }
    });
});
//Restore All User Info Api
$("#user_info_api_restoreAll").on("click", ".user_info_api_restoreAllButton", function(){
    swal({
        title: "Are you sure?",
        text: "want to Restore All Team !",
        icon: "warning",
        buttons: true,
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
                    type: "Get",
                    url: "{{ route('restoreall_user_info_api') }}",
                    success: function (data) {
                        swal("Good job! Restore all User Info API successfully!", {
                            icon: "success",
                        });
                        window.location='{{url("admin/user_info_api")}}';
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            } else {
                swal("Your User Info API not restore!");
                console.log('error');
            }
        });
});
</script>
