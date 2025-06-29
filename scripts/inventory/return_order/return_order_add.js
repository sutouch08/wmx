var click = 0;

$('.input-qty').focus(function() {
	$(this).select();
})


$('.input-qty').keyup(function(e) {
	if(e.keyCode === 13) {
		let no = parseDefault(parseInt($(this).data('no')), 1);
		no++;
		$('.input-'+no).focus().select();
	}
})


$('#barcode-zone').keyup(function(e) {
	if(e.keyCode === 13) {
		setTimeout(() => {
			getZone();
		},200);
	}
})


$('#barcode-zone').change(function() {
	let code = $(this).val().trim();

	if(code.length == 0) {
		$('#zone-code').val('');
		$('#zone-name').val('');
	}
	else {
		getZone();
	}
})


$('#barcode-zone').autocomplete({
	source:BASE_URL + 'auto_complete/get_zone_code_and_name',
	autoFocus:true,
	close:function() {
		let arr = $(this).val().split(' | ');

		if(arr.length == 2) {
			$(this).val(arr[0]);
			$('#zone-code').val(arr[0]);
			$('#zone-name').val(arr[1]);
		}
		else {
			$(this).val('');
			$('#zone-code').val('');
			$('#zone-name').val('');
		}
	}
})


function changeZone() {
	$('#zone-code').val('');
	$('#zone-name').val('');
	$('#barcode-zone').val('').focus();
}


function saveAsDraft() {
	if(click == 0) {
		click = 1;
		error = 0;

		clearErrorByClass('input-qty');

		let h = {
			'code' : $('#code').val(),
			'zone_code' : $('#zone-code').val(),
			'zone_name' : $('#zone-name').val().trim(),
			'rows' : []
		}

		$('.input-qty').each(function() {
			let el = $(this);
			let id = el.data('id');
			let return_qty = parseDefault(parseFloat(removeCommas($('#return-qty-'+id).val())), 0);
			let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);

			if(qty < 0 || qty > return_qty) {
				el.hasError();
				error++;
			}

			h.rows.push({
				'id' : id,
				'qty' : qty,
				'line_num' : el.data('linenum'),
				'no' : el.data('no'),
				'product_code' : el.data('pdcode')
			});
		});

		if(error > 0) {
			click = 0;
			beep();

			swal({
				title:'Oops !',
				text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
				type:'error'
			});

			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save_as_draft',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				click = 0;

				if(rs.trim() === 'success') {
					swal({
						title:'Success',
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
				click = 0;
				showError(rs);
			}
		});
	}
}


function save() {
	if(click == 0) {
		click = 1;
		let error = 0;

		clearErrorByClass('input-qty');

		let h = {
			'code' : $('#code').val(),
			'zone_code' : $('#zone-code').val(),
			'zone_name' : $('#zone-name').val().trim(),
			'rows' : []
		}

		if(h.zone_code == "") {
			$('#barcode-zone').hasError();
			click = 0;
			swal("กรุณาระบุโซนรับเข้า");
			return false;
		}

		$('.input-qty').each(function() {
			let el = $(this);
			let id = el.data('id');
			let return_qty = parseDefault(parseFloat(removeCommas($('#return-qty-'+id).val())), 0);
			let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);

			if(qty < 0 || qty > return_qty || qty < return_qty) {
				el.hasError();
				error++;
			}

			h.rows.push({
				'id' : id,
				'qty' : qty,
				'line_num' : el.data('linenum'),
				'no' : el.data('no'),
				'product_code' : el.data('pdcode')
			});
		});

		if(error > 0) {
			click = 0;
			beep();

			swal({
				title:'Oops !',
				text:'พบรายการที่ไม่ถูกต้อง กรุณาแก้ไข',
				type:'error'
			});

			return false;
		}

		load_in();

		$.ajax({
			url:HOME + 'save',
			type:'POST',
			cache:false,
			data:{
				'data' : JSON.stringify(h)
			},
			success:function(rs) {
				load_out();

				click = 0;

				if(rs.trim() === 'success') {
					swal({
						title:'Success',
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
				click = 0;
				showError(rs);
			}
		});
	}
}


function getZone() {
	let code = $('#barcode-zone').val().trim();

	if(code.length > 0) {
		$.ajax({
			url:HOME + 'get_zone',
			type:'POST',
			cache:false,
			data:{
				'zone_code' : code
			},
			success:function(rs) {
				if(isJson(rs)) {
					let ds = JSON.parse(rs);

					if(ds.status === 'success') {

						if(ds.data.active == 1) {
							$('#zone-code').val(ds.data.code);
							$('#zone-name').val(ds.data.name);
							$('#qty').val(1);
							$('#barcode').val('').focus();
						}
						else {
							showError("This zone is inactive");
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
	else {
		$('#zone-code').val('');
		$('#zone-name').val('');
	}
}


function recalRow(id) {
	let el = $('#qty-'+id);
	el.clearError();
	let return_qty = parseDefault(parseFloat(el.data('qty')), 0);
	let qty = parseDefault(parseFloat(el.val()), 0);

	if( qty < 0) {
		qty = 0;
		el.val(0);
	}

	if(qty > return_qty) {
		el.hasError();
	}

	recalTotal();
}


function recalTotal() {
	let totalQty = 0;

	$('.input-qty').each(function() {
		let qty = parseDefault(parseFloat($(this).val()), 0);
		totalQty += qty;
	})

	$('#total-receive').text(addCommas(totalQty.toFixed(2)));
}
