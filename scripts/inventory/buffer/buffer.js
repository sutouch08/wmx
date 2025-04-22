$("#fromDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#toDate").datepicker("option", "minDate", ds);
	}
});


$("#toDate").datepicker({
	dateFormat: 'dd-mm-yy',
	onClose: function(ds){
		$("#fromDate").datepicker("option", "maxDate", ds);
	}
});


function deleteBuffer(id, order, product) {
  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการลบ " + order + "/"+product+" หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function() {
    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete_buffer',
        type:"POST",
        cache:"false",
        data:{
          'id' : id
        },
        success: function(rs) {
          if(rs.trim() == 'success' ){
            swal({
              title: 'Deleted',
              type: 'success',
              timer: 1000
            });

            $('#row-'+id).remove();
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
      });
    }, 100)
  });
}
