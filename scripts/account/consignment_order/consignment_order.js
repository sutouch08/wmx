var HOME = BASE_URL + 'account/consignment_order/';

function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


function goEdit(code)
{
  window.location.href = HOME + 'edit/'+code;
}


function cancel(code) {
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  },
  function() {
    $('#cancel-code').val(code);
    $('#cancel-reason').val('').removeClass('has-error');
    $('#cancel-modal').modal('show');
  });
}


function doCancel() {
  $('#cancel-reason').clearError();

  let code = $('#cancel-code').val();
  let reason = $('#cancel-reason').val().trim();

  if(reason.length < 10) {
    $('#cancel-reason').hasError();
    return false;
  }

  $('#cancel-modal').modal('hide');

  load_in();

  setTimeout(() => {
    $.ajax({
      url:HOME + 'cancel',
      type:'POST',
      cache:false,
      data:{
        'code' : code,
        'reason' : reason
      },
      success:function(rs) {
        load_out();

        if(rs.trim() === 'success') {
          swal({
						title: 'Cancled',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }, 100);
}


$('#cancel-modal').on('shown.bs.modal', function() {
	$('#cancel-reason').focus();
});


function sendToErp(code) {
  load_in();

  $.ajax({
    url:HOME + 'send_to_erp/' + code,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'success',
          type:'success',
          timer:1000
        });
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}


$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});



$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});



// JavaScript Document
function printConsignOrder(){
  var code = $('#consign_code').val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_consign/'+ code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}
