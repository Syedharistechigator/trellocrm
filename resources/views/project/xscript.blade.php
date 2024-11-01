<script>
// search agent payment 
$('.clientType').on('click', function() {
    var client_type = $(this).attr('data-type');
    document.getElementById("client_type").value = client_type;

   if(client_type == 'new'){
        $('#showClient').hide(); 
        $('#name_div').show();
        $('#email_div').show();
        $('#phone_div').show();   
   }else{
        $('#name_div').hide();
        $('#phone_div').hide();
        $('#email_div').hide();
        $('#showClient').show(); 
   }
});

//Create Brand
$('#create_project_form').on('submit', function(e){
     e.preventDefault();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('project.store') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){        
            if($.isEmptyObject(data.error)){
                $("#create_project_form")[0].reset();
                console.log(data);
                $("#projecteModal").modal('hide');
                
                swal("Good job!", "Project successfully Created!", "success")
				.then(() => {
					location.reload();
				});

            }else{
                printErrorMsg(data.error);
                //swal("Errors!", data.error, "error");  
            }
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error");     
        }
    });          
});

function printErrorMsg (msg) {
    $(".print-error-msg").find("ul").html('');
    $(".print-error-msg").css('display','block');
    $.each( msg, function( key, value ) {
        $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
    });
}

$('#project-description-button-edit').on('click', function(e){
    console.log('Test Update');
    $('#detail-box').hide(); 
    $('#detail-text-edit').show();
    $('#project-description-button-edit').hide();
    $('#project-description-button-update').show();
});

$('#project-description-button-update').on('click', function(e){
    console.log('Test Update');       
    var project_id =  $('#project_id').val(); 
    var ProjectDetails =  $('#detail-text-edit').val();

    $.ajax({
        type: "GET",
        dataType: "json",
        url: "{{ route('updateProjectDescription') }}",
        data: {'project_id': project_id, 'ProjectDetails': ProjectDetails},
        success: function(result){
            console.log(result.project_description);
            swal("Good job!", "Update successfully!", "success");
            $('#detail-box').html(result.project_description);
            $('#detail-box').show(); 
            $('#detail-text-edit').hide();
            $('#project-description-button-edit').show();
            $('#project-description-button-update').hide(); 
        },
        error: function(data){
            console.log(data);
            swal("Error!", "Request Fail!", "error");
        }
    });
});


$("#projectTable").on("click", ".statusChange", function(){    
    console.log('test');
    var project_id = $(this).data('id');
    document.getElementById("status_hdn").value = project_id;
    console.log(project_id);
});


//change Status
$('#changeProjectStatus').on('click', function(e){
    e.preventDefault();
    var project_id = $('#status_hdn').val();
    var projectStatus = $('#project-status').val();
    
    if(projectStatus != ''){
        console.log(projectStatus);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('changeClientProjectStatus') }}",
            data: {'project_id': project_id, 'projectStatus': projectStatus},
            success: function(data){
                $("#changeStatusModal").modal('hide');

                swal("Good job!", "Status change successfully!", "success")
				.then(() => {
					location.reload();
				});
            },
            error: function(data){
                console.log(data);
                swal("aaaaError!", "Request Fail!", "error");
            }
        });
    }else{
        swal("Error!", "Select Brand", "error");
    }
});


