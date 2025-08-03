function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}

//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}


function approve() {
	let code = $('#code').val();

  swal({
    title:'Approval',
    text:'ต้องการอนุมัติเอกสารนี้หรือไม่ ?',
    type:'info',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    confirmButonColor:'#81a87b',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      doApprove(code);
    }, 100);
  })
}


function doApprove(code) {
  load_in();

  $.ajax({
    url:HOME + 'do_approve',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Approved',
          type:'success',
          timer:1000
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
}


function reject() {
	let code = $('#code').val();

  swal({
    title:'Rejection',
    text:'ต้องการ Reject เอกสารนี้หรือไม่ ?',
    type:'info',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    confirmButonColor:'#DD6855',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      doReject(code);
    }, 100);
  })
}


function doReject(code) {
  load_in();

  $.ajax({
    url:HOME + 'do_reject',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Rejected',
          type:'success',
          timer:1000
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
}


function confirmCancel(code){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการยกเลิก '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
		}, function() {
      $('#cancle-code').val(code);
      $('#cancle-reason').val('').removeClass('has-error');
      $('#cancle-modal').modal('show');
	});
}


function cancle(code){
  var reason = $('#cancle-reason').val().trim();

  if(reason.length < 5)
  {
    $('#cancle-modal').modal('show');
    return false;
  }

  load_in();

  setTimeout(() => {
    $.ajax({
      url:HOME + 'cancel/'+code,
      type:"POST",
      cache:"false",
      data:{
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
            goBack();
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
  }, 100);
}


function doCancle() {
	let code = $('#cancle-code').val();
	let reason = $.trim($('#cancle-reason').val());

	if( reason.length < 5) {
		$('#cancle-reason').addClass('has-error').focus();
		return false;
	}

	$('#cancle-modal').modal('hide');

	return cancle(code);
}


$('#cancle-modal').on('shown.bs.modal', function() {
	$('#cancle-reason').focus();
});
