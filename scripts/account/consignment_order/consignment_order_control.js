window.addEventListener('load', () => {
  inputInit();
})

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
    if( rs == 'no item found') {
      $(this).val('');
    }
    else {
      setTimeout(() => {
        getItemByCode();
      }, 100)
    }
  }
});


function getItemByCode() {
  let code = $('#item-code').val().trim();
  var zone_code = $('#zone_code').val();

  if(code.length > 0) {
    $.ajax({
      url: HOME + 'get_item_by_code',
      type:'GET',
      cache:'false',
      data:{
        'code' : code,
        'zone_code' : zone_code
      },
      success:function(rs) {
        if( isJson(rs) ) {
          let ds = JSON.parse(rs);

          $('#product_code').val(ds.pdCode);
          $('#barcode-item').val(ds.barcode);
          $('#item-price').val(ds.price);
          $('#item-disc').val(ds.disc);
          $('#stock-qty').text(ds.stock);
          $('#count_stock').val(ds.count_stock);
          $('#item-price').focus().select();
        }
        else {
          showError(rs);
          clearFields();
        }
      }
    });
  }
}


function getItemByBarcode() {
  let barcode = $('#barcode-item').val().trim();
  let zone_code = $('#zone_code').val();

  if(barcode.length > 0)
  {
    $.ajax({
      url: HOME + 'get_item_by_barcode',
      type:'GET',
      cache:'false',
      data:{
        'barcode' : barcode,
        'zone_code' : zone_code
      },
      success:function(rs) {
        if(isJson(rs)) {
          let ds = JSON.parse(rs);
          $('#product_code').val(ds.pdCode);
          $('#item-code').val(ds.product);
          $('#item-price').val(ds.price);
          $('#item-disc').val(ds.disc);
          $('#stock-qty').text(ds.stock);
          $('#count_stock').val(ds.count_stock);
          $('#item-price').focus().select();
        }
        else {
          showError(rs);
          clearFields();
        }
      }
    });
  }
}


$('#item-price').keydown(function(e) {
  if(e.keyCode == 32) {
    e.preventDefault();
    $('#item-qty').focus();
  }
});


$('#item-price').keyup(function(e) {
  if(e.keyCode == 13) {
    setTimeout(() => {
      $('#item-disc').focus().select();
    },50);
  }
});


$('#item-price').focusout(function() {
  let amount = parseDefaultFloat($(this).val(), 0);
  if(amount <= 0) {
    $(this).val(0);
    $('#item-disc').val(0);
  }
});


$('#item-disc').keyup(function(e) {
  if(e.keyCode == 13) {
    setTimeout(() => {
      $('#item-qty').focus().select();
    },50);
  }
});


$('#item-disc').keydown(function(e) {
  if(e.keyCode == 32) {
    e.preventDefault();
    let val = $(this).val().trim();

    if(val.length > 0) {
      let lastChar = val.slice(-1);

      if( ! isNaN(lastChar)) {
        val = val + '%'
        $(this).val(val);
      }
    }
  }
})


$('#item-disc').focusout(function() {
  calAmount();
});


$('#item-qty').keyup(function(e) {
  if(e.keyCode == 13) {
    let qty = parseDefaultFloat($(this).val(), 1);

    if(qty > 0) {
      addToDetail();
    }
  }

  calAmount();
});


function calAmount() {
  let qty = parseDefaultFloat($('#item-qty').val(), 0);
  let price = parseDefaultFloat($('#item-price').val(), 0);
  let disc = parseDiscount($('#item-disc').val(), price);
  let amount = qty * (price - disc.discountAmount);

  $('#item-amount').val(addCommas(amount.toFixed(2)));
}


function addToDetail() {
  clearErrorByClass('c');

  let h = {
    'code' : $('#consign_code').val(),
    'product_code' : $('#product_code').val().trim(),
    'qty' : parseDefaultFloat($('#item-qty').val(), 1),
    'price' : parseDefaultFloat($('#item-price').val(), 0),
    'disc' : $('#item-disc').val().trim(),
    'auz' : $('#auz').val(),
    'count_stock' : $('#count_stock').val()
  }

  let stock = parseDefaultFloat($('#stock-qty').val(), 0);

  if(h.product_code.length == 0) {
    beep();
    $('#item-code').hasError();
    return false;
  }

  if(h.qty < 0) {
    beep();
    $('#item-qty').hasError();
    return false;
  }

  if(h.qty > stock && h.auz == 0 && h.count_stock == 1) {
    beep();
    $('#stock-qty').hasError();
    $('#item-qty').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'add_detail',
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
          let id = ds.data.id;

          if($('#row-'+id).length) {
            $('#qty-'+id).val(ds.data.qty);

            reCal(id);
            clearFields();
          }
          else {
            let source = $('#new-row-template').html();
            let output = $('#detail-table');

            render_prepend(source, ds.data, output);
            inputInit();
            reIndex();
            reCalAll();
            clearFields();
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
    }
  })
}


function clearFields(){
  $('#barcode-item').val('');
  $('#item-code').val('');
  $('#item-price').val('');
  $('#item-disc').val('');
  $('#stock-qty').text(0);
  $('#item-qty').val('');
  $('#item-amount').text('');
  $('#product_code').val('');
  $('#barcode-item').focus();
}


function reCal(id) {
  let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
  let disc = parseDiscount($('#disc-'+id).val(), price);
  let qty = parseDefaultFloat(removeCommas($('#qty-'+id).val()), 0);
  let amount = qty * (price - disc.discountAmount);

  $('#amount-'+id).val(addCommas(amount.toFixed(2)));

  updateTotalQty();
  updateTotalAmount();
}


