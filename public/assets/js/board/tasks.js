"use strict";
if (document.getElementById("user_id") && document.getElementById("client_id") ) {

  document.getElementById("user_id").selectedIndex = -1;
  document.getElementById("client_id").selectedIndex = -1;
}
$(document).ready(() => {
  //select2
  setTimeout(() => {
    $("#user_id").select2({
      placeholder: "Select user",
    });
    $("#client_id").select2({
      placeholder: "Select Client",
    });
   
  }, 100);
});
function queryParams2(p){

  var from = $('#tasks_start_date').val();
  var to = $('#tasks_end_date').val();
  if(from !== '' && to !== ''){
    from = moment(from).format('YYYY-MM-DD');
    to = moment(to).format('YYYY-MM-DD');
  }
  return {
    "user_id": $('#user_id').val(),
    "client_id": $('#client_id').val(),
    "project": $('#projects_name').val(),
    "status": $('#tasks_status').val(),
    "from": from,
    "to": to,
    limit:p.limit,
    sort:p.sort,
    order:p.order,
    offset:p.offset,
    search:p.search
  };
}

function queryParams(p){

  var from = $('#tasks_start_date').val();
  var to = $('#tasks_end_date').val();
  if(from !== '' && to !== ''){
    from = moment(from).format('YYYY-MM-DD');
    to = moment(to).format('YYYY-MM-DD');
  }
  return {
    "user_id": $('#user_id').val(),
    "client_id": $('#client_id').val(),
    "project": $('#projects_name').val(),
    "status": $('#tasks_status').val(),
    "from": from,
    "to": to,
    limit:p.limit,
    sort:p.sort,
    order:p.order,
    offset:p.offset,
    search:p.search
  };
}

$('#fillter-tasks').on('click',function(e){
  e.preventDefault();
  $('#tasks_list').bootstrapTable('refresh');
});
    
$(function() {

  $('#tasks_between').daterangepicker({

        showDropdowns: true,
        alwaysShowCalendars:true,
        autoUpdateInput: false,
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate:moment().subtract(29, 'days'),
        endDate:moment(),
        locale: {
          "format": "DD/MM/YYYY",
          "separator": " - ",
          "cancelLabel": 'Clear'
        }
  });

  $('#tasks_between').on('apply.daterangepicker', function(ev, picker) {
      $('#tasks_start_date').val(picker.startDate.format('MM/DD/YYYY'));
      $('#tasks_end_date').val(picker.endDate.format('MM/DD/YYYY'));
      $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
  });

  $('#tasks_between').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
      $('#tasks_start_date').val('');
      $('#tasks_end_date').val('');
  });

});

!function (a) {
    "use strict";
    var t = function () {
        this.$body = a("body")
    };
    t.prototype.init = function () {
        a('[data-plugin="dragula"]').each(function () {
            var t = a(this).data("containers"), n = [];
            if (t) for (var i = 0; i < t.length; i++) n.push(a("#" + t[i])[0]); else n = [a(this)[0]];
            var r = a(this).data("handleclass");
            r ? dragula(n, {
                moves: function (a, t, n) {
                    return n.classList.contains(r)
                }
            }) : dragula(n).on('drop', function (el, target, source, sibling) {

                var sort = [];
                $("#"+target.id+" > div").each(function () {
                    sort[$(this).index()]=$(this).attr('id');
                });


                var id = el.id;
                var old_status = $("#"+source.id).data('status');
                var new_status = $("#"+target.id).data('status');
                var project_id = '1';
                console.log(id);
                console.log(old_status);
                console.log(new_status);

                $("#"+source.id).parent().find('.count').text($("#"+source.id+" > div").length);
                $("#"+target.id).parent().find('.count').text($("#"+target.id+" > div").length);
                
                $.ajax({
                    url: base_url+'board/task_card_change',
                    type: 'GET',
                    data: "id="+id+"&card_id="+new_status+"&old_card_id="+old_status+"&sort="+sort,
                    dataType: "json",
                    success: function(data){
                        sort = data['sort'];
                    }
                });
                

            });


        })
    }, a.Dragula = new t, a.Dragula.Constructor = t
}(window.jQuery), function (a) {
    "use strict";
    a.Dragula.init()
}(window.jQuery);

function queryParams1(p){
  return {
   "id": $('#xyz').data('id'),
    limit:p.limit,
    sort:p.sort,
    order:p.order,
    offset:p.offset,
    search:p.search
  };
}