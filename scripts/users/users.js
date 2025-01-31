function addNew(){
  window.location.href = HOME +'add_new';
}


function goBack(){
  window.location.href = HOME;
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function getReset(id){
  window.location.href = HOME + 'reset_password/'+id;
}


function showPwd() {
  $('#pwd').attr('type', 'text');
  $('#cm-pwd').attr('type', 'text');
  $('.show-eye').addClass('hide');
  $('.hide-eye').removeClass('hide');
}


function hidePwd() {
  $('#pwd').attr('type', 'password');
  $('#cm-pwd').attr('type', 'password');
  $('.hide-eye').addClass('hide');
  $('.show-eye').removeClass('hide');
}


function add() {
  clearErrorByClass('e');

  let h = {
    'uname' : $('#uname').val().trim(),
    'dname' : $('#dname').val().trim(),
    'pwd' : $('#pwd').val().trim(),
    'cmp' : $('#cm-pwd').val().trim(),
    'id_profile' : $('#profile').val(),
    'active' : $('#active').is(':checked') ? 1 : 0,
    'force' : $('#force-reset').is(':checked') ? 1 : 0
  };

  if(h.uname.length == 0) {
    $('#uname').hasError("User name is required");
    return false;
  }

  if(h.dname.length == 0) {
    $('#dname').hasError("Display name is required");
    return false;
  }

  if(h.pwd.length == 0) {
    $('#pwd').hasError('Password is required !');
    return false;
  }

  if(h.pwd.length > 0) {
    if( ! validatePassword(h.pwd)) {
      $('#pwd').hasError('รหัสผ่านต้องมีความยาว 8 - 20 ตัวอักษร และต้องประกอบด้วย ตัวอักษรภาษาอังกฤษ พิมพ์เล็ก พิมพ์ใหญ่ และตัวเลขอย่างน้อย อย่างละตัว');
      return false;
    }

    if(h.pwd != h.cmp) {
      $('#cm-pwd').hasError('Password missmatch!');
			return false;
    }
  }

  if(h.id_profile == "") {
    $('#profile').hasError("Please select permission profile");
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
            text:'Create user successfully',
            type:'success'
          }, function() {
            window.location.reload();
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
      load_out();
      showError(rs);
    }
  })
}


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'uname' : $('#uname').val().trim(),
    'dname' : $('#dname').val().trim(),
    'id_profile' : $('#profile').val(),
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(h.uname.length == 0) {
    $('#uname').hasError("User name is required");
    return false;
  }

  if(h.dname.length == 0) {
    $('#dname').hasError("Display name is required");
    return false;
  }

  if(h.id_profile == "") {
    $('#profile').hasError("Please select permission profile");
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
      load_out();
      showError(rs);
    }
  })
}


function getDelete(id, uname){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ uname +' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: BASE_URL + 'users/users/delete_user/'+id,
      type:'GET',
      cache:false,
      success:function(rs){
        if(rs == 'success'){
          swal({
            title:'Deleted',
            title:'User deleted',
            type:'success',
            time: 1000
          });

          setTimeout(function(){
            window.location.reload();
          }, 1500)
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


function validatePassword(input) {
	var passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,20}$/;

	if(input.match(passw))
	{
		return true;
	}
	else
	{
		return false;
	}
}


function changePassword() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'pwd' : $('#pwd').val().trim(),
    'cmp' : $('#cm-pwd').val().trim(),
    'force' : $('#force-reset').is(':checked') ? 1 : 0
  };

  if(h.pwd.length == 0) {
    $('#pwd').hasError('Password is required !');
    return false;
  }

  if(h.pwd.length > 0) {
    if( ! validatePassword(h.pwd)) {
      $('#pwd').hasError('รหัสผ่านต้องมีความยาว 8 - 20 ตัวอักษร และต้องประกอบด้วย ตัวอักษรภาษาอังกฤษ พิมพ์เล็ก พิมพ์ใหญ่ และตัวเลขอย่างน้อย อย่างละตัว');
      return false;
    }

    if(h.pwd != h.cmp) {
      $('#cm-pwd').hasError('Password missmatch!');
			return false;
    }
  }

  load_in();

  $.ajax({
    url:HOME + 'change_password',
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
            text:'Password has been changed',
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
      load_out();
      showError(rs);
    }
  })
}


function getPermission(id) {
  load_in();
  $('#user_id').val(id);
  $.ajax({
    url:HOME + 'get_user_permissions/'+id,
    type:'GET',
    cache:false,
    success:function(rs) {
      load_out();

      if( isJson(rs)) {
        let ds = $.parseJSON(rs);

        console.log(ds);
        let source = $('#permission-template').html();
        let output = $('#permission-result');

        $('#permission-text').text(ds.header);

        render(source, ds, output);

        $('#permission-modal').modal('show');
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    }
  });
}


function doExport() {
  let token = Date.now();
  $('#token').val(token);
  $('#permission-modal').modal('hide');
  get_download(token);
  $('#permission-form').submit();
}


function getAllPermission() {
  $('#all-permission-modal').modal('show');
}

function exportAll(option) {
  let token = Date.now();
  $('#all').val(option);
  $('#all-token').val(token);
  $('#all-permission-modal').modal('hide');
  get_download(token);
  $('#all-permission-form').submit();
}
