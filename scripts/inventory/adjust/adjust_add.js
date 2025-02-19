window.addEventListener('load', () => {
  zone_init();
  item_init();
})

$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});

$('#post-date').datepicker({
  dateFormat:'dd-mm-yy'
});

function add() {
  clearErrorByClass('e');

  let h = {
    'date_add' : $('#date_add').val().trim(),
    'posting_date' : $('#post-date').val().trim(),
    'reference' : $('#reference').val().trim(),
    'warehouse_id' : $('#warehouse').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date_add').hasError();
    return false;
  }

  if(h.warehouse_id == "") {
    $('#warehouse').hasError();
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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          //receive ds.data as code
          edit(ds.data);
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
  });
}


function save() {
  let code = $('#code').val();

  load_in();

  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Saved',
          text:'บันทึกรายการเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          viewDetail(code);
        }, 1200);
      }else{
        swal("Error", rs, 'error');
      }
    }
  })
}


function unsave(){
  var code = $('#code').val();
  load_in();
  $.ajax({
    url:HOME + 'unsave',
    type:'POST',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();
      rs = $.trim(rs);
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'Unsaved successfull',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          goEdit(code);
        }, 1200);

      }else{
        swal({
          title:'Error!!',
          text: rs,
          type:'error'
        });
      }
    }
  })
}


