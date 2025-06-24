$('#bill_sub_district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#bill_sub_district').val(adr[0]);
			$('#bill_district').val(adr[1]);
			$('#bill_province').val(adr[2]);
			$('#bill_postcode').val(adr[3]);
		}
	}
});


$('#bill_district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#bill_district').val(adr[0]);
			$('#bill_province').val(adr[1]);
			$('#bill_postcode').val(adr[2]);
		}
	}
});


$('#bill_province').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#bill_province').val(adr[0]);
			$('#bill_postcode').val(adr[1]);
		}
	}
})


function removeAddress(id){
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: false
	}, function(){
		$.ajax({
			url:BASE_URL + 'masters/customers/delete_ship_to',
			type:"POST",
			cache:"false",
			data:{
				"id_address" : id
			},
			success: function(rs) {
				if( rs.trim() == 'success' ){
					swal({
						title : "สำเร็จ",
						type:'success',
						timer: 1000
					});

					reloadShipToTable();
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
		});
	});
}


function editAddress(id) {
	$.ajax({
		url:BASE_URL + 'masters/customers/get_ship_to',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id
		},
		success: function(rs){
			var rs = $.trim(rs);
			if( isJson(rs) ){
				var ds = $.parseJSON(rs);
				$("#id_address").val(ds.id);
				$("#Fname").val(ds.consignee);
				$("#address1").val(ds.address);
				$("#sub_district").val(ds.sub_district);
				$('#district').val(ds.district);
				$("#province").val(ds.province);
				$("#postcode").val(ds.postcode);
				$("#phone").val(ds.phone);
				$("#email").val(ds.email);
				$("#alias").val(ds.name);
				$("#addressModal").modal('show');
			}
			else {
				swal("ข้อผิดพลาด!", "ไม่พบข้อมูลที่อยู่", "error");
			}
		}
	});
}


function setDefault(id){
	var customer_ref = $('#customers_code').val();
	$.ajax({
		url:BASE_URL + 'orders/orders/set_default_address',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id,
			"customer_ref" : customer_ref
		},
		success: function(rs){
			$(".btn-address").removeClass('btn-success');
			$("#btn-"+id).addClass('btn-success');
		}
	});
}


function addNewAddress(){
	clearAddressField();
	$("#addressModal").modal('show');
}


$('#sub_district').autocomplete({
	source:BASE_URL + 'auto_complete/sub_district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 4){
			$('#sub_district').val(adr[0]);
			$('#district').val(adr[1]);
			$('#province').val(adr[2]);
			$('#postcode').val(adr[3]);
		}
	}
});


$('#district').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 3){
			$('#district').val(adr[0]);
			$('#province').val(adr[1]);
			$('#postcode').val(adr[2]);
		}
	}
});


$('#province').autocomplete({
	source:BASE_URL + 'auto_complete/district',
	autoFocus:true,
	open:function(event){
		var $ul = $(this).autocomplete('widget');
		$ul.css('width', 'auto');
	},
	close:function(){
		var rs = $.trim($(this).val());
		var adr = rs.split('>>');
		if(adr.length == 2){
			$('#province').val(adr[0]);
			$('#postcode').val(adr[1]);
		}
	}
})


function clearAddressField() {
	$("#id_address").val('');
	$("#Fname").val('');
	$("#address1").val('');
	$('#sub_district').val('');
	$('#district').val('');
	$("#province").val('');
	$("#postcode").val('');
	$("#phone").val('');
	$("#alias").val('');
}


function reloadShipToTable() {
	var customer_code = $('#code').val();

	$.ajax({
		url:BASE_URL + 'masters/customers/get_ship_to_table',
		type:"POST",
		cache:"false",
		data:{
			'customer_code' : customer_code
		},
		success: function(rs){
			var rs = $.trim(rs);
			if(isJson(rs)){
				var source 	= $("#addressTableTemplate").html();
				var data= $.parseJSON(rs);
				var output 	= $("#adrs");
				render(source, data, output);
			}
			else {
				$("#adrs").html('<tr class="font-size-11"><td colspan="7" class="text-center">--- ไม่พบที่อยู่ ---</td></tr>');
			}
		}
	});
}


function saveShipTo() {
	clearErrorByClass('e');

	let h = {
		'adrType' : 'S',
		'id_address' : $('#id_address').val(),
		'customer_code' : $('#code').val(),
		'customer_name' : $('#name').val(),
		'consignee' : $('#Fname').val(),
		'name' : $('#alias').val().trim(),
		'address' : $('#address1').val().trim(),
		'sub_district' : $('#sub_district').val().trim(),
		'district' : $('#district').val().trim(),
		'province' : $('#province').val().trim(),
		'postcode' : $('#postcode').val().trim(),
		'phone' : $('#phone').val().trim()
	}

	if( h.name.length == 0 ) {
		$('#Fname').hasError();
		return false;
	}

	if( h.address.length == 0 ){
		$('#address1').hasError();
		return false;
	}

	if( h.alias == '' ){
		$('#alias').hasError();
		return false;
	}

	$("#addressModal").modal('hide');

	load_in();

	$.ajax({
		url:BASE_URL + 'masters/customers/add_ship_to',
		type:'POST',
		cache:false,
		data: {
			'data' : JSON.stringify(h)
		},
		success: function(rs){
			load_out();

			if(rs.trim() === 'success') {
				reloadShipToTable();
				clearAddressField();
			}
			else {
				swal({
					title:'ข้อผิดพลาด',
					text:rs,
					type:'error'
				});
				$("#addressModal").modal('show');
			}
		}
	});
}


function updateBillTo() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('b');

    let h = {
      'customer_code' : $('#code').val(),
      'branch_code' : $('#bill_branch_code').val().trim(),
      'branch_name' : $('#bill_branch_name').val().trim(),
      'address' : $('#bill_address').val().trim(),
      'sub_district' : $('#bill_sub_district').val().trim(),
      'district' : $('#bill_district').val().trim(),
      'province' : $('#bill_province').val().trim(),
      'postcode' : $('#bill_postcode').val().trim(),
      'country' : $('#bill_country').val().trim(),
      'phone' : $('#bill_phone').val().trim()
    }

    if(h.branch_code.length === 0) {
      click = 0;
      $('#bill_branch_name').hasError();
      return false;
    }

    if(h.branch_name.length === 0) {
      click = 0;
      $('#bill_branch_name').hasError();
      return false;
    }

    if(h.address.length === 0) {
      click = 0;
      $('#bill_address').hasError();
      return false;
    }

    if(h.sub_district.length === 0) {
      click = 0;
      $('#bill_sub_district').hasError();
      return false;
    }

    if(h.district.length === 0) {
      click = 0;
      $('#bill_district').hasError();
      return false;
    }

    if(h.province.length === 0) {
      click = 0;
      $('#bill_province').hasError();
      return false;
    }

    $.ajax({
      url:HOME + 'update_bill_to',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        click = 0;
        if(rs.trim() === 'success') {
          swal({
            title:'Success',
            type:'success',
            timer:1000
          })
        }
        else {
          beep();
          showError(rs);
        }
      },
      error:function(rs) {
        click = 0;
        beep();
        showError(rs);
      }
    })
  }
}
