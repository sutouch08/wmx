function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code)
{
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code)
{
  window.location.href = HOME + 'view_detail/'+code;
}


function goCancel(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
		}, function(){
			$('#cancle-code').val(code);
			$('#force-cancel').prop('checked', false);
			$('#cancle-reason').val('').removeClass('has-error');

			cancle(code);
	});
}


function cancle(code){
	let reason = $('#cancle-reason').val().trim();

	if(reason.length < 10)
	{
		$('#cancle-modal').modal('show');
		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle',
		type:"POST",
		cache:"false",
		data:{
			"code" : code,
			"reason" : reason
		},
		success: function(rs){
			load_out();

			if( rs.trim() == 'success' ){
				swal({
					title: 'Cancled',
					type: 'success',
					timer: 1000
				});

				setTimeout(function(){
					refresh()
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


function printPO()
{
  let code = $('#code').val();
  let url = HOME + 'print_po/'+code;
  var center = ($(document).width() - 800) /2;
	window.open(url, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
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