function reCalAll() {
  let totalAmount = 0;
  let totalQty = 0;

  $('.input-price').each(function() {
    let id = $(this).data('id');
    let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
    let disc = parseDiscount($('#disc-'+id).val(), price);
    let qty = parseDefaultFloat(removeCommas($('#qty-'+id).val()), 0);
    let amount = qty * (price - disc.discountAmount);
    $('#amount-'+id).val(addCommas(amount.toFixed(2)));

    totalAmount += amount;
    totalQty += qty;
  });

  $('#total-amount').text(addCommas(totalAmount.toFixed(2)));
  $('#total-qty').text(addCommas(totalQty.toFixed(2)));
}


function updateTotalAmount() {
  let total = 0;

  $('.amount').each(function() {
    let amount = parseDefaultFloat(removeCommas($(this).val()), 0);
    total += amount;
    console.log(amount);
  });

  $('#total-amount').text(addCommas(total.toFixed(2)));
}


function updateTotalQty() {
  let total = 0;

  $('.input-qty').each(function() {
    let qty = parseDefaultFloat(removeCommas($(this).val()), 0);
    total += qty;
  });

  $('#total-qty').text(addCommas(total.toFixed(2)));
}


function moveCursorToEnd(el) {
  len = el.val().length;
  el[0].setSelectionRange(len, len);
}


function inputInit() {
  $('.input-price').click(function() {
    moveCursorToEnd($(this));
  })

  $('.input-price').focusin(function() {
    let val = $(this).val();
    $(this).val(removeCommas(val));
    moveCursorToEnd($(this));
  });

  $('.input-price').focusout(function() {
    let val = parseDefaultFloat($(this).val(), 0);
    $(this).val(addCommas(val.toFixed(2)));
  })

  $('.input-price').change(function() {
    $(this).clearError();

    let id = $(this).data('id');
    updateRow(id);
  })

  $('.input-price').keyup(function(e) {
    if(e.keyCode == 13) {
      let id = $(this).data('id');
      $('#disc-'+id).focus();
    }
  })

  $('.input-disc').click(function() {
    moveCursorToEnd($(this));
  })

  $('.input-disc').focusin(function() {
    moveCursorToEnd($(this));
  })

  $('.input-disc').change(function() {
    $(this).clearError();
    let id = $(this).data('id');
    updateRow(id);

    // let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
    // let disc = parseDiscount($(this).val(), price);
    //
    // if(disc.discountAmount < 0 || disc.discountAmount > price ) {
    //   $(this).hasError();
    // }
    //
    // reCal(id);
  })

  $('.input-disc').keyup(function(e) {
    if(e.keyCode == 13) {
      let id = parseDefaultInt($(this).data('id'), 1);
      $('#qty-'+id).focus();
    }
  })

  $('.input-disc').keydown(function(e) {
    if(e.keyCode == 32) {
      e.preventDefault();
      let val = $(this).val().trim();

      if(val.length > 0) {
        let lastChar = val.slice(-1);

        if( ! isNaN(lastChar)) {
          val = val + '%'
          $(this).val(val);
        }
      }
    }
  })

  $('.input-qty').click(function() {
    moveCursorToEnd($(this));
  })

  $('.input-qty').focusin(function() {
    let val = $(this).val();
    $(this).val(removeCommas(val));
    moveCursorToEnd($(this));
  })

  $('.input-qty').focusout(function() {
    let val = $(this).val();
    $(this).val(addCommas(val));
  })

  $('.input-qty').change(function() {
    $(this).clearError();

    let id = $(this).data('id');
    updateRow(id);
    // let qty = parseDefaultFloat(removeCommas($(this).val(), 0));
    //
    // if(qty <= 0) {
    //   $(this).hasError();
    // }
    //
    // reCal(id);
  })

  $('.input-qty').keyup(function(e) {
    if(e.keyCode == 13) {
      let id = parseDefaultInt($(this).data('id'), 1);
      id++;
      $('#price-'+id).focus();
    }
  })

}


function checkAll() {
  if($('#chk-all').is(':checked')) {
    $('.chk').prop('checked', true);
  }
  else {
    $('.chk').prop('checked', false);
  }
}


function removeChecked() {
  let count = $('.chk:checked').length;

  if(count) {
    swal({
      title:'Are you sure ?',
      text:'ต้องการลบ ' + count + ' รายการที่เลือกหรือไม่ ?',
      type:'warning',
      showCancelButton:true,
      confirmButtonText:'Yes',
      cancelButtonText:'No',
      confirmButtonColor:'#FA5858',
      closeOnConfirm:true
    }, function() {
      load_in();

      setTimeout(() => {
        let code = $('#consign_code').val();
        let ids = [];

        $('.chk:checked').each(function() {
          ids.push( $(this).val());
        })

        if(ids.length > 0) {
          $.ajax({
            url:HOME + 'delete_details',
            type:'POST',
            cache:false,
            data:{
              'code' : code,
              'ids' : ids
            },
            success:function(rs) {
              load_out();

              if(rs.trim() === 'success') {
                swal({
                  title:'Deleted',
                  type:'success',
                  timer:1000
                });

                $('.chk:checked').each(function() {
                  let id = $(this).val();

                  $('#row-'+id).remove();
                });

                reIndex();
                reCalAll();
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
        }
      }, 200);
    })
  }
}


function updateRow(id) {
  let error = 0;

  let code = $('#consign_code').val();
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

  if(error > 0) {
    return false;
  }

  let h = {
    'id' : id,
    'code' : code,
    'price' : price,
    'qty' : qty,
    'discount' : discount,
    'discount_amount' : disc.discountAmount,
    'amount' : amount
  };

  $.ajax({
    url:HOME + 'update_detail',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      if(rs.trim() === 'success') {
        reCal(id);
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


  updateTotalQty();
  updateTotalAmount();
}
