<script>

//Create Spending
$('#addspendingBtn').on('click', function(e){

     e.preventDefault();

        var brand_key =  $('#brand_hnd').val(); 
        var team_key =  $('#team_hnd').val(); 
        var spending_date =  $('#spending_date').val(); 
        var spending_platform =  $('#spending_platform').val(); 
        var spending_amount =  $('#spending_amount').val(); 
        
        // console.log(team_key);

        $.ajax({
        type: "GET",
        dataType: "json",
        url: "{{ route('spending_create') }}",
        data: {
            'brand_key': brand_key,
            'team_key': team_key,
            'spending_date': spending_date,
            'spending_platform': spending_platform,
            'spending_amount': spending_amount,
        },
        success: function(data){
            $("#spendingModal").modal('hide');
            $("#SpendingTable").load(" #SpendingTable > *");
            swal("Good job!", "Spending successfully Created!", "success");
        },
        error: function(){
          swal("Error!", "Request Fail!", "error");     
        }
        });    
});


//update Spending
$('#spending_update_form').on('submit', function(e){
    e.preventDefault();
    
        var tid = $('#hdn').val();
       
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: "/admin/spending/"+tid,
        method:'post',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(result){
            console.log(result);
            swal("Good job!", "Team successfully Updated!", "success");
            setTimeout(function(){
                window.location='{{ route('spending.index') }}';
            }, 2000);  
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
        });    
});


// Delete Brand
$("#SpendingTables").on("click", ".spendingDelButton", function(){
   
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this speindings!",
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
                    url: "spending/"+cid,
                    success: function (data) {
                        swal("Poof! Your team has been deleted!", {
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













</script>