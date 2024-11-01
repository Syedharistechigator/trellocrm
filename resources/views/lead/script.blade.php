<script>
{{--// search brand payment--}}
{{--$('#brand').on('change', function() {--}}
{{--    $.ajaxSetup({--}}
{{--        headers: {--}}
{{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--        }--}}
{{--    });--}}

{{--    var teamKey = $(this).val();--}}
{{--    console.log(teamKey);--}}

{{--    $.ajax({--}}
{{--        url: "{{ route('teambrandleads') }}",--}}
{{--        method:'POST',--}}
{{--        data:{search :teamKey},--}}
{{--        success: function(result){--}}
{{--            console.log(result);--}}
{{--            $("#LeadTable").html(result);--}}
{{--            $('#LeadTable').DataTable({--}}
{{--                "destroy": true, //use for reinitialize datatable--}}
{{--            });--}}
{{--        }--}}
{{--    });--}}
{{--});--}}

{{--// search monthly payment--}}
{{--$('#month-input').on('change', function() {--}}
{{--    var month = $(this).val();--}}

{{--    $.ajaxSetup({--}}
{{--        headers: {--}}
{{--            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
{{--        }--}}
{{--    });--}}

{{--    $.ajax({--}}
{{--        url: "{{ route('monthleads') }}",--}}
{{--        method:'POST',--}}
{{--        data:{search :month},--}}
{{--        success: function(result){--}}
{{--            $("#LeadTable").html(result);--}}
{{--            $('#LeadTable').DataTable({--}}
{{--                "destroy": true,--}}
{{--            });--}}
{{--        }--}}
{{--    });--}}
{{--});--}}

{{--$('#mylead').on('click', function() {--}}
{{--    $.ajax({--}}
{{--        url: "{{ route('myLead') }}",--}}
{{--        method:'get',--}}
{{--        success: function(result){--}}
{{--            console.log(result);--}}
{{--            $("#LeadTable").html(result);--}}
{{--            $('#LeadTable').DataTable({--}}
{{--                "destroy": true, //use for reinitialize datatable--}}
{{--            });--}}
{{--        }--}}
{{--    });--}}
{{--});--}}

{{--$("#LeadTable").on("click", ".statusChange", function(){--}}
{{--    console.log('test');--}}
{{--    var lead_id = $(this).data('id');--}}
{{--    document.getElementById("status_hdn").value = lead_id;--}}
{{--    console.log(lead_id);--}}
{{--});--}}

$(document).ready(function () {
    $('#create-lead-show-modal, .assign-lead-show-modal, .statusChange, .LeadComments').click(function () {
        $('#brand, #date-range, [type=search]').prop('disabled', true);
    });
    $('#create-lead-modal, #cxmCommentsModal, #changeStatusModal, #assignLead').on('hidden.bs.modal', function () {
        $('#brand, #date-range, [type=search]').prop('disabled', false);
    });
    function getParam(){
        window.location.href = "{{ route('user.leads.index') }}?brandKey=" + encodeURIComponent($('#brand').val()) + "&dateRange=" + encodeURIComponent($('#date-range').val()) + "&listType=" + encodeURIComponent($("#lead_type").val());
    }
    $('#brand').on('change',getParam);

    $('#mylead , #alllead').on('click', function () {
        $("#lead_type").val($(this).data('id'));
        getParam();
    });

    //Create Lead
    $('#create-lead-form').on('submit', function (e) {
        e.preventDefault();
        $('.page-loader-wrapper').css({'display': 'block', 'background': 'rgba(238, 238, 238, 0.7)'});
        AjaxRequestPost('{{ route('user.lead.create') }}',$(this).serialize(),'Lead created successfully');
    });

    // Update lead id to change status
    $("#LeadTable").on("click", ".statusChange", function () {
        $('#status_hdn').val($(this).data('id'));
    });
});


//change Status
$('#changeStatusBtn').on('click', function(e){
    e.preventDefault();
    var lead_id = $('#status_hdn').val();
    var LeadStatus = $('#lead-status').val();

    if(LeadStatus != ''){
        console.log(LeadStatus);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('changeUserLeadStatus') }}",
            data: {'lead_id': lead_id, 'LeadStatus': LeadStatus},
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

//Lead Assing to Agent
$("#LeadTable").on("click", ".cxm-assing", function(){
    console.log('cxm...');
    var lead_id = $(this).data('id');
    document.getElementById("lead_hdn").value = lead_id;
    console.log(lead_id);
});

//Assing to agent
$('#assingAgentBtn').on('click', function(e){
    e.preventDefault();
    var lead_id =  $('#lead_hdn').val();
    var agent_id =  $('#agent_key').val();

    console.log(agent_id);

    if(agent_id != ''){
        console.log(agent_id);
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "{{ route('assignedLead') }}",
            data: {'lead_id': lead_id, 'agenti_id': agent_id},
            success: function(data){
                $("#assignLead").modal('hide');

                swal("Good job!", "Lead Assign to sales Agent successfully!", "success")
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

function cxmCopyInvoiceURL(brnadUrl, invoiceId){
    //let cxmInvoiceUrl = '{{ url("/payment") }}/' + invoiceId;
    let cxmInvoiceUrl = brnadUrl+'checkout?invoicekey='+invoiceId;
    let $cxmTemp = $("<input>");
    $('.cxm-copy-invoice-url').append($cxmTemp);
    $cxmTemp.val(cxmInvoiceUrl).select();
    document.execCommand("copy");
    $cxmTemp.remove();
}

//Create Invoice
$('#invoice-Form').on('submit', function(e){
    e.preventDefault();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('invoice.store') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
        success: function(data){
            $("#invoice-Form")[0].reset();
            console.log(data);
            $("#invoiceModal").modal('hide');
            var key = data.invoice_key;
            var brandUrl = data.brand_url;
            console.log(key);
            swal({
                title: "Good job!",
                text: "Invoice successfully Created!",
                icon: "success",
                buttons: {
                    cancel: {
                        text: "OK",
                        value: null,
                        visible: true,
                        className: "btn-primary",
                        closeModal: true,
                    },
                    confirm: {
                        text: "Copy Invoice URL",
                        value: key,
                        className: "cxm-copy-invoice-url"
                    }
                },
                dangerMode: true,
            })
            .then(() => {

                if(key){
                    cxmCopyInvoiceURL(brandUrl, key);
                    swal("Job done!", "Copy Invoice URL successfully!", "success", {timer: 1000});
                    location.reload();
                } else {
                    location.reload();

                }
            });
        },
        error: function(){
            swal("Errors!", "Request Fail!", "error");
        }
    });
});

//Create Team Member
{{--$('#addLeadBtn').on('click', function(e){--}}
{{--    e.preventDefault();--}}
{{--    $('.page-loader-wrapper').css({'display':'block', 'background':'rgba(238, 238, 238, 0.7)'});--}}

{{--    var team_key    =  $('#team_hnd').val();--}}
{{--    var brand       =  $('#brand_key').val();--}}
{{--    var name        =  $('#name').val();--}}
{{--    var email       =  $('#email').val();--}}
{{--    var source      =  $('#source').val();--}}
{{--    var phone       =  $('#phone').val();--}}
{{--    var value       =  $('#value').val();--}}
{{--    var title       =  $('#title').val();--}}

{{--    console.log('Brand Key: '+brand);--}}

{{--    $.ajax({--}}
{{--        type: "GET",--}}
{{--        dataType: "json",--}}
{{--        url: "{{ route('user.lead.create') }}",--}}
{{--        data: {--}}
{{--            'team_key': team_key,--}}
{{--            'brand_key': brand,--}}
{{--            'name': name,--}}
{{--            'email': email,--}}
{{--            'phone': phone,--}}
{{--            'source': source,--}}
{{--            'value': value,--}}
{{--            'title': title,--}}
{{--        },--}}
{{--        success: function(data){--}}
{{--            $('.page-loader-wrapper').css('display', 'none');--}}
{{--            $("#leadModal").modal('hide');--}}

{{--            swal("Good job!", "Team successfully Created!", "success")--}}
{{--            .then(() => {--}}
{{--                location.reload();--}}
{{--            });--}}
{{--        },--}}
{{--        error: function(){--}}
{{--          $('.page-loader-wrapper').css('display', 'none');--}}
{{--          swal("Error!", "Request Fail!", "error");--}}
{{--        }--}}
{{--    });--}}
{{--});--}}




$('#team_hnd').on('change', function() {
  var team = $(this).val();
  console.log(team);
  $.ajax({
        type: "GET",
        url: "brandteam/"+team,
            success: function (data) {
                console.log(data);
                var len = data.length;
                console.log(len);
                $("#brand_key").empty();
                for( var i = 0; i<len; i++){
                    var id = data[i]['brand_key'];
                    var name = data[i]['brand_name'];
                    $("#brand_key").append("<option value='"+id+"'>"+name+"</option>");
                }
                $("#brand_key").val("").selectpicker('refresh');
            }
        });
});

//Get Lead Comments
$("#LeadTable").on("click", ".LeadComments", function(){
    console.log('test');
    var lead_id = $(this).data('id');
    document.getElementById("leadId").value = lead_id;

    console.log(lead_id);

    $.ajax({
        type: "GET",
        datatype: 'JSON',
        url: "/userleadcomments/"+lead_id,
        success: function (data) {
            console.log(data);
            console.log('{{Auth::user()->id}}');
            let LoginUserId = '{{Auth::user()->id}}';

            $("#lead_comments_data").empty();
            if(data.length<1){
                $("#lead_comments_data").append('<div class="alert alert-info mx-3">Data not found.</div>');
            }
            $.each(data, function(i,v){
                let activeClass = 'text-right';
                if(LoginUserId == v.creatorid){
                    activeClass = 'text-left';
                }

                let colorClass = '';
                if(v.type == 'admin'){
                    colorClass = 'bg-blue';
                }else if(v.type == 'lead'){
                    colorClass = 'bg-indigo';
                }else if(v.type == 'ppc'){
                    colorClass = 'bg-green';
                }else if(v.type == 'qa'){
                    colorClass = 'bg-lime';
                }else{
                    colorClass = 'bg-amber';
                }

                console.log(i+'|'+v.id);

                $("#lead_comments_data").append('<div class="'+activeClass+'"><div class="mb-0"><span class="badge '+colorClass+' rounded-0 mb-0" style="text-transform: capitalize"><i class="zmdi zmdi-account"></i> '+v.userName+' - '+v.type+'</span><span class="badge '+colorClass+' rounded-0 mb-0"><i class="zmdi zmdi-calendar"></i> '+v.commentDate+'</span></div><div class="p-2 pb-0 mb-2">'+v.comment_text+'</div></div>');
            });

        }
    });
});

// Create Lead Comments

$('#lead_comments_form').on('submit', function(e){
    e.preventDefault();

    console.log('Comments Form');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: "{{ route('userCreateComments') }}",
        method:'POST',
        data: $(this).serialize(), // get all form field value in serialize form
            success: function(data){
                    console.log(data.leadid);
                    $("#lead_comments_form")[0].reset();
                    //swal("Good job!", "Invoice successfully Paid!", "success");
                    $.ajax({
                        type: "GET",
                        datatype: 'JSON',
                        url: "/userleadcomments/"+data.leadid,
                        success: function (data) {
                            let LoginUserId = '{{Auth::user()->id}}';
                            $("#lead_comments_data").empty();
                            if(data.length<1){
                                $("#lead_comments_data").append('<div class="alert alert-warning">Data not found.</div>');
                            }
                            $.each(data, function(i,v){
                                let activeClass = 'text-right';
                                if(LoginUserId == v.creatorid){
                                    activeClass = 'text-left';
                                }

                                let colorClass = '';
                                if(v.type == 'admin'){
                                    colorClass = 'bg-blue';
                                }else if(v.type == 'lead'){
                                    colorClass = 'bg-indigo';
                                }else if(v.type == 'ppc'){
                                    colorClass = 'bg-green';
                                }else if(v.type == 'qa'){
                                    colorClass = 'bg-lime';
                                }else{
                                    colorClass = 'bg-amber';
                                }
                                console.log(i+'|'+v.id);

                                $("#lead_comments_data").append('<div class="'+activeClass+'"><div class="mb-0"><span class="badge '+colorClass+' rounded-0 mb-0" style="text-transform: capitalize"><i class="zmdi zmdi-account"></i> '+v.userName+' - '+v.type+'</span><span class="badge '+colorClass+' rounded-0 mb-0"><i class="zmdi zmdi-calendar"></i> '+v.commentDate+'</span></div><div class="p-2 pb-0 mb-2">'+v.comment_text+'</div></div>');
                            });
                        }
                    });
                },
                error: function(){
                    swal("Errors!", "Request Fail!", "error");
                }
    });


});


</script>
