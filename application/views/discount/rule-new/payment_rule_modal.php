
<div class="modal fade" id="payment-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Select Payment</h4>
      </div>
      <div class="modal-body" id="payment-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if( ! empty($payments)) : ?>
      <?php $pm = $this->discount_rule_model->getRulePayment($rule->id); ?>
      <?php foreach($payments as $rs) : ?>
        <?php $se = isset($pm[$rs->id]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-payment"
								name="chk-payment-<?php echo $rs->id; ?>"
								id="chk-payment-<?php echo $rs->id; ?>"
								value="<?php echo $rs->id; ?>" <?php echo $se; ?> />
								<span class="lbl"><?php echo $rs->name; ?></span>
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
