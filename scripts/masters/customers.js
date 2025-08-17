var click = 0;

function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function changeURL(id, view, tab)
{
	var url = BASE_URL + 'masters/customers/'+view+'/' + id + '/' + tab;
	var stObj = { stage: 'stage' };
	window.history.pushState(stObj, 'customers', url);
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e')

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'tax_id' : $('#tax-id').val().trim(),
      'group' : $('#group').val(),
      'kind' : $('#kind').val(),
      'type' : $('#type').val(),
      'class' : $('#class').val(),
      'area' : $('#area').val(),
      'sale_code' : $('#sale').val(),
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

    if(h.group == "") {
      click = 0;
      $('#group').hasError('Required');
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
    clearErrorByClass('e')

    let h = {
      'id' : $('#id').val(),
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'tax_id' : $('#tax-id').val().trim(),
      'group' : $('#group').val(),
      'kind' : $('#kind').val(),
      'type' : $('#type').val(),
      'class' : $('#class').val(),
      'area' : $('#area').val(),
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
            html:true,
            timer:1000
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


function getDelete(id, code, name) {
  let h = {
    'id' : id,
    'code' : code,
    'name' : name
  }

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
      load_in();

      $.ajax({
        url:HOME + 'delete',
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
          showError(rs)
        }
      })
    }, 100)
  })
}


$('#date').datepicker();
