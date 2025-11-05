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


$('#item-search').autocomplete({
  source:BASE_URL + 'auto_complete/get_product_code_and_name',
  autoFocus:true,
  close:function() {
    let arr = $(this).val().split(' | ');

    if(arr.length == 2) {
      $(this).val(arr[0]);
    }
    else {
      $(this).val('');
    }
  }
})


function reScan() {
  clearFields();
  startScan(getItemByBarcode);
}


function showItemPanel() {
  let is_edit = $('#is-edit').val();

  if(is_edit == 1) {
    $('#add-btn').addClass('hide');
    $('#edit-btn').removeClass('hide');
  }
  else {
    $('#add-btn').removeClass('hide');
    $('#edit-btn').addClass('hide');
  }

  $('#item-panel').removeClass('hide');
}


function getItemByBarcode() {
  let barcode = $('#scan-result').val();
  let warehouse_code = $('#warehouse').val();

  if(barcode.length > 0)
  {
    load_in();

    $.ajax({
      url: HOME + 'get_item_by_barcode',
      type:'POST',
      cache:'false',
      data:{
        'barcode' : barcode,
        'warehouse_code' : warehouse_code
      },
      success:function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status === 'success') {
            let price = parseDefaultFloat(ds.data.price, 0);
            let discLabel = parseDefaultFloat($('#gp').val(), 0) + '%';

            $('#barcode').val(ds.data.barcode);
            $('#item-code').val(ds.data.pdCode);
            $('#item-name').val(ds.data.pdName);
            $('#item-price').val(addCommas(price.toFixed(2)));
            $('#item-disc').val(discLabel);
            $('#stock-qty').val(ds.data.stock);
            $('#count-stock').val(ds.data.count_stock);
            $('#qty').val(1);
            $('#amount').val(addCommas(price.toFixed(2)));
            $('#is-edit').val(0);

            calAmount();

            showItemPanel();
          }
          else {
            showError(ds.message);
          }
        }
        else {
          showError(rs);
          clearFields();
        }
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }
}


function getItemByCode() {
  let code = $('#item-search').val().trim();
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
            closeItemSearch();

            let price = parseDefaultFloat(ds.data.price, 0);
            let discLabel = parseDefaultFloat($('#gp').val(), 0) + '%';

            $('#barcode').val(ds.data.barcode);
            $('#item-code').val(ds.data.pdCode);
            $('#item-name').val(ds.data.pdName);
            $('#item-price').val(addCommas(price.toFixed(2)));
            $('#item-disc').val(discLabel);
            $('#stock-qty').val(ds.data.stock);
            $('#count-stock').val(ds.data.count_stock);
            $('#qty').val(1);
            $('#amount').val(addCommas(price.toFixed(2)));
            $('#is-edit').val(0);

            calAmount();
            showItemPanel();
          }
          else {
            showError(ds.message);
          }
        }
        else {
          showError(rs);
          clearFields();
        }
      },
      error:function(rs) {
        showError(rs);
      }
    });
  }
}


function closeItemPanel() {
  $('#item-panel').addClass('hide');
}


function clearFields() {
  $('#barcode').val('');
  $('#item-code').val('');
  $('#item-name').val('');
  $('#item-price').val('');
  $('#item-disc').val(0);
  $('#stock-qty').val(0);
  $('#count-stock').val(1);
  $('#qty').val(1);
  $('#amount').val(0);
  $('#is-edit').val(0);
  $('#row-id').val('');
  $('#item-panel').addClass('hide');
}


function decrease() {
  let qty = parseDefaultInt(removeCommas($('#qty').val()), 1);
  qty--;

  if(qty < 1) {
    $('#qty').val(1);
  }
  else {
    $('#qty').val(addCommas(qty));
  }

  calAmount();
}


function increase() {
  let qty = parseDefaultInt(removeCommas($('#qty').val()), 1);
  qty++;

  if(qty < 1) {
    $('#qty').val(1);
  }
  else {
    $('#qty').val(addCommas(qty));
  }

  calAmount();
}


