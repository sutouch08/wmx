function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function edit(code){
  window.location.href = HOME + 'edit/'+code;
}


function viewDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}


function doReceive(code) {
  let uuid = get_uuid();

  $.ajax({
    url:HOME + 'is_document_avalible',
    type:'GET',
    cache:false,
    data:{
      'code' : code,
      'uid': uuid
    },
    success:function(rs) {
      if(rs.trim() === 'available') {
        window.location.href = HOME + 'process/'+code+'/'+uuid;
      }
    }
  })
}


function confirmCancel(code) {
  swal({
    title:'คุณแน่ใจ ?',
    text:'ต้องการยกเลิกเอกสาร '+code+' หรือไม่ ?',
    type:'warning',
    html:true,
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    $('#cancel-code').val(code);
    $('#cancel-reason').val('').removeClass('has-error');
    cancel(code);
  })
}
