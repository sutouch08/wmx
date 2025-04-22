var click = 0;

function add() {
  clearErrorByClass('e');

  let h = {
    'date_add' : $('#date').val().trim(),
    'warehouse_code' : $('#warehouse').val(),
    'reference' : $('#reference').val().trim(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date_add').hasError();
    showError('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    showError('กรุณาระบุคลัง');
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


//---  บันทึกเอกสาร
function save() {
  if(click == 0) {
    click = 1;
    var code = $('#move_code').val();

    //--- check temp
    $.ajax({
      url:HOME + 'check_temp_exists/'+code,
      type:'POST',
      cache:'false',
      success:function(rs){
        var rs = $.trim(rs);
        //--- ถ้าไม่มียอดค้างใน temp
        if( rs == 'not_exists'){
          saveMove(code);
        }
        else{
          click = 0;
          swal({
            title:'ข้อผิดพลาด !',
            text:'พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ',
            type:'error'
          });
        }
      }
    });
  }
}


function saveMove(code) {
  load_in();

  $.ajax({
    url:HOME + 'save_move/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      click = 0;
      load_out();

      if(rs.trim() === 'success') {
        swal({
          title:'Saved',
          text: 'บันทึกเอกสารเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function() {
          goDetail(code);
        }, 1200);
      }
      else {
        beep();
        showError(rs);
      }
    },
    error:function(rs) {
      click = 0;
      beep();
      showError(rs);
    }
  });
}
