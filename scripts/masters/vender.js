function addNew(){
  window.location.href = HOME + 'add_new';
}


function getEdit(id){
  window.location.href = HOME + 'edit/'+id;
}


function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function getDelete(id, name){
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + name + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
		$.ajax({
			url:HOME + 'delete',
			type:'POST',
			cache:false,
			data:{
				'id' : id
			},
			success:function(rs) {
				var rs = $.trim(rs);
				if(rs === 'success') {
					swal({
						title:'Deleted',
						type:'success',
						timer:1000
					});

					$('#row-'+id).remove();
					reIndex();
				}
				else {
					swal({
						title:'Error!',
						text:rs,
						type:'error',
						html:true
					});
				}
			}
		});
  });
}


function add() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val().trim(),
    'run_no' : $('#code').data('runno'),
    'name' : $('#name').val().trim(),
    'credit_term' : $('#credit_term').val().trim(),
    'tax_id' : $('#tax_id').val().trim(),
    'branch_code' : $('#branch_code').val().trim(),
    'branch_name' : $('#branch_name').val().trim(),
    'address' : $('#address').val().trim(),
    'phone' : $('#phone').val().trim(),
    'active' : $('#active').val()
  }


	if(h.code.length === 0) {
    $('#code').hasError();
		return false;
	}

	if(h.name.length === 0) {
		$('#name').hasError();
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
					type:'success',
					timer:1000
				});

				setTimeout(function() {
					addNew();
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
}


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'name' : $('#name').val().trim(),
    'credit_term' : $('#credit_term').val().trim(),
    'tax_id' : $('#tax_id').val().trim(),
    'branch_code' : $('#branch_code').val().trim(),
    'branch_name' : $('#branch_name').val().trim(),
    'address' : $('#address').val().trim(),
    'phone' : $('#phone').val().trim(),
    'active' : $('#active').val()
  }

	if(h.name.length === 0) {
		$('#name').hasError();
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

			if(rs.trim() === 'success') {
				swal({
					title:'Success',
					type:'success',
					timer:1000
				});
			}
			else {
				showError(rs);
			}
		},
		error:function(rs) {
			showError(rs);
		}
	});
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
