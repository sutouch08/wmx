var click = 0;

$('#date_add').datepicker({
  dateFormat: 'dd-mm-yy',
  onClose:function(sd) {
    $('#require_date').datepicker('option', 'minDate', sd)
  }
});


$('#require_date').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#date_add').datepicker('option', 'maxDate', sd)
  }
});


$('#vender_code').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete("widget").css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#vender_code').val(arr[0]);
      $('#vender_name').val(arr[1]);
    }else{
      $('#vender_code').val('');
      $('#vender_name').val('');
    }
  }
});


$('#vender_name').autocomplete({
  source: BASE_URL + 'auto_complete/get_vender_code_and_name',
  autoFocus:true,
  open:function(event, ui){
    $(this).autocomplete('widget').css({
      'width' : 'auto',
      'min-width' : $(this).width() + 'px'
    })
  },
  close:function(){
    var arr = $(this).val().split(' | ');
    if(arr.length == 2){
      $('#vender_code').val(arr[0]);
      $('#vender_name').val(arr[1]);
    }else{
      $('#vender_code').val('');
      $('#vender_name').val('');
    }
  }
});


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'doc_date' : $('#date_add').val().trim(),
      'due_date' : $('#require_date').val().trim(),
      'vender_code' : $('#vender_code').val().trim(),
      'vender_name' : $('#vender_name').val().trim(),
      'remark' : $('#remark').val().trim()
    }

    if( ! isDate(h.doc_date)) {
      $('#doc_date').hasError();
      click = 0;
      return false;
    }

    if(h.vender_code.length == 0) {
      $('#vender_code').hasError();
      click = 0;
      return false;
    }

    if(h.vender_name.length == 0) {
      $('#vender_name').hasError();
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

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            goEdit(ds.code);
          }
          else {
            click = 0;
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
        click = 0;
        beep();
        showError(rs);
      }
    })
  }
}


function update(){
  if(click == 0) {
    click = 1;
    clearErrorByClass('edit');

    let h = {
      'code' : $('#code').val(),
      'vender_code' : $('#vender_code').val().trim(),
      'vender_name' : $('#vender_name').val().trim(),
      'doc_date' : $('#date_add').val(),
      'due_date' : $('#require_date').val(),
      'remark' : $('#remark').val().trim()
    }

    if( ! isDate(h.doc_date)) {
      $('#date_add').hasError();
      click = 0;
      return false;
    }

    if(h.vender_code.length == 0) {
      $('#vender_code').hasError();
      click = 0;
      return false;
    }

    if(h.vender_name.length == 0) {
      $('#vender_name').hasError();
      click = 0;
      return false;
    }

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;

        if(rs.trim() == 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          $('.edit').attr('disabled', 'disabled');
          $('#btn-update').addClass('hide');
          $('#btn-edit').removeClass('hide');
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


function save(){
  if(click == 0) {
    click = 1;
    clearErrorByClass('qty');

    let code = $('#code').val();
    let err = 0;
    let rows = 0;

    $('.qty').each(function() {
      let qty = parseDefault(parseFloat($(this).val()), 0);
      if(qty <= 0) {
        $(this).hasError();
        err++;
      }
      rows++;
    });

    if(err > 0) {
      beep();
      click = 0;
      showError('จำนวนไม่ถูกต้อง กรุณาแก้ไข');
      return false;
    }

    if(rows == 0) {
      beep();
      click = 0;
      showError('ไม่พบรายการสินค้า');
      return false;
    }

    $.ajax({
      url:HOME + 'save',
      type:'POST',
      cache:false,
      data:{
        'code' : code
      },
      success:function(rs) {
        click = 0;

        if(rs.trim() === 'success'){
          swal({
            title:'Success',
            text:'',
            type:'success',
            timer:1000
          });

          setTimeout(function(){
            viewDetail(code)
          }, 1200);

        }
        else{
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        click = 0;
        showError(rs);
      }
    });
  }
}


function unsave() {
  swal({
    title:'ย้อนสถานะ',
    text:'ต้องการย้อนสถานะเอกสารให้กลับมาแก้ไขได้หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    closeOnConfirm:true
  }, function() {
    load_in();

    let code = $('#code').val();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'unsave',
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


function closePO(){
  let code = $('#code').val();

  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการปิด '" + code + "' หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function(){

    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'close_po',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success'){
            swal({
              title:'Closed',
              text:'Close PO successfull',
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


function createReceive(code) {
  let target = BASE_URL + 'inventory/receive_po/add_new/'+code;
  window.open(target, '_blank');
}
