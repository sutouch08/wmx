function goDelete(code){
	swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'ใช่, ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: true
		}, function(){
			$('#cancle-reason').val('');
			$('#cancle-code').val(code);
			$('#force-cancel').prop('checked', false);
			$('#cancle-modal').on('shown.bs.modal', function() {
				$('#cancle-reason').focus();
			});

			setTimeout(function() {
				$('#cancle-modal').modal('show');
			}, 200);
	});
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $('#cancle-reason').val();
	let force_cancel = $('#force-cancel').is(':checked') ? 1 : 0;

	if(reason.length == 0) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}
	else {
		$('#cancle-reason').removeClass('has-error');
	}

	$('#cancle-modal').modal('hide');

	if(code.length == 0) {
		swal({
			title:'Error!',
			text:'Invalid Document Code',
			type:'error'
		});

		return false;
	}

	load_in();

	$.ajax({
		url: HOME + 'cancle_received',
		type:"POST",
		cache:"false",
		data:{
			"receive_code" : code,
			"reason" : reason,
			"force_cancel" : force_cancel
		},
		success: function(rs){
			load_out();

			var rs = $.trim(rs);
			if( rs == 'success' ) {
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
				swal("Error !", rs, "error");
			}
		},
		error:function(xhr, status, error) {
			load_out();
			swal({
				title:'Error!',
				text:xhr.responseText,
				type:'error',
				html:true
			});
		}
	});
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
	window.location.href = HOME + 'edit/'+ code;
}


function viewDetail(code){
	window.location.href = HOME + 'view_detail/'+ code;
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
function printReceived(){
	var code = $("#receive_code").val();
	var center = ($(document).width() - 800) /2;
  var target = HOME + 'print_detail/'+code;
  window.open(target, "_blank", "width=800, height=900, left="+center+", scrollbars=yes");
}
