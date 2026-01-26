function saveCustomer() {
  let h = {
    'id' : $('#id_rule').val(),
    'all' : $('#all_customer').val(),
    'customer' : $('#customer_id').val(),
    'group' : $('#customer_group').val(),
    'type' : $('#customer_type').val(),
    'kind' : $('#customer_kind').val(),
    'area' : $('#customer_area').val(),
    'grade' : $('#customer_grade').val(),
    'customerList' : [],
    'groupList' : [],
    'typeList' : [],
    'kindList' : [],
    'areaList' : [],
    'gradeList' : []
  }

  if(h.all == 'N') {

    if(h.customer == 'Y' && $('.customer-chk').length == 0) {
      swal('กรุณาระบุลูกค้าอย่างน้อย 1 ราย');
      return false;
    }

    if(h.customer == 'N') {
      count_group = $('.chk-group:checked').length;
      count_type  = $('.chk-type:checked').length;
      cound_kind  = $('.chk-kind:checked').length;
      count_area  = $('.chk-area:checked').length;
      cound_grade = $('.chk-grade:checked').length;
      sum_count = count_group + count_type + cound_kind + count_area + cound_grade;

      if(sum_count == 0) {
        swal("Warning", "กรุณาระบุอย่างน้อย 1 เงื่อนไข", "warning");
        return false;
      }

      if(h.group == 'Y' && count_group == 0 ) {
        swal('Warning', 'กรุณาเลือกกลุ่มลูกค้าอย่างน้อย 1 รายการ', 'warning');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นชนิดแล้วไม่ได้เลือก
      if(h.type == 'Y' && count_type == 0 ) {
        swal('Warning', 'กรุณาเลือกชนิดลูกค้าอย่างน้อย 1 รายการ', 'warning');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นประเภทแล้วไม่ได้เลือก
      if(customer_kind == 'Y' && cound_kind == 0 ) {
        swal('Warning', 'กรุณาเลือกประเภทลูกค้าอย่างน้อย 1 รายการ', 'warning');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นเขตแล้วไม่ได้เลือก
      if(h.area == 'Y' && count_area == 0 ) {
        swal('Warning', 'กรุณาเลือกเขตลูกค้าอย่างน้อย 1 รายการ', 'warning');
        return false;
      }

      //---- กรณีลือกลูกค้าแบบเป็นเกรดแล้วไม่ได้เลือก
      if(h.grade == 'Y' && cound_grade == 0 ) {
        swal('Warning', 'กรุณาเลือกเกรดลูกค้าอย่างน้อย 1 รายการ', 'warning');
        return false;
      }
    }

    if(h.customer == 'Y') {
      $('.customer-chk').each(function() {
        h.customerList.push({
          'id' : $(this).val(),
          'code' : $(this).data('code')
        });
      });
    }
    else {

      if(h.group == 'Y') {
        $('.chk-group:checked').each(function() {
          h.groupList.push($(this).val());
        });
      }

      if(h.type == 'Y') {
        $('.chk-type:checked').each(function() {
          h.typeList.push($(this).val());
        });
      }

      if(h.kind == 'Y') {
        $('.chk-kind:checked').each(function() {
          h.kindList.push($(this).val())
        })
      }

      if(h.area == 'Y') {
        $('.chk-area:checked').each(function() {
          h.areaList.push($(this).val())
        })
      }

      if(h.grade == 'Y') {
        $('.chk-grade:checked').each(function() {
          h.gradeList.push($(this).val())
        })
      }
    }
  }


  load_in();

  $.ajax({
    url:HOME + 'set_customer_rule',
    type:'POST',
    cache:false,
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs) {
      load_out();

      if(rs.trim() == 'success'){
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


function checkCustomerAll(el) {
	if(el.is(':checked')) {
		$('.customer-chk').prop('checked', true);
	}
	else {
		$('.customer-chk').prop('checked', false);
	}
}


function showCustomerGroup(){
  $('#cust-group-modal').modal('show');
}


function showCustomerGrade(){
  $('#cust-grade-modal').modal('show');
}


function showCustomerType(){
  $('#cust-type-modal').modal('show');
}


function showCustomerKind(){
  $('#cust-kind-modal').modal('show');
}


function showCustomerArea(){
  $('#cust-area-modal').modal('show');
}


$('#txt-cust-id-box').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      addCustId();
    }
  }
});


$('.chk-group').change(function(e){
  count = 0;
  $('.chk-group').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-group').text(count);
});


$('.chk-type').change(function(e){
  count = 0;
  $('.chk-type').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-type').text(count);
});


$('.chk-kind').change(function(e){
  count = 0;
  $('.chk-kind').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-kind').text(count);
});


$('.chk-area').change(function(e){
  count = 0;
  $('.chk-area').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-area').text(count);
});


$('.chk-grade').change(function(e){
  count = 0;
  $('.chk-grade').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-grade').text(count);
});


$('#txt-cust-id-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_customer_code_name_id',
  autoFocus:true,
  close:function(){
    arr = $(this).val().split(' | ');
    if(arr.length == 3){
      code = arr[0];
      name = arr[1];
      id = arr[2];

      $(this).val(name);
      $('#customer-id').val(id)
      $('#customer-id').data('code', code)
      $('#customer-id').data('name', name)
    }
    else {
      $(this).val('');
      $('#customer-id').val('')
      $('#customer-id').data('code', '')
      $('#customer-id').data('name', '')
    }
  }
});


function addCustId() {
  let id = $('#customer-id').val();
  let code = $('#customer-id').data('code');
  let name = $('#customer-id').data('name');
  let txt = $('#txt-cust-id-box').val().trim();

  if(code != "" && id != "" && txt != "") {
    let ds = {"id" :id, "code" : code, "name" : name}
    let source = $('#customerRowTemplate').html();
    let output = $('#customerList');

    render_append(source, ds, output);
    reIndex('C');

    $('#customer-id').val('');
    $('#customer-id').data('code', '');
    $('#customer-id').data('name', '');
    $('#txt-cust-id-box').val('').focus();
  }
}


function removeCustomer(){
  $('.customer-chk:checked').each(function() {
    let id = $(this).val();
    $('#customer-row-'+id).remove();
	});

  reIndex('C');
}


//--- เลือกลูกค้าทั้งหมด
function toggleAllCustomer(option) {
  $('#all_customer').val(option);

  if(option == 'Y'){
    $('#btn-cust-all-yes').addClass('btn-primary');
    $('#btn-cust-all-no').removeClass('btn-primary');
    disActiveCustomerControl();
  }

  if(option == 'N'){
    $('#btn-cust-all-no').addClass('btn-primary');
    $('#btn-cust-all-yes').removeClass('btn-primary');
    $('.not-all').removeAttr('disabled');
    activeCustomerControl();
  }
}


function disActiveCustomerControl(){
  toggleCustomerGroup();
  toggleCustomerType();
  toggleCustomerKind();
  toggleCustomerArea();
  toggleCustomerGrade();
  $('.not-all').attr('disabled', 'disabled');
}


function activeCustomerControl(){
  customer_id = $('#customer_id').val();
  if(customer_id == 'Y') {
    toggleCustomerGroup();
    toggleCustomerType();
    toggleCustomerKind();
    toggleCustomerArea();
    toggleCustomerGrade();
    return;
  }

  toggleCustomerGroup($('#customer_group').val());
  toggleCustomerType($('#customer_type').val());
  toggleCustomerKind($('#customer_kind').val());
  toggleCustomerArea($('#customer_area').val());
  toggleCustomerGrade($('#customer_grade').val());
}


function toggleCustomerId(option){
  if(option == '' || option == undefined){
    option = $('#customer_id').val();
  }

  $('#customer_id').val(option);

  if(option == 'Y'){
    $('#btn-cust-id-yes').addClass('btn-primary');
    $('#btn-cust-id-no').removeClass('btn-primary');
    $('#txt-cust-id-box').removeAttr('disabled');
    $('#btn-cust-id-add').removeAttr('disabled');
    $('#btn-cust-id-upload').removeAttr('disabled');
  }

  if(option == 'N'){
    $('#btn-cust-id-no').addClass('btn-primary');
    $('#btn-cust-id-yes').removeClass('btn-primary');
    $('#txt-cust-id-box').attr('disabled', 'disabled');
    $('#btn-cust-id-add').attr('disabled', 'disabled');
    $('#btn-cust-id-upload').attr('disabled', 'disabled');
  }

  activeCustomerControl();
}


function toggleCustomerGroup(option){
  if(option == '' || option == undefined){
    option = $('#customer_group').val();
  }

  $('#customer_group').val(option);
  all = $('#all_customer').val();
  sc = $('#customer_id').val();
  if(option == 'Y' && sc == 'N' && all == 'N'){
    $('#btn-cust-group-no').removeClass('btn-primary');
    $('#btn-cust-group-yes').addClass('btn-primary');
    $('#btn-cust-group-no').removeAttr('disabled');
    $('#btn-cust-group-yes').removeAttr('disabled');
    $('#btn-select-cust-group').removeAttr('disabled');

    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-group-yes').removeClass('btn-primary');
    $('#btn-cust-group-no').addClass('btn-primary');
    $('#btn-cust-group-no').removeAttr('disabled');
    $('#btn-cust-group-yes').removeAttr('disabled');
    $('#btn-select-cust-group').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-group-yes').attr('disabled', 'disabled');
    $('#btn-cust-group-no').attr('disabled', 'disabled');
    $('#btn-select-cust-group').attr('disabled', 'disabled');
    return;
  }
}


function toggleCustomerType(option){
  if(option == '' || option == undefined){
    option = $('#customer_type').val();
  }

  $('#customer_type').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-type-no').removeClass('btn-primary');
    $('#btn-cust-type-yes').addClass('btn-primary');
    $('#btn-cust-type-no').removeAttr('disabled');
    $('#btn-cust-type-yes').removeAttr('disabled');
    $('#btn-select-cust-type').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-type-yes').removeClass('btn-primary');
    $('#btn-cust-type-no').addClass('btn-primary');
    $('#btn-cust-type-yes').removeAttr('disabled');
    $('#btn-cust-type-no').removeAttr('disabled');
    $('#btn-select-cust-type').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-type-yes').attr('disabled', 'disabled');
    $('#btn-cust-type-no').attr('disabled', 'disabled');
    $('#btn-select-cust-type').attr('disabled', 'disabled');
    return;
  }
}


function toggleCustomerKind(option){
  if(option == '' || option == undefined){
    option = $('#customer_kind').val();
  }


  $('#customer_kind').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();

  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-kind-no').removeClass('btn-primary');
    $('#btn-cust-kind-yes').addClass('btn-primary');
    $('#btn-cust-kind-no').removeAttr('disabled');
    $('#btn-cust-kind-yes').removeAttr('disabled');
    $('#btn-select-cust-kind').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-kind-yes').removeClass('btn-primary');
    $('#btn-cust-kind-no').addClass('btn-primary');
    $('#btn-cust-kind-no').removeAttr('disabled');
    $('#btn-cust-kind-yes').removeAttr('disabled');
    $('#btn-select-cust-kind').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-kind-yes').attr('disabled', 'disabled');
    $('#btn-cust-kind-no').attr('disabled', 'disabled');
    $('#btn-select-cust-kind').attr('disabled', 'disabled');
  }
}