//edit Project
$("#projectTable").on("click", ".editproject", function(){    
    console.log('Edit project');
    var project_id = $(this).data('id');
    document.getElementById("project_hdn").value = project_id;
    console.log(project_id);

    $.ajax({
        type: "GET",
        url: "{{url('project/')}}/"+project_id+'/edit',
        success: function (data) {
            console.log(data);

            var cxmToDay = (data.project_date_start?new Date(data.project_date_start) :new Date());
            let cxmStartDate = cxmToDay.getFullYear() +'-'+ (cxmToDay.getMonth()+1) +'-'+ ((cxmToDay.getDate() < '10')?('0'+cxmToDay.getDate()) :cxmToDay.getDate());

            var cxmToDay1 = (data.project_date_due?new Date(data.project_date_due) :new Date());
            let cxmDueDate = cxmToDay1.getFullYear() +'-'+ (cxmToDay1.getMonth()+1) +'-'+ ((cxmToDay1.getDate() < '10')?('0'+cxmToDay1.getDate()) :cxmToDay1.getDate());
            
            $('#edit_brand_key').val(data.brand_key).prop('selected', true);
            $('#edit_agent_id').val(data.agent_id).prop('selected', true);
            $('#edit_logo_category').val(data.category_id).prop('selected', true);
            $('#edit_project_title').val(data.project_title);
            $('#edit_project_description').val(data.project_description);
            $('#edit_start_date').val(cxmStartDate); 
            $('#edit_due_date').val(cxmDueDate); 
            $('#edit_project_cost').val(data.project_cost); 
           
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

//update Project
$('#project_update_form').on('submit', function(e){
    e.preventDefault();
    
        var bid = $('#project_hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({   
        url: "{{url('project/')}}/"+bid,
        //url: "/project/"+bid,
        method:'post',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(result){
            console.log(result);
            $("#EditProjecteModal").modal('hide');
            swal("Good job!", "Brand successfully Updated!", "success");
            setTimeout(function(){
                window.location='{{url("project")}}';
            }, 1000);  
        },
        error: function(){
            swal("Error!", "Request Fail!", "error");
        }
        });    
});


//Create Client Invoice
$('#create-project-invoice').on('submit', function(e){
     e.preventDefault();
     let cxmAmount = $('#amount').val();
    let cxmTotalAmount = $('#total_amount').val();
    let cxmGrossTotalAmount = 0;
    cxmGrossTotalAmount = cxmTotalAmount?cxmTotalAmount : cxmAmount;

    console.log('tet');
    if(cxmGrossTotalAmount <= "{{env('PAYMENT_LIMIT')}}"){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
        url: "{{ route('createProjectInvoice') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $("#create-project-invoice")[0].reset();
            console.log(data);
            $("#cxmInvoiceModal").modal('hide');

            swal("Good job!", "Invoice successfully Created!", "success");
            setInterval('location.reload()', 1000);
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error");     
        }
        });   
    } else {
        swal("Amount Exceeds The Limit", "Total amount should be less than {{env('PAYMENT_LIMIT')}}!", "info");
    }          
});

//edit invoice
$("#InvoiceTable").on("click", ".editInvoice", function(){    
    console.log('Edit Invoice');
    var invoice_id = $(this).data('id');
    document.getElementById("invoice_hdn").value = invoice_id;

    $.ajax({
        type: "GET",
        url: "{{url('invoice/')}}/"+invoice_id+'/edit',
        //url: "invoice/"+invoice_id+'/edit',
        success: function (data) {
            console.log(data);

            var cxmToDay = (data.due_date?new Date(data.due_date) :new Date());
            let cxmDueDate = cxmToDay.getFullYear() +'-'+ (cxmToDay.getMonth()+1) +'-'+ ((cxmToDay.getDate() < '10')?('0'+cxmToDay.getDate()) :cxmToDay.getDate());
            console.log(cxmDueDate);

            $('#edit_amount').val(data.final_amount);
            $('#edit_due_date').val(cxmDueDate);
            $('#edit_invoice_description').text(data.invoice_descriptione); 
            // $('#edit_brand_key').val(data.brand_key).prop('selected', true);
            // $('#edit_agent_id').val(data.agent_id).prop('selected', true);
            // $('#sales_type').val(data.sales_type).prop('selected', true);

            $('#edit_tax').val(data.tax_percentage);
            $('#edit_total_amount').val(data.total_amount);

            $('#edit_brand_key').selectpicker('val', data.brand_key);
            $('#edit_agent_id').selectpicker('val', data.agent_id);
            $('#sales_type').selectpicker('val', data.sales_type);
        },
        error: function (data) {
            console.log('Error:', data);
        }
    });
});

//update Invoice
$('#invoice_update_form').on('submit', function(e){
    e.preventDefault();
    let cxmAmount = $('#edit_amount').val();
    let cxmTotalAmount = $('#edit_total_amount').val();
    let cxmGrossTotalAmount = 0;
    cxmGrossTotalAmount = cxmTotalAmount?cxmTotalAmount : cxmAmount;
    // console.log(cxmAmount + ' | ' + cxmTotalAmount + ' <|> ' + cxmGrossTotalAmount);
    if(cxmGrossTotalAmount <= "{{env('PAYMENT_LIMIT')}}"){
        var bid = $('#invoice_hdn').val();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            // url: "{{url('invoice/')}}/"+bid,
            url: "/invoice/"+bid,
            method:'post',
            data: $(this).serialize(), // get all form field value in serialize form
            success: function(result){
                console.log(result);
                $("#editInvoiceModal").modal('hide');
                swal("Good job!", "Invoice successfully Updated!", "success");
                setTimeout(function(){
                    window.location='{{url("invoice")}}';
                }, 2000);  
            },
            error: function(){
                swal("Error!", "Request Fail!", "error");
            }
        });
        
    } else {
        swal("Amount Exceeds The Limit", "Total amount should be less than 2500!", "info");
    }     
});

$("#InvoiceTable").on("click", ".copy-url", function(){
   
   var copyText = $(this).attr('id');
   $(this).css('background-color','#f00');

   console.log(copyText);
    /* Select the text field */

   let $cxmTemp = $("<input>");
   $(this).append($cxmTemp);
   $cxmTemp.val(copyText).select();
   document.execCommand("copy");
   $cxmTemp.remove();

   /* Alert the copied text */
   // alert("Copied the text: " + copyText.value);
   swal("Good job!", "URL Successfully Copied!", "success");
});


