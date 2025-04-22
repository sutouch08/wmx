var click = 0;
//--- เพิ่มเอกสารโอนคลังใหม่
function add() {
  if(click == 0) {
    click = 1;

    clearErrorByClass('h');

    let h = {
      'date_add' : $('#date').val(),
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
      $('#from-warehouse').hasError();
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
      $('#from-warehouse').hasError();
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
            let uuid = get_uuid();
            window.location.href = HOME + 'edit/'+ds.code+'/'+uuid;
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


//--- update เอกสาร
function update() {
  $('.h').removeClass('has-error');

  //--- ไอดีเอกสาร สำหรับส่งไปอ้างอิงการแก้ไข
  var code = $('#transfer_code').val();

  //--- คลังต้นทาง
  var from_warehouse = $('#from_warehouse_code').val();
  var old_from_wh = $('#old_from_warehouse_code').val();
  //--- คลังปลายทาง
  var to_warehouse = $('#to_warehouse_code').val();
  var old_to_wh = $('#old_to_warehouse_code').val();
  //--  วันที่เอกสาร
  var date_add = $('#date').val();
  //--- หมายเหตุ
  var remark = $('#remark').val();

  //--- ตรวจสอบไอดี
  if(code == ''){
    swal('Error !', 'ไม่พบเลขที่เอกสาร', 'error');
    return false;
  }

  //--- ตรวจสอบวันที่
  if( ! isDate(date_add)){
    swal('วันที่ไม่ถูกต้อง');
    $('#date').addClass('has-error');
    return false;
  }

  //--- ตรวจสอบคลังต้นทาง
  if(from_warehouse == ''){
    swal('กรุณาเลือกคลังต้นทาง');
    $('.f').addClass('has-error');
    return false;
  }

  //--- ตรวจสอบคลังปลายทาง
  if(to_warehouse == ''){
    swal('กรุณาเลือกคลังปลายทาง');
    $('.t').addClass('has-error');
    return false;
  }

  //--- ตรวจสอบว่าเป็นคนละคลังกันหรือไม่ (ต้องเป็นคนละคลังกัน)
  if( from_warehouse == to_warehouse){
    swal('คลังต้นทางต้องไม่ตรงกับคลังปลายทาง');
    $('.f').addClass('has-error');
    $('.t').addClass('has-error');
    return false;
  }

  //--- ตรวจสอบหากมีการเปลี่ยนคลัง ต้องเช็คก่อนว่ามีการทำรายการไปแล้วหรือยัง
  if(from_warehouse != old_from_wh || to_warehouse != old_to_wh)
  {
    $.ajax({
      url:HOME + 'is_exists_detail/'+code,
      type:'POST',
      cache:false,
      success:function(rs)
      {
        if(rs === 'exists')
        {
          swal({
            title:'Warning !',
            text:'มีการทำรายการแล้วไม่สามารถเปลี่ยนคลังได้',
            type:'warning'
          });

          return false;
        }
        else
        {
          do_update(code, date_add, from_warehouse, to_warehouse, remark);
        }
      }
    })
  }
  else
  {
    do_update(code, date_add, from_warehouse, to_warehouse, remark);
  }
}



function do_update(code, date_add, from_warehouse, to_warehouse, remark)
{
	var api = $('#api').val();
	var wx_code = $('#wx_code').val();

  load_in();
  //--- ถ้าไม่มีอะไรผิดพลาด ส่งข้อมูไป update
  $.ajax({
    url: HOME + 'update/'+code,
    type:'POST',
    cache:'false',
    data:{
      'date_add' : date_add,
      'from_warehouse' : from_warehouse,
      'to_warehouse' : to_warehouse,
      'remark' : remark,
			'wx_code' : wx_code
    },
    success:function(rs){
      load_out();

      var rs = $.trim(rs)
      if( rs == 'success'){
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);

      }else{
        swal('Error', rs, 'error');
      }
    }
  });
}



//--- แก้ไขหัวเอกสาร
function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}



//---  บันทึกเอกสาร
function save() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('h');

    let h = {
      'code' : $('#transfer_code').val(),
      'date_add' : $('#date').val(),
      'from_warehouse' : $('#from-warehouse').val(),
      'to_warehouse' : $('#to-warehouse').val()
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
      $('#from-warehouse').hasError();
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
      $('#from-warehouse').hasError();
      $('#to-warehouse').hasError();
      click = 0;
      return false;
    }

    //--- check temp
    $.ajax({
      url:HOME + 'check_temp_exists/'+h.code,
      type:'POST',
      cache:'false',
      success:function(rs) {
        //--- ถ้าไม่มียอดค้างใน temp
        if( rs.trim() == 'not_exists') {
          //--- ส่งข้อมูลไป formula
          saveTransfer(h.code);
        }
        else{
          click = 0;
          beep();
          showError('พบรายการที่ยังไม่โอนเข้าปลายทาง กรุณาตรวจสอบ');
        }
      },
      error:function(rs) {
        click = 0;
        beep();
        showError(rs);
      }
    });
  }
}


