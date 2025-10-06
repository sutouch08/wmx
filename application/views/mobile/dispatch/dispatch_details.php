<div class="row">
  <div class="page-wrap" id="detail-table">
    <?php if( ! empty($details)) : ?>
      <?php $channels = get_channels_array(); ?>
      <?php $no = 1; ?>
  		<?php $totalQty = 0; ?>
  		<?php $totalShipped = 0; ?>
      <?php foreach($details as $rs) : ?>
        <?php $channels_name = empty($channels[$rs->channels_code]) ? NULL : $channels[$rs->channels_code]; ?>
        <div class="list-block dispatch-row" data-id="<?php echo $rs->id; ?>" id="dispatch-<?php echo $rs->id; ?>">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no" id="no-<?php echo $rs->id; ?>"><?php echo $no; ?></div>
							<div class="display-inline-block width-100">
								<span class="display-block font-size-12">Order : <?php echo $rs->order_code; ?> </span>
                <span class="display-block font-size-11">Ref : <?php echo $rs->reference; ?> </span>
                <span class="display-block font-size-11">Channels : <?php echo $channels_name; ?></span>

								<span class="float-left font-size-11 width-30">กล่อง [ทั้งหมด]:</span>
								<input type="number" class="float-left font-size-11 text-label padding-0 width-20"
								id="carton-qty-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo $rs->carton_qty; ?>" readonly/>

								<span class="float-left font-size-11 width-30">กล่อง [ยิงแล้ว]</span>
								<input type="number" class="float-left font-size-11 text-label padding-0 width-20"
								id="carton-shopped-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>"
								value="<?php echo $rs->carton_shipped; ?>" readonly/>
							</div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>


<script id="row-template" type="text/x-handlebarsTemplate">
  <div class="list-block dispatch-row" data-id="{{id}}" id="dispatch-{{id}}">
    <div class="list-link" >
      <div class="list-link-inner width-100">
        <div class="margin-right-10 no" id="no-{{id}}"></div>
        <div class="display-inline-block width-100">
          <span class="display-block font-size-12">Order : {{order_code}} </span>
          <span class="display-block font-size-11">Ref : {{reference}} </span>
          <span class="display-block font-size-11">Channels : {{channels}}</span>

          <span class="float-left font-size-11 width-30">กล่อง [ทั้งหมด]:</span>
          <input type="number" class="float-left font-size-11 text-label padding-0 width-20"
          id="carton-qty-{{id}}" value="{{carton_qty}}" readonly/>

          <span class="float-left font-size-11 width-30">กล่อง [ยิงแล้ว]</span>
          <input type="number" class="float-left font-size-11 text-label padding-0 width-20"
          id="carton-shopped-{{id}}" value="{{carton_shipped}}?>" readonly/>
        </div>
      </div>
    </div>
  </div>
</script>
