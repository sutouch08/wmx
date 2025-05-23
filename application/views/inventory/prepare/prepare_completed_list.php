<?php $showZone = get_cookie('showZone') ? '' : 'hide'; ?>
<?php $showBtn  = get_cookie('showZone') ? 'hide' : '';  ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1">
      <thead>
        <tr><td colspan="6" align="center">รายการที่ครบแล้ว</td></tr>
        <tr>
          <th class="fix-width-150 middle text-center">บาร์โค้ด</th>
          <th class="min-width-300 middle">สินค้า</th>
          <th class="fix-width-100 middle text-center">จำนวน</th>
          <th class="fix-width-100 middle text-center">จัดแล้ว</th>
          <th class="fix-width-100 middle text-center">คงเหลือ</th>
          <th class="fix-width-200 text-right">จัดจากโซน</th>
        </tr>
      </thead>
      <tbody id="complete-table">

<?php  if(!empty($complete_details)) : ?>
<?php   foreach($complete_details as $rs) : ?>
    <tr class="font-size-12">
      <td class="middle text-center"><?php echo $rs->barcode; ?></td>
      <td class="middle"><b class="blue"><?php echo $rs->product_code; ?></b>  | <?php echo $rs->product_name; ?></td>
      <td class="middle text-center"><?php echo number($rs->qty); ?></td>
      <td class="middle text-center"><?php echo number($rs->prepared); ?></td>
      <td class="middle text-center"><?php echo number($rs->qty - $rs->prepared); ?></td>
      <td class="middle text-right">
        <button
          type="button"
          class="btn btn-default btn-xs btn-pop <?php echo $showBtn; ?>"
          data-container="body"
          data-toggle="popover"
          data-placement="left"
          data-trigger="focus"
          data-content="<?php echo $rs->from_zone; ?>"
          data-original-title=""
          title="">
          จากโซน
        </button>
        <span class="zoneLabel <?php echo $showZone; ?>" style="display:inline-block;">
            <?php echo $rs->from_zone; ?>
        </span>
        <button type="button" class="btn btn-minier btn-danger"
        onclick="removeBuffer('<?php echo $order->code; ?>', '<?php echo $rs->product_code; ?>', '<?php echo $rs->id; ?>')">
          <i class="fa fa-trash"></i>
        </button>
      </td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>

        </tbody>
      </table>
    </div>
  </div>
