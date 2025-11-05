var zero_qty = 0;
var click = 0;

window.addEventListener('load', () => {
  customerInit();
})

function customerInit() {
  let whsCode = $('#warehouse').val();

  $('#customer-code').autocomplete({
    source:HOME + 'get_customer_by_warehouse/'+whsCode,
    autoFocus:true,
    close:function() {
      let arr = $(this).val().split(' | ');

      if(arr.length == 3) {
        $(this).val(arr[0]);
        $('#customer-name').val(arr[1]);
        $('#gp').val(arr[2]);

        warehouseInit();
      }
      else {
        $(this).val('');
        $('#customer-name').val('');
        $('#gp').val('');
        warehouseInit();
      }
    }
  })
}


$('#customer-code').change(function() {
  if($(this).val().trim() == "") {
    $('#customer-name').val('');
    warehouseInit();
  }
})


function warehouseInit() {
  let custCode = $('#customer-code').val().trim();
  let whsCode = $('#warehouse').val();

  $.ajax({
    url:HOME + 'get_consignment_warehouse_by_customer',
    type:'POST',
    cache:false,
    data:{
      'customer_code' : custCode,
      'warehouse_code' : whsCode
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $('#warehouse-template').html();
          let output = $('#warehouse');

          render(source, ds.data, output);

          $('#warehouse').select2();
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


function updateCustomer() {
  let whsCode = $('#warehouse').val();
  let customer_code = $('#customer-code').val().trim();

  if(whsCode != "") {
    $.ajax({
      url:HOME + 'get_consignment_customer_by_warehouse',
      type:'POST',
      cache:false,
      data:{
        'warehouse_code' : whsCode,
        'customer_code' : customer_code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            if(ds.data != null && ds.data != "" && ds.data != undefined) {
              $('#customer-code').val(ds.data.customer_code);
              $('#customer-name').val(ds.data.customer_name);
              $('#gp').val(ds.data.gp);
            }
            else {
              $('#customer-code').val('');
              $('#customer-name').val('');
              $('#gp').val('');
            }

            customerInit();
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
  else {
    customerInit();
  }
}


function confirmSave(save_type) {
  if(save_type == 'A') {
    swal({
  		title: "บันทึกและอนุมัติ",
  		text: "เมื่ออนุมัติแล้วจะไม่สามารถแก้ไขได้อีก ต้องการดำเนินการต่อหรือไม่ ?",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#8CC152",
  		confirmButtonText: 'Save and Approve',
  		cancelButtonText: 'No',
  		closeOnConfirm: true
    }, function(){
      setTimeout(() => {
        save(save_type);
      }, 100);
    });
  }
  else {
    save(save_type);
  }
}


function save(save_type) {
  clearErrorByClass('e');

  let code = $('#code').val();
  let error = 0;

  if($('.qty').length == 0) {
    showError("ไม่พบรายการสินค้า");
    return false;
  }

  $('.qty').each(function() {
    let el = $(this);
    let id = el.data('id');
    let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
    let disc = parseDiscount($('#disc-'+id).val(), price);
    let qty = parseDefaultFloat(removeCommas(el.val()), 0);

    if(price < 0) {
      error++;
      $('#price-'+id).hasError();
    }

    if(disc.discountAmount > price) {
      error++;
      $('#disc-'+id).hasError();
    }

    if(qty <= 0) {
      error++;
      el.hasError();
    }
  });

  if(error > 0) {
    showError("กรุณาแก้ไขรายการที่ไม่ถูกต้อง");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'save',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'save_type' : save_type  // S = save, A = Save and approve
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          if(ds.ex == 1) {
            swal({
              title:'Oops',
              text:ds.message,
              type:'info'
            }, function() {
              viewDetail(code);
            });
          }
          else {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              viewDetail(code);
            }, 1200);
          }
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


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#posting-date').datepicker({
  dateFormat:'dd-mm-yy'
});


function add() {
  if(click === 0) {
    click = 1;
    clearErrorByClass('r');

    let h = {
      'customer_code' : $('#customer-code').val().trim(),
      'customer_name' : $('#customer-name').val().trim(),
      'gp' : parseDefaultFloat($('#gp').val(), 0),
      'date_add' : $('#date').val(),
      'posting_date' : $('#posting-date').val(),
      'warehouse_code' : $('#warehouse').val(),
      'remark' : $('#remark').val().trim()
    };

    if( ! isDate(h.date_add)) {
      $('#date').hasError();
      click = 0;
      return false;
    }

    if( ! isDate(h.posting_date)) {
      $('#posting-date').hasError();
      click = 0;
      return false;
    }

    if(h.customer_code.length == 0 || h.customer_name.length == 0) {
      $('#customer-code').hasError();
      $('#customer-name').hasError();
      click = 0;
      return false;
    }

    if(h.gp > 100 || h.gp < 0) {
      $('#gp').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      click = 0;
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
        click = 0;
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            goEdit(ds.code);
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
        click = 0;
        showError(rs);
      }
    })
  }
}


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update() {
  clearErrorByClass('r');

  let h = {
    'code' : $('#code').val(),
    'customer_code' : $('#customer-code').val().trim(),
    'customer_name' : $('#customer-name').val().trim(),
    'gp' : parseDefaultFloat($('#gp').val(), 0),
    'date_add' : $('#date').val(),
    'posting_date' : $('#posting-date').val(),
    'warehouse_code' : $('#warehouse').val(),
    'remark' : $('#remark').val().trim()
  };


  if( ! isDate(h.date_add)) {
    $('#date').hasError();
    return false;
  }

  if( ! isDate(h.posting_date)) {
    $('#posting-date').hasError();
    return false;
  }

  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    $('#customer-code').hasError();
    $('#customer-name').hasError();
    return false;
  }

  if(h.gp > 100 || h.gp < 0) {
    $('#gp').hasError();
    return false;
  }

  if(h.warehouse_code == "") {
    $('#warehouse').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'update',
    type:'POST',
    cache:false,
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();
      if(rs.trim() === 'success') {
        swal({
          title:'Updated',
          type:'success',
          timer:1000
        });

        setTimeout(() => {
          refresh();
        }, 1200);
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


function deleteRow(id, code){
  swal({
		title: "คุณแน่ใจ ?",
		text: "ต้องการลบ '"+code+"' หรือไม่ ?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#FA5858",
		confirmButtonText: 'ใช่, ฉันต้องการลบ',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
		}, function(){
      deleteDetail(id);
	});
}


function getSample(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'get_sample_file/'+token;
}


function getUploadFile() {
  $('#show-file-name').val('');
  $('#uploadFile').val('');
  $('#upload-modal').modal('show');
}


function getFile(){
  $('#uploadFile').click();
}


$("#uploadFile").change(function() {
	if($(this).val() != '')
	{
		let file = this.files[0];
		let name = file.name;
		let type = file.type;
		let size = file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}

    $('#show-file-name').val(name);
	}
});


function uploadfile() {
  let code = $('#code').val();
  let excel = $('#uploadFile')[0].files[0];
	$("#upload-modal").modal('hide');
	let fd = new FormData();
	fd.append('excel', $('input[type=file]')[0].files[0]);

	load_in();

	$.ajax({
		url:HOME + 'import_excel_file/'+code,
		type:'POST',
    cache: false,
    data: fd,
    processData:false,
    contentType: false,
		success: function(rs) {
			load_out();
			if(rs.trim() == 'success')
			{
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

				setTimeout(function(){
          refresh();
        }, 1200);
			}
			else
			{
        showError(rs);
			}
		},
		error:function(rs){
			showError(rs);
		}
	});
}


function doApprove(code) {
  swal({
    title:'Approval',
    text:'เมื่ออนุมัติแล้วจะไม่สามารถแก้ไขได้อีก<br/>ต้องการอนุมัติ '+ code + ' หรือไม่ ?',
    type:'info',
    html:true,
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No',
    confirmButtonColor:'#81a87b',
    closeOnConfirm:true
  }, function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'approve',
        type:'POST',
        cache:false,
        data:{
          'code' : code
        },
        success:function(rs) {
          load_out();

          if(isJson(rs)) {
            let ds = JSON.parse(rs);

            if(ds.status === 'success') {
              if(ds.ex == 1) {
                swal({
                  title:'Approved',
                  type:'info',
                  text: ds.message
                }, function() {
                  refresh();
                });
              }
              else {
                swal({
                  title:'Approved',
                  type:'success',
                  timer:1000
                });

                setTimeout(() => {
                  refresh();
                }, 1200);
              }
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
    }, 100);
  })
}
