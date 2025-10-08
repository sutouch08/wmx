$('#fromDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#toDate').datepicker('option', 'minDate', sd);
  }
});


$('#toDate').datepicker({
  dateFormat:'dd-mm-yy',
  onClose:function(sd) {
    $('#fromDate').datepicker('option', 'maxDate', sd);
  }
});


function prepareList() {
  window.location.href = HOME;
}


function processList() {
  window.location.href = HOME + 'view_process';
}


function goPrepare(code) {
  load_in();
  window.location.href = HOME + 'process/'+code;
}


function toggleOrderScanBox() {
  let sb = $('#order-scan-box');

  if(sb.hasClass('slide-in')) {
    sb.removeClass('slide-in');
  }
  else {
    sb.addClass('slide-in');
    setTimeout(() => {
      $('#barcode-order').focus();
    }, 200);
  }
}


$('#barcode-order').keyup(function(e) {
  if(e.keyCode === 13) {
    let code = $(this).val().trim();
    if(code.length) {
      goPrepare(code);
    }
  }
})


function resetFilter(page) {
  $.ajax({
    url:HOME + 'clear_filter',
    type:'GET',
    cache:false,
    success:function() {
      if(page == 'processList') {
        processList();
      }

      if(page == 'prepareList') {
        prepareList();
      }
    }
  })
}
