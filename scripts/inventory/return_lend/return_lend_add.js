var click = 0;

function save() {
	if(click == 0) {
		click = 1;
		clearErrorByClass('e');

		var error = 0;
		let code = $('#code').val();
		let zone_code = $('#zone_code').val();
		let zoneName = $('#zone').val();
		let empID = $('#employee').val();
		let empName = $('#employee option:selected').data('name');
		let lendCode = $('#lend_code').val();
		let date_add = $('#date-add').val();
		let remark = $('#remark').val().trim();

		if( ! isDate(date_add)) {
			click = 0;
			$('#date-add').hasError();
			swal("วันที่ไม่ถูกต้อง");
			return false;
		}

		if(zone_code.length == 0 || zoneName.length == 0){
			click = 0;
			$('#zone_code').hasError();
			swal("กรุณาระบุโซนรับเข้า");
			return false;
		}

		if(empName.length == 0 || empID == ''){
			click = 0;
			swal("กรุณาระบุผู้ยืม");
			return false;
		}

		if(lendCode.length == 0){
			click = 0;
			$('#lend_code').hasError();
			swal("กรุณาระบุใบยืมสินค้า");
			return false;
		}

		let header = {
			"code" : code,
			"date_add" : date_add,
			"empID" : empID,
			"empName" : empName,
			"lendCode" : lendCode,
			"zone_code" : zone_code,
			"remark" : remark
		}

		let rows = [];

		$('.qty').each(function() {
			let no = $(this).data('no');
			let qty = parseDefault(parseFloat($(this).val()), 0);
			let limit = parseDefault(parseFloat($('#backlogs-'+no).val()), 0);
			let itemCode = $(this).data('product');
			let itemName = $(this).data('name');

			if(qty > 0 && qty <= limit) {

				let row = {
					"product_code" : itemCode,
					"product_name" : itemName,
					"qty" : qty
				}

				rows.push(row);

				$(this).removeClass('has-error');
			}

			if(qty < 0 || qty > limit){
				error++;
				$(this).addClass('has-error');
			}
		});

		if(error > 0) {
			click = 0;
			swal({
				title:'Error!',
				text:"จำนวนที่คืนต้องไม่มากกว่ายอดค้างรับ และ ต้องไม่น้อยกว่า 0",
				type:'error'
			});

			return false;
		}

		if(rows.length < 1) {
			click = 0;
			swal({
				title:'Error!',
				text:"ต้องคืนอย่างน้อย 1 ตัว",
				type:'error'
			});

			return false
		}

		load_in();

		$.ajax({
			url:HOME + 'add',
			type:'POST',
			cache:false,
			data:{
				"header" : JSON.stringify(header),
				"details" : JSON.stringify(rows)
			},
			success:function(rs) {
				load_out();
				click = 0;
				if(isJson(rs)) {
					let ds = JSON.parse(rs);
					if(ds.status === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						setTimeout(function() {
							viewDetail(ds.code);
						}, 1200);
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
				click = 0;
				beep();
				showError(rs);
			}
		});
	}
}


$('#date-add').datepicker({
	dateFormat:'dd-mm-yy'
});


$('#zone').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$("#zone").val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$("#zone").val('');
			$('#zone_code').val('');
		}
	}
});


$('#zone_code').autocomplete({
	source : BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function(){
		var arr = $(this).val().split(' | ');
		if(arr.length == 2){
			$("#zone").val(arr[1]);
			$('#zone_code').val(arr[0]);
		}else{
			$(this).val('');
			$('#zone').val('');
			$('#zone_code').val('');
		}
	}
});


function recalTotal(){
	var totalQty = 0;
	$('.qty').each(function(){
		let qty = $(this).val();
		qty = parseDefault(parseFloat(qty),0);
		totalQty += qty;
	});

	$('#totalQty').text(addCommas(totalQty));
}