function getEdit(){
  $('#date_add').removeAttr('disabled');
  $('#post-date').removeAttr('disabled');
  $('#reference').removeAttr('disabled');
  $('#warehouse').removeAttr('disabled');
  $('#remark').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function updateHeader() {
  clearErrorByClass('e');
  let h = {
    'code' : $('#code').val(),
    'date_add' : $('#date_add').val(),
    'reference' : $('#reference').val().trim(),
    'warehouse_id' : $('#warehouse').val(),
    'remark' : $('#remark').val().trim()
  };

  let warehouse_id = $('#warehouse-id').val(); //-- Current warehouse

  if( ! isDate(h.date_add)) {
    $('#date_add').hasError();
    return false;
  }

  if(h.warehouse_id == "") {
    $('#warehouse').hasError();
    return false;
  }

  if(h.warehouse_id != warehouse_id) {
    swal({
      title:'คำเตือน !',
      text:'มีการเปลี่ยนแปลงคลัง รายการสินค้าจะถูกเคลียร์<br/>ต้องการดำเนินการต่อหรือไม่ ?',
      type:'warning',
      html:true,
      showCancelButton:true,
      cancelButtonText:'No',
      confirmButtonText:'Yes',
      confirmButtonColor:'#DD6855',
      closeOnConfirm:true
    }, function() {
      setTimeout(() => {
        update();
      }, 200);
    })
  }
  else {
    update();
  }
}


function update() {
  clearErrorByClass('e');
  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val(),
    'date_add' : $('#date_add').val().trim(),
    'posting_date' : $('#post-date').val().trim(),
    'reference' : $('#reference').val().trim(),
    'warehouse_id' : $('#warehouse').val(),
    'remark' : $('#remark').val().trim()
  };

  if( ! isDate(h.date_add)) {
    $('#date_add').hasError();
    return false;
  }

  if(h.warehouse_id == "") {
    $('#warehouse').hasError();
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

      if(rs.trim() == 'success') {
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          window.location.reload();
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
}


function zone_init() {
  let warehouse_id = $('#warehouse').val();
  let code = "";

  $('#zone').autocomplete({
    source: BASE_URL + 'auto_complete/get_zone_code_and_name/'+warehouse_id,
    autoFocus:true,
    open:function(event) {
      var $ul = $(this).autocomplete('widget');
      $ul.css('width', 'auto');
    },
    select:function(event, ui) {
      code = ui.item === undefined ? "" : ui.item.code;
      var name = ui.item === undefined ? "" : ui.item.name;
      var id = ui.item === undefined ? "" : ui.item.id;

      if(code !== undefined && code.length) {
        $('#zone').val(code);
        $('#zone-name').val(name);
        $('#zone-code').val(code);
        $('#zone-id').val(id);
      }
      else {
        $('#zone').val('');
        $('#zone-name').val('');
        $('#zone-code').val('');
        $('#zone-id').val('');
      }
    },
    close:function() {
      $('#zone').val(code);
      set_zone();
    }
  })
}


function set_zone() {
  let zone = $('#zone-code').val();

  if(zone.length > 0) {
    $('#zone').attr('disabled', 'disabled');
    $('#btn-set-zone').addClass('hide');
    $('#btn-change-zone').removeClass('hide');

    $('#pd-code').removeAttr('disabled');
    $('#qty-up').removeAttr('disabled');
    $('#qty-down').removeAttr('disabled');
    $('#btn-add').removeAttr('disabled');
    $('#pd-code').focus();
  }
}


function item_init() {
  let code = "";
  $('#pd-code').autocomplete({
    source: BASE_URL + 'auto_complete/get_item_code',
    autoFocus:true,
    open:function(e, ui) {
      var $ul = $(this).autocomplete('widget');
      $ul.css('width', 'auto');
    },
    select:function(e, ui) {
      code = ui.item === undefined ? "" : ui.item.code;
      id = ui.item === undefined ? "" : ui.item.id;

      if(code !== undefined && code.length) {
        $('#pd-id').val(id);
      }
      else {
        $('#pd-id').val('');
      }
    },
    close:function() {
      if(code.length) {
        $('#pd-code').val(code);
        get_stock_zone();
      }
    }
  });
}


function get_stock_zone() {
  let code = $('#pd-code').val().trim();
  let id = $('#pd-id').val();
  let zone_id = $('#zone-id').val();

  if(code.length === 0 || code === 'not found' || id == "") {
    $(this).val('');
    return false;
  }

  if(zone_id == "") {
    return false;
  }

  $.ajax({
    url:HOME + 'get_stock_zone',
    type:'GET',
    cache:false,
    data:{
      'zone_id' : zone_id,
      'product_code' : code,
      'product_id' : id
    },
    success:function(rs) {
      let stock = parseDefault(parseFloat(rs.trim()), 0);
      $('#stock-qty').val(stock);
      setTimeout(() => {
        $('#qty-up').focus();
      }, 200);
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


$('#qty-up').keyup(function(e) {
  let down_qty = parseDefault(parseFloat($('#qty-down').val().trim()), 0);
  let up_qty = parseDefault(parseFloat($(this).val().trim()), 0);

  if(up_qty < 0) {
    $(this).val(0);
  }
  else {
    $(this).val(up_qty);
  }

  if(up_qty > 0 && down_qty != 0){
    $('#qty-down').val(0);
  }

  if(e.keyCode === 13){
    $('#qty-down').focus();
  }
});


$('#qty-down').keyup(function(e){
  let down_qty = parseDefault(parseFloat($(this).val().trim()), 0);
  let up_qty = parseDefault(parseFloat($('#qty-up').val().trim()), 0);
  let stock_qty = parseDefault(parseFloat($('#stock-qty').val()), 0);

  if(down_qty < 0) {
    $(this).val(0);
  }
  else {
    $(this).val(down_qty);
  }

  if(down_qty > stock_qty) {
    $(this).val(stock_qty);
  }

  if(down_qty > 0 && up_qty != 0){
    $('#qty-up').val(0);
  }

  if(e.keyCode === 13){
    add_detail();
  }
})


function add_detail() {
  let h = {
    'code' : $('#code').val(),
    'product_code' : $('#pd-code').val().trim(),
    'product_id' : $('#pd-id').val(),
    'zone_code' : $('#zone-code').val(),
    'zone_id' : $('#zone-id').val(),
    'qty_up' : parseDefault(parseFloat($('#qty-up').val().trim()), 0),
    'qty_down' : parseDefault(parseFloat($('#qty-down').val().trim()), 0)
  }

  if(h.code.length == 0){
    swal('ไม่พบเลขที่เอกสาร');
    return false;
  }

  if(h.product_code.length == 0 || h.product_id == '') {
    swal('กรุณาระบุรหัสสินค้า');
    return false;
  }

  if(h.zone_code.length == 0 || h.zone_id == ''){
    swal('กรุณาระบุโซน');
    return false;
  }

  if(h.qty_up == 0 && h.qty_down == 0){
    swal('กรุณาระบุจำนวนที่จะปรับยอด');
    return false;
  }

  $('#btn-add').attr('disabled');

  load_in();

  $.ajax({
    url:HOME + 'add_detail',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs){
      load_out();

      if(isJson(rs)) {
        let res = JSON.parse(rs);

        if(res.status === 'success') {
          let ds = res.data;

          if($('#row-'+ds.id).length == 1) {
            //--- update ยอดในรายการ
            $('#qty-up-'+ ds.id).text(ds.up);
            $('#qty-down-'+ ds.id).text(ds.down);

            //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
            setColor(ds.id);

            //--- Reset Input control พร้อมสำหรับรายการต่อไป
            getReady();
          }
          else {
            //--- ถ้ายังไม่มีรายการในตารางดำเนินการเพิ่มใหม่
            ds.no = 1;
            var source = $('#detail-template').html();
            var output = $('#detail-table');

            //--- เพิ่มแถวใหม่ต่อท้ายตาราง
            render_append(source, ds, output);

            reIndex('no');

            //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
            setColor(ds.id);

            //--- Reset Input control พร้อมสำหรับรายการต่อไป
            getReady();
          }
        }
        else {
          showError(res.message);
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


//--- Reset Input control พร้อมสำหรับรายการต่อไป
function getReady(){
  $('#pd-code').val('');
  $('#qty-up').val('');
  $('#qty-down').val('');
  $('#pd-code').focus();
}


//--- ไอไลท์แถวที่มีการเปลี่ยนแปลงล่าสุด
function setColor(id){
  //--- เอาสีน้ำเงินออกจากทุกรายการก่อน
  $('.rox').removeClass('blue');

  //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
  $('#row-' + id).addClass('blue');
}


//--- เปลียนโซนใหม่
function changeZone(){
  //--- clear ค่าต่างๆ
  $('#zone-code').val('');
  $('#zone-name').val('');
  $('#zone').val('').removeAttr('disabled');
  $('#qty-up').val('').attr('disabled','disabled');
  $('#qty-down').val('').attr('disabled','disabled');
  $('#pd-code').val('').attr('disabled','disabled');
  $('#btn-change-zone').addClass('hide');
  $('#btn-set-zone').removeClass('hide');
  $('#btn-add').attr('disabled', 'disabled');

  $('#zone').focus();
}


//--- ลบรายการ 1 บรรทัด
function deleteDetail(id, pdCode){
  swal({
		title: 'คุณแน่ใจ ?',
		text: 'ต้องการลบ '+ pdCode +' หรือไม่ ?',
		type: 'warning',
		showCancelButton: true,
		comfirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
	}, function() {
    load_in();
    setTimeout(() => {
      $.ajax({
  			url: HOME + "delete_detail",
  			type:"POST",
  			cache:"false",
  			data:{
  				"id" : id
  			},
  			success: function(rs){
          load_out();
  				if( rs.trim() == 'success' ){
  					swal({
  						title:'Deleted',
  						type: 'success',
  						timer: 1000
  					});

  					$("#row-"+id).remove();

            reIndex();

  				}
          else {
            showError(rs);
  				}
  			},
        error:function(rs) {
          showError(rs);
        }
  		});
    }, 100);
	});
}
