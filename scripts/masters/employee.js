var click = 0;

function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}



function toggleActive(option){
  $('#active').val(option);
  if(option == 1){
    $('#active-on').addClass('btn-success');
    $('#active-off').removeClass('btn-danger');
  }
  else
  {
    $('#active-on').removeClass('btn-success');
    $('#active-off').addClass('btn-danger');
  }
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'active' : $('#active').val() == 0 ? 0 : 1
    };

    if(h.code.length == 0) {
      $('#code').hasError();
      click = 0;
      return false;
    }

    if(h.name.length == 0) {
      $('#name').hasError();
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

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มพนักงานเรียบร้อยแล้ว <br/> ต้องการเพิ่มอีกหรือไม่ ?',
            type:'success',
            html:true,
            showCancelButton:true,
            cancelButtonText:'No',
            confirmButtonText:'Yes',
            closeOnConfirm:true
          }, function(isConfirm) {
            if(isConfirm) {
              setTimeout(() => {
                addNew();
              }, 200);
            }
            else {
              goBack();
            }
          })
        }
        else {
          beep();
          click = 0;
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        click = 0;
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
      'id' : $('#id').val(),
      'code' : $('#code').val().trim(),
      'name' : $('#name').val().trim(),
      'active' : $('#active').val() == 0 ? 0 : 1
    };

    if(h.code.length == 0) {
      $('#code').hasError();
      click = 0;
      return false;
    }

    if(h.name.length == 0) {
      $('#name').hasError();
      click = 0;
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
        click = 0;
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
        click = 0;
        showError(rs);
      }
    })
  }
}


function getDelete(id, code, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ': '+name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
  },function(){
    setTimeout(() => {
      $.ajax({
        url:HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
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
  })
}
