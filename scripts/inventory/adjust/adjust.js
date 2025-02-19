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


function approve() {
	let code = $('#code').val();
  load_in();

	$.ajax({
		url:HOME + 'do_approve',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs){
      load_out();
			var rs = $.trim(rs);
			if(rs === 'success'){
				swal({
					title:'Approved',
					type:'success',
					timer:1000
				});

				setTimeout(function(){
					window.location.reload();
				}, 1200);
			}
			else{
				swal("Error!!", rs, 'error');
			}
		}
	})
}


function reject() {
  let code = $('#code').val();

  swal({
    title:'Rejection',
    text:'ต้องการปฏิเสธเอกสารนี้หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    cancelButtonText:'Cancel',
    confirmButtonText:'Reject',
    confirmButtonColor:'#DD6855',
    closeOnConfirm:true
  }, function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url:HOME + 'do_reject',
        type:'POST',
        cache:false,
        data:{
          'code' : code
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
              window.location.reload();
            }, 1200);
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      });
    }, 100);
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
