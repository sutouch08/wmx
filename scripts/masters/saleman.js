var click = 0;

function addNew() {
  window.location.href = HOME + 'add_new';
}


function goEdit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let name = $('#name').val().trim();
    let active = $('#active').val();

    if(name.length == 0) {
      click = 0;
      $('#name').hasError();
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'add',
      type:'POST',
      cache:false,
      data:{
        'name' : name,
        'active' : active
      },
      success:function(rs) {
        click = 0;
        load_out();

        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มข้อมูลเรียบร้อยแล้ว ต้องการเพิ่มต่อหรือไม่ ?',
            type:'success',
            html:true,
            showCancelButton:true,
            cancelButtonText:'No',
            confirmButtonText:'Yes'
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

    let id = $('#slp-id').val();
    let name = $('#name').val().trim();
    let active = $('#active').val();

    if(name.length == 0) {
      click = 0;
      $('#name').hasError();
      return false;
    }

    load_in();

    $.ajax({
      url:HOME + 'update',
      type:'POST',
      cache:false,
      data:{
        'id' : id,
        'name' : name,
        'active' : active
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


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ name + ' หรือไม่ ?',
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


function toggleActive(option) {
  $('#active').val(option);
  if(option == 1) {
    $('#active-on').addClass('btn-success');
    $('#active-off').removeClass('btn-danger');
  }

  if(option == 0) {
    $('#active-on').removeClass('btn-success');
    $('#active-off').addClass('btn-danger');
  }
}
