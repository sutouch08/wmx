var click = 0;

$('#date-add').datepicker({
  dateFormat:'dd-mm-yy'
});


$("#customer-code").autocomplete({
	source: BASE_URL + 'auto_complete/get_sponsor',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customer-code").val(code);
			$("#customer-name").val(name);
      getBudget(code);
		}
    else {
			$("#customer-code").val('');
			$('#customer-name').val('');
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
		}
	}
});


$("#customer-name").autocomplete({
	source: BASE_URL + 'auto_complete/get_sponsor',
	autoFocus: true,
	close: function(){
		var rs = $.trim($(this).val());
		var arr = rs.split(' | ');
		if( arr.length == 2 ){
			var code = arr[0];
			var name = arr[1];
			$("#customer-code").val(code);
			$("#customer-name").val(name);
      getBudget(code);
		}
    else {
			$("#customer-code").val('');
			$("#customer-name").val('');
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
		}
	}
});


function getBudget(code) {
  load_in();

  $.ajax({
    url:HOME + 'get_budget',
    type:'GET',
    cache:false,
    data:{
      'code' : code
    },
    success:function(rs){
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        $('#budget-amount').val(ds.amount_label);
        $('#budget-amount').data('amount', ds.amount);
        $('#budget-id').val(ds.budget_id);
        $('#budget-code').val(ds.budget_code);
      }
      else {
        $('#budget-amount').val(0.00);
        $('#budget-amount').data('amount', 0);
        $('#budget-id').val('');
        $('#budget-code').val('');
      }
    },
    error:function(rs) {
      load_out();
      $('#budget-amount').val(0.00);
      $('#budget-amount').data('amount', 0);
      $('#budget-id').val('');
      $('#budget-code').val('');
    }
  });
}


$('#customer-name').focusout(function(){
  var code = $(this).val();

  if(code.length == 0)
  {
    $('#customer-code').val('');
    $('#budget-amount').val(0.00);
    $('#budget-amount').data('amount', 0);
    $('#budget-id').val('');
    $('#budget-code').val('');
  }
});


$('#customer-code').focusout(function(){
  var code = $(this).val();

  if(code.length == 0)
  {
    $('#customer-name').val('');
    $('#budget-amount').val(0.00);
    $('#budget-amount').data('amount', 0);
    $('#budget-id').val('');
    $('#budget-code').val('');
  }
});


function add() {
  if(click == 0) {
    click = 1;
    clearErrorByClass('e');

    let h = {
      'customer_code' : $('#customer-code').val(),
      'customer_name' : $('#customer-name').val(),
      'date_add' : $('#date-add').val(),
      'customer_ref' : $('#customer-ref').val(),
      'warehouse_code' : $('#warehouse').val(),
      'budget_id' : $('#budget-id').val(),
      'budget_code' : $('#budget-code').val(),
      'budget_amount' : parseDefault(parseFloat($('#budget-amount').data('amount')), 0),
      'remark' : $('#remark').val().trim()
    };

    if(h.customer_code.length == 0) {
      $('#customer-code').hasError();
      click = 0;
      return false;
    }

    if(h.customer_name.length == 0) {
      $('#customer-name').hasError();
      click = 0;
      return false;
    }

    if( ! isDate(h.date_add))
    {
      $('#date-add').hasError();
      click = 0;
      return false;
    }

    if(h.budget_id == 0 || h.budget_id == null || h.budget_id == "") {
      $('#budget-amount').hasError();
      click = 0;
      return false;
    }

    if(h.budget_amount <= 0) {
      $('#budget-amount').hasError();
      click = 0;
      return false;
    }

    if(h.customer_ref.length == 0)
    {
      $('#customer-ref').hasError();
      click = 0;
      return false;
    }

    if(h.warehouse_code == "") {
      $('#warehouse').hasError();
      swal('กรุณาเลือกคลัง');
      click = 0;
      return false;
    }

    load_in();

    $.ajax({
      url:BASE_URL + 'orders/sponsor/add',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            edit(ds.code);            
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
      }
    });
  }

}


function getProductGrid(){
	let pdCode 	= $("#pd-box").val();
	let whCode = $('#warehouse').val();

	if( pdCode.length > 0  ) {
		load_in();

		$.ajax({
			url: BASE_URL + 'orders/orders/get_order_grid',
			type:"GET",
			cache:"false",
			data:{
				"model_code" : pdCode,
				"warehouse_code" : whCode,
				"isView" : 0
			},
			success: function(rs){
				load_out();
				if(isJson(rs)) {
					let ds = $.parseJSON(rs);
					$('#modal').css('width', ds.tableWidth + 'px');
					$('#modalTitle').html(ds.modelCode + ' | ' + ds.modelName);
					$('#modalBody').html(ds.table);
					$('#orderGrid').modal('show');
				}
				else {
					swal(rs);
				}
			}
		});
	}
}


