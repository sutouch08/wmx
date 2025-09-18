var zero_qty = 0;
var click = 0;

function save() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('c');
    error = 0;

    let code = $('#consign_code').val();
    let len = $('.input-qty').length;

    if(len == 0) {
      swal("ไม่พบรายการสินค้า");
      click = 0;
      return false;
    }

    $('.input-qty').each(function() {
      let id = $(this).data('id');
      let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
      let discount = $('#disc-'+id).val();
      let disc = parseDiscount(discount, price);
      let qty = parseDefaultFloat(removeCommas($('#qty-'+id).val()), 0);
      let amount = qty * (price - disc.discountAmount);

      if(price < 0) {
        $('#price-'+id).hasError();
        error++;
      }

      if(disc.discountAmount < 0 || disc.discountAmount > price) {
        $('#disc-'+id).hasError();
        error++;
      }

      if(qty <= 0) {
        $('#qty-'+id).hasError();
        error++;
      }
    })

    if(error > 0) {
      click = 0;
      swal("พบข้อผิดพลาด กรุณาแก้ไข");
      return false;
    }

    swal({
  		title: "บันทึกขายและตัดสต็อก",
  		text: "เมื่อบันทึกแล้วจะไม่สามารถแก้ไขได้ ต้องการบันทึกหรือไม่ ?",
  		type: "warning",
  		showCancelButton: true,
  		confirmButtonColor: "#8CC152",
  		confirmButtonText: 'บันทึก',
  		cancelButtonText: 'ยกเลิก',
  		closeOnConfirm: true
    },
    function() {
      load_in();

      setTimeout(() => {
        $.ajax({
          url:HOME + 'save_consign/' + code,
          type:'POST',
          cache:false,
          success:function(rs) {
            click = 0;
            load_out();

            if(isJson(rs)) {
              let ds = JSON.parse(rs);

              if(ds.status === 'success') {

                if(ds.ex == 1) {
                  //-- ex = 1 mean save document success but export data to Oracle failed
                  swal({
                    title:'Oops !',
                    text:ds.message,
                    type:'info'
                  }, function() {
                    viewDetail(code);
                  })
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
                beep();
                showError(ds.message);
              }
            }
            else {
              beep();
              showError(rs);
            }
          },
          error:function(rs) {
            beep();
            showError(rs);
            click = 0;
          }
        })
      },100);
    });
  }
}


function rollback() {
  let code = $('#consign_code').val();

  swal({
    title: "Rollback Status",
    text: "ต้องการย้อนสถานะเอกสารกลับมาแก้ไขใหม่หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#8CC152",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  },
  function() {
    setTimeout(() => {
      load_in();

      $.ajax({
        url:HOME + 'rollback/' + code,
        typ:'GET',
        cache:false,
        success:function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              goEdit(code);
            }, 1200);
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
  });
}


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});



$("#customerCode").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      zoneInit(code, true);
		}else{
			$("#customerCode").val('');
			$("#customer").val('');
      zoneInit('');
		}
	}
});


$("#customer").autocomplete({
	source: BASE_URL + 'auto_complete/get_customer_code_and_name',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customerCode").val(code);
			$("#customer").val(name);
      zoneInit(code, true);
		}else{
			$("#customerCode").val('');
			$(this).val('');
      zoneInit('');
		}
	}
});

$('#pd-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_style_code',
  autoFocus:true
})


//---	กำหนดให้สามารถค้นหาโซนได้ก่อนจะค้นหาลูกค้า(กรณี edit header)
$(document).ready(function(){
	var customer_code = $('#customerCode').val();
	zoneInit(customer_code, false);
});



