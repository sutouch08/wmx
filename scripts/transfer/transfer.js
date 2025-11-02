function addNew(){
  window.location.href = HOME + 'add_new';
}


function edit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function goDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function goCancel(code) {
  swal({
    title:'คุณแต่ใจ ?',
    text:'ต้องการยกเลิกเอกสารนี้หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'#DD6B55',
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    $('#cancel-code').val(code);
    $('#cancel-reason').val('');
    $('#cancel-modal').modal('show');
  })
}


$('#cancel-modal').on('shown.bs.modal', function() {
  $('#cancel-reason').focus();
});


function doCancel() {
  let code = $('#cancel-code').val();
  let reason = $('#cancel-reason').val().trim();

  if( reason.length < 10) {
    $('#cancel-reason').hasError().focus();
    return false;
  }

  $('#cancel-modal').modal('hide');

  setTimeout(() => {
    load_in();

    $.ajax({
      url:HOME + 'cancel',
      type:"POST",
      cache:"false",
      data:{
        "code" : code,
        "reason" : reason
      },
      success: function(rs) {
        load_out();

        if( rs.trim() == 'success' ) {
          swal({
            title:'Success',
            text: 'ยกเลิกเอกสารเรียบร้อยแล้ว',
            type: 'success',
            timer: 1000
          });

          setTimeout(function(){
            refresh();
          }, 1200);
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }, 200);
}


$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd){
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#posting-date').datepicker({
  dateFormat:'dd-mm-yy'
})


function printTransfer(code) {
  let center = ($(document).width() - 800) /2;
  let target = HOME + 'print_transfer/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}


function sendToERP(code) {
  load_in();

  $.ajax({
    url:HOME + 'send_to_erp',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Success',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          refresh();
        }, 1200);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}
