function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code) {
  let uuid = get_uuid();
  $.ajax({
    url:HOME + 'is_document_avalible',
    type:'GET',
    cache:false,
    data:{
      'code' : code,
      'uuid' : uuid
    },
    success:function(rs) {
      if(rs === 'available') {
        window.location.href = HOME + 'edit/'+code+'/'+uuid;
      }
      else {
        swal({
          title:'Oops!',
          text:'เอกสารกำลังถูกเปิด/แก้ไข โดยเครื่องอื่นอยู่ ไม่สามารถแก้ไขได้ในขณะนี้',
          type:'warning'
        });
      }
    }
  });
}


function goDetail(code){
  window.location.href = HOME + 'view_detail/'+code;
}


//--- สลับมาใช้บาร์โค้ดในการคีย์สินค้า
function goUseBarcode(){
  let code = $('#transfer_code').val();
  let uuid = get_uuid();
  window.location.href = HOME + 'edit/'+code+'/'+uuid+'/barcode';
}


//--- สลับมาใช้การคื่ย์มือในการย้ายสินค้า
function goUseKeyboard(){
  let code = $('#transfer_code').val();
  let uuid = get_uuid();
  window.location.href = HOME + 'edit/'+code+'/'+uuid;
}


function goDelete(code, status){
  var title = 'ต้องการยกเลิก '+ code +' หรือไม่ ?';

	swal({
		title: 'คุณแน่ใจ ?',
		text: title,
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
	}, function(){
    $('#cancle-code').val(code);
    $('#cancle-reason').val('').removeClass('has-error');
    cancle(code);
	});
}


function cancle(code){
	let reason = $.trim($('#cancle-reason').val());
  let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

  load_in();

  $.ajax({
    url:HOME + 'delete_transfer/'+code,
    type:"POST",
    cache:"false",
    data:{
      "reason" : reason,
      "force_cancel" : force_cancel
    },
    success: function(rs) {
      load_out();
      var rs = $.trim(rs);
      if( rs == 'success' ) {
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
          swal("ข้อผิดพลาด", rs, "error");
        }, 200);
      }
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


function printTransfer(){
	var center = ($(document).width() - 800) /2;
  var code = $('#transfer_code').val();
  var target = HOME + 'print_transfer/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}
