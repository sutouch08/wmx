function goBack(){
  window.location.href = HOME;
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function edit(id){
  window.location.href = HOME + 'edit/'+id;
}


function goGen() {
  window.location.href = HOME + 'generate';
}


function add() {
  clearErrorByClass('e');

  let h = {
    'whs_id' : $('#warehouse option:selected').val(),
    'whs_code' : $('#warehouse option:selected').data('code'),
    'row' : $('#row').val().trim(),
    'col' : $('#col').val().trim(),
    'loc' : $('#loc').val().trim(),
    'code' : $('#code').val().trim(),
    'full_code' : $('#full-code').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'name' : $('#name').val().trim(),
    'freeze' : $('#freeze').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') == false ? 0 : 1
  };

  if(h.whs_id == "" || h.whs_code == undefined) {
    $('#warehouse').hasError("กรุณาระบุคลัง");
    return false;
  }

  if(h.row.length == 0) {
    $('#row').hasError();
    return false;
  }

  if(h.col.length == 0) {
    $('#col').hasError();
    return false;
  }

  if(h.loc.length == 0) {
    $('#loc').hasError();
    return false;
  }

  if(h.code.length == 0) {
    $('#code').hasError("Code is required");
    return false;
  }

  if(h.full_code.length == 0) {
    $('#full-code').hasError("Invalid location code");
    return false;
  }

  if(h.barcode.length == 0) {
    $('#barcode').hasError("Barcode is required");
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError("Name is required");
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

        if(ds.status == 'success') {
          swal({
            title:'Success',
            text:'เพิ่ม Location สำเร็จต้องการเพิ่มต่อหรือไม่ ?',
            type:'success'
          }, function(isConfirm) {
            if(isConfirm) {
              $('#row').val('');
              $('#col').val('');
              $('#loc').val('');
              $('#code').val('');
              $('#full-code').val('');
              $('#barcode').val('');
              $('#name').val('');

              setTimeout(() => {
                $('#code').focus();
              }, 200);
            }
            else {
              goBack();
            }
          })
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


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'whs_id' : $('#warehouse option:selected').val(),
    'whs_code' : $('#warehouse option:selected').data('code'),
    'row' : $('#row').val().trim(),
    'col' : $('#col').val().trim(),
    'loc' : $('#loc').val().trim(),
    'code' : $('#code').val().trim(),
    'full_code' : $('#full-code').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'name' : $('#name').val().trim(),
    'freeze' : $('#freeze').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') == false ? 0 : 1
  };

  if(h.whs_id == "" || h.whs_code == undefined) {
    $('#warehouse').hasError("กรุณาระบุคลัง");
    return false;
  }

  if(h.row.length == 0) {
    $('#row').hasError();
    return false;
  }

  if(h.col.length == 0) {
    $('#col').hasError();
    return false;
  }

  if(h.loc.length == 0) {
    $('#loc').hasError();
    return false;
  }

  if(h.code.length == 0) {
    $('#code').hasError("Code is required");
    return false;
  }

  if(h.full_code.length == 0) {
    $('#full-code').hasError("Invalid location code");
    return false;
  }

  if(h.barcode.length == 0) {
    $('#barcode').hasError("Barcode is required");
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError("Name is required");
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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          })
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


function updateCode() {
  let row = $('#row').val().trim().toUpperCase();
  let col = $('#col').val().trim();
  let loc = $('#loc').val().trim().toUpperCase();
  let code = row.length == 1 ? row + (col.length > 0 ? "-" + col + (loc.length == 1 ? "-" + loc : "") : "") : "";
  $('#row').val(row);
  $('#loc').val(loc);
  $('#code').val(code);
  updateFullCode();
}


function updateFullCode() {
  let prefix = $('#warehouse option:selected').data('code');
  let code = $('#code').val().trim();
  let fullCode = prefix == undefined ? "" : prefix + "-"+code;
  $('#full-code').val(fullCode);

  updateBarcode();
  updateName();
}


function updateBarcode() {
  let barcode = $('#barcode').val().trim();

  if($('#chk-barcode').is(':checked')) {
    barcode = $('#full-code').val().trim();
    $('#barcode').val(barcode);
  }
  else {
    $('#barcode').val(barcode);
  }
}


function updateName() {
  let name = $('#name').val().trim();
  if($('#chk-name').is(':checked')) {
    name = $('#code').val().trim();
    $('#name').val(name);
  }
  else {
    $('#name').val(name);
  }
}


function toggleCheckAll() {
  if($('#chk-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}


function checkAllRow() {
  if($('#chk-all-row').is(':checked')) {
    $('.chk-row').prop('checked', true);
  }
  else {
    $('.chk-row').prop('checked', false);
  }
}


function checkAllLoc() {
  if($('#chk-all-loc').is(':checked')) {
    $('.chk-loc').prop('checked', true);
  }
  else {
    $('.chk-loc').prop('checked', false);
  }
}


function genZone() {
  clearErrorByClass('e');
  let warehouse_id = $('#warehouse').val();
  let warehouse_code = $('#warehouse option:selected').data('code');
  let rows = [];
  let start = $('#column-start').val().trim();
  let end = $('#column-end').val().trim();
  let digit = $('#column-digit').val();
  let locs = [];
  let freeze = $('#freeze').is(':checked') ? 1 : 0;
  let active = $('#active').is(':checked') == false ? 0 : 1;

  if(warehouse_id == "" || warehouse_code == undefined) {
    $('#warehouse').hasError();
    return false;
  }

  $('.chk-row').each(function() {
    if($(this).is(':checked')) {
      rows.push($(this).val());
    }
  });

  if(rows.length == 0) {
    $('#row').hasError();
    return false;
  }

  if(start == "") {
    $('#column-start').hasError();
    return false;
  }

  if(end == "") {
    $('#column-end').hasError();
    return false;
  }

  digit = parseDefault(parseInt(digit), 2);
  start = parseDefault(parseInt(start), 1);
  end = parseDefault(parseInt(end), 1);

  if(digit == 2 && end > 99) {
    $('#column-end').hasError();
    showError("จำนวนสุดท้ายต้องไม่เกิน 99");
    return false;
  }

  if(digit == 3 && end > 999) {
    $('#column-end').hasError();
    showError("จำนวนสุดท้ายต้องไม่เกิน 999");
    return false;
  }

  if( start > end ) {
    $('#column-start').hasError();
    $('#column-end').hasError();
    showError('ช่วงตัวเลขไม่ถูกต้อง');
    return false;
  }

  $('.chk-loc').each(function() {
    if($(this).is(':checked')) {
      locs.push($(this).val());
    }
  });

  if(locs.length == 0) {
    $('#loc').hasError();
    return false;
  }

  let h = {
    'warehouse_id' : warehouse_id,
    'warehouse_code' : warehouse_code,
    'rows' : rows,
    'digit' : digit,
    'start' : start,
    'end' : end,
    'locs' : locs,
    'freeze' : freeze,
    'active' : active
  };

  load_in();

  $.ajax({
    url:HOME + 'generate_location',
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
          swal({
            title:'Success',
            text:ds.message,
            type:'success',
            html:true
          }, function() {
            goBack();
          });
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


function getDelete(id, code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
  },function() {
    load_in();
    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            });

            $('#row-'+id).remove();
            reIndex();
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

function exportFilter(){
  let code = $('#code').val();
  let uname = $('#u-name').val();
  let customer = $('#customer').val();
  let warehouse = $('#warehouse').val();

  $('#export-code').val(code);
  $('#export-uname').val(uname);
  $('#export-customer').val(customer);
  $('#export-warehouse').val(warehouse);

  var token = $('#token').val();
  get_download(token);
  $('#exportForm').submit();
}



function generateQrcode() {
  if($('.chk:checked').length) {

    let h = [];

    $('.chk:checked').each(function() {
      let code = $(this).data('code');
      let name = $(this).data('name');

      h.push({'code' : code, 'name' : name});
    });

    if(h.length) {

      var mapForm = document.createElement('form');
      mapForm.target = "Map";
      mapForm.method = "POST";
      mapForm.action = HOME + "generate_qrcode";

      var mapInput = document.createElement("input");
      mapInput.type = "hidden";
      mapInput.name = "data";
      mapInput.value = JSON.stringify(h);

      mapForm.appendChild(mapInput);

      document.body.appendChild(mapForm);

      map = window.open("", "Map", "status=0,title=0,height=900,width=800,scrollbars=1");

      if(map) {
        mapForm.submit();
      }
      else {
        swal('You must allow popups for this map to work.');
      }
    }
  }
}
