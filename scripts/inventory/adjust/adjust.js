function goBack(){
  window.location.href = HOME;
}

function addNew(){
  window.location.href = HOME + 'add_new';
}


function goEdit(code){
  window.location.href = HOME + 'edit/'+code;
}

//--- ไปหน้ารายละเอียดออเดอร์
function goDetail(code){
  window.location.href = HOME + 'view_detail/'+ code;
}


function approve(){
	var code = $('#code').val();

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

			if(rs.trim() === 'success'){
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


function unapprove(){
  var code = $('#code').val();

  load_in();

	$.ajax({
		url:HOME + 'un_approve',
		type:'POST',
		cache:false,
		data:{
			'code' : code
		},
		success:function(rs){
      load_out();

			if(rs.trim() === 'success'){
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
