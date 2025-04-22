var click = 0;

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


function moveBack(id, pdCode, zoneName) {
  swal({
    title:'ย้ายกลับ',
    text:pdCode + ' จะถูกย้ายกลับโซน '+zoneName + '<br/>ต้องการดำเนินการต่อหรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();
      $.ajax({
        url:HOME + 'move_back',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
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
      })
    }, 100)
  })
}


function removeCancel(id, pdCode) {
  swal({
    title:'<span class="red">! ลบรายการ</span>',
    text:'<span class="red">'+pdCode + ' จะถูกลบออกจาก Cancel zone โดยไม่ถูกย้ายกลับโซนเดิม ใช้ในกรณีที่มีข้อผิดพลาดเรื่อง cancel เท่านั้น ' + '<br/>ต้องการดำเนินการต่อหรือไม่ ?</span>',
    type:'warning',
    html:true,
    showCancelButton:true,
    cancelButtonText:'No',
    confirmButtonText:'Yes',
    confirmButtonColor:'red',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();
      $.ajax({
        url:HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
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
      })
    }, 100)
  })
}
