function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function goDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


//--- สลับมาใช้บาร์โค้ดในการคีย์สินค้า
function goUseBarcode(){
  var code = $('#move_code').val();
  window.location.href = HOME + 'edit/'+code+'/Y';
}


//--- สลับมาใช้การคื่ย์มือในการย้ายสินค้า
function goUseKeyboard(){
  var code = $('#move_code').val();
  window.location.href = HOME + 'edit/'+code+'/N';
}


function goDelete(code, status){
  var title = 'ต้องการยกเลิก '+ code +' หรือไม่ ?';

	swal({
		title: 'คุณแน่ใจ ?',
		text: title,
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
	}, function() {
    $('#cancle-code').val(code);
    $('#cancle-reason').val('').removeClass('has-error');
    cancle(code);
	});
}


function cancle(code){
	var reason = $.trim($('#cancle-reason').val());

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

  load_in();

  $.ajax({
    url:HOME + 'delete_move/'+code,
    type:"POST",
    cache:"false",
    data:{
      "reason" : reason
    },
    success: function(rs) {
      load_out();
      if( rs.trim() == 'success' ) {
        setTimeout(() => {
          swal({
            title:'Success',
            text: 'ยกเลิกเอกสารเรียบร้อยแล้ว',
            type: 'success',
            timer: 1000
          });

          setTimeout(function(){
            goBack();
          }, 1200);
        }, 200);

      }
      else {
        setTimeout(() => {
          beep();
          showError(rs);
        }, 200);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  });
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 10) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return cancle(code);
}


$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});


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


function printMove(){
	var center = ($(document).width() - 800) /2;
  var code = $('#move_code').val();
  var target = HOME + 'print_move/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}
