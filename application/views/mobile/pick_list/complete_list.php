<div class="page-wrap move-out" id="complete-box">
  <div class="nav-title nav-title-center">
  	<a onclick="toggleCompleteBox()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center">รายการที่ครบแล้ว</div>
  </div>
  <div id="complete-list">
    <?php  if( ! empty($complete)) : ?>
      <?php $no = 1; ?>
      <?php   foreach($complete as $rs) : ?>
        <div class="list-block complete-item" id="complete-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
          <div class="list-link">
            <div class="list-link-inner width-100">
              <div class="display-inline-block width-100">
                <span class="display-block font-size-12"><?php echo $rs->product_code; ?></span>
                <span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
                <span class="float-left font-size-11 width-20">จำนวน:</span>
                <input type="text" class="float-left font-size-11 text-label padding-0 width-30"
                id="release-qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
                value="<?php echo number($rs->release_qty); ?>" readonly/>
                <span class="float-left font-size-11 width-20">จัดแล้ว:</span>
                <input type="text" class="float-left font-size-11 text-label padding-0 width-30 picked-qty"
                id="pick-qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
                value="<?php echo number($rs->pick_qty); ?>" readonly/>
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>


<script id="complete-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}

    {{else}}
      <div class="list-block complete-item" id="complete-{{id}}" data-id="{{id}}">
        <div class="list-link">
          <div class="list-link-inner width-100">
            <div class="display-inline-block width-100">
              <span class="display-block font-size-12">{{product_code}}></span>
              <span class="display-block font-size-11">{{product_name}}</span>
              <span class="float-left font-size-11 width-20">จำนวน:</span>
              <input type="text" class="float-left font-size-11 text-label padding-0 width-30"
              id="release-qty-{{id}}" data-id="{{id}}"
              value="{{releaseQty}}" readonly/>
              <span class="float-left font-size-11 width-20">จัดแล้ว:</span>
              <input type="text" class="float-left font-size-11 text-label padding-0 width-30 picked-qty"
              id="pick-qty-{{id}}" data-id="{{id}}"
              value="{{pickQtty}}" readonly/>
            </div>
          </div>
        </div>
      </div>
    {{/if}}
  {{/each}}
</script>


<script id="complete-row-template" type="text/x-handlebarsTemplate">
<div class="list-block complete-item" id="complete-{{id}}" data-id="{{id}}">
  <div class="list-link">
    <div class="list-link-inner width-100">
      <div class="display-inline-block width-100">
        <span class="display-block font-size-12">{{product_code}}</span>
        <span class="display-block font-size-11">{{product_name}}</span>
        <span class="float-left font-size-11 width-20">จำนวน:</span>
        <input type="text" class="float-left font-size-11 text-label padding-0 width-30"
        id="release-qty-{{id}}" data-id="{{id}}"
        value="{{releaseQty}}" readonly/>
        <span class="float-left font-size-11 width-20">จัดแล้ว:</span>
        <input type="text" class="float-left font-size-11 text-label padding-0 width-30 picked-qty"
        id="pick-qty-{{id}}" data-id="{{id}}"
        value="{{pickQtty}}" readonly/>
      </div>
    </div>
  </div>
</div>
</script>
