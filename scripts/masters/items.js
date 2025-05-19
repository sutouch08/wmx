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
	let error = 0;

	let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'model_code' : $('#model').val().trim(),
    'color_code' : $('#color').val().trim(),
    'size_code' : $('#size').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'cost' : parseDefault(parseFloat($('#cost').val()), 0),
    'price' : parseDefault(parseFloat($('#price').val()), 0),
    'unit_code' : $('#unit-code').val().trim(),
    'main_group_code' : $('#main-group').val(),
    'main_group_name' : $('#main-group option:selected').data('name'),
    'group_code' : $('#group').val(),
    'group_name' : $('#group option:selected').data('name'),
    'segment_code' : $('#segment').val(),
    'segment_name' : $('#segment option:selected').data('name'),
    'class_code' : $('#class').val(),
    'class_name' : $('#class option:selected').data('name'),
    'family_code' : $('#family').val(),
    'family_name' : $('#family option:selected').data('name'),
    'type_code' : $('#type').val(),
    'type_name' : $('#type option:selected').data('name'),
    'kind_code' : $('#kind').val(),
    'kind_name' : $('#kind option:selected').data('name'),
    'gender_code' : $('#gender').val(),
    'gender_name' : $('#gender option:selected').data('name'),
    'sport_type_code' : $('#sport-type').val(),
    'sport_type_name' : $('#sport-type option:selected').data('name'),
    'collection_code' : $('#collection').val(),
    'collection_name' : $('#collection option:selected').data('name'),
    'brand_code' : $('#brand').val(),
    'brand_name' : $('#brand option:selected').data('name'),
    'year' : $('#year').val(),
    'api_rate' : $('#api-rate').val().trim(),
    'is_api' : $('#is_api').is(':checked') ? 1 : 0,
    'count_stock' : $('#count_stock').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
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
			"data" : JSON.stringify(h)
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


$('#model').autocomplete({
  source: BASE_URL + 'auto_complete/get_model_code',
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
