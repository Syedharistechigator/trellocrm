<script>

//var placementFrom = 'top';
//var placementAlign = 'center';
//var animateEnter = "";
//var animateExit = "";
//var colorName = 'alert-success';
//var notifText = 'Status change successfully.';
//showNotification(colorName, notifText, placementFrom, placementAlign, animateEnter, animateExit);

//Create Brand
$('#brand_form').on('submit', function(e){
	e.preventDefault();
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('brand.store') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $("#brand_form")[0].reset();
            swal("Good job!", "Brand successfully Created!", "success");
        },
        error: function(){
          swal("Error!", "Request Fail!", "error");     
        }
    });    
});

// Delete Brand
$("#BrandTable").on("click", ".delButton", function(){   
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this brand!",
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
                    type: "DELETE",
                    url: "brand/"+cid,
                    success: function (data) {
                        swal("Poof! Your brand has been deleted!", {
                            icon: "success",
                        });
                        setInterval('location.reload()', 2000);        // Using .reload() method.
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        } else {
          swal("Your imaginary file is safe!");
          console.log('error');
        }
    });
});

// Restore Brand
$("#BrandTable").on("click", ".restoreButton", function(){
    var restoreId = $(this).data("id");
    console.log(restoreId);
   
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
   
    $.ajax({
        type: "Get",
        url: "restore/"+restoreId,
        success: function (data) {
           console.log("tet");
           swal("Good job!", "Brand successfully Restore!", "success");
           setInterval('location.reload()', 2000);        // Using .reload() method.
        },
        error: function (data) {
            console.log('Error:', data);
            swal("Error!", "Request Fail!", "error");
        }
    });
});

//Restore All Brand
$("#restoreAll").on("click", ".restoreAllButton", function(){
    swal({
        title: "Are you sure?",
        text: "want to Restore All Brand !",
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
                    url: "{{ route('restoreallbrand') }}",
                    success: function (data) {
                        swal("Good job! Restore all brand successfully!", {
                            icon: "success",
                        });
                        window.location='{{url('admin/brand')}}';
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        } else {
          swal("Your Brnad not restore!");
          console.log('error');
        }
    });
});

// publish & unpublish function
$("#BrandTable").on("change", ".toggle-class", function(){
    var status = $(this).prop('checked') == true ? 1 : 0; 
    var brand_id = $(this).data('id'); 

    $.ajax({
        type: "GET",
        dataType: "json",
        url: '/changeStatus',
        data: {'status': status, 'brand_id': brand_id},
        success: function(data){
            swal("Good job!", "Status change successfully!", "success");
            console.log(data.success)
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
    });
});

//update Brand
$('#brand_update_form').on('submit', function(e){
    e.preventDefault();    
    var bid = $('#hdn').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "/admin/brand/"+bid,
        method:'post',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(result){
            console.log(result);
            swal("Good job!", "Brand successfully Updated!", "success");
            setTimeout(function(){
                window.location='{{url('admin/brand')}}';
            }, 2000);  
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
    });    
});

// Restore Brand
$("#BrandTable").on("click", ".brandforcedel", function(){   
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
                    url: "forcedelete/"+delId,
                    success: function (data) {
                        swal("Good job! brand delete successfully!", {
                            icon: "success",
                        });
                        setInterval('location.reload()', 2000);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
            });
        } else {
          swal("Your Brnad not Delete!");
          console.log('error');
        }
    });
});

</script>