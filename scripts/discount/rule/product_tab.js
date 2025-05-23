var click = 0;

function saveProduct() {
  rule_id = $('#id_rule').val();

  let h = {
    'id' : $('#id_rule').val(),
    'all' : $('#all_product').val(), //--- Y, N
    'sku' : $('#product_id').val(), // -- Y, N
    'model' : $('#product_model').val(), //=- Y, N
    'main_group' : $('#product_main_group').val(),
    'group' : $('#product_group').val(),
    'segment' : $('#product_segment').val(),
    'class' : $('#product_class').val(),
    'family' : $('#product_family').val(),
    'type' : $('#product_type').val(),
    'kind' : $('#product_kind').val(),
    'gender' : $('#product_gender').val(),
    'sport_type' : $('#product_sport_type').val(),
    'collection' : $('#product_collection').val(),
    'brand': $('#product_brand').val(),
    'year' : $('#product_year').val(),
    'skuList' : [],
    'modelList' : [],
    'mainGroupList' : [],
    'groupList' : [],
    'segmentList' : [],
    'classList' : [],
    'familyList' : [],
    'typeList' : [],
    'kindList' : [],
    'genderList' : [],
    'sportTypeList' : [],
    'collectionList' : [],
    'brandList' : [],
    'yearList' : []
  };

  //--- ถ้าเลือกสินค้าทั้งหมดจะไม่สนใจเงื่อนไขอื่นๆ
  if(h.all == 'N') {
    if(h.sku == 'Y' && $('.item-chk').length == 0) {
      swal("Warning", "Please select at least 1 SKU", "warning");
      return false;
    }

    if(h.model == 'Y' && $('.model-chk').length == 0) {
      swal('Warning', 'Please select at least 1 Model', 'warning');
      return false;
    }

    if(h.sku == 'N' && h.model == 'N') {
      let mainGroupCount = $('.chk-pd-main-group:checked').length;
      let groupCount = $('.chk-pd-group:checked').length;
      let segmentCount = $('.chk-pd-segment:checked').length;
      let classCount = $('.chk-pd-class:checked').length;
      let familyCount = $('.chk-pd-family:checked').length;
      let typeCount = $('.chk-pd-type:checked').length;
      let kindCount = $('.chk-pd-kind:checked').length;
      let genderCount = $('.chk-pd-gender:checked').length;
      let sportTypeCount = $('.chk-pd-sport-type:checked').length;
      let collectionCount = $('.chk-pd-collection:checked').length;
      let brandCount = $('.chk-pd-brand:checked').length;
      let yearCount = $('.chk-pd-year:checked').length;

      let allCount = mainGroupCount + groupCount + segmentCount + classCount + familyCount + kindCount + typeCount + genderCount + sportTypeCount + collectionCount + brandCount + yearCount;

      if(allCount == 0) {
        swal('Warning', 'Please select at least 1 of attributes', 'warning');
        return false;
      }

      if(h.main_group == 'Y' && mainGroupCount == 0) {
        swal('Warning', 'Please select product main group', 'warning');
        return false;
      }


      if(h.group == 'Y' && groupCount == 0) {
        swal('Warning', 'Please select product sub group', 'warning');
        return false;
      }

      if(h.segment == 'Y' && segmentCount == 0) {
        swal('Warning', 'Please select product segment', 'warning');
        return false;
      }

      if(h.class == 'Y' && classCount == 0) {
        swal('Warning', 'Please select product class', 'warning');
        return false;
      }

      if(h.family == 'Y' && familyCount == 0) {
        swal('Warning', 'Please select product family', 'warning');
        return false;
      }

      if(h.type == 'Y' && typeCount == 0) {
        swal('Warning', 'Please select product type', 'warning');
        return false;
      }

      if(h.kind == 'Y' && kindCount == 0) {
        swal('Warning', 'Please select product kind', 'warning');
        return false;
      }

      if(h.gender == 'Y' && genderCount == 0) {
        swal('Warning', 'Please select product Gender', 'warning');
        return false;
      }

      if(h.sport_type == 'Y' && sportTypeCount == 0) {
        swal('Warning', 'Please select product sport type', 'warning');
        return false;
      }

      if(h.collection == 'Y' && collectionCount == 0) {
        swal('Warning', 'Please select product clbu/collection', 'warning');
        return false;
      }

      if(h.brand == 'Y' && brandCount == 0 ){
        swal('Warning', 'Please select product brand', 'warning');
        return false;
      }

      if(h.year == 'Y' && yearCount == 0) {
        swal('Warning', 'Please select product sub group', 'warning');
        return false;
      }
    }
  }

  if(h.sku == 'Y') {
    $('.item-chk').each(function() {
      let el = $(this);
      h.skuList.push({'id' : el.val(), 'code' : el.data('code')});
    })
  }

  if(h.model == 'Y') {
    $('.model-chk').each(function() {
      let el = $(this);
      h.modelList.push({'id' : el.val(), 'code' : el.data('code')});
    })
  }

  if(h.main_group == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-main-group:checked').each(function() {
      h.mainGroupList.push($(this).val());
    })
  }

  if(h.group == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-group:checked').each(function() {
      h.groupList.push($(this).val());
    })
  }

  if(h.segment == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-segment:checked').each(function() {
      h.segmentList.push($(this).val());
    })
  }

  if(h.class == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-class:checked').each(function() {
      h.classList.push($(this).val());
    })
  }

  if(h.family == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-family:checked').each(function() {
      h.familyList.push($(this).val());
    })
  }

  if(h.type == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-type:checked').each(function() {
      h.typeList.push($(this).val());
    })
  }

  if(h.kind == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-kind:checked').each(function() {
      h.kindList.push($(this).val());
    })
  }

  if(h.gender == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-gender:checked').each(function() {
      h.genderList.push($(this).val());
    })
  }

  if(h.sport_type == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-sport-type:checked').each(function() {
      h.sportTypeList.push($(this).val());
    })
  }

  if(h.collection == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-collection:checked').each(function() {
      h.collectionList.push($(this).val());
    })
  }

  if(h.brand == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-brand:checked').each(function() {
      h.brandList.push($(this).val());
    })
  }

  if(h.year == 'Y' && h.sku == 'N' && h.model == 'N') {
    $('.chk-pd-year:checked').each(function() {
      h.yearList.push($(this).val());
    })
  }

  load_in();

  $.ajax({
    url: BASE_URL + 'discount/discount_rule/set_product_rule',
    type:'POST',
    cache:'false',
    data:{
      'data' : JSON.stringify(h)
    },
    success:function(rs){

      load_out();

      if(rs.trim() == 'success'){
        swal({
          title:'Saved',
          type:'success',
          timer:1000
        });
      }
      else{
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


function checkItemAll(el) {
	if(el.is(':checked')) {
		$('.item-chk').prop('checked', true);
	}
	else {
		$('.item-chk').prop('checked', false);
	}
}


function checkModelAll(el) {
	if(el.is(':checked')) {
		$('.model-chk').prop('checked', true);
	}
	else {
		$('.model-chk').prop('checked', false);
	}
}


function showProductMainGroup(){
  $('#pd-main-group-modal').modal('show');
}


function showProductGroup(){
  $('#pd-group-modal').modal('show');
}


function showProductSegment(){
  $('#pd-segment-modal').modal('show');
}


function showProductClass(){
  $('#pd-class-modal').modal('show');
}


function showProductFamily(){
  $('#pd-family-modal').modal('show');
}


function showProductType(){
  $('#pd-type-modal').modal('show');
}


function showProductKind(){
  $('#pd-kind-modal').modal('show');
}


function showProductGender(){
  $('#pd-gender-modal').modal('show');
}


function showProductSportType(){
  $('#pd-sport-type-modal').modal('show');
}


function showProductCollection(){
  $('#pd-collection-modal').modal('show');
}


function showProductBrand(){
  $('#pd-brand-modal').modal('show');
}


function showProductYear(){
  $('#pd-year-modal').modal('show');
}


$('#txt-product-id-box').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      addProductId();
    }
  }
});


