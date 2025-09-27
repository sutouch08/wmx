<div class="page-wrap move-out" id="trans-box">
  <div class="nav-title nav-title-center">
  	<a onclick="toggleTransBox()"><i class="fa fa-angle-left fa-2x"></i></a>
  	<div class="font-size-18 text-center">Transection</div>
  </div>
  <div class="" id="trans-list">
    <?php  if( ! empty($transection)) : ?>
      <?php $no = 1; ?>
      <?php   foreach($transection as $rs) : ?>
      <div class="list-block trans-item" id="trans-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>">
        <div class="list-link">
          <div class="list-link-inner width-100">
            <div class="display-inline-block width-100">
              <span class="display-block font-size-12"><?php echo $rs->product_code; ?></span>
              <span class="display-block font-size-11"><?php echo $rs->product_name; ?></span>
              <span class="float-left font-size-11 width-15">จำนวน:</span>
              <input type="text" class="float-left font-size-11 text-label padding-0 width-25" value="<?php echo number($rs->qty); ?>" readonly />
              <span class="float-left font-size-11 width-15">โซน:</span>
              <input type="text" class="float-left font-size-11 text-label padding-0 width-45" value="<?php echo $rs->zone_code; ?>" readonly />
            </div>
          </div>
        </div>
      </div>

<?php $no++; ?>
<?php endforeach; ?>
<?php endif; ?>

  </div>
</div>
<script id="trans-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    {{#if nodata}}

    {{else}}
    <div class="list-block trans-item" id="trans-{{id}}" data-id="{{id}}">
      <div class="list-link">
        <div class="list-link-inner width-100">
          <div class="display-inline-block width-100">
            <span class="display-block font-size-12">{{product_code}}</span>
            <span class="display-block font-size-11">{{product_name}}</span>
            <span class="float-left font-size-11 width-15">จำนวน:</span>
            <input type="text" class="float-left font-size-11 text-label padding-0 width-25" value="{{qty}}" readonly />
            <span class="float-left font-size-11 width-15">โซน:</span>
            <input type="text" class="float-left font-size-11 text-label padding-0 width-45" value="{{zone_code}}" readonly />
            <span class="float-left font-size-11 width-50">User:  {{user}}</span>
            <span class="float-left font-size-11 width-50">วันที่:  {{date_upd}}</span>
          </div>
          <button type="button" class="btn btn-minier btn-danger btn-trans-del"
            onclick="removeTransection({{id}}, {{qty}}, '{{product_code}}', '{{zone_code}}')">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    </div>
    {{/if}}
  {{/each}}
</script>
