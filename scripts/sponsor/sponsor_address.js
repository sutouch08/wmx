

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


function addNewAddress(){
  clearAddressField();
  $("#addressModal").modal('show');
}


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
	var customer_code = $('#customer-code').val();

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

        let order_address_id = $('#id_address').val();

        $('.btn-address').removeClass('btn-success');
        $('#btn-'+order_address_id).addClass('btn-success');
			}
			else {
				$("#adrs").html('<tr class="font-size-11"><td colspan="7" class="text-center">--- ไม่พบที่อยู่ ---</td></tr>');
			}
		}
	});
}


function reloadShipToRow(id) {
  let order_address_id = $('#id_address').val();

  $.ajax({
    url:BASE_URL + 'masters/customers/get_ship_to',
    type:'POST',
    cache:false,
    data:{
      'id_address' : id
    },
    success:function(rs) {
      if(isJson(rs)) {
        let ds = JSON.parse(rs);
        let source = $('#addressTemplate').html();
        let output = $('#'+id);

        render(source, ds, output);

        $('.btn-address').removeClass('btn-success');
        $('#btn-'+order_address_id).addClass('btn-success');
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


function saveShipTo() {
	clearErrorByClass('e');

	let h = {
		'adrType' : 'S',
		'id_address' : $('#address-id').val(),
		'customer_code' : $('#customer-code').val(),
		'customer_name' : $('#customer-name').val(),
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
        if(h.id_address != "") {
          reloadShipToRow(h.id_address);
        }
        else {
          reloadShipToTable();
        }

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


function editAddress(id) {
	$.ajax({
		url:BASE_URL + 'masters/customers/get_ship_to',
		type:"POST",
		cache:"false",
		data:{
			"id_address" : id
		},
		success: function(rs) {
			if( isJson(rs) ){
				let ds = JSON.parse(rs);
				$("#address-id").val(ds.id);
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


function removeAddress(id){
	swal({
		title: 'ต้องการลบที่อยู่ ?',
		text: 'คุณแน่ใจว่าต้องการลบที่อยู่นี้ โปรดจำไว้ว่าการกระทำนี้ไม่สามารถกู้คืนได้',
		type: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#DD6855',
		confirmButtonText: 'ใช่ ลบเลย',
		cancelButtonText: 'ยกเลิก',
		closeOnConfirm: true
	}, function(){
		$.ajax({
			url:BASE_URL + 'masters/customers/delete_ship_to',
			type:"POST",
			cache:"false",
			data:{
				"id_address" : id
			},
			success: function(rs) {
				if( rs.trim() == 'success' ) {
          $('#'+id).reomve();
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