$('#txt-model-id-box').keyup(function(e){
  if(e.keyCode == 13){
    if($(this).val() != ''){
      addModelId();
    }
  }
});


$('.chk-pd-main-group').change(function(e){
  count = 0;
  $('.chk-pd-main-group').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-main-group').text(count);
});


$('.chk-pd-group').change(function(e){
  count = 0;
  $('.chk-pd-group').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-group').text(count);
});


$('.chk-pd-segment').change(function(e){
  count = 0;
  $('.chk-pd-segment').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-segment').text(count);
});


$('.chk-pd-class').change(function(e){
  count = 0;
  $('.chk-pd-class').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-class').text(count);
});


$('.chk-pd-family').change(function(e){
  count = 0;
  $('.chk-pd-family').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-family').text(count);
});


$('.chk-pd-type').change(function(e){
  count = 0;
  $('.chk-pd-type').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-type').text(count);
});


$('.chk-pd-kind').change(function(e){
  count = 0;
  $('.chk-pd-kind').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-kind').text(count);
});


$('.chk-pd-gender').change(function(e){
  count = 0;
  $('.chk-pd-gender').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-gender').text(count);
});


$('.chk-pd-sport-type').change(function(e){
  count = 0;
  $('.chk-pd-sport-type').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-sport-type').text(count);
});


