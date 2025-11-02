var click = 0;

function addNew() {
  window.location.href = HOME + 'add_new';
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code) {
  window.location.href = HOME + 'view_detail/'+code;
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'role' : $('#role').val(),
      'sell' : $('#sell').is(':checked') ? 1 : 0,
      'lend' : $('#lend').is(':checked') ? 1 : 0,
      'prepare' : $('#prepare').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0
    }

    if(h.code.length == 0) {
      click = 0;
      $('#code').hasError('Required');
      return false;
    }

    if(h.name.length == 0) {
      click = 0;
      $('#name').hasError('Required');
      return false;
    }

    if(h.role == '') {
      click = 0;
      $('#role').hasError();
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data: {
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มข้อมูลเรียบร้อยแล้ว ต้องการเพิ่มอีกหรือไม่ ?',
            type:'success',
            html:true,
            showCancelButton:true,
            cancelButtonText:'No',
            confirmButtonText:'Yes',
            closeOnConfirm:true
          }, function(isConfirm) {
            if(isConfirm) {
              addNew();
            }
            else {
              goBack();
            }
          })
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
    })
  }
}


function update() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'role' : $('#role').val(),
      'sell' : $('#sell').is(':checked') ? 1 : 0,
      'lend' : $('#lend').is(':checked') ? 1 : 0,
      'prepare' : $('#prepare').is(':checked') ? 1 : 0,
      'active' : $('#active').is(':checked') ? 1 : 0
    }

    if(h.code.length == 0) {
      click = 0;
      $('#code').hasError('Required');
      return false;
    }

    if(h.name.length == 0) {
      click = 0;
      $('#name').hasError('Required');
      return false;
    }

    if(h.role == '') {
      click = 0;
      $('#role').hasError();
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data: {
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });
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
    })
  }
}



$('#search-box').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      let code = arr[0];
      let name = arr[1];
      $(this).val(name);
      $('#customer-code').val(code);
    }
    else {
      $(this).val('');
      $('#customer-code').val('');
    }
  }
});


$('#search-box').keyup(function(e){
  if(e.keyCode == 13){
    addCustomer();
  }
});


function addCustomer() {
  let code = $('#code').val().trim();
  let customer_code = $('#customer-code').val().trim();
  let customer_name = $('#search-box').val().trim();
  let exists = 0;

  if(customer_code == '' || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  $('.customer-code').each(function() {
    if($(this).data('code') == customer_code) {
      exists++;
    }
  });

  if(exists > 0) {
    $('#customer-code').val('');
    $('#search-box').val('').focus();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_customer',
    type:'POST',
    cache:false,
    data:{
      'warehouse_code' : code,
      'customer_code' : customer_code,
      'customer_name' : customer_name
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#customer-template').html();
          let output = $('#cust-table');

          render_append(source, ds.data, output);

          reIndex();

          $('#customer-code').val('');
          $('#search-box').val('').focus();
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


function deleteCustomer(id,code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete_customer',
      type:'POST',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs.trim() === 'success') {
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#row-'+id).remove();
          reIndex();
          $('#search-box').focus();
        }
        else{
          showError(rs);
        }
      },
      error:function(rs) {
        showError(rs);
      }
    })

  })
}


function getDelete(code){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {
    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          if(rs.trim() === 'success') {
            swal({
              title:'Deleted',
              text:'ลบคลัง '+code+' เรียบร้อยแล้ว',
              type:'success',
              timer:1000
            });

            $('#row-'+code).remove();

            reIndex();
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
  })
}


function exportFilter(){
  let code = $('#code').val();
  let role = $('#role').val();
  let is_consignment = $('#is_consignment').val();
  let sell = $('#sell').val();
  let prepare = $('#prepare').val();
  let lend = $('#lend').val();
  let active = $('#active').val();
  let auz = $('#auz').val();
  let is_pos = $('#is_pos').val();

  $('#export-code').val(code);
  $('#export-role').val(role);
  $('#export-is-consignment').val(is_consignment);
  $('#export-sell').val(sell);
  $('#export-prepare').val(prepare);
  $('#export-lend').val(lend);
  $('#export-active').val(active);
  $('#export-auz').val(auz);
  $('#export-is-pos').val(is_pos);


  var token = $('#token').val();
  get_download(token);
  $('#exportForm').submit();
}
