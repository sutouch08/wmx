function addNew(){
  window.location.href = HOME + 'add_new';
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}


function goEdit(code) {
  window.location.href = HOME + 'edit/'+code;
}


function resetFilter() {
  $.ajax({
    url:HOME + 'clear_filter',
    type:'GET',
    cache:false,
    success:function() {
      goBack();
    }
  })
}


function rollback(code) {
  swal({
    title: "ย้อนสถานะ",
    text: "ต้องการย้อนสถานะ '"+code+"' กลับมาแก้ไขหรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    load_in();
    setTimeout(() => {
      $.ajax({
        url:HOME + 'rollback',
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
    }, 100);
  });
}


function sendToErp(code) {
  load_in();

  $.ajax({
    url:HOME + 'do_export',
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


function confirmCancel(code) {
  closeMore();

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
    $('#cancel-code').val(code);
    $('#cancel-reason').val('').removeClass('has-error');

    setTimeout(() => {
      showModal('cancel-modal');
    }, 100);
  });
}


function doCancel() {
  $('#cancel-reason').clearError();

	let code = $('#cancel-code').val();
	let reason = $('#cancel-reason').val().trim();

	if( reason.length < 10) {
    $('#cancel-reason').hasError().focus();
		return false;
	}

	closeModal('cancel-modal');

  setTimeout(() => {
    load_in();

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
            title:'Canceled',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            viewDetail(code);
          }, 1200);
        }
        else {
          swal({
            title:'Error !',
            text:rs,
            type:'error',
            html:true
          }, function() {
            showModal('cancel-modal');
          })
        }
      },
      error:function(rs) {
        load_out();

        swal({
          title:'Error !',
          text:rs.responseText,
          type:'error',
          html:true
        }, function() {
          showModal('cancel-modal');
        })
      }
    });

  }, 200);
}


$('#cancel-modal').on('shown.bs.modal', function() {
	$('#cancel-reason').focus();
});


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
