<div class="modal fade" id="pd-main-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Main Group</h4>
      </div>
      <div class="modal-body" id="pd-main-group-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_main_groups)) : ?>
      <?php foreach($product_main_groups as $rs) : ?>
        <?php $se = isset($pdMainGroup[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-main-group"
                  name="chk-pd-main-group-<?php echo $rs->code; ?>"
                  id="chk-pd-main-group-<?php echo $rs->code; ?>"
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

<div class="modal fade" id="pd-group-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Sub Group</h4>
      </div>
      <div class="modal-body" id="pd-group-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_groups)) : ?>
      <?php foreach($product_groups as $rs) : ?>
        <?php $se = isset($pdGroup[$rs->code]) ? 'checked' : ''; ?>
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


<div class="modal fade" id="pd-segment-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Segment</h4>
      </div>
      <div class="modal-body" id="pd-segment-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_segments)) : ?>
      <?php foreach($product_segments as $rs) : ?>
        <?php $se = isset($pdSegment[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-segment"
                  name="chk-pd-segment-<?php echo $rs->code; ?>"
                  id="chk-pd-segment-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-class-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Class</h4>
      </div>
      <div class="modal-body" id="pd-class-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_classes)) : ?>
      <?php foreach($product_classes as $rs) : ?>
        <?php $se = isset($pdClass[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-class"
                  name="chk-pd-class-<?php echo $rs->code; ?>"
                  id="chk-pd-class-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-family-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Family</h4>
      </div>
      <div class="modal-body" id="pd-family-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_families)) : ?>
      <?php foreach($product_families as $rs) : ?>
        <?php $se = isset($pdFamily[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-family"
                  name="chk-pd-family-<?php echo $rs->code; ?>"
                  id="chk-pd-family-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Type</h4>
      </div>
      <div class="modal-body" id="pd-type-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
				    <?php if( ! empty($product_types)) : ?>
				      <?php foreach($product_types as $rs) : ?>
								<?php $se = isset($pdType[$rs->code]) ? 'checked' : ''; ?>
				            <label class="display-block">
				              <input type="checkbox"
											class="ace chk-pd-type"
											name="chk-pd-type-<?php echo $rs->code; ?>"
											id="chk-pd-type-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-kind-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Kind</h4>
      </div>
      <div class="modal-body" id="pd-kind-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_kinds)) : ?>
      <?php foreach($product_kinds as $rs) : ?>
        <?php $se = isset($pdKind[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-kind"
                  name="chk-pd-kind-<?php echo $rs->code; ?>"
                  id="chk-pd-kind-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-gender-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Gender</h4>
      </div>
      <div class="modal-body" id="pd-gender-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_genders)) : ?>
      <?php foreach($product_genders as $rs) : ?>
        <?php $se = isset($pdGender[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-gender"
                  name="chk-pd-gender-<?php echo $rs->code; ?>"
                  id="chk-pd-gender-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-sport-type-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Sport Type</h4>
      </div>
      <div class="modal-body" id="pd-sport-type-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if(! empty($product_sport_types)) : ?>
      <?php foreach($product_sport_types as $rs) : ?>
        <?php $se = isset($pdSportType[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-pd-sport-type"
								name="chk-pd-sport-type-<?php echo $rs->code; ?>"
								id="chk-pd-sport-type-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-collection-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Clbu/Collection</h4>
      </div>
      <div class="modal-body" id="pd-subgroup-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_collections)) : ?>
      <?php foreach($product_collections as $rs) : ?>
        <?php $se = isset($pdCollection[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-collection"
                  name="chk-pd-collection-<?php echo $rs->code; ?>"
                  id="chk-pd-collection-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-brand-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Brands</h4>
      </div>
      <div class="modal-body" id="pd-brand-body">
        <div class="row" style="margin-left:0px;">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">

    <?php if( ! empty($product_brands)) : ?>
      <?php foreach($product_brands as $rs) : ?>
        <?php $se = isset($pdBrand[$rs->code]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
								class="ace chk-pd-brand"
								name="chk-pd-brand-<?php echo $rs->code; ?>"
								id="chk-pd-brand-<?php echo $rs->code; ?>"
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


<div class="modal fade" id="pd-year-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:400px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">Product Year</h4>
      </div>
      <div class="modal-body" id="pd-year-body">
        <div class="row">
          <div class="col-sm-12" style="height:60vh; overflow:auto;">
    <?php if( ! empty($product_years)) : ?>
      <?php foreach($product_years as $rs) : ?>
        <?php $se = isset($pdYear[$rs->year]) ? 'checked' : ''; ?>
              <label class="display-block">
                <input type="checkbox"
                  class=" ace chk-pd-year"
                  name="chk-pd-year-<?php echo $rs->year; ?>"
                  id="chk-pd-year-<?php echo $rs->year; ?>"
                  value="<?php echo $rs->year; ?>" <?php echo $se; ?> />
                  <span class="lbl">&nbsp;&nbsp;<?php echo $rs->year; ?></span>
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
