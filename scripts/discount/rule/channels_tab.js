function saveChannels() {
  let h = {
    'id' : $('#id_rule').val(),
    'all' : $('#all_channels').val(),
    'channelsList' : []
  }

  countChannels = $('.chk-channels:checked').length;

  if(h.all == 'N' && countChannels == 0) {
    swal('Warning', 'กรุณาระบุช่องทางการขายอย่างน้อย 1 รายการ', 'warning');
    return false;
  }

  if(h.all == 'N') {
    $('.chk-channels:checked').each(function() {
      h.channelsList.push($(this).val());
    })
  }

  load_in();

  $.ajax({
    url: HOME + 'set_channels_rule',
    type:'POST',
    cache:false,
    data: {
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


function toggleChannels(option){
  if(option == '' || option == undefined){
    option = $('#all_channels').val();
  }

  $('#all_channels').val(option);

  if(option == 'Y'){
    $('#btn-all-channels').addClass('btn-primary');
    $('#btn-select-channels').removeClass('btn-primary');
    $('#btn-show-channels').attr('disabled', 'disabled');
    return;
  }

  if(option == 'N'){
    $('#btn-all-channels').removeClass('btn-primary');
    $('#btn-select-channels').addClass('btn-primary');
    $('#btn-show-channels').removeAttr('disabled');
  }
}


$('.chk-channels').change(function(event) {
  count = 0;
  $('.chk-channels').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-channels').text(count);
});




function showSelectChannels(){
  $('#channels-modal').modal('show');
}




$(document).ready(function() {
  toggleChannels();
});
