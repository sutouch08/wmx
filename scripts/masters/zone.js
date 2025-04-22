var click = 0;

function addNew() {
  window.location.href = HOME + 'add_new';
}

function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');
    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'warehouse_code' : $('#warehouse').val(),
      'active' : $('#active').is(':checked') ? 1 : 0,
      'is_pickface' : $('#is_pickface').is(':checked') ? 1 : 0
    }

    if(h.warehouse_code == '') {
      click = 0;
      $('#warehouse').hasError('Please select');
      return false;
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

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
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
              $('#name').val('');
              $('#code').val('').focus();
            }
            else {
              goBack();
            }
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
    });
  }
}


function update() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');
    let h = {
      'id' : $('#id').val(),
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'warehouse_code' : $('#warehouse').val(),
      'active' : $('#active').is(':checked') ? 1 : 0,
      'is_pickface' : $('#is_pickface').is(':checked') ? 1 : 0
    }

    if(h.warehouse_code == '') {
      click = 0;
      $('#warehouse').hasError('Please select');
      return false;
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

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
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
    });
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


function togglePickface(id) {
  let is_pickface = $('#is-pickface-'+id).val();

  is_pickface = is_pickface == '1' ? '0' : '1';

  $.ajax({
    url:HOME + 'update_pickface/',
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'is_pickface' : is_pickface
    },
    success:function(rs) {
      if(rs == 'success') {
        $('#is-pickface-'+id).val(is_pickface);
        if(is_pickface == '1') {
          $('#pickface-label-'+id).text('Yes');
        }
        else {
          $('#pickface-label-'+id).html('No');
        }
      }
      else {
        swal({
          title:'Failed !',
          text:rs,
          type:'error'
        })
      }
    }
  })
}


function addEmployee() {
  let code = $('#zone_code').val();
  let empID = $('#empID').val();
  let empName = $('#empID option:selected').data('name');
  if(code === undefined ){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(empID == '' || empName.length == 0){
    swal('ชื่อพนักงานไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_employee',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'empID' : empID,
      'empName' : empName
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'เพิ่มพนักงานเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}


$('#search-box').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus:true,
  close:function(){
    let arr = $(this).val().split(' | ');
    if(arr.length == 2){
      let code = arr[0];
      let name = arr[1];
      $(this).val(name);
      $('#customer_code').val(code);
    }else{
      $(this).val('');
      $('#customer_code').val('');
    }
  }
});


$('#search-box').keyup(function(e){
  if(e.keyCode == 13){
    addCustomer();
  }
});


function addCustomer(){
  let code = $('#zone_code').val();
  let customer_code = $('#customer_code').val();
  let customer_name = $('#search-box').val();
  if(code === undefined){
    swal('ไม่พบรหัสโซน');
    return false;
  }

  if(customer_code == '' || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add_customer',
    type:'POST',
    cache:false,
    data:{
      'zone_code' : code,
      'customer_code' : customer_code
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Success',
          text:'เพิ่มลูกค้าเรียบร้อยแล้ว',
          type:'success',
          timer:1000
        });

        setTimeout(function(){
          window.location.reload();
        }, 1200);
      }else{
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
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
  },function(){
    $.ajax({
      url: HOME + 'delete/' + code,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+code).remove();
          reIndex();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
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
      url: HOME + 'delete_customer/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+code+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#row-'+id).remove();
          reIndex();
          $('#search-box').focus();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}


function deleteEmployee(id,name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: HOME + 'delete_employee/' + id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            text:'ลบ '+name+' เรียบร้อยแล้ว',
            type:'success',
            timer:1000
          });
          $('#emp-'+id).remove();
          reIndex();
          $('#search-box').focus();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

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


function editZone() {
  $('#user_id').removeAttr('disabled').focus();
  $('#pos-api').removeAttr('disabled');
  $('#is-pickface').removeAttr('disabled');
  $('#btn-u-edit').addClass('hide');
  $('#btn-u-update').removeClass('hide');
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
