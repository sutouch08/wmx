function goBack(){
  window.location.href = HOME;
}


function addNew() {
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
    'name' : $('#name').val().trim(),
    'menu' : []
  }

  if(h.name.length == 0) {
    $('#name').hasError("Name is required !");
    return false;
  }

  $('.menu-code').each(function() {
    let code = $(this).val();
    let menu = {
      'code' : code,
      'view' : $('#view-'+code).is(':checked') ? 1 : 0,
      'add' : $('#add-'+code).is(':checked') ? 1 : 0,
      'edit' : $('#edit-'+code).is(':checked') ? 1 : 0,
      'delete' : $('#delete-'+code).is(':checked') ? 1 : 0,
      'approve' : $('#approve-'+code).is(':checked') ? 1 : 0
    };

    h.menu.push(menu);
  });

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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          swal({
            title:'Success',
            text:'เพิ่มรายการสำเร็จแล้ว <br/>ต้องการเพิ่มใหม่หรือไม่ ?',
            type:'success',
            html:true,
            showCancelButton:true,
            cancelButtonText:'No',
            confirmButtonText:'Yes',
            closeOnConfirm:true
          }, function(isConfirm) {
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
      load_out();
      showError(rs);
    }
  })
}


function update() {
  clearErrorByClass('e');

  let h = {
    'id' : $('#profile-id').val(),
    'name' : $('#name').val().trim(),
    'menu' : []
  }

  if(h.name.length == 0) {
    $('#name').hasError("Name is required !");
    return false;
  }

  $('.menu-code').each(function() {
    let code = $(this).val();
    let menu = {
      'code' : code,
      'view' : $('#view-'+code).is(':checked') ? 1 : 0,
      'add' : $('#add-'+code).is(':checked') ? 1 : 0,
      'edit' : $('#edit-'+code).is(':checked') ? 1 : 0,
      'delete' : $('#delete-'+code).is(':checked') ? 1 : 0,
      'approve' : $('#approve-'+code).is(':checked') ? 1 : 0
    };

    h.menu.push(menu);
  });

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

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          swal({
            title:'Success',
            type:'success',
            html:true,
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
      load_out();
      showError(rs);
    }
  })
}


function groupViewCheck(el, id) {
	if(el.is(":checked")){
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}
  else {
		$(".view-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupAddCheck(el, id) {
	if(el.is(":checked")){
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}
  else {
		$(".add-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupEditCheck(el, id) {
	if(el.is(":checked")) {
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}
  else {
		$(".edit-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupDeleteCheck(el, id) {
	if(el.is(":checked")){
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}
  else {
		$(".delete-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupApproveCheck(el, id) {
	if(el.is(":checked")){
		$(".approve-"+id).each(function(index, element) {
			$(this).prop("checked",true);
		});
	}
  else {
		$(".approve-"+id).each(function(index, element) {
			$(this).prop("checked",false);
		});
	}
}


function groupAllCheck(el, id) {
  let view = $("#view-group-"+id);
  let add = $("#add-group-"+id);
  let edit = $("#edit-group-"+id);
  let del  = $("#delete-group-"+id);
  let ap = $('#approve-group-'+id);

	if(el.is(":checked")) {
		view.prop("checked", true);
		groupViewCheck(view, id);
		add.prop("checked", true);
		groupAddCheck(add, id);
		edit.prop("checked", true);
		groupEditCheck(edit, id);
		del.prop("checked", true);
		groupDeleteCheck(del, id);
    ap.prop("checked", true);
    groupApproveCheck(ap, id);

	}
  else {
    view.prop("checked", false);
		groupViewCheck(view, id);
		add.prop("checked", false);
		groupAddCheck(add, id);
		edit.prop("checked", false);
		groupEditCheck(edit, id);
		del.prop("checked", false);
		groupDeleteCheck(del, id);
    ap.prop("checked", false);
    groupApproveCheck(ap, id);
	}
}


function checkAll(menuCode) {
  let el = $('#all-'+menuCode);
  if(el.is(":checked")) {
    $("."+menuCode).each(function() {
      $(this).prop("checked", true);
    });
  }
  else {
    $("."+menuCode).each(function() {
      $(this).prop("checked", false);
    });
  }
}


function confirmDelete(id, name, option) {
  swal({
    title:'Are sure ?',
    text:'ต้องการลบ '+ name +' หรือไม่ ?',
    type:'warning',
    showCancelButton: true,
		confirmButtonColor: '#FA5858',
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		closeOnConfirm: true
  },function() {
    load_in();

    setTimeout(() => {
      $.ajax({
        url: HOME + 'delete',
        type:'POST',
        cache:false,
        data:{
          'id' : id
        },
        success:function(rs) {
          load_out();
          if(rs == 'success') {
            swal({
              title:'Deleted',
              type:'success',
              time: 1000
            });

            setTimeout(function() {
              if(option == 'goBack') {
                goBack();
              }
              else {
                window.location.reload();
              }
            }, 1500)
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
    }, 100);
  })
}
