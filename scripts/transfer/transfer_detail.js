function doExport()
{
	var code = $('#transfer_code').val();
	load_in();
	$.ajax({
		url:HOME + 'export_transfer/' + code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error',
					html:true
				});
			}
		}
	});
}


function removeTemp(id, item_code) {
	swal({
		title:'คุณแน่ใจ ?',
		text:'ต้องการลบ '+item_code+' หรือไม่ ?',
		type:'warning',
		showCancelButton:true,
		confirmButtonColor:'#d15b47',
		confirmButtonText:'Yes',
		cancelButtonText:'No',
		closeOnConfirm:true
	}, function() {
		load_in();

		setTimeout(() => {
			$.ajax({
				url:HOME + 'delete_temp',
				type:'POST',
				cache:false,
				data:{
					'id' : id
				},
				success:function(rs) {
					load_out();

					if(rs === 'success') {
						swal({
							title:'Success',
							type:'success',
							timer:1000
						});

						$('#row-temp-'+id).remove();
						reIndex('tmp-no');
					}
					else {
						swal({
							title:'Error!',
							text:rs,
							type:'error',
							html:true
						});
					}
				}
			})
		}, 200);
	})
}





function updateTransferQty(id) {
	let el = $('#trans-qty-'+id);
	let prevQty = parseDefault(parseFloat(el.data('qty')), 0);
	let qty = parseDefault(parseFloat(el.val()), 0);
	let wmsQty = parseDefault(parseFloat(el.data('wms')), 0);

	if(qty < wmsQty) {
		beep();
		showError('ยอดตั้งต้องไม่น้อยกว่ายอดที่รับแล้ว');
		el.val(prevQty);
		return false;
	}

	load_in();

	$.ajax({
		url:HOME + 'update_transfer_qty',
		type:'POST',
		cache:false,
		data:{
			'id' : id,
			'qty' : qty
		},
		success:function(rs) {
			load_out();

			if(rs.trim() === 'success') {
				el.data('qty', qty);
				reCal(-1);
			}
			else {
				beep();
				el.val(prevQty);
				showError(rs);
			}
		},
		error:function(rs) {
			beep();
			showError(rs);
		}
	});
}





//------------  ตาราง transfer_detail
function getTransferTable(){
	var code	= $("#transfer_code").val();

	$.ajax({
		url: HOME + 'get_transfer_table/'+ code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#transferTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#transfer-list");
				render(source, data, output);
			}
		}
	});
}




function getTempTable(){
	var code = $("#transfer_code").val();
	$.ajax({
		url: HOME + 'get_temp_table/'+code,
		type:"GET",
    cache:"false",
		success: function(rs){
			if( isJson(rs) ){
				var source 	= $("#tempTableTemplate").html();
				var data		= $.parseJSON(rs);
				var output	= $("#temp-list");
				render(source, data, output);
			}
		}
	});
}






function recalZoneQty() {
	$('.input-qty').each(function() {
		if($(this).val() != 0) {
			let no = $(this).data('no');
			let limit = parseDefault(parseFloat($(this).data('limit')), 0);
			let qty = parseDefault(parseFloat($(this).val()), 0);

			if(qty > 0 && limit > 0) {
				let nQty = limit - qty;

				$('#qty-label-'+no).text(addCommas(nQty));
				$(this).data('limit', nQty);
				$(this).val('');

				if(nQty <= 0) {
					$('#zone-row-'+no).remove();
					reIndex('zone-no');
				}
			}
		}
	})
}


//----- นับจำนวน ช่องที่มีการใส่ตัวเลข
function countInput(){
	var count = 0;
	$(".input-qty").each(function(index, element) {
        count += ($(this).val() == "" ? 0 : 1 );
    });
	return count;
}

function accept() {
	let canAccept = $('#can-accept').val() == 1 ? true : false;
	let code = $('#transfer_code').val();

	if(canAccept) {
		$('#accept-modal').on('shown.bs.modal', () => $('#accept-note').focus());
		$('#accept-modal').modal('show');
	}
	else {

		swal({
			title:'Acception',
			text:'ยินยอมให้โอนสินค้าเข้าโซนของคุณใช่หรือไม่ ?',
			type:'info',
			showCancelButton:true,
			confirmButtonColor:'#87B87F',
			confirmButtonText:'ยืนยัน',
			cancelButtonText:'ยกเลิก',
			closeOnConfirm:true
		}, function() {
			load_in();

			$.ajax({
				url:HOME + 'accept_zone',
				type:'POST',
				cache:false,
				data: {
					'code' : code
				},
				success:function(rs) {
					load_out();
					if(isJson(rs))
					{
						let ds = JSON.parse(rs);
						if(ds.status === 'success') {
							swal({
								title:'Success',
								type:'success',
								timer:1000
							});

							setTimeout(() => {
								window.location.reload();
							}, 1200);
						}
						else if(ds.status === 'warning') {

							swal({
								title:'Warning',
								text:ds.message,
								type:'warning',
								html:true
							}, () => {
								setTimeout(() => {
									window.location.reload();
								}, 500);
							});
						}
						else {
							swal({
								title:'Error!',
								text: rs,
								type:'error',
								html:true
							});
						}
					}
				}
			})
		})
	}
}


function acceptConfirm() {
	let code = $('#transfer_code').val();
	let note = $.trim($('#accept-note').val());

	if(note.length < 10) {
		$('#accept-error').text('กรุณาระบุหมายเหตุอย่างนี้อย 10 ตัวอักษร');
		return false;
	}
	else {
		$('#accept-error').text('');
	}

	load_in();

	$.ajax({
		url:HOME + 'accept_confirm',
		type:'POST',
		cache:false,
		data:{
			"code" : code,
			"accept_remark" : note
		},
		success:function(rs) {
			load_out();
			if(isJson(rs))
			{
				let ds = JSON.parse(rs);
				if(ds.status === 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(() => {
						window.location.reload();
					}, 1200);
				}
				else if(ds.status === 'warning') {

					swal({
						title:'Warning',
						text:ds.message,
						type:'warning',
						html:true
					}, () => {
						setTimeout(() => {
							window.location.reload();
						}, 500);
					});
				}
				else {
					swal({
						title:'Error!',
						text: rs,
						type:'error',
						html:true
					});
				}
			}
		}
	});
}


function pullBack(code) {
	load_in();

  $.ajax({
    url:HOME + '/pull_back',
    type:'POST',
    cache:'false',
    data:{
      'transfer_code' : code
    },
    success:function(rs) {
			load_out();
      if(rs.trim() == 'success') {
        swal({
					title:'Success',
					type:'success',
					timer:1000
				});

				setTimeout(() => {
					window.location.reload();
				}, 1200);
      }
			else {
      showError(rs);
      }
    },
		error:function(rs) {
			showError(rs);
		}
  });
}
