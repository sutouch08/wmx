var click = 0;

function confirmSave() {
  closeMoreMenu();

  let code = $('#code').val();

  swal({
    title:'Confirm Close',
    text:'เมื่อปิดเอกสารแล้วจะไม่สามารถแก้ไขได้อีก </br/>ต้องการปิดเอกสารนี้หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'check_temp_exists/'+code,
        type:'POST',
        cache:false,
        success:function(rs) {
          if(rs.trim() === 'not_exists') {
            save();
          }
          else {
            beep();
            showError('พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ');
          }
        },
        error:function(rs) {
          beep();
          showError(rs);
        }
      });
    }, 100);
  });
}


function save() {
  let code = $('#code').val();

  $.ajax({
    url:HOME + 'save',
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
          viewDetail(code);
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


function add() {
  clearErrorByClass('r');

  let h = {
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'reference' : $('#reference').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      "data" : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          goEdit(ds.code);
        }
        else {
          showError(ds.message);
        }
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


function rollback() {
  let code = $('#code').val();

  swal({
    title:'ย้อนสถานะ',
    text:'ต้องการย้อนสถานะ '+code+' กลับมาแก้ไขใหม่หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
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
              goEdit(code);
            }, 1200);
          }
          else {
            beep();
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      })
    }, 100);
  })
}


function confirmCancel() {
  let code = $('#code').val();
  closeMoreMenu();

  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการยกเลิก ' + code + ' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
	}, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'cancel',
        type:'POST',
        cache:false,
        data:{
          'code' : code
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
              refresh();
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
	});
}