function zoneInit(customer_code, edit)
{
  if(edit) {
    $('#zone_code').val('');
    $('#zone').val('');
  }

  $('#zone').autocomplete({
    source:BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
    autoFocus: true,
    close:function(){
      var rs = $.trim($(this).val());
      var arr = rs.split(' | ');
      if(arr.length == 2)
      {
        var code = arr[0];
        var name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }else{
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  });

  $('#zone_code').autocomplete({
    source:BASE_URL + 'auto_complete/get_consign_zone/' + customer_code,
    autoFocus: true,
    close:function(){
      var rs = $.trim($(this).val());
      var arr = rs.split(' | ');
      if(arr.length == 2)
      {
        var code = arr[0];
        var name = arr[1];
        $('#zone_code').val(code);
        $('#zone').val(name);
      }else{
        $('#zone_code').val('');
        $('#zone').val('');
      }
    }
  });
}




function add(){
  var customer_code = $('#customerCode').val();
  var customer_name = $('#customer').val();
  var date_add = $('#date').val();
  var zone_code = $('#zone_code').val();
  var zone_name = $('#zone').val();


  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  if(!isDate(date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(zone_code.length == 0 || zone_name.length == 0)
  {
    swal('โซนไม่ถูกต้อง');
    return false;
  }

  $('#addForm').submit();
}


var customer;
var payment;
var date;


function getEdit(){
  $('.edit').removeAttr('disabled');
  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function update() {
  
  let code = $('#consign_code').val();
  let date = $('#date').val();
  let remark = $('#remark').val();
  var customer_code = $('#customerCode').val();
  var customer_name = $('#customer').val();
  var zone_code = $('#zone_code').val();
  var zone_name = $('#zone').val();

  if(!isDate(date)){
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(customer_code.length == 0 || customer_name.length == 0){
    swal('ชื่อลูกค้าไม่ถูกต้อง');
    return false;
  }

  if(zone_code.length == 0 || zone_name.length == 0)
  {
    swal('โซนไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'update',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'date' : date,
      'remark' : remark
    },
    success:function(rs){
      load_out();
      if(rs === 'success'){
        swal({
          title:'Updted',
          type:'success',
          timer:1000
        });

        $('.edit').attr('disabled', 'disabled');
        $('#btn-edit').removeClass('hide');
        $('#btn-update').addClass('hide');
      }
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


function deleteDetail(id){
  $.ajax({
    url: HOME + 'delete_detail/'+id,
    type:'POST',
    cache:'false',
    success:function(rs) {
      if(rs.trim() == 'success') {
        swal({
          title:'Deleted',
          type:'success',
          timer:1000
        });

        $('#row-'+id).remove();
        reIndex();
        updateTotalQty();
        updateTotalAmount();
      }
    }
  });
}


function getSample(){
  var token	= new Date().getTime();
	get_download(token);
	window.location.href = HOME + 'get_sample_file/'+token;
}


function getUploadFile(){
  $('#upload-modal').modal('show');
}


function getFile(){
  $('#uploadFile').click();
}

$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}
		//readURL(this);
    $('#show-file-name').text(name);
	}
});


function uploadfile(){
  var code = $('#consign_code').val();
  var excel = $('#uploadFile')[0].files[0];

	$("#upload-modal").modal('hide');

	var fd = new FormData();

	fd.append('excel', $('input[type=file]')[0].files[0]);
	load_in();

	$.ajax({
		url:HOME + 'import_excel_file/'+code,
		type:"POST",
    cache: "false",
    data: fd,
    processData:false,
    contentType: false,
		success: function(rs){
			load_out();
			var rs = $.trim(rs);
			if( rs == 'success')
			{
        swal({
          title:'Success',
          type:'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);
			}
			else
			{
				swal({
          title:"Error!",
          text:rs,
          type:"error",
          html:true
        });
			}
		},
		error:function(xhr, status, error){
			load_out();
			var errorMessage = xhr.status + ': '+xhr.statusText;
			swal({
				title:'Error!',
				text:'Error-'+errorMessage,
				type:'error'
			});
		}
	});
}


function validateOrder(){
  var prefix = $('#prefix').val();
  var runNo = parseInt($('#runNo').val());
  let code = $('#code').val();
  if(code.length == 0){
    add();
    return false;
  }

  let arr = code.split('-');

  if(arr.length == 2){
    if(arr[0] !== prefix){
      swal('Prefix ต้องเป็น '+prefix);
      return false;
    }else if(arr[1].length != (4 + runNo)){
      swal('Run Number ไม่ถูกต้อง');
      return false;
    }else{
      $.ajax({
        url: HOME + 'is_exists/'+code,
        type:'GET',
        cache:false,
        success:function(rs){
          if(rs == 'not_exists'){
            add();
          }else{
            swal({
              title:'Error!!',
              text: rs,
              type: 'error'
            });
          }
        }
      })
    }

  }else{
    swal('เลขที่เอกสารไม่ถูกต้อง');
    return false;
  }

}