function saveAsRequest() {
  let code = $('#transfer_code').val().trim();
  load_in();

  $.ajax({
    url:HOME + 'save_as_request',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        if(ds.status == 'success') {
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
        else if(ds.status == 'warning') {
          swal({
            title:'Warning',
            text:ds.message,
            type:'warning',
            html:true
          }, () => {
            goDetail(code);
          });
        }
        else {
          swal({
            title:'Error!',
            text:ds.message,
            type:'error',
            html:true
          });
        }
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error',
          html:true
        });
      }
    }
  });
}


function saveTransfer(code) {
  load_in();

  $.ajax({
    url:HOME + 'save_transfer/'+code,
    type:'POST',
    cache:false,
    success:function(rs) {
      load_out();
      click = 0;

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



function unSave(){
  var code = $('#transfer_code').val();
  swal({
		title: 'คำเตือน !!',
		text: 'หากต้องการยกเลิกการบันทึก คุณต้องยกเลิกเอกสารนี้ใน SAP ก่อน ต้องการยกเลิกการบันทึก '+ code +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการ',
		cancelButtonText: 'ไม่ใช่',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:HOME + 'unsave_transfer/'+ code,
			type:"POST",
      cache:"false",
			success: function(rs){
				var rs = $.trim(rs);
				if( rs == 'success' ){
					swal({
						title:'Success',
						text: 'ดำเนินการเรียบร้อยแล้ว',
						type: 'success',
						timer: 1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);

				}else{
					swal("ข้อผิดพลาด", rs, "error");
				}
			}
		});
	});
}

$('#fromWhsCode').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $(this).val(code);
      $('#from_warehouse_code').val(code);
      $('#from_warehouse').val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $(this).val('');
      $('#from_warehouse_code').val('');
      $('#from_warehouse').val('');
    }
  }
});

$('#toWhsCode').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $(this).val(code);
      $('#to_warehouse_code').val(code);
      $('#to_warehouse').val(name);
      $('#remark').focus();
    }
    else
    {
      $(this).val('');
      $('#to_warehouse_code').val('');
      $('#to_warehouse').val('');
    }
  }
});

$('#from_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $('#from_warehouse').val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $('#from_warehouse_code').val('');
      $('#from_warehouse').val('');
    }
  }
});


$('#to_warehouse_code').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function() {
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $('#to_warehouse').val(name);
      $('#remark').focus();
    }
    else
    {
      $('#to_warehouse_code').val('');
      $('#to_warehouse').val('');
    }
  }
});




$('#from_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#from_warehouse_code').val(code);
      $(this).val(name);
      $('#to_warehouse_code').focus();
    }
    else
    {
      $('#from_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#to_warehouse').autocomplete({
  source:BASE_URL + 'auto_complete/get_warehouse_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    if(arr.length == 2)
    {
      code = arr[0];
      name = arr[1];
      $('#to_warehouse_code').val(code);
      $(this).val(name);
      $('#remark').focus();
    }
    else
    {
      $('#to_warehouse_code').val('');
      $(this).val('');
    }
  }
});


$('#wx_code').autocomplete({
	source:BASE_URL + 'auto_complete/get_wx_code',
	autoFocus:true,
	close:function() {
		var rs = $(this).val();
		if(rs == 'not found') {
			$(this).val('');
		}
	}
})