// Send Email
$("#InvoiceTable").on("click", ".sendEmail", function(){
   swal({
       title: "Email To Client",
       text: "Are you sure?",
       icon: "warning",
       buttons: true,
       dangerMode: true,
   })
   .then((willDelete) => {
       if (willDelete) {
          var id = $(this).data("id");
          
           $.ajax({
              type: "GET",
              url: "{{url('sendinvoice/')}}/"+id,
              success: function (data) {
                swal("Good job!", "Send Email Successfully!", "success");
              },
              error: function (data) {
                swal("Errors!", "Request Fail!", "error");
              }
           });
       } 
   });
});


// publish Invoice
$("#InvoiceTable").on("click", ".publishInvoice", function(){
   
   swal({
       title: "Publish Invoice",
       text: "Are you sure?",
       icon: "warning",
       buttons: true,
       dangerMode: true,
   })
   .then((willDelete) => {
       if (willDelete) {
          var id = $(this).data("id");

           $.ajax({
              type: "GET",
              url: "{{url('publishinvoice/')}}/"+id,
              success: function (data) {
                console.log(data); 
                swal("Good job!", "Publish Invoice Successfully!", "success");
                setInterval('location.reload()', 1000);
              },
              error: function (data) {
                 swal("Errors!", "Request Fail!", "error");
              }
           });
       } 
   });
});

// Upload Project File
$('#upload-project-file').on('submit', function(e){
    e.preventDefault();
    var form_data = new FormData(this);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('uploadProjectFile') }}",
        method:'POST',
        dataType: "JSON",
        data: form_data, 
        processData: false,
        contentType: false,
        success: function(data){
            $("#upload-project-file")[0].reset();
            console.log(data);

            $("#cxmFileModal").modal('hide');

            swal("Good job!", "Your file upload successfully!", "success")
            .then(() => {
                location.reload();
            });
        },
        error: function(data){
            console.log(data);
            swal("Errors!", "Request Fail!", "error", {buttons: false, timer: 2000});
        }
    });
});


//Create Project Comment 
$('#comment_form').on('submit', function(e){
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('createComment') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $("#comment_form")[0].reset();
            // console.log(data);
            $('.chat-widget .cxm-chat-list > li:last-child').after(data);           
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error", {buttons: false, timer: 2000});     
        }
    });          
});



// Delete File
$("#cxmFileTable").on("click", ".deleteFile", function(){
   
    swal({
        title: "Are you sure?",
        text: "Once deleted, you will not be able to recover this File!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDelete) => {
        if (willDelete) {
           var fid = $(this).data("id");
           var url = '{{ route("deletefile", ":id") }}';
               url = url.replace(':id', fid);
 
           $.ajax({
                    type: "GET",
                    url:url,

                    success: function (data) {
                        swal("Poof! Your File has been deleted!", {
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



$("#cxmFileTable").on("click", ".toggle-class", function(){
 var status = $(this).prop('checked') == true ? 1 : 0;

var file_id = $(this).data('id');

$.ajax({
    type: "GET",
    dataType: "json",
    url: '/visibilityFilestatus',
    data: {'status': status, 'file_id': file_id},
    success: function(data){
        swal("Good job!", "Status change successfully!", "success");
       location.reload(); 
        console.log(data.success)
    },
    error: function(){
        swal("Error!", "Request Fail!", "error");
    }
});
});


function cxmRefreshChat(){
    let cxmProjectId = {'projectId': null}
    @if(isset($project->id))
    cxmProjectId = {'projectId': '{{ $project->id }}'}
    @endif

    $.ajax({
        url: "{{route('allComments')}}",
        method:'GET',
        data: {'projectId': cxmProjectId},
        success: function(data){
            $('.chat-widget .cxm-chat-list').html(data);
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error", {buttons: false, timer: 2000});     
        }
    });
}


//Assign Account Project
$("#projectTable").on("click", ".AccountManager", function(){    
    console.log('test');
    var project_id = $(this).data('id');
    document.getElementById("account_hdn").value = project_id;
    console.log(project_id);
});


//change Status
$('#changeAccountManager').on('click', function(e){
    e.preventDefault();
    var project_id = $('#account_hdn').val();
    var projectManager = $('#account-manager').val();
    
    if(projectManager != ''){
        console.log(projectManager);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('changeProjectManager') }}",
            data: {'project_id': project_id, 'projectManager': projectManager},
            success: function(data){
                $("#changeStatusModal").modal('hide');

                swal("Good job!", "Successfully Assign Account Manager!", "success")
				.then(() => {
					location.reload();
				});
            },
            error: function(data){
                console.log(data);
                swal("aaaaError!", "Request Fail!", "error");
            }
        });
    }else{
        swal("Error!", "Select Project Manager", "error");
    }
});


//Create Direct Project Payment
$('#create-project-payment').on('submit', function(e){
    e.preventDefault();
    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('createProjectPayment') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $('.page-loader-wrapper').css('display', 'none');
            $("#create-project-payment")[0].reset();
            console.log(data);
            $("#cxmPaymentModal").modal('hide');

            swal("Good job!", "Invoice successfully Created!", "success");
            setInterval('location.reload()', 1000);
        },
        error: function(){
          swal("Errors!", "Request Fail!", "error");     
        }
    });          
});

</script>