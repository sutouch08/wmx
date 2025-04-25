<div class="modal fade" id="pd-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Group</h4>
      </div>
      <div class="modal-body" id="pd-group-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_groups)) : ?>
      <?php $sd = $this->discount_rule_model->getRuleProductGroup($id); ?>
      <?php foreach($product_groups as $rs) : ?>
        <?php $se = isset($sd[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-group"
                  name="chk-pd-group-<?php echo $rs->code; ?>"
                  id="chk-pd-group-<?php echo $rs->code; ?>"
                  value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                  <span class="lbl">&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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


<div class="modal fade" id="pd-cat-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Category</h4>
      </div>
      <div class="modal-body" id="pd-cat-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12">
    <?php if(! empty($product_categorys)) : ?>
      <?php foreach($product_categorys as $rs) : ?>
        <?php $se = isset($pdCategory[$rs->id]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-pd-cat"
								name="chk-pd-cat-<?php echo $rs->id; ?>"
								id="chk-pd-cat-<?php echo $rs->id; ?>"
								value="<?php echo $rs->id; ?>" <?php echo $se; ?> />
								<span class="lbl">&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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


<div class="modal fade" id="pd-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Type</h4>
      </div>
      <div class="modal-body" id="pd-type-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12">
				    <?php if( ! empty($product_types)) : ?>
				      <?php foreach($product_types as $rs) : ?>
								<?php $se = isset($pdType[$rs->id]) ? 'checked' : ''; ?>
				            <label class="display-block">
				              <input type="checkbox"
											class="ace chk-pd-type"
											name="chk-pd-type-<?php echo $rs->id; ?>"
											id="chk-pd-type-<?php echo $rs->id; ?>"
											value="<?php echo $rs->id; ?>" />
				              <span class="lbl">&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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



<div class="modal fade" id="pd-brand-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">เลือกยี่ห้อสินค้า</h4>
      </div>
      <div class="modal-body" id="pd-brand-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12">

    <?php if( ! empty($product_brands)) : ?>
      <?php foreach($product_brands as $rs) : ?>
        <?php $se = isset($pdBrand[$rs->id]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-pd-brand"
								name="chk-pd-brand-<?php echo $rs->id; ?>"
								id="chk-pd-brand-<?php echo $rs->id; ?>"
								value="<?php echo $rs->id; ?>" <?php echo $se; ?> />
								<span class="lbl">&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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
