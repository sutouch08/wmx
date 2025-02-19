<div class="modal fade" id="cancel-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
 <div class="modal-dialog" style="max-width:95vw; margin-left:auto; margin-right:auto;">
   <div class="modal-content">
       <div class="modal-header" style="border-bottom:solid 1px #ddd;">
       <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
       <h4 class="modal-title text-center">เหตุผลในการยกเลิก</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <label>คำอธิบาย (จำเป็น อย่างน้อย 5 ตัวอักษร)</label>
            <textarea class="form-control input-sm" id="cancel-reason" maxlength="100" style="min-height:100px;" placeholder="กรุณาระบุเหตุผลในการยกเลิก"></textarea>
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

<script>
  function cancel(code) {
    setTimeout(() => {
      let reason = $('#cancel-reason').val().trim();
      let code = $('#cancel-code').val().trim();

      if(reason.length < 5) {
        $('#cancel-modal').modal('show');
        return false;
      }

      $('#cancel-modal').modal('hide');

      load_in();

      $.ajax({
        url:HOME + 'cancel',
        type:"POST",
        cache:false,
        data:{
          "reason" : reason,
          "code" : code
        },
        success: function(rs) {
          load_out();

          if(rs.trim() === 'success') {
            swal({
              title:'Success',
              type:'success',
              timer:1000
            });

            setTimeout(() => {
              window.location.reload();
            }, 1200);
          }
          else {
            showError(rs);
          }
        },
        error:function(rs) {
          showError(rs);
        }
      });
    }, 100);
  }

  function doCancel() {
    let code = $('#cancel-code').val();
    let reason = $('#cancel-reason').val().trim();

    if( reason.length < 5) {
      $('#cancel-reason').addClass('has-error').focus();
      return false;
    }

    $('#cancel-modal').modal('hide');

    return cancel(code);
  }


  $('#cancel-modal').on('shown.bs.modal', function() {
    $('#cancel-reason').focus();
  });

</script>