$('.chk-pd-collection').change(function(e){
  count = 0;
  $('.chk-pd-collection').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-collection').text(count);
});


$('.chk-pd-brand').change(function(e){
  count = 0;
  $('.chk-pd-brand').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-brand').text(count);
});


$('.chk-pd-year').change(function(e){
  count = 0;
  $('.chk-pd-year').each(function(index, el) {
    if($(this).is(':checked')){
      count++;
    }
  });
  $('#badge-pd-year').text(count);
});


$('#txt-product-id-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_item_code_name_id',
  autoFocus:true,
  close:function(){
    arr = $(this).val().split(' | ');
    if(arr.length == 3){
			code = arr[0];
      name = arr[1];
			id = arr[2];
      $('#product-id').val(id);
      $('#product-id').data('code', code);
      $('#product-id').data('name', name);
      $(this).val(code);
    }
    else {
      $(this).val('');
      $('#product-id').val('');
      $('#product-id').data('code', '');
      $('#product-id').data('name', '');
    }
  }
});


function addProductId(){
  let id = $('#product-id').val()
  let code = $('#product-id').data('code')
  let name = $('#product-id').data('name');

  if(code.length > 0 && id != "") {
		addProduct(id, code, name);
    $('#product-id').val('');
    $('#product-id').data('code', '');
    $('#product-id').data('name', '');
    $('#txt-product-id-box').val('').focus();
  }
}


function addProduct(id, code, name) {
	if(code.length && $('#item-id-'+id).length == 0) {
    let ds = {"id" : id, "code" : code, "name" : name};
    let source = $('#itemRowTemplate').html();
    let output = $('#itemList');

    render_append(source, ds, output);
	}
}


function removeItem(){
  $('.item-chk').each(function() {
		if($(this).is(':checked')) {
			let id = $(this).val();
			$('#item-row-'+id).remove();
		}
	})
}



$('#txt-model-id-box').autocomplete({
  source: BASE_URL + 'auto_complete/get_model_name',
  autoFocus:true,
  close:function(){
    arr = $(this).val().split(' | ');
    if(arr.length == 3){
      code = arr[0];
			name = arr[1];
      id = arr[2];
      $(this).val(code);
      $('#model-id').val(id);
      $('#model-id').data('code', code);
      $('#model-id').data('name', name);
    }
    else{
      $(this).val('');
      $('#model-id').val('');
      $('#model-id').data('code', '');
      $('#model-id').data('name', '');
    }
  }
});


function addModelId() {
  let id = $('#model-id').val();
  let code = $('#model-id').data('code');
  let name = $('#model-id').data('name');

  if(code.length > 0 && id != "") {
    addModel(id, code, name);
    $('#model-id').val('');
    $('#model-id').data('code', '');
    $('#model-id').data('name', '');
    $('#txt-model-id-box').val('').focus();
  }
}


