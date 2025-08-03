var click = 0;

window.addEventListener('load', () => {
  zoneInit();
});

$('#date_add').datepicker({
  dateFormat:'dd-mm-yy'
});


function save() {
  if(click == 0) {
    click = 1;
    let code = $('#code').val();
    let error = 0;

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

        if(rs.trim() === 'success'){
          swal({
            title:'Saved',
            text:'บันทึกรายการเรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            goDetail(code);
          }, 1200);
        }
        else {
          beep();
          showError(rs);
          click = 0;
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
        click = 0;
      }
    })
  }
}


function getEdit() {
  $('.e').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function updateHeader(){
  let code = $('#code').val();
  let date_add = $('#date_add').val();
  let reference = $('#reference').val();
  let remark = $('#remark').val();

  $.ajax({
    url:HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date_add' : date_add,
      'reference' : reference,
      'remark' : remark
    },
    success:function(rs){

      if(rs.trim() == 'success'){
        swal({
          title:'Updated',
          text:'ปรับปรุงข้อมูลเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        $('#date_add').attr('disabled', 'disabled');
        $('#reference').attr('disabled', 'disabled');
        $('#remark').attr('disabled', 'disabled');
        $('#btn-edit').removeClass('hide');
        $('#btn-update').addClass('hide');
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
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'date_add' : $('#date_add').val(),
      'warehouse_code' : $('#warehouse').val(),
      'reference' : $('#reference').val().trim(),
      'remark' : $('#remark').val().trim()
    };

    if( ! isDate(h.date_add)){
      swal("วันที่ไม่ถูกต้อง");
      click = 0;
      $('#date_add').hasError();
      return false;
    }

    if( h.warehouse_code == '') {
      swal("กรุณาระบุคลัง");
      click = 0;
      $('#warehouse').hasError();
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'data':JSON.stringify(h)
      },
      success:function(rs){
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            goEdit(ds.code);
          }
          else {
            beep();
            showError(ds.message);
          }
        }
        else {
          click = 0;
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
        click = 0;
      }
    });
  }
}


function zoneInit() {
  let warehouse_code = $('#warehouse').val();

  $('#zone-code').autocomplete({
    source: BASE_URL + 'auto_complete/get_zone_code_and_name/'+warehouse_code,
    autoFocus:true,
    close:function(){
      let rs = $(this).val();
      let arr = rs.split(' | ');
      if(arr.length == 2){
        let code = arr[0];
        let name = arr[1];
        $('#zone-code').val(code);
        $('#zone-name').val(name);
      }
      else {
        $('#zone-code').val('');
        $('#zone-name').val('');
      }
    }
  })
}


$('#zone-code').keyup(function(e){
  if(e.keyCode === 13) {
    if($(this).val().length) {
      $('#pd-code').val('').focus();
    }
  }
})


$('#pd-code').autocomplete({
  source: BASE_URL + 'auto_complete/get_item_code',
  autoFocus:true,
  close:function(){
    let rs = $(this).val();
    let arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});


$('#pd-code').keyup(function(e){
  if(e.keyCode === 13){
    clearErrorByClass('c');

    let code = $(this).val().trim();
    let zone = $('#zone-code').val().trim();

    if(code.length == 0 || code == 'not found') {
      $('#pd-code').val('').hasError();
      return false;
    }

    if(zone.length == 0 || zone == 'not found') {
      $('#zone-code').val('').hasError();
      return false;
    }

    $.ajax({
      url:HOME + 'get_stock_zone',
      type:'GET',
      cache:false,
      data:{
        'zone_code' : zone,
        'product_code' : code
      },
      success:function(rs){
        let stock = parseDefault(parseFloat(rs), 0);
        $('#stock-qty').val(stock);
      }
    })

    $('#qty-up').focus();
  }
});


$('#qty-up').keyup(function(e){
  let down_qty = parseDefault(parseFloat($('#qty-down').val()), 0);
  let up_qty = parseDefault(parseFloat($('#qty-up').val()), 0);

  if(e.keyCode === 13){
    if(up_qty <= 0) {
      $(this).val(0);
    }

    $('#qty-down').focus().select();
  }
});


$('#qty-down').keyup(function(e){
  let down_qty = parseDefault(parseFloat($('#qty-down').val()), 0);
  let up_qty = parseDefault(parseFloat($('#qty-up').val()), 0);
  let stock_qty = parseDefault(parseFloat($('#stock-qty').val()), 0);

  if(e.keyCode === 13) {
    if(up_qty < 0 || down_qty < 0) {
      $('#qty-up').hasError();
      $('#qty-down').hasError();
      return false;
    }

    if((up_qty > 0 && down_qty > 0) || (down_qty == 0 && up_qty == 0) || (up_qty == down_qty)) {
      $('#qty-up').hasError();
      $('#qty-down').hasError();
      return false;
    }

    if(down_qty > 0 && down_qty > stock_qty) {
      $('#qty-down').hasError();
      return false;
    }

    add_detail();
  }
})


function add_detail() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('c');

    let h = {
      'code' : $('#code').val().trim(),
      'product_code' : $('#pd-code').val().trim(),
      'zone_code' : $('#zone-code').val().trim(),
      'qty_up' : parseDefault(parseFloat($('#qty-up').val()), 0),
      'qty_down' : parseDefault(parseFloat($('#qty-down').val()), 0)
    }

    if(h.code.length == 0) {
      beep();
      swal('ไม่พบเลขที่เอกสาร');
      click = 0;
      return false;
    }

    if(h.product_code.length == 0) {
      beep();
      $('#pd-code').hasError();
      click = 0;
      swal('กรุณาระบุรหัสสินค้า');
      return false;
    }

    if(h.zone_code.length == 0) {
      beep();
      $('#zone-code').hasError();
      click = 0;
      swal('กรุณาระบุโซน');
      return false;
    }

    if((h.qty_up <= 0 && h.qty_down <= 0) || (h.qty_up == h.qty_down) || (h.qty_up > 0 && h.qty_down != 0) || (h.qty_down > 0 && h.qty_up != 0)) {
      beep();
      $('#qty-up').hasError();
      $('#qty-down').hasError();
      swal('กรุณาระบุจำนวนที่จะปรับยอด');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add_detail',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();
        click = 0;
        if(isJson(rs)) {
          //--- แปลง json ให้เป็น object
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            //--  ตรวจสอบว่ามีรายการปรับยอดอยู่แล้วหรือไม่
            //--- ถ้ามีจะ update ยอด
            if( $('#row-' + ds.row.id ).length == 1){
              //--- update ยอดในรายการ
              $('#qty-up-'+ ds.row.id).text(ds.row.up);
              $('#qty-down-'+ ds.row.id).text(ds.row.down);

              //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
              setColor(ds.row.id);

              //--- Reset Input control พร้อมสำหรับรายการต่อไป
              getReady();
            }
            else {
              //--- ถ้ายังไม่มีรายการในตารางดำเนินการเพิ่มใหม่
              //--- ลำดับล่าสุด

              var source = $('#detail-template').html();
              var output = $('#detail-table');

              //--- เพิ่มแถวใหม่ต่อท้ายตาราง
              render_append(source, ds.row, output);

              //--- เติมสีน้ำเงินในแถวที่มีการเปลี่ยนแปลง
              setColor(ds.row.id);

              reIndex('no');

              //--- Reset Input control พร้อมสำหรับรายการต่อไป
              getReady();
            }
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
        click = 0;
      }
    })
  }
}


