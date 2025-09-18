function doCancle() {
  $('#reason-id').clearError();
  $('#cancle-reason').clearError();

  let h = {
    'code' : $('#order_code').val(),
    'reason_id' : $('#reason-id').val(),
    'reason' : $('#cancle-reason').val().trim()
  };

  if(h.reason_id == '') {
    $('#reason-id').hasError();
    return false;
  }

	$('#cancle-modal').modal('hide');

	load_in();

  $.ajax({
    url:HOME + 'cancel_order',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
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
          window.location.reload();
        }, 1200);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


function showReason() {
	$('#cancle-reason-modal').modal('show');
}

function showCancleModal() {
  $('#reason-id').val('').removeClass('has-error');
  $('#cancle-reason').val('').removeClass('has-error');
  $('#cancle-modal').modal('show');
}