function addModel(id, code, name) {
  if(code.length && $('#model-'+id.length == 0)) {
    let ds = {"id" : id, "code" : code, "name" : name};
    let source = $('#modelRowTemplate').html();
    let output = $('#modelList');
    render_append(source, ds, output);
  }
}


function removeModel(){
  $('.model-chk').each(function() {
		let id = $(this).val();

		if($(this).is(':checked')) {
			$('#model-row-'+id).remove();
		}
	});
}


//--- เลือกสินค้าทั้งหมด
function toggleAllProduct(option){
  $('#all_product').val(option);

  if(option == 'Y'){
    $('#btn-pd-all-yes').addClass('btn-primary');
    $('#btn-pd-all-no').removeClass('btn-primary');
    disActiveProductControl();
  }else if(option == 'N'){
    $('#btn-pd-all-no').addClass('btn-primary');
    $('#btn-pd-all-yes').removeClass('btn-primary');
    $('.not-pd-all').removeAttr('disabled');
		toggleProductId();
		toggleModelId();
    activeProductControl();
  }
}



function disActiveProductControl(){
  $('.not-pd-all').attr('disabled', 'disabled');
}


function activeProductControl(){
	product_id = $('#product_id').val();
  product_model = $('#product_model').val();

  if(product_model == 'Y' || product_id == 'Y') {
    toggleProductMainGroup();
    toggleProductGroup();
    toggleProductSegment();
    toggleProductClass();
    toggleProductFamily();
    toggleProductType();
    toggleProductKind();
    toggleProductGender();
    toggleProductSportType();
    toggleProductCollection();
    toggleProductBrand();
    toggleProductYear();
    return;
  }

  toggleProductMainGroup($('#product_main_group').val());
  toggleProductGroup($('#product_group').val());
  toggleProductSegment($('#product_segment').val());
  toggleProductClass($('#product_class').val());
  toggleProductFamily($('#product_family').val());
  toggleProductType($('#product_type').val());
  toggleProductKind($('#product_kind').val());
  toggleProductGender($('#product_gender').val());
  toggleProductSportType($('#product_sport_type').val());
  toggleProductCollection($('#product_collection').val());
  toggleProductBrand($('#product_brand').val());
  toggleProductYear($('#product_year').val());
}


function toggleProductId(option){
  if(option == '' || option == undefined){
    option = $('#product_id').val();
  }

  $('#product_id').val(option);

	all = $('#all_product').val();

  if(option == 'Y' && all == 'N') {
    $('#btn-product-id-yes').addClass('btn-primary');
    $('#btn-product-id-no').removeClass('btn-primary');
		$('#btn-product-id-add').removeAttr('disabled');
    $('#txt-product-id-box').removeAttr('disabled');
		$('#btn-product-import').removeAttr('disabled');
    activeProductModel('N');
  }

  if(option == 'N') {
    $('#btn-product-id-no').addClass('btn-primary');
    $('#btn-product-id-yes').removeClass('btn-primary');
		$('#btn-product-id-add').attr('disabled', 'disabled');
    $('#txt-product-id-box').attr('disabled', 'disabled');
		$('#btn-product-import').attr('disabled', 'disabled');
    activeProductModel('Y');
  }

  toggleModelId();
  activeProductControl();
}


function activeProductModel(option) {
  if(option == 'Y') {
    $('#btn-model-id-yes').removeAttr('disabled');
    $('#btn-model-id-no').removeAttr('disabled');
		$('#btn-model-id-add').removeAttr('disabled');
    $('#txt-model-id-box').removeAttr('disabled');
		$('#btn-model-import').removeAttr('disabled');
  }

  if(option == 'N') {
    $('#btn-model-id-no').attr('disabled', 'disabled');
    $('#btn-model-id-yes').attr('disabled', 'disabled');
    $('#btn-model-id-add').attr('disabled', 'disabled');
    $('#txt-model-id-box').attr('disabled', 'disabled');
    $('#btn-model-import').attr('disabled', 'disabled');
  }
}


