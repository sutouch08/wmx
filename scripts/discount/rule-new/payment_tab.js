function savePayment() {
  let h = {
    'id' : $('#id_rule').val(),
    'all' : $('#all_payment').val(),
    'paymentList' : []
  }

  let countPayment = $('.chk-payment:checked').length;

  if(h.all == 'N' && countPayment == 0){
    swal('Warning', 'กรุณาระบุช่องทางการชำระเงินอย่างน้อย 1 รายการ', 'warning');
    return false;
  }

  if(h.all == 'N') {
    $('.chk-payment:checked').each(function() {
      h.paymentList.push($(this).val())
    })
  }

  load_in();

  $.ajax({
    url: HOME + 'set_payment_rule',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success') {
        swal({
          title:'Saved',
          type:'success',
          timer:1000
        });

				setTimeout(function() {
					window.location.reload();
				}, 1200)
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


function togglePayment(option){
  if(option == '' || option == undefined){
    option = $('#all_payment').val();
  }

  $('#all_payment').val(option);

  if(option == 'Y'){
    $('#btn-all-payment').addClass('btn-primary');
    $('#btn-select-payment').removeClass('btn-primary');
    $('#btn-show-payment').attr('disabled', 'disabled');
    return;
  }

  if(option == 'N'){
    $('#btn-all-payment').removeClass('btn-primary');
    $('#btn-select-payment').addClass('btn-primary');
    $('#btn-show-payment').removeAttr('disabled');
  }

}


$('.chk-payment').change(function(event) {
  count = 0;
  $('.chk-payment').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-payment').text(count);
});


function showSelectPayment(){
  $('#payment-modal').modal('show');
}

$(document).ready(function() {
  togglePayment();
});
