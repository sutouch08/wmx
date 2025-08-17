var click = 0;

function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(code){
  window.location.href = HOME + 'edit/'+code;
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'position' : $('#position').val(),
      'is_online' : $('#is-online').is(':checked') ? 1 : 0
    };

    if(h.code.length == 0) {
      click = 0;
      $('#code').hasError();
      swal("Code is required !");
      return false;
    }

    if(h.name.length == 0) {
      click = 0;
      $('#name').hasError();
      swal("Name is required !");
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

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });

          setTimeout(() => {
            addNew();
          }, 1200);
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


function update() {
  $('#name').clearError();

  let h = {
    'id' : $("#id").val(),
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'position' : $('#position').val(),
    'is_online' : $('#is-online').is(':checked') ? 1 : 0
  }

  if(h.name.length == 0) {
    $('#name').hasError();
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
      beep();
      showError(rs);
    }
  })
}


function getDelete(code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function() {
    setTimeout(() => {
      $.ajax({
        url:HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          if(rs.trim() === 'success') {
            swal({
              title:'Deleted',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              refresh();
            }, 1200)
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


$('#customer_name').autocomplete({
  source:BASE_URL + 'auto_complete/get_customer_code_and_name',
  autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customer_code").val(code);
			$("#customer_name").val(name);
		}else{
			$("#customer_code").val('');
			$(this).val('');
		}
	}
})


function toggleOnline(code){
  var option = $('#online-'+code).val();
  $.ajax({
    url:BASE_URL + 'masters/channels/toggle_online',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'is_online' : option
    },
    success:function(rs){
      var rs = $.trim(rs);
      if(rs == '1'){
        $('#online-label-'+code).html('<i class="fa fa-check green"></i>');
        $('#online-'+code).val(rs);
      }else if(rs == '0'){
        $('#online-label-'+code).html('<i class="fa fa-times"></i>');
        $('#online-'+code).val(rs);
      }else{
        swal('Error', rs, 'error');
      }
    }
  })
}
