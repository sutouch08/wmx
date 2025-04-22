<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
		<h3 class="title">
			<?php echo $this->title; ?>
		</h3>
	</div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" id="item_code" name="item_code"  value="<?php echo $item_code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" id="zone_code" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>
  <div class="col-lg-1 col-md-1-harf col-sm-1-harfcol-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harfcol-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
<div class="col-lg-1 col-md-1-harf col-sm-1-harfcol-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-purple btn-block" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Download</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<form id="exportFrom" method="post" action="<?php echo $this->home; ?>/export">
  <input type="hidden" id="item" name="item">
  <input type="hidden" id="zone" name="zone">
  <input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped table-bordered" style="min-width:840px;">
      <tr>
        <th class="fix-width-40 text-center">#</th>
        <th class="min-width-200">สินค้า</th>
    		<th class="fix-width-200">โซน</th>
        <th class="fix-width-100 text-center">ในโซน</th>
				<th class="fix-width-100 text-center">Buffer</th>
				<th class="fix-width-100 text-center">Cancel</th>
				<th class="fix-width-100 text-center">รวม</th>
      </tr>
      <tbody>
    <?php if( ! empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
			<?php $bQty = get_buffer_qty_by_product_and_zone($rs->product_code, $rs->zone_code); ?>
			<?php $cQty = get_cancle_qty_by_product_and_zone($rs->product_code, $rs->zone_code); ?>
      <tr class="font-size-12">
        <td class="text-center"><?php echo $no; ?></td>
        <td><?php echo $rs->product_code; ?></td>
    		<td class=""><?php echo $rs->zone_code; ?></td>
        <td class="text-center"> <?php echo number($rs->qty); ?></td>
    		<td class="text-center"> <?php echo number($bQty); ?></td>
				<td class="text-center"> <?php echo number($cQty); ?></td>
				<td class="text-center"> <?php echo number($rs->qty + $bQty + $cQty); ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="7" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>

  function getSearch()
  {
    $('#searchForm').submit();
  }

  function clearFilter()
  {
    $.get(BASE_URL + 'inventory/stock/clear_filter', function(){
      window.location.href = "<?php echo $this->home; ?>";
    });
  }

  $('.search').keyup(function(e){
    if(e.keyCode == 13){
      var item = $('#item_code').val();
      var zone = $('#zone_code').val();
      if(item.length > 0 || zone.length > 0){
        getSearch();
      }
    }
  })


  function doExport(){
    var item = $('#item_code').val();
    var zone = $('#zone_code').val();
		var system = $('#show_system').val();
    var token = $('#token').val();
    if(item.length > 0 || zone.length > 0)
    {
      $('#item').val(item);
      $('#zone').val(zone);
			$('#system').val(system);
      get_download(token);
      $('#exportFrom').submit();
    }
  }
</script>

<?php $this->load->view('include/footer'); ?>
