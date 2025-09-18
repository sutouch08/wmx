<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
</div>
<hr/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Doc No.</label>
      <input type="text" class="form-control input-sm text-center search" name="code" value="<?php echo $code; ?>" />
    </div>
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Ref No.</label>
      <input type="text" class="form-control input-sm text-center search" name="reference" value="<?php echo $reference; ?>" />
    </div>
    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>From Warehouse</label>
      <select class="width-100 filter" name="from_warehouse" id="from-warehouse">
        <option value="all">ทั้งหมด</option>
        <?php echo select_warehouse($from_warehouse); ?>
      </select>
    </div>
    <div class="col-lg-1-harf col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
      <label>Zone.</label>
      <input type="text" class="form-control input-sm padding-5 serach" name="zone" value="<?php echo $zone; ?>" />
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label>Status</label>
      <select class="form-control input-sm" name="status" onchange="getSearch()">
        <option value="all">ทั้งหมด</option>
        <option value="P" <?php echo is_selected('P', $status); ?>>Pending</option>
        <option value="O" <?php echo is_selected('O', $status); ?>>In progress</option>
        <option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
        <option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
      </select>
    </div>

    <div class="col-lg-1-harf col-md-2-harf col-sm-3 col-xs-6 padding-5">
      <label>Doc Date</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
      </div>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
    </div>
    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
      <label class="display-block not-show">btn</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
    </div>
  </div>

  <input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1" style="min-width:1200px;">
      <thead>
        <tr class="font-size-11">
          <th class="fix-width-100"></th>
          <th class="fix-width-40 text-center">#</th>
          <th class="fix-width-100 text-center">Date</th>
          <th class="fix-width-100">Doc No.</th>
          <th class="fix-width-80 text-center">สถานะ</th>
          <th class="fix-width-100">Ref No.</th>
          <th class="fix-width-100">Fultillment No.</th>
          <th class="fix-width-100">From Whs</th>
          <th class="fix-width-100">To Whs</th>
					<th class="min-width-150">To Zone</th>
          <th class="fix-width-80 text-right">Qty</th>
          <th class="fix-width-100">User</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment($this->segment) + 1; ?>
<?php   foreach($docs as $rs) : ?>
          <tr class="font-size-11" id="row-<?php echo $rs->id; ?>">
            <td class="middle">
              <button type="button" class="btn btn-minier btn-info" title="รายละเอียด" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
          <?php if(($this->pm->can_delete && $rs->status != 'D') OR ($rs->status != 'D' && $this->_SuperAdmin)) : ?>
              <button type="button" class="btn btn-minier btn-danger" title="ยกเลิก" onclick="goDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
          <?php endif; ?>
          <?php if($this->pm->can_edit && ($rs->status == 'P' OR $rs->status == 'O')) : ?>
              <button type="button" class="btn btn-minier btn-purple" title="รับเข้า" onclick="goProcess('<?php echo $rs->code; ?>')"><i class="fa fa-check"></i></button>
          <?php endif; ?>
            </td>
            <td class="middle text-center no"><?php echo $no; ?></td>
            <td class="middle text-center"><?php echo thai_date($rs->date_add, FALSE); ?></td>
            <td class="middle"> <?php echo $rs->code; ?></td>
            <td class="middle text-center">
              <?php if($rs->status == 'C') : ?>
                <span class="green">Closed</span>
              <?php elseif($rs->status == 'O') : ?>
                <span class="purple">In progress</span>
              <?php elseif($rs->status == 'D') : ?>
                <span class="red">Canceled</span>
              <?php else : ?>
                <span class="orange">Pending</span>
              <?php endif; ?>
            </td>
            <td class="middle"><?php echo $rs->reference; ?></td>
            <td class="middle"><?php echo $rs->fulfillment_code; ?></td>
            <td class="middle"><?php echo $rs->from_warehouse; ?></td>
            <td class="middle"><?php echo $rs->warehouse_code; ?></td>
						<td class="middle"><?php echo $rs->zone_name; ?></td>
            <td class="middle text-right"><?php echo number($rs->total_qty); ?></td>
            <td class="middle"><?php echo $rs->user; ?></td>
          </tr>
<?php     $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="10" class="text-center">--- ไม่พบรายการ ---</td>
        </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script>
  $('#from-warehouse').select2();
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/receive_product/receive_product.js?v=<?php echo date('Ymd');?>"></script>
<?php $this->load->view('include/footer'); ?>
