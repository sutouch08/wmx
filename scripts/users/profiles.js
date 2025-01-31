function addNew() {
  window.location.href = HOME + 'add_new';
}


function goBack(){
  window.location.href = HOME;
}


function add() {
  clearErrorByClass('e');

  let name = $('#name').val().trim();

  if(name.length == 0) {
    $('#name').hasError("Name is required !");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'add',
    type:'POST',
    cache:false,
    data:{
      'name' : name
    },
    success:function(rs) {
      load_out();
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
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

function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ name +' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
    confirmButtonColor: '#FA5858',
    confirmButtonText: 'ใช่, ฉันต้องการลบ',
    cancelButtonText: 'ยกเลิก',
    closeOnConfirm: false
  },function(){
    window.location.href = BASE_URL + 'users/profiles/delete_profile/'+id;
  })
}