function toggleModelId(option){
  if(option == '' || option == undefined){
    option = $('#product_model').val();
  }

  $('#product_model').val(option);
  let all = $('#all_product').val();
  let sku = $('#product_id').val();

  if(option == 'Y' && all == 'N' && sku == 'N') {
    $('#btn-model-id-yes').addClass('btn-primary');
    $('#btn-model-id-no').removeClass('btn-primary');
		$('#btn-model-id-add').removeAttr('disabled');
    $('#txt-model-id-box').removeAttr('disabled');
		$('#btn-model-import').removeAttr('disabled');
  }
  else if(option == 'N'){
    $('#btn-model-id-no').addClass('btn-primary');
    $('#btn-model-id-yes').removeClass('btn-primary');
		$('#btn-model-id-add').attr('disabled', 'disabled');
    $('#txt-model-id-box').attr('disabled', 'disabled');
		$('#btn-model-import').attr('disabled', 'disabled');
  }

  activeProductControl();
}


function toggleProductMainGroup(option) {
  if(option == '' || option == undefined) {
    option = $('#product_main_group').val();
  }

  $('#product_main_group').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-main-group-no').removeClass('btn-primary');
    $('#btn-pd-main-group-yes').addClass('btn-primary');
    $('#btn-pd-main-group-no').removeAttr('disabled');
    $('#btn-pd-main-group-yes').removeAttr('disabled');
    $('#btn-select-pd-main-group').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-main-group-yes').removeClass('btn-primary');
    $('#btn-pd-main-group-no').addClass('btn-primary');
    $('#btn-pd-main-group-no').removeAttr('disabled');
    $('#btn-pd-main-group-yes').removeAttr('disabled');
    $('#btn-select-pd-main-group').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-main-group-yes').attr('disabled', 'disabled');
    $('#btn-pd-main-group-no').attr('disabled', 'disabled');
    $('#btn-select-pd-main-group').attr('disabled', 'disabled');
  }
}


function toggleProductGroup(option) {
  if(option == '' || option == undefined) {
    option = $('#product_group').val();
  }

  $('#product_group').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-group-no').removeClass('btn-primary');
    $('#btn-pd-group-yes').addClass('btn-primary');
    $('#btn-pd-group-no').removeAttr('disabled');
    $('#btn-pd-group-yes').removeAttr('disabled');
    $('#btn-select-pd-group').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-group-yes').removeClass('btn-primary');
    $('#btn-pd-group-no').addClass('btn-primary');
    $('#btn-pd-group-no').removeAttr('disabled');
    $('#btn-pd-group-yes').removeAttr('disabled');
    $('#btn-select-pd-group').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-group-yes').attr('disabled', 'disabled');
    $('#btn-pd-group-no').attr('disabled', 'disabled');
    $('#btn-select-pd-group').attr('disabled', 'disabled');
  }
}


function toggleProductSegment(option) {
  if(option == '' || option == undefined) {
    option = $('#product_segment').val();
  }

  $('#product_segment').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-segment-no').removeClass('btn-primary');
    $('#btn-pd-segment-yes').addClass('btn-primary');
    $('#btn-pd-segment-no').removeAttr('disabled');
    $('#btn-pd-segment-yes').removeAttr('disabled');
    $('#btn-select-pd-segment').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-segment-yes').removeClass('btn-primary');
    $('#btn-pd-segment-no').addClass('btn-primary');
    $('#btn-pd-segment-no').removeAttr('disabled');
    $('#btn-pd-segment-yes').removeAttr('disabled');
    $('#btn-select-pd-segment').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-segment-yes').attr('disabled', 'disabled');
    $('#btn-pd-segment-no').attr('disabled', 'disabled');
    $('#btn-select-pd-segment').attr('disabled', 'disabled');
  }
}