function getItemGrid(){
	let itemCode 	= $("#item-code").val();
	let whCode = $('#warehouse').val();

	if( itemCode.length > 0  ){
		$.ajax({
			url:BASE_URL + 'orders/orders/get_item_grid',
			type:'GET',
			cache:false,
			data:{
				'warehouse_code' : whCode,
				'itemCode' : itemCode,
				'isView' : 0
			},
			success:function(rs){
				var rs = rs.split(' | ');

				if(rs[0] === 'success') {
					$('#stock-qty').val(rs[2]);
					$('#input-qty').val('').focus();
				}
        else {
					$('#stock-qty').val('');
					$('#input-qty').val('');
					beep();
          showError(rs[0]);
				}
			}
		})
	}
}


function addToOrder() {
  clearErrorByClass('order-grid');

  let err = 0;
  let code = $('#order_code').val();
  let h = {
    'code' : code,
    'items' : []
  };

  $(".order-grid").each(function() {
    let el = $(this);
    if(el.val() != '') {
      let qty = parseDefault(parseFloat(el.val()), 0);
      let limit = parseDefault(parseFloat(el.data('limit')), 0);

      if(qty < 0 || qty > limit) {
        el.hasError();
        err++;
      }

      if(qty > 0){
        h.items.push({
          'sku' : el.data('sku'),
          'qty' :  qty
        });
      }
    }
  });

  if(err > 0) {
    return false;
  }

  if(h.items.length > 0) {
    $('#orderGrid').modal('hide');

    load_in();

    $.ajax({
      url:HOME + 'add_details',
      type:'POST',
      cache:false,
      data:{
        'data' : JSON.stringify(h)
      },
      success:function(rs) {
        load_out();

        if(rs.trim() === 'success') {
          updateDetailTable();
        }
        else {
          showError(rs);
        }
      },
      error:function(rs) {
        beep();
        showError(rs);
      }
    })
  }
}


//---- เพิ่มรายการสินค้าเช้าออเดอร์
function addItemToOrder(){
  console.log('add');
  $('#item-code').clearError();
  $('#input-qty').clearError();

	let orderCode = $('#order_code').val();
  let code = $('#order_code').val();

  let h = {
    'code' : code,
    'items' : []
  };

	let qty = parseDefault(parseInt($('#input-qty').val()), 0);
	let limit = parseDefault(parseInt($('#stock-qty').val()), 0);
	let itemCode = $('#item-code').val();

  if(itemCode.length == 0) {
    $('#item-code').hasError();
    return false;
  }

  if(qty <= 0 || qty > limit)
  {
    $('#input-qty').hasError();
    return false;
  }

  h.items.push({'sku' : itemCode, 'qty' : qty});

  $.ajax({
    url:HOME + 'add_details',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        updateDetailTable();

        setTimeout(function() {
          $('#item-code').val('');
          $('#stock-qty').val('');
          $('#input-qty').val('');
          $('#item-code').focus();
        },1200);
      }
      else {
        showError(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}

//---- for update price on noncount item
function updateItemPrice(id) {
  let code = $('#order_code').val();
  let price = parseDefault(parseFloat($('#price-'+id).val()), 0.00);
  let currentPrice = parseDefault(parseFloat($('#price-'+id).data('price')), 0);
  price = price < 0 ? price * -1 : price;

  recalItem(id);

  load_in();

  $.ajax({
    url:HOME + 'update_item_price',
    type:'POST',
    cache:false,
    data:{
      'order_code' : code,
      'id' : id,
      'price' : price
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        //--- update current
        $('#price-'+id).data('price', price);
      }
      else {
        showError(rs);
        //--- roll back data
        $('#price-'+id).val(currentPrice);
        recalItem(id);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
      $('#price-'+id).val(currentPrice);
      recalItem(id);
    }
  })
}


function updateItem(id) {
  clearErrorByClass('e');

  let h = {
    'id' : id,
    'code' : $('#order_code').val(),
    'qty' : parseDefault(parseFloat($('#qty-'+id).val()), 0),
    'price' : parseDefault(parseFloat($('#price-'+id).val()), 0.00)
  }

  let currentQty = parseDefault(parseFloat($('#qty-'+id).data('qty')), 0);
	let currentPrice = parseDefault(parseFloat($('#price-'+id).data('price')), 0);

  if(h.qty <= 0) {
    $('#qty-'+id).hasError();
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'update_item',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        $('#price-'+id).data('price', h.price);
        $('#qty-'+id).data('qty', h.qty);
      }
      else {
        showError(rs);
        $('#price-'+id).val(currentPrice);
        $('#qty-'+id).val(currentQty);
      }

      recalItem(id);
    },
    error:function(rs) {
      showError(rs);
      $('#qty-'+id).val(currentQty);
      recalItem(id);
    }
  })
}


function recalItem(id) {
	let price = parseDefault(parseFloat($('#price-'+id).val()), 0);
  let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);

  if(price < 0) {
    price = price * (-1);
    $('#price-'+id).val(price.toFixed(2));
  }

	let lineTotal = price * qty;
	$('#line-total-'+id).val(addCommas(lineTotal.toFixed(2)));
	recalTotal();
}


