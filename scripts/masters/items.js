function addNew(){
  window.location.href = HOME + 'add_new';
}

function getEdit(id){
	window.location.href = HOME + 'edit/'+id;
}

function viewDetail(id) {
  window.location.href = HOME + 'view_detail/'+id;
}


function add() {
  $('.r').removeClass('has-error');
  $('.e').text('');

	let error = 0;

	let data = {};

	data.code = $('#code').val().trim();
	data.old_code = $('#old_code').val().trim();
	data.name = $('#name').val().trim(); // required
	data.style = $('#style').val().trim();
	data.old_style = $('#old_style').val().trim();
	data.color_code = $('#color').val().trim(); // required
	data.size_code = $('#size').val().trim(); // required
	data.barcode = $('#barcode').val().trim();
	data.cost = parseDefault(parseFloat($('#cost').val()), 0);
	data.price = parseDefault(parseFloat($('#price').val()), 0);
	data.unit_code = $('#unit_code').val(); // required
	data.brand_code = $('#brand').val();
	data.group_code = $('#group').val();
	data.main_group_code = $('#mainGroup').val(); // required
	data.sub_group_code = $('#subGroup').val();
	data.category_code = $('#category').val();
	data.kind_code = $('#kind').val();
	data.type_code = $('#type').val();
  data.collection_code = $('#collection').val();
	data.year = $('#year').val();
	data.count_stock = $('#count_stock').is(':checked') ? 1 : 0;
	data.can_sell = $('#can_sell').is(':checked') ? 1 : 0;
	data.is_api = $('#is_api').is(':checked') ? 1 : 0;
	data.active = $('#active').is(':checked') ? 1 : 0;

  if(data.code.length === 0) {
    set_error($('#code'), $('#code-error'), "required");
		error++;
  }

	if(data.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}

	// if(data.style.length === 0) {
	// 	set_error($('#style'), $('#style-error'), "required");
	// 	error++;
	// }

	if(data.color_code.length === 0) {
		set_error($('#color'), $('#color-error'), "required");
		error++;
	}

	if(data.size_code.length === 0) {
		set_error($('#size'), $('#size-error'), "required");
		error++;
	}

	if(data.unit_code.length === 0) {
		set_error($('#unit_code'), $('#unit-error'), "required");
		error++;
	}

	if(data.main_group_code.length === 0) {
		set_error($('#mainGroup'), $('#mainGroup-error'), "required");
		error++;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});

        setTimeout(() => {
          addNew();
        }, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:"Error!",
				text:'Error : '+xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}