//--- Reset Input control พร้อมสำหรับรายการต่อไป
function getReady(){
  $('#pd-code').val('');
  $('#qty-up').val('');
  $('#qty-down').val('');
  $('#stock-qty').val('');
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
  $('#qty-up').val('');
  $('#qty-down').val('');
  $('#pd-code').val('');
  $('#zone-name').val('');
  $('#zone-code').val('').focus();
}


function checkAll() {
  if($('#chk-all').is(':checked')) {
    $('.chk-row').prop('checked', true);
  }
  else {
    $('.chk-row').prop('checked', false);
  }
}


function confirmRemove() {
  let code = $('#code').val().trim();

  if($('.chk-row:checked').length > 0) {
    swal({
      title:'คุณแน่ใจ ?',
      text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
      type:'warning',
      showCancelButton:true,
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      confirmButonColor:'#DD6855',
      closeOnConfirm:true
    }, function() {

      setTimeout(() => {
        let rows = [];

        $('.chk-row:checked').each(function() {
          rows.push($(this).val());
        })

        if(rows.length == 0) {
          swal('กรุณาเลือกรายการ');
          return false;
        }

        load_in();

        $.ajax({
          url:HOME + 'remove_selected_details',
          type:'POST',
          cache:false,
          data:{
            'code' : code,
            'ids' : JSON.stringify(rows)
          },
          success:function(rs) {
            load_out();

            if(rs.trim() === 'success') {
              $('.chk-row:checked').each(function() {
                id = $(this).val();
                $('#row-'+id).remove();
              })

              reIndex();
            }
            else {
              beep();
              showError(rs);
            }
          },
          error:function(rs) {
            load_out();
            beep();
            showError(rs);
          }
        })
      }, 100);
    })
  }
}
