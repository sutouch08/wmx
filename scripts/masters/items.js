function goBack(){
  window.location.href = HOME;
}


function addNew(){
  window.location.href = HOME + 'add_new';
}


function edit(id){
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
    'barcode' : $('#barcode').val().trim(),
    'model' : $('#model').val().trim(),
    'color' : $('#color').val().trim(),
    'size' : $('#size').val().trim(),
    'cost' : $('#cost').val().trim(),
    'price' : $('#price').val().trim(),
    'unit' : $('#unit').val().trim(),
    'brand' : $('#brand').val().trim(),
    'main_group' : $('#main-group').val().trim(),
    'group' : $('#group').val().trim(),
    'category' : $('#category').val().trim(),
    'kind' : $('#kind').val().trim(),
    'type' : $('#type').val().trim(),
    'collection' : $('#collection').val().trim(),
    'year' : $('#year').val().trim(),
    'count' : $('#count-stock').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(h.code.length == 0) {
    $('#code').hasError('Required!');
    return false;
  }

  if(h.name.length == 0) {
    $('#name').hasError('required');
    return false;
  }

  if(h.barcode.length == 0) {
    $('#barcode').hasError('required');
    return false;
  }

	load_in();

	$.ajax({
		url:HOME + 'add',
		type:'POST',
		cache:false,
		data:{
			"data" : JSON.stringify(h)
		},
		success:function(rs) {
			load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มรายากรสำเร็จ ต้องการเพิ่มอีกหรือไม่ ?',
            type:'success',
            showCancelButton:true,
            cancelButtonText:'No',
            confirmButtonText:'Yes',
            closeOnConfirm:true
          },function(isConfirm) {
            if(isConfirm) {
              addNew();
            }
            else {
              goBack();
            }
          });
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
}


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#id').val(),
    'code' : $('#code').val().trim(),
    'name' : $('#name').val().trim(),
    'barcode' : $('#barcode').val().trim(),
    'model' : $('#model').val().trim(),
    'color' : $('#color').val().trim(),
    'size' : $('#size').val().trim(),
    'cost' : $('#cost').val().trim(),
    'price' : $('#price').val().trim(),
    'unit' : $('#unit').val().trim(),
    'brand' : $('#brand').val().trim(),
    'main_group' : $('#main-group').val().trim(),
    'group' : $('#group').val().trim(),
    'category' : $('#category').val().trim(),
    'kind' : $('#kind').val().trim(),
    'type' : $('#type').val().trim(),
    'collection' : $('#collection').val().trim(),
    'year' : $('#year').val().trim(),
    'count' : $('#count-stock').is(':checked') ? 1 : 0,
    'active' : $('#active').is(':checked') ? 1 : 0
  };

  if(h.name.length == 0) {
    $('#name').hasError('required');
    return false;
  }

  if(h.barcode.length == 0) {
    $('#barcode').hasError('required');
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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          });
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
