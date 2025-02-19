function goBack() {
  window.location.href = HOME;
}


function addNew() {
  window.location.href = HOME + 'add_new';
}


function edit(id) {
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function add() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'auz' : $('#auz').is(':checked') ? 1 : 0,
    'freeze' : $('#freeze').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  }

  if(h.code.length == 0) {
    $('#code').hasError("Required !");
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError("Required !");
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

        if(ds.status == "success") {
          swal({
            title:'Success',
            text:"สร้างคลังสำเร็จ ต้องการส้รางเพิ่มหรือไม่ ?",
            type:'success',
            html:true,
            showCancelButton:true,
            confirmButtonText:'Yes',
            cancelButtonText:'No',
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
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'auz' : $('#auz').is(':checked') ? 1 : 0,
    'freeze' : $('#freeze').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  }

  if(h.code.length == 0) {
    $('#code').hasError("Required !");
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError("Required !");
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

        if(ds.status == "success") {
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
      load_out();
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
    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status === 'success') {
              swal({
                title:'Deleted',
                type:'success',
                timer:1000
              });

              $('#row-'+id).remove();
              reIndex();
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
    }, 100);
  })
}


function toggleAuz(option)
{
  $('#auz').val(option);
  if(option == 1){
    $('#btn-auz-yes').addClass('btn-success');
    $('#btn-auz-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-auz-yes').removeClass('btn-success');
    $('#btn-auz-no').addClass('btn-danger');
  }
}


function toggleActive(option)
{
  $('#active').val(option);
  if(option == 1){
    $('#btn-active-yes').addClass('btn-success');
    $('#btn-active-no').removeClass('btn-danger');
  }
  else
  {
    $('#btn-active-yes').removeClass('btn-success');
    $('#btn-active-no').addClass('btn-danger');
  }
}