function toggleCustomerArea(option){
  if(option == '' || option == undefined){
    option = $('#customer_area').val();
  }

  $('#customer_area').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-area-no').removeClass('btn-primary');
    $('#btn-cust-area-yes').addClass('btn-primary');
    $('#btn-cust-area-no').removeAttr('disabled');
    $('#btn-cust-area-yes').removeAttr('disabled');
    $('#btn-select-cust-area').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-area-yes').removeClass('btn-primary');
    $('#btn-cust-area-no').addClass('btn-primary');
    $('#btn-cust-area-yes').removeAttr('disabled');
    $('#btn-cust-area-no').removeAttr('disabled');
    $('#btn-select-cust-area').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-area-yes').attr('disabled', 'disabled');
    $('#btn-cust-area-no').attr('disabled', 'disabled');
    $('#btn-select-cust-area').attr('disabled', 'disabled');
  }
}


function toggleCustomerGrade(option){
  if(option == '' || option == undefined){
    option = $('#customer_grade').val();
  }

  $('#customer_grade').val(option);
  sc = $('#customer_id').val();
  all = $('#all_customer').val();
  if(option == 'Y' && all == 'N' && sc == 'N'){
    $('#btn-cust-grade-no').removeClass('btn-primary');
    $('#btn-cust-grade-yes').addClass('btn-primary');
    $('#btn-cust-grade-no').removeAttr('disabled');
    $('#btn-cust-grade-yes').removeAttr('disabled');
    $('#btn-select-cust-grade').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N'){
    $('#btn-cust-grade-yes').removeClass('btn-primary');
    $('#btn-cust-grade-no').addClass('btn-primary');
    $('#btn-cust-grade-no').removeAttr('disabled');
    $('#btn-cust-grade-yes').removeAttr('disabled');
    $('#btn-select-cust-grade').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y'){
    $('#btn-cust-grade-no').attr('disabled', 'disabled');
    $('#btn-cust-grade-yes').attr('disabled', 'disabled');
    $('#btn-select-cust-grade').attr('disabled', 'disabled');
  }
}


$(document).ready(function() {
  var all = $('#all_customer').val();
  var custId = $('#customer_id').val();
  toggleAllCustomer(all);
  toggleCustomerId(custId);
});