function toggleProductClass(option) {
  if(option == '' || option == undefined) {
    option = $('#product_class').val();
  }

  $('#product_class').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-class-no').removeClass('btn-primary');
    $('#btn-pd-class-yes').addClass('btn-primary');
    $('#btn-pd-class-no').removeAttr('disabled');
    $('#btn-pd-class-yes').removeAttr('disabled');
    $('#btn-select-pd-class').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-class-yes').removeClass('btn-primary');
    $('#btn-pd-class-no').addClass('btn-primary');
    $('#btn-pd-class-no').removeAttr('disabled');
    $('#btn-pd-class-yes').removeAttr('disabled');
    $('#btn-select-pd-class').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-class-yes').attr('disabled', 'disabled');
    $('#btn-pd-class-no').attr('disabled', 'disabled');
    $('#btn-select-pd-class').attr('disabled', 'disabled');
  }
}


function toggleProductFamily(option) {
  if(option == '' || option == undefined) {
    option = $('#product_family').val();
  }

  $('#product_family').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-family-no').removeClass('btn-primary');
    $('#btn-pd-family-yes').addClass('btn-primary');
    $('#btn-pd-family-no').removeAttr('disabled');
    $('#btn-pd-family-yes').removeAttr('disabled');
    $('#btn-select-pd-family').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-family-yes').removeClass('btn-primary');
    $('#btn-pd-family-no').addClass('btn-primary');
    $('#btn-pd-family-no').removeAttr('disabled');
    $('#btn-pd-family-yes').removeAttr('disabled');
    $('#btn-select-pd-family').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-family-yes').attr('disabled', 'disabled');
    $('#btn-pd-family-no').attr('disabled', 'disabled');
    $('#btn-select-pd-family').attr('disabled', 'disabled');
  }
}


function toggleProductType(option){
  if(option == '' || option == undefined){
    option = $('#product_type').val();
  }

  $('#product_type').val(option);
  sc = $('#product_model').val();
	pd = $('#product_id').val();
  all = $('#all_product').val();
  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N'){
    $('#btn-pd-type-no').removeClass('btn-primary');
    $('#btn-pd-type-yes').addClass('btn-primary');
    $('#btn-pd-type-no').removeAttr('disabled');
    $('#btn-pd-type-yes').removeAttr('disabled');
    $('#btn-select-pd-type').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-type-yes').removeClass('btn-primary');
    $('#btn-pd-type-no').addClass('btn-primary');
    $('#btn-pd-type-yes').removeAttr('disabled');
    $('#btn-pd-type-no').removeAttr('disabled');
    $('#btn-select-pd-type').attr('disabled', 'disabled');

    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-type-yes').attr('disabled', 'disabled');
    $('#btn-pd-type-no').attr('disabled', 'disabled');
    $('#btn-select-pd-type').attr('disabled', 'disabled');
  }
}


function toggleProductKind(option) {
  if(option == '' || option == undefined) {
    option = $('#product_kind').val();
  }

  $('#product_kind').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-kind-no').removeClass('btn-primary');
    $('#btn-pd-kind-yes').addClass('btn-primary');
    $('#btn-pd-kind-no').removeAttr('disabled');
    $('#btn-pd-kind-yes').removeAttr('disabled');
    $('#btn-select-pd-kind').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-kind-yes').removeClass('btn-primary');
    $('#btn-pd-kind-no').addClass('btn-primary');
    $('#btn-pd-kind-no').removeAttr('disabled');
    $('#btn-pd-kind-yes').removeAttr('disabled');
    $('#btn-select-pd-kind').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-kind-yes').attr('disabled', 'disabled');
    $('#btn-pd-kind-no').attr('disabled', 'disabled');
    $('#btn-select-pd-kind').attr('disabled', 'disabled');
  }
}


function toggleProductGender(option){
  if(option == '' || option == undefined){
    option = $('#product_gender').val();
  }

  $('#product_gender').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ){
    $('#btn-pd-gender-no').removeClass('btn-primary');
    $('#btn-pd-gender-yes').addClass('btn-primary');
    $('#btn-pd-gender-no').removeAttr('disabled');
    $('#btn-pd-gender-yes').removeAttr('disabled');
    $('#btn-select-pd-gender').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-gender-yes').removeClass('btn-primary');
    $('#btn-pd-gender-no').addClass('btn-primary');
    $('#btn-pd-gender-no').removeAttr('disabled');
    $('#btn-pd-gender-yes').removeAttr('disabled');
    $('#btn-select-pd-gender').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-gender-yes').attr('disabled', 'disabled');
    $('#btn-pd-gender-no').attr('disabled', 'disabled');
    $('#btn-select-pd-gender').attr('disabled', 'disabled');
  }
}


