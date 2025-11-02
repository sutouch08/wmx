<div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:95%; margin-left:auto; margin-right:auto;">
    <div class="modal-content">
      <div class="modal-header" style="border-bottom:solid 1px #ddd;">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title text-center">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>คำอธิบาย (จำเป็น)</label>
            <textarea class="form-control input-sm" id="cancel-reason" maxlength="250" style="min-height:100px;" placeholder="กรุณาระบุเหตุผลในการยกเลิกอย่างน้อย 10 ตัวอักษร"></textarea>
            <input type="hidden" id="cancel-code" value="" />
          </div>
        </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-info" onclick="doCancel()">Submit</button>
      </div>
    </div>
  </div>
</div>