function toggleMore() {
  if($('#more-menu').hasClass('run-in')) {
    $('#more-menu').removeClass('run-in');
  }
  else {
    $('#more-menu').addClass('run-in');
  }
}


function showMore() {
  $('#more-menu').addClass('run-in');
}


function closeMore() {
  $('#more-menu').removeClass('run-in');
}


function showItemSearch() {
  $('#item-search-backdrop').removeClass('hide');

  $('#item-search').val('').focus();
}


function closeItemSearch() {
  $('#item-search-backdrop').addClass('hide');
}


$('#qty').change(function() {
  calAmount();
});


$('#item-disc').change(function() {
  calAmount();
});


$('#item-price').change(function() {
  calAmount();
});


function calAmount() {
  let qty = parseDefaultInt(removeCommas($('#qty').val()), 1);
  let price = parseDefaultFloat(removeCommas($('#item-price').val()), 0);
  let disc = parseDiscount($('#item-disc').val(), price);
  let discount = disc.discountAmount * qty;
  let amount = (price * qty) - discount;
  $('#amount').val(addCommas(amount.toFixed(2)));
}


function addDetail() {
  clearErrorByClass('c');

  let h = {
    'code' : $('#code').val(),
    'product_code' : $('#item-code').val().trim(),
    'qty' : parseDefaultFloat(removeCommas($('#qty').val()), 1),
    'price' : parseDefaultFloat(removeCommas($('#item-price').val()), 0),
    'disc' : $('#item-disc').val()
  }

  let stock = parseDefaultInt(removeCommas($('#stock-qty').val()), 0);
  let count_stock = $('#count-stock').val();

  if(h.qty <= 0) {
    $('#qty').hasError();
    return false;
  }

  if(h.qty > stock && count_stock == 1) {
    $('#qty').hasError();
    return false;
  }

  if(h.product_code == '') {
    $('#item-code').hasError();
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

          if($('#qty-'+id).length == 1)
          {
            $('#qty-'+id).text(addCommas(data.qty));
            reCal(id);
            reCalTotal();
            clearFields();
          }
          else
          {
            var source = $('#item-template').html();
            var output = $('#detail-table');
            render_prepend(source, ds.data, output);
            reIndex();
            reCal(id);
            reCalTotal();
            clearFields();
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


function updateDetail() {
  clearErrorByClass('c');

  let id = $('#row-id').val();

  let h = {
    'code' : $('#code').val(),
    'id' :id,
    'qty' : parseDefaultFloat(removeCommas($('#qty').val()), 1),
    'price' : parseDefaultFloat(removeCommas($('#item-price').val()), 0),
    'disc' : $('#item-disc').val()
  }

  let stock = parseDefaultInt(removeCommas($('#stock-qty').val()), 0);
  let count_stock = $('#count-stock').val();

  if(h.qty <= 0) {
    $('#qty').hasError();
    return false;
  }

  if(h.qty > stock && count_stock == 1) {
    $('#qty').hasError();
    return false;
  }

  load_in();

  $.ajax({
    url: HOME + 'update_detail',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() === 'success') {
        let amount = $('#amount-'+id).val();
        $('#price-'+id).val(addCommas(h.price.toFixed(2)));
        $('#qty-'+id).val(addCommas(h.qty));
        $('#disc-'+id).val(h.disc);
        $('#amount-'+id).val(amount);
        reCal(id);
        reCalTotal();
        clearFields();
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


function reCalAll() {
  $('.qty').each(function() {
    let id = $(this).data('id');
    reCal(id);
  });

  reCalTotal();
}


function reCal(id) {
  let price = parseDefaultFloat(removeCommas($('#price-'+id).val()), 0);
  let disc = parseDiscount($('#disc-'+id).val(), price);
  let qty = parseDefaultInt(removeCommas($('#qty-'+id).val()),1);
  let amount = qty * (price - disc.discountAmount);
  $('#amount-'+id).val(addCommas(amount.toFixed(2)));
}


function reCalTotal() {
  updateTotalQty();
  updateTotalAmount();
}


function updateTotalAmount() {
  let total = 0;

  $('.amount').each(function() {
    let amount = parseDefaultFloat(removeCommas($(this).val()), 0);
    total += amount;
  });

  $('#total-amount').val(addCommas(total.toFixed(2)));
}


function updateTotalQty() {
  let total = 0;

  $('.qty').each(function() {
    let qty = parseDefaultFloat(removeCommas($(this).val()), 0);
    total += qty;
  });

  $('#total-qty').val(addCommas(total.toFixed(2)));
}


function removeRow() {
  let id = $('.chk:checked').val();

  if(! id == "" || !id == undefined) {
    closeMore();
    let el = $('#list-'+id);
    let no = $('#no-'+id).text();
    swal({
      title:'Delete',
      text:'ต้องการลบรายการที่เลือกหรือไม่ ?',
      type:'warning',
      showCancelButton:true,
      cancelButtonText:'No',
      confirmButtonText:'Yes',
      confirmButtonColor:'#d15b47',
      closeOnConfirm:true
    }, function() {
      setTimeout(() => {
        deleteRow(id);
      }, 100)
    })
  }
}


function deleteRow(id) {
  let code = $('#code').val();

  $.ajax({
    url: HOME + 'delete_detail',
    type:'POST',
    cache:'false',
    data:{
      'code' : code,
      'id' : id
    },
    success:function(rs) {
      if(rs.trim() == 'success') {
        swal({
          title:'Deleted',
          type:'success',
          timer:1000
        });

        $('#list-block-'+id).remove();
        reIndex();
        reCalTotal();
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


function editRow() {
  let id = $('.chk:checked').val();

  if(! id == "" || !id == undefined) {
    getDetail(id);
  }
}


function getDetail(id) {
  load_in();

  $.ajax({
    url:HOME + 'get_detail/'+id,
    type:'POST',
    cache:false,
    data:{
      'id' : id,
      'warehouse_code' : $('#warehouse').val()
    },
    success:function(rs) {
      load_out();

      if(isJson(rs)) {
        let ds = JSON.parse(rs);

        if(ds.status === 'success') {
          let price = parseDefaultFloat(ds.data.price, 0);
          let amount = parseDefaultFloat(ds.data.amount,0);
          $('#barcode').val(ds.data.barcode);
          $('#item-code').val(ds.data.product_code);
          $('#item-name').val(ds.data.product_name);
          $('#item-price').val(addCommas(price.toFixed(2)));
          $('#item-disc').val(ds.data.discount);
          $('#stock-qty').val(addCommas(ds.data.stock));
          $('#count-stock').val(ds.data.count_stock);
          $('#qty').val(addCommas(ds.data.qty));
          $('#amount').val(addCommas(amount.toFixed(2)));
          $('#is-edit').val(1);
          $('#row-id').val(ds.data.id);

          calAmount();

          showItemPanel();
        }
        else {
          showError(ds.message);
        }
      }
      else {
        showError();
      }
    },
    error:function(rs) {
      showError(rs);
    }
  })
}


function confirmSave() {
  closeMore();

  swal({
    title: "บันทึก",
    text: "ต้องการบันทึกเอกสารหรือไม่ ?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#8CC152",
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    closeOnConfirm: true
  }, function(){
    setTimeout(() => {
      save();
    }, 100);
  });
}


function save() {
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
      'code' : code
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


$('#date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#posting-date').datepicker({
  dateFormat:'dd-mm-yy'
});


$('#warehouse').select2();


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


function toggleActive(id) {
  let chk = $('#list-'+id).is(':checked');
  $('.chk').prop('checked', false);
  $('.list-block').removeClass('active');

  if( ! chk) {
    $('#list-'+id).prop('checked', true);
    $('#list-block-'+id).addClass('active');
  }
}
