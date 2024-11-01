<script>
$('#team_key').on('change', function() {
  var team = $(this).val();
  console.log(team);
  $.ajax({
        type: "GET",
        url: "showbrands/"+team,
            success: function (data) { 
                var len = data.length;
                $("#brand").empty();
                for( var i = 0; i<len; i++){
                        var id = data[i]['brandKey'];
                        var name = data[i]['brandName'];
                            $("#brand").append("<option value='"+id+"'>"+name+"</option>");

                }  

            }
    });
}); 

$('#brand').on('change', function() {
  var brand = $(this).val();
  console.log(brand); 
  $.ajax({
        type: "GET",
        url: "brandproject/"+brand,
            success: function (data) {
                console.log(data);  
                var len = data.length;
                console.log(len);   
                $("#projects").empty();
                for( var i = 0; i<len; i++){
                            var id = data[i]['id'];
                            var name = data[i]['project_title'];
                            $("#projects").append("<option value='"+id+"'>"+name+"</option>");
                            

                }  

            }
         });
}); 

$('#expense-Form').on('change', '#projects',function() {
  var project = $(this).val();
  console.log(project);
 
  $.ajax({
        type: "GET",
        url: "projectdetail/"+project,
            success: function (data) {
                console.log(data);  
                document.getElementById("clientId").value = data.clientid;
            }
         });
}); 

//create Expense

$('#expense-Form').on('submit', function(e){
     e.preventDefault();

     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: "{{ route('expense.store') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $("#expense-Form")[0].reset();
            console.log(data);
            $("#ExpenseModal").modal('hide');

            swal("Good job!", "Invoice successfully Created!", "success");
            setInterval('location.reload()', 1000);
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error");     
        }
        });          
});
</script>