<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:600px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Import File</h4>
      </div>
      <div class="modal-body">
        <form id="upload-form" name="upload-form" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <div class="input-group width-100">
                <input type="text" class="form-control" id="show-file-name" placeholder="กรุณาเลือกไฟล์ Excel" readonly />
                <span class="input-group-btn">
                  <button type="button" class="btn btn-white btn-default"  onclick="getFile()">เลือกไฟล์</button>
                </span>
              </div>
            </div>
          </div>
          <input type="file" class="hide" name="uploadFile" id="uploadFile" accept=".xlsx" />
          <input type="hidden" id="target" value="" />
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-default btn-100" onclick="closeModal('upload-modal')">ยกเลิก</button>
        <button type="button" class="btn btn-sm btn-primary btn-100" onclick="uploadfile()">นำเข้า</button>
      </div>
    </div>
  </div>
</div>

<script>

function getUploadFile(target){
  $('#target').val(target);
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
		//readURL(this);
    $('#show-file-name').val(name);
	}
});


function uploadfile()	{
  $('#upload-modal').modal('hide');
  var target = $('#target').val();
  let url = target == 'C' ? 'get_customer_list' : (target == 'P' ? 'get_sku_list' : 'get_model_list');
  var file	= $("#uploadFile")[0].files[0];
  var fd = new FormData();
  fd.append('uploadFile', $('input[type=file]')[0].files[0]);
  if( file !== '')
  {
    load_in();

    $.ajax({
      url:BASE_URL + 'discount/discount_rule/'+url,
      type:"POST",
      cache:false,
      data: fd,
      processData:false,
      contentType: false,
      success: function(rs) {
        load_out();

        if(isJson(rs)) {
          let ds = JSON.parse(rs);

          if(ds.status == 'success') {
            let source = target == 'C' ? $('#customerRowsTemplate').html() : (target == 'P' ? $('#itemRowsTemplate').html() : $('#modelRowsTemplate').html());
            let output = target == 'C' ? $('#customerList') : (target == 'P' ? $('#itemList') : $('#modelList'));
            render(source, ds.data, output);
            reIndex(target);
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });
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
    });
  }
}


</script>
