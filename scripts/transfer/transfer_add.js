var click = 0;
//--- เพิ่มเอกสารโอนคลังใหม่
function add() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');

    let h = {
      'date_add' : $('#date').val(),
      'shipped_date' : $('#posting-date').val(),
      'from_warehouse' : $('#from-warehouse').val(),
      'to_warehouse' : $('#to-warehouse').val(),
      'remark' : $('#remark').val().trim()
    }

    //--- ตรวจสอบวันที่
    if( ! isDate(h.date_add))
    {
      click = 0;
      swal('วันที่ไม่ถูกต้อง');
      $('#date').hasError();
      return false;
    }

    //--- ตรวจสอบคลังต้นทาง
    if(h.from_warehouse == '') {
      click = 0;
      swal('คลังต้นทางไม่ถูกต้อง');
      $('#fromWhs').hasError();
      return false;
    }

    //--- ตรวจสอบคลังปลายทาง
    if(h.to_warehouse == ''){
      click = 0;
      swal('คลังปลายทางไม่ถูกต้อง');
      $('#to-warehouse').hasError();
      return false;
    }

    //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
    if( h.from_warehouse == h.to_warehouse) {
      swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
      $('#fromWhs').hasError();
      $('#to-warehouse').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();
        click = 0;

        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          if(ds.status == 'success') {
            window.location.href = HOME + 'edit/'+ds.code;
          }
          else {
            beep();
            showError(ds.message);
          }
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
}


function update() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');

    let h = {
      'code' : $('#code').val(),
      'date_add' : $('#date').val(),
      'shipped_date' : $('#posting-date').val(),
      'from_warehouse' : $('#from-warehouse').val(),
      'to_warehouse' : $('#to-warehouse').val(),
      'remark' : $('#remark').val().trim()
    }

    //--- ตรวจสอบวันที่
    if( ! isDate(h.date_add))
    {
      click = 0;
      swal('วันที่ไม่ถูกต้อง');
      $('#date').hasError();
      return false;
    }

    //--- ตรวจสอบคลังต้นทาง
    if(h.from_warehouse == '') {
      click = 0;
      swal('คลังต้นทางไม่ถูกต้อง');
      $('#fromWhs').hasError();
      return false;
    }

    //--- ตรวจสอบคลังปลายทาง
    if(h.to_warehouse == ''){
      click = 0;
      swal('คลังปลายทางไม่ถูกต้อง');
      $('#to-warehouse').hasError();
      return false;
    }

    //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
    if( h.from_warehouse == h.to_warehouse) {
      swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
      $('#fromWhs').hasError();
      $('#to-warehouse').hasError();
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();
        click = 0;

        if(rs.trim() == 'success') {
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
          beep();
          showError(ds.message);
        }                
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    });
  }
}


function confirmSave() {
  let code = $('#code').val();
  swal({
    title:'บันทึกเอกสาร',
    text:'เมื่อบันทึกแล้วจะไม่สามารถแก้ไขได้อีก <br/>ต้องการดำเนินการต่อหรือไม่',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'#87b87f',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'save',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status === 'success') {
              if(ds.ex == 1) {
                swal({
                  title:'Success',
                  type:'success',
                  timer:1000
                });

                setTimeout(() => {
                  goDetail(code);
                }, 1200);
              }
              else {
                swal({
                  title:'Oop!',
                  text:'บันทึกเอกสารสำเร็จ แต่ส่งข้อมูลไป ERP ไม่สำเร็จ <br/> กรุณากดส่งข้อมูลอีกครั้งภายหลัง',
                  type:'info',
                  html:true
                }, function() {
                  goDetail(code);
                })
              }
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
    }, 100);
  })
}


function getEdit() {
  $('.edit').removeAttr('disabled');

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}
