
<div class="modal fade" id="channels-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">เลือกช่องทางการขาย</h4>
      </div>
      <div class="modal-body" id="channels-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
            <?php if( ! empty($channels_list)) : ?>
              <?php foreach($channels_list as $rs) : ?>
                <?php $se = isset($channels[$rs->code]) ? 'checked' : ''; ?>
                <label class="display-block">
                  <input type="checkbox"
                  class="ace chk-channels"
                  data-code="<?php echo $rs->code; ?>"
                  data-name="<?php echo $rs->name; ?>"
                  value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                  <span class="lbl">&nbsp;&nbsp; <?php echo $rs->name; ?></span>
                </label>
              <?php endforeach; ?>
            <?php endif;?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>