function update() {
  $('.r').removeClass('has-error');
  $('.e').text('');

	let error = 0;

	let data = {};

	data.code = $('#code').val().trim();
	data.old_code = $('#old_code').val().trim();
	data.name = $('#name').val().trim(); // required
	data.style = $('#style').val().trim(); // required
	data.old_style = $('#old_style').val().trim();
	data.color_code = $('#color').val().trim(); // required
	data.size_code = $('#size').val().trim(); // required
	data.barcode = $('#barcode').val().trim();
	data.cost = parseDefault(parseFloat($('#cost').val()), 0);
	data.price = parseDefault(parseFloat($('#price').val()), 0);
	data.unit_code = $('#unit_code').val(); // required
	data.brand_code = $('#brand').val();
	data.group_code = $('#group').val();
	data.main_group_code = $('#mainGroup').val(); // required
	data.sub_group_code = $('#subGroup').val();
	data.category_code = $('#category').val();
	data.kind_code = $('#kind').val();
	data.type_code = $('#type').val();
  data.collection_code = $('#collection').val();
	data.year = $('#year').val();
	data.count_stock = $('#count_stock').is(':checked') ? 1 : 0;
	data.can_sell = $('#can_sell').is(':checked') ? 1 : 0;
	data.is_api = $('#is_api').is(':checked') ? 1 : 0;
	data.active = $('#active').is(':checked') ? 1 : 0;

	if(data.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}

	// if(data.style.length === 0) {
	// 	set_error($('#style'), $('#style-error'), "required");
	// 	error++;
	// }

	if(data.color_code.length === 0) {
		set_error($('#color'), $('#color-error'), "required");
		error++;
	}

	if(data.size_code.length === 0) {
		set_error($('#size'), $('#size-error'), "required");
		error++;
	}

	if(data.unit_code.length === 0) {
		set_error($('#unit_code'), $('#unit-error'), "required");
		error++;
	}

	if(data.main_group_code.length === 0) {
		set_error($('#mainGroup'), $('#mainGroup-error'), "required");
		error++;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:"Error!",
				text:'Error : '+xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}


function duplicate(id){
  window.location.href = HOME + 'duplicate/'+id;
}


function addDuplicate() {
  $('.r').removeClass('has-error');
  $('.e').text('');

	let error = 0;

	let data = {};
  data.c_code = $('#c-code').val();
	data.code = $('#code').val().trim();
	data.old_code = $('#old_code').val().trim();
	data.name = $('#name').val().trim(); // required
	data.style = $('#style').val().trim(); // required
	data.old_style = $('#old_style').val().trim();
	data.color_code = $('#color').val().trim(); // required
	data.size_code = $('#size').val().trim(); // required
	data.barcode = $('#barcode').val().trim();
	data.cost = parseDefault(parseFloat($('#cost').val()), 0);
	data.price = parseDefault(parseFloat($('#price').val()), 0);
	data.unit_code = $('#unit_code').val(); // required
	data.brand_code = $('#brand').val();
	data.group_code = $('#group').val();
	data.main_group_code = $('#mainGroup').val(); // required
	data.sub_group_code = $('#subGroup').val();
	data.category_code = $('#category').val();
	data.kind_code = $('#kind').val();
	data.type_code = $('#type').val();
  data.collection_code = $('#collection').val();
	data.year = $('#year').val();
	data.count_stock = $('#count_stock').is(':checked') ? 1 : 0;
	data.can_sell = $('#can_sell').is(':checked') ? 1 : 0;
	data.is_api = $('#is_api').is(':checked') ? 1 : 0;
	data.active = $('#active').is(':checked') ? 1 : 0;

  if(data.code.length === 0) {
    set_error($('#code'), $('#code-error'), "required");
		error++;
  }

  if(data.c_code == data.code) {
    set_error($('#code'), $('#code-error'), "รหัสซ้ำ");
		error++;
  }

	if(data.name.length === 0) {
		set_error($('#name'), $('#name-error'), "required");
		error++;
	}
  //
	// if(data.style.length === 0) {
	// 	set_error($('#style'), $('#style-error'), "required");
	// 	error++;
	// }

	if(data.color_code.length === 0) {
		set_error($('#color'), $('#color-error'), "required");
		error++;
	}

	if(data.size_code.length === 0) {
		set_error($('#size'), $('#size-error'), "required");
		error++;
	}

	if(data.unit_code.length === 0) {
		set_error($('#unit_code'), $('#unit-error'), "required");
		error++;
	}

	if(data.main_group_code.length === 0) {
		set_error($('#mainGroup'), $('#mainGroup-error'), "required");
		error++;
	}

	if(error > 0) {
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(data)
		},
		success:function(rs) {
			load_out();
			var rs = rs.trim();
			if(rs == 'success') {
				swal({
					title:"Success",
					type:'success',
					timer:1000
				});

        setTimeout(() => {
          addNew();
        }, 1200);
			}
			else {
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				})
			}
		},
		error:function(xhr) {
			load_out();
			swal({
				title:"Error!",
				text:'Error : '+xhr.responseText,
				type:'error',
				html:true
			})
		}
	})
}


$('#style').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true,
  close:function() {
    let rs = $(this).val();
    let arr = rs.split(' | ');
    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
});

$('#color').autocomplete({
  source: BASE_URL + 'auto_complete/get_color_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


$('#size').autocomplete({
  source:BASE_URL + 'auto_complete/get_size_code_and_name',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    var err = rs.split(' | ');
    if(err.length == 2){
      $(this).val(err[0]);
    }else{
      $(this).val('');
    }
  }
});


function clearFilter(){
  var url = HOME + 'clear_filter';
  var page = BASE_URL + 'masters/products';
  $.get(url, function(){
    goBack();
  });
}


function getDelete(id, code, no){
  let url = BASE_URL + 'masters/items/delete_item/';// + encodeURIComponent(code);
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ ' + code + ' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
  },function(){
    $.ajax({
      url: url,
      type:'GET',
      cache:false,
      data:{
        'id' : id
      },
      success:function(rs){
        if(rs === 'success'){
          swal({
            title:'Deleted',
            type:'success',
            timer:1000
          });

          $('#row-'+no).remove();
        }else{
          swal({
            title:'Error!',
            text:rs,
            type:'error'
          });
        }
      }
    })

  })
}

function getTemplate(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = BASE_URL + 'masters/items/download_template/'+token;
}

function getSearch(){
  $('#searchForm').submit();
}