function toggleProductSportType(option){
  if(option == '' || option == undefined){
    option = $('#product_sport_type').val();
  }

  $('#product_sport_type').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ){
    $('#btn-pd-sport-type-no').removeClass('btn-primary');
    $('#btn-pd-sport-type-yes').addClass('btn-primary');
    $('#btn-pd-sport-type-no').removeAttr('disabled');
    $('#btn-pd-sport-type-yes').removeAttr('disabled');
    $('#btn-select-pd-sport-type').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-sport-type-yes').removeClass('btn-primary');
    $('#btn-pd-sport-type-no').addClass('btn-primary');
    $('#btn-pd-sport-type-no').removeAttr('disabled');
    $('#btn-pd-sport-type-yes').removeAttr('disabled');
    $('#btn-select-pd-sport-type').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-sport-type-yes').attr('disabled', 'disabled');
    $('#btn-pd-sport-type-no').attr('disabled', 'disabled');
    $('#btn-select-pd-sport-type').attr('disabled', 'disabled');
  }
}


function toggleProductCollection(option){
  if(option == '' || option == undefined){
    option = $('#product_collection').val();
  }

  $('#product_collection').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ){
    $('#btn-pd-collection-no').removeClass('btn-primary');
    $('#btn-pd-collection-yes').addClass('btn-primary');
    $('#btn-pd-collection-no').removeAttr('disabled');
    $('#btn-pd-collection-yes').removeAttr('disabled');
    $('#btn-select-pd-collection').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-collection-yes').removeClass('btn-primary');
    $('#btn-pd-collection-no').addClass('btn-primary');
    $('#btn-pd-collection-no').removeAttr('disabled');
    $('#btn-pd-collection-yes').removeAttr('disabled');
    $('#btn-select-pd-collection').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-collection-yes').attr('disabled', 'disabled');
    $('#btn-pd-collection-no').attr('disabled', 'disabled');
    $('#btn-select-pd-collection').attr('disabled', 'disabled');
  }
}


function toggleProductBrand(option){
  if(option == '' || option == undefined){
    option = $('#product_brand').val();
  }

  $('#product_brand').val(option);
  sc = $('#product_model').val();
	pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N'){
    $('#btn-pd-brand-no').removeClass('btn-primary');
    $('#btn-pd-brand-yes').addClass('btn-primary');
    $('#btn-pd-brand-no').removeAttr('disabled');
    $('#btn-pd-brand-yes').removeAttr('disabled');
    $('#btn-select-pd-brand').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && all == 'N' && pd =='N'){
    $('#btn-pd-brand-yes').removeClass('btn-primary');
    $('#btn-pd-brand-no').addClass('btn-primary');
    $('#btn-pd-brand-no').removeAttr('disabled');
    $('#btn-pd-brand-yes').removeAttr('disabled');
    $('#btn-select-pd-brand').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-brand-yes').attr('disabled', 'disabled');
    $('#btn-pd-brand-no').attr('disabled', 'disabled');
    $('#btn-select-pd-brand').attr('disabled', 'disabled');
  }
}


function toggleProductYear(option) {
  if(option == '' || option == undefined) {
    option = $('#product_year').val();
  }

  $('#product_year').val(option);
  sc = $('#product_model').val();
  pd = $('#product_id').val();
  all = $('#all_product').val();

  if(option == 'Y' && all == 'N' && sc == 'N' && pd == 'N' ) {
    $('#btn-pd-year-no').removeClass('btn-primary');
    $('#btn-pd-year-yes').addClass('btn-primary');
    $('#btn-pd-year-no').removeAttr('disabled');
    $('#btn-pd-year-yes').removeAttr('disabled');
    $('#btn-select-pd-year').removeAttr('disabled');
    return;
  }

  if(option == 'N' && sc == 'N' && pd == 'N' && all == 'N'){
    $('#btn-pd-year-yes').removeClass('btn-primary');
    $('#btn-pd-year-no').addClass('btn-primary');
    $('#btn-pd-year-no').removeAttr('disabled');
    $('#btn-pd-year-yes').removeAttr('disabled');
    $('#btn-select-pd-year').attr('disabled', 'disabled');
    return;
  }

  if(all == 'Y' || sc == 'Y' || pd == 'Y'){
    $('#btn-pd-year-yes').attr('disabled', 'disabled');
    $('#btn-pd-year-no').attr('disabled', 'disabled');
    $('#btn-select-pd-year').attr('disabled', 'disabled');
  }
}


