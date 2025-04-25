
<div class="modal fade" id="cust-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Customer Group</h4>
      </div>
      <div class="modal-body" id="cust-group-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if( ! empty($customer_groups)) : ?>
      <?php foreach($customer_groups as $rs) : ?>
        <?php $se = isset($custGroup[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-group"
								id="chk-group-<?php echo $rs->code; ?>"
								value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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


<div class="modal fade" id="cust-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Customer Type</h4>
      </div>
      <div class="modal-body" id="cust-type-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if(! empty($customer_types)) : ?>
      <?php foreach($customer_types as $rs) : ?>
        <?php $se = isset($custType[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-type"
								id="chk-type-<?php echo $rs->code; ?>"
								value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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




<div class="modal fade" id="cust-kind-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Customer Kind</h4>
      </div>
      <div class="modal-body" id="cust-kind-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if( ! empty($customer_kinds)) : ?>
      <?php foreach($customer_kinds as $rs) : ?>
        <?php $se = isset($custKind[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-kind"
								id="chk-kind-<?php echo $rs->code; ?>"
								value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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




<div class="modal fade" id="cust-area-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Customer Area</h4>
      </div>
      <div class="modal-body" id="cust-area-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if( ! empty($customer_areas)) : ?>
      <?php foreach($customer_areas as $rs) : ?>
        <?php $se = isset($custArea[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-area"
								id="chk-area-<?php echo $rs->code; ?>"
								value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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




<div class="modal fade" id="cust-grade-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Customer Grade</h4>
      </div>
      <div class="modal-body" id="cust-grade-body">
        <div class="row">
          <div class="col-sm-12">
    <?php if( ! empty($customer_grades)) : ?>
      <?php foreach($customer_grades as $rs) : ?>
        <?php $se = isset($custGrade[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class=" ace chk-grade"
								id="chk-grade-<?php echo $rs->code; ?>"
								value="<?php echo $rs->code; ?>" <?php echo $se; ?> />
                <span class="lbl">&nbsp;&nbsp;&nbsp;<?php echo $rs->name; ?></span>
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
