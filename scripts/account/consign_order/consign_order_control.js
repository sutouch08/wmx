$('#barcode-item').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      getItemByBarcode();
    }
  }
});


$('#item-code').keyup(function(e) {
  if(e.keyCode == 13){
    getItemByCode();
  }
});


$('#item-code').autocomplete({
  source: BASE_URL + 'auto_complete/get_product_code',
  autoFocus:true,
  close:function(){
    var rs = $(this).val();
    if( rs == 'no item found'){
      $(this).val('');
    }
  }
});


function getItemByCode() {
  let code = $('#item-code').val().trim();
  let warehouse_code = $('#warehouse').val();

  if(code.length > 0)
  {
    load_in();

    $.ajax({
      url: HOME + 'get_item_by_code',
      type:'POST',
      cache:'false',
      data:{
        'product_code' : code,
        'warehouse_code' : warehouse_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let discLabel = parseDefaultFloat($('#gp').val(), 0) + '%';
            $('#item-price').val(ds.data.price);
            $('#item-disc').val(discLabel);
            $('#item-stock').val(ds.data.stock);
            $('#count-stock').val(ds.data.count_stock);

            $('#item-price').focus().select();
          }
          else {
            beep();
            showError(ds.message);
            clearFileds();
          }
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

}


$('#item-price').keydown(function(e) {
  if(e.keyCode == 32){
    e.preventDefault();
    $('#input-qty').focus();
  }
});


$('#item-price').keyup(function(e){
  if(e.keyCode == 13 && $(this).val() != ''){
    $('#item-disc').focus();
    $('#item-disc').select();
  }

  calAmount();
});


$('#item-price').focusout(function(event) {
  var amount = parseFloat($(this).val());
  if(amount <= 0){
    $('#item-disc').val(0);
  }

  if(amount < 0 ){
    $(this).val(0);
  }
});


$('#item-disc').keyup(function(e){
  if(e.keyCode == 13){
    $('#input-qty').focus();
    $('#input-qty').select();
  }

  calAmount();
});


$('#input-qty').keyup(function(e){
  if(e.keyCode == 13) {
    let qty = parseDefaultInt($(this).val(), 0);

    if(qty > 0) {
      addToDetail();
      return;
    }
  }

  calAmount();
});


$('#chk-all').change(function() {
  if($(this).is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
})


function removeChecked() {
  if($('.chk:checked').length) {
    swal({
      title:'Are you sure ?',
      text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
      type:'warning',
      html:true,
      showCancelButton:true,
      confirmButtonColor:'#d15b47',
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      closeOnConfirm:true
    }, function() {
      let h = {
        'code' : $('#code').val(),
        'ids' : []
      };

      $('.chk:checked').each(function() {
        h.ids.push($(this).val());
      });

      setTimeout(() => {
        load_in();

        $.ajax({
          url:HOME + 'remove_rows',
          type:'POST',
          cache:false,
          data: {
            "data" : JSON.stringify(h)
          },
          success:function(rs) {
            load_out();

            if(rs.trim() === 'success') {
              swal({
                title:'Success',
                type:'success',
                timer:1000
              });

              h.ids.forEach((id) => {
                $('#row-'+id).remove();
              });

              reIndex();
              reCalAll();
            }
          },
          error:function(rs) {
            showError(rs);
          }
        })
      }, 100);
    })

  }
}


function calAmount() {
  let qty = parseDefaultInt($('#input-qty').val(), 0);
  let price = parseDefaultFloat(removeCommas($('#item-price').val()), 0);
  let disc = parseDiscount($('#item-disc').val(), price);
  let discount = disc.discountAmount * qty;
  let amount = (price * qty) - discount;
  $('#item-amount').val(addCommas(amount.toFixed(2)));
}


function addToDetail() {
  clearErrorByClass('e');

  let h = {
    'code' : $('#code').val(),
    'product_code' : $('#item-code').val().trim(),
    'qty' : parseDefaultInt($('#input-qty').val(), 0),
    'price' : parseDefaultFloat(removeCommas($('#item-price').val()), 0),
    'disc' : $('#item-disc').val()
  }

  let stock = parseDefaultInt(removeCommas($('#item-stock').val()), 0);
  let count_stock = $('#count-stock').val();

  if(h.qty <= 0) {
    $('#input-qty').hasError();
    return false;
  }

  if(h.qty > stock && count_stock == 1) {
    $('#input-qty').hasError();
    swal('ยอดในโซนไม่พอตัด');
    return false;
  }

  if(h.product_code == '') {
    $('#item-code').hasError();
    swal('สินค้าไม่ถูกต้อง');
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'add_detail',
    type:'POST',
    cache:false,
    data: {
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let data = ds.data;
          let id = data.id;

          if($('#row-'+id).length == 1)
          {
            $('#qty-'+id).val(addCommas(data.qty));
          }
          else
          {
            var source = $('#new-row-template').html();
            var output = $('#detail-table');
            render_prepend(source, data, output);
          }

          reIndex();
          reCalAll();
          clearFields();
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


function updateRowPrice(id) {
  let el = $('#price-'+id);
  el.clearError();
  let prev = el.data('prev');
  let price = parseDefaultFloat(removeCommas(el.val()), 0);

  if(price < 0) {
    el.hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update_row_price',
    type:'POST',
    cache:false,
    data:{
      'price' : price,
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() == 'success') {
        el.data('prev', addCommas(price));
        reCal(id);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function updateRowDisc(id) {
  let el = $('#disc-'+id);
  el.clearError();
  let prev = el.data('prev');
  let discLabel = el.val();
  let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
  let disc = parseDiscount(discLabel, price);

  if(disc.discountAmount > price) {
    el.hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update_row_disc',
    type:'POST',
    cache:false,
    data:{
      'discount' : discLabel,
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() == 'success') {
        el.data('prev', discLabel);
        reCal(id);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function updateRowQty(id) {
  let el = $('#qty-'+id);
  el.clearError();
  let prev = el.data('prev');
  let qty = parseDefaultFloat(removeCommas(el.val()), 0);

  if(qty <= 0) {
    el.hasError();
    return false;
  }

  $.ajax({
    url:HOME + 'update_row_qty',
    type:'POST',
    cache:false,
    data:{
      'qty' : qty,
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() == 'success') {
        el.data('prev', addCommas(qty));
        reCal(id);
      }
      else {
        swal({
          title:'Error!',
          text:rs,
          type:'error'
        });
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function clearFields() {
  $('#item-code').val('');
  $('#item-price').val('');
  $('#item-disc').val('');
  $('#item-stock').val('');
  $('#input-qty').val('');
  $('#item-amount').val('');
  $('#item-code').focus();
}


function reCal(id) {
  let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
  let disc = parseDiscount($('#disc-'+id).val(), price);
  let qty = parseDefaultInt(removeCommas($('#qty-'+id).val()),1);
  let amount = qty * (price - disc.discountAmount);
  $('#amount-'+id).val(addCommas(amount.toFixed(2)));
  updateTotalQty();
  updateTotalAmount();
}


function reCalAll() {
  $('.rox').each(function() {
    let id = $(this).data('id');
    reCal(id);
  });

  updateTotalQty();
  updateTotalAmount();
}


function updateTotalAmount(){
  let total = 0;
  $('.amount').each(function() {
    let amount = parseDefault(parseFloat(removeCommas($(this).val())), 0);
    total += amount;
  });

  total = parseFloat(total).toFixed(2);
  $('#total-amount').text(addCommas(total));
}


function updateTotalQty(){
  var total = 0;
  $('.qty').each(function(index, el) {
    let qty = parseInt(removeCommas($(this).val()));
    total += qty;
  });

  $('#total-qty').text(addCommas(total));
}