$(document).ready(function() {
  var all = $('#all_product').val();
  var modelId = $('#product_model').val();
	var productId = $('#product_id').val();

  toggleAllProduct(all);
  toggleModelId(modelId);
	toggleProductId(productId);
});


function getUploadFile(){
	$('#upload-modal').modal('show');
}


function getFile(){
	$('#uploadFile').click();
}


$("#uploadFile").change(function(){
	if($(this).val() != '')
	{
		var file 		= this.files[0];
		var name		= file.name;
		var type 		= file.type;
		var size		= file.size;

		if( size > 5000000 )
		{
			swal("ขนาดไฟล์ใหญ่เกินไป", "ไฟล์แนบต้องมีขนาดไม่เกิน 5 MB", "error");
			$(this).val('');
			return false;
		}

		$('#show-file-name').text(name);
	}
});


function readExcelFile() {
		$('#upload-modal').modal('hide');
		var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xlsx|.xls)$/;
	 /*Checks whether the file is a valid excel file*/
	 if (regex.test($("#uploadFile").val().toLowerCase())) {
			 var xlsxflag = false; /*Flag for checking whether excel is .xls format or .xlsx format*/
			 if ($("#uploadFile").val().toLowerCase().indexOf(".xlsx") > 0) {
					 xlsxflag = true;
			 }
			 /*Checks whether the browser supports HTML5*/
			 if (typeof (FileReader) != "undefined") {
					 var reader = new FileReader();
					 reader.onload = function (e) {
							 var data = e.target.result;
							 /*Converts the excel data in to object*/
							 if (xlsxflag) {
									 var workbook = XLSX.read(data, { type: 'binary' });
							 }
							 else {
									 var workbook = XLS.read(data, { type: 'binary' });
							 }
							 /*Gets all the sheetnames of excel in to a variable*/
							 var sheet_name_list = workbook.SheetNames;

							 var cnt = 0; /*This is used for restricting the script to consider only first sheet of excel*/
							 sheet_name_list.forEach(function (y) { /*Iterate through all sheets*/
									 /*Convert the cell value to Json*/
									 if (xlsxflag) {
											 var exceljson = XLSX.utils.sheet_to_json(workbook.Sheets[y]);
									 }
									 else {
											 var exceljson = XLS.utils.sheet_to_row_object_array(workbook.Sheets[y]);
									 }

									 if (exceljson.length > 0 && cnt == 0) {
											 addToList(exceljson);
											 cnt++;
									 }
							 });
					 }

					 if (xlsxflag) {/*If excel file is .xlsx extension than creates a Array Buffer from excel*/
							 reader.readAsArrayBuffer($("#uploadFile")[0].files[0]);
					 }
					 else {
							 reader.readAsBinaryString($("#uploadFile")[0].files[0]);
					 }
			 }
			 else {
					 swal({
						 title:'Error!',
						 text:"Sorry! Your browser does not support HTML5!",
						 type:'error'
					 });
			 }
	 }
	 else {
			 swal({
				 title:'Error!',
				 text:"Please upload a valid Excel file!",
				 type:'error'
			 });
	 }
}


function addToList(jsondata) {
	if(jsondata.length) {
		//--- clear current li in model-list
		$('#model-list li').remove();
		//--- clear current hidden input list
		$('.modelId').remove();
		$('#psCount').text('0');

		for (var i = 0; i < jsondata.length; i++) {
			//console.log(jsondata[i]);
			var code = $.trim(jsondata[i].Model);
			addModel(code);
		}
	}
}