function recalTotal() {
	let totalQty = 0;
  let totalAmount = 0;

	$('.line-total').each(function() {
		let id = $(this).data('id');
		let price = parseDefault(parseFloat($('#price-'+id).val()), 0);
		let qty = parseDefault(parseFloat($('#qty-'+id).val()), 0);
	  let amount = qty * price;

    totalQty += qty;
    totalAmount += amount;
	});

	$('#total-qty').val(addCommas(totalQty.toFixed(2)));
	$('#total-amount').val(addCommas(totalAmount.toFixed(2)));
}


function updateDetailTable(){
	var order_code = $("#order_code").val();

	$.ajax({
		url: HOME + 'get_detail_table/'+order_code,
		type:"GET",
    cache:"false",
		success: function(rs) {
			if( isJson(rs) ){
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let source = $("#details-template").html();
  				let output = $("#detail-table");
  				render(source, ds.data, output);

          recalTotal();
          reIndex();
        }
        else {
          showError(ds.message);
        }
			}
		},
    error:function(rs) {
      showError(rs);
    }
	});
}


function removeDetail(id, name) {
  let code = $('#order_code').val();

  swal({
    title: "คุณแน่ใจ ?",
    text: "ต้องการลบ '" + name + "' หรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: 'ใช่, ฉันต้องการลบ',
    cancelButtonText: 'ยกเลิก',
    closeOnConfirm: true
  }, function() {
    $.ajax({
      url: HOME + 'remove_detail',
      type:"POST",
      cache:"false",
      data:{
        'code' : code,
        'id' : id
      },
      success: function(rs) {
        if(rs.trim() === 'success') {
          $('#row-'+id).remove();
          recalTotal();
          reIndex();
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


$("#pd-box").autocomplete({
	source: BASE_URL + 'auto_complete/get_model_code_and_name',
	autoFocus: true,
  close:function(){
    var rs = $(this).val();
    var arr = rs.split(' | ');
    $(this).val(arr[0]);
  }
});


$('#pd-box').keyup(function(event) {
	if(event.keyCode == 13){
		var code = $(this).val();
		if(code.length > 0){
			setTimeout(function(){
				getProductGrid();
			}, 300);
		}
	}
});


$('#item-code').autocomplete({
	source:BASE_URL + 'auto_complete/get_product_code',
	minLength: 4,
	autoFocus:true
});


$('#item-code').keyup(function(e){
	if(e.keyCode == 13){
		var code = $(this).val();
		if(code.length > 4){
			setTimeout(function(){
				getItemGrid();
			}, 200);
		}
	}
});


$('#input-qty').keyup(function(e){
	if(e.keyCode == 13){
		addItemToOrder();
	}
});


function getEdit() {
  $('.h').removeAttr('disabled');

  $('#btn-edit').addClass('hide');
  $('#btn-update').removeClass('hide');
}


function updateOrder() {
  let h = {
    'code' : $('#order_code').val(),
    'customer_code' : $('#customerCode').val(),
    'customer_name' : $('#customer').val(),
    'date_add' : $('#date').val(),
    'empName' : $('#user_ref').val(),
    'warehouse_code' : $('#warehouse').val(),
    'transformed' : $('#transformed').val(),
    'remark' : $('#remark').val()
  };


  if(h.customer_code.length == 0 || h.customer_name.length == 0) {
    swal('ชื่อผู้รับไม่ถูกต้อง');
    return false;
  }

  if( ! isDate(h.date_add))
  {
    swal('วันที่ไม่ถูกต้อง');
    return false;
  }

  if(h.empName.length == 0)
  {
    swal('ชื่อผู้เบิกไม่ถูกต้อง');
    return false;
  }

  if(h.warehouse_code == ""){
    swal('กรุณาเลือกคลัง');
    return false;
  }

	load_in();

	$.ajax({
		url:BASE_URL + 'orders/sponsor/update_order',
		type:"POST",
		cache:"false",
		data:{
      "data" : JSON.stringify(h)
    },
		success: function(rs) {
			load_out();

			if( rs == 'success' ){
				swal({
          title: 'Done !',
          type: 'success',
          timer: 1000
        });

				setTimeout(function(){
          window.location.reload();
        }, 1200);

			}else{
				swal({
          title: "Error!",
          text: rs,
          type: 'error'
        });
			}
		}
	});
}


function setSender() {
  let code = $('#order_code').val();
  let id_sender = $('#sender').val();

  if(id_sender == "") {
    swal("กรุณาเลือกผู้จัดส่ง");
    return false;
  }

  load_in();

  $.ajax({
    url:HOME + 'set_sender',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'id_sender' : id_sender
    },
    success:function(rs) {
      load_out();
      if(rs.trim() != 'success') {
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


function setAddress(id) {
  let code = $('#order_code').val();

  load_in();
  $.ajax({
    url:HOME + 'set_address',
    type:'POST',
    cache:false,
    data:{
      'code' : code,
      'id_address' : id
    },
    success:function(rs) {
      load_out();
      if(rs.trim() == 'success') {
        $('.btn-address').removeClass('btn-success');
        $('#btn-'+id).addClass('btn-success');
      }
      else {
        beep();
        swal(rs);
      }
    },
    error:function(rs) {
      beep();
      showError(rs);
    }
  })
}
