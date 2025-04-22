<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
		<h3 class="title" ><?php echo $this->title; ?></h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	</div>
</div>
<hr class="padding-5" />

<div class="row">
    <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>เลขที่เอกสาร</label>
        <input type="text" class="form-control input-sm text-center" value="<?php echo $doc->code; ?>" disabled />
    </div>
		<div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-6 padding-5">
    	<label>วันที่</label>
      <input type="text" class="form-control input-sm text-center" id="date_add" value="<?php echo thai_date($doc->date_add) ?>" readonly disabled/>
    </div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>อ้างถึง</label>
			<input type="text" class="form-control input-sm text-center" id="reference" value="<?php echo $doc->reference; ?>" disabled />
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm" id="zone" value="<?php echo $doc->from_zone; ?>" disabled />
		</div>
		<div class="col-lg-3-harf col-md-5 col-sm-4 col-xs-12 padding-5">
			<label class="not-show">โซนแปรสภาพ</label>
			<input type="text" class="form-control input-sm" id="zoneName" value="<?php echo $doc->zone_name; ?>" disabled />
		</div>
		<?php $status = $doc->status == '2' ? 'Canceled' : ($doc->status == '1' ? 'Saved' : 'Draft'); ?>
		<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สถานะ</label>
			<input type="text" class="form-control input-sm text-center" value="<?php echo $status; ?>" disabled />
		</div>

		<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>User</label>
			<input type="text" class="form-control input-sm" id="user" value="<?php echo $doc->user; ?>" disabled />
		</div>
		<div class="col-lg-10 col-md-8-harf col-sm-8-harf col-xs-12 padding-5">
	   	<label>หมายเหตุ</label>
	    <input type="text" class="form-control input-sm" id="remark" placeholder="ระบุหมายเหตุเอกสาร (ถ้ามี)" value="<?php echo $doc->remark; ?>" disabled/>
	  </div>

		<?php if($doc->status == 2) : ?>
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
		   	<label>เหตุผลในการยกเลิก</label>
		    <input type="text" class="form-control input-sm" id="remark" value="<?php echo $doc->cancle_reason; ?>" disabled/>
		  </div>
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
		   	<label>ยกเลิกโดย</label>
		    <input type="text" class="form-control input-sm"  value="<?php echo $doc->cancle_user; ?>" disabled/>
		  </div>
		<?php endif; ?>

    <input type="hidden" id="code" value="<?php echo $doc->code; ?>" />
</div>

<hr class="margin-top-15 margin-bottom-15 padding-5"/>

<?php
if($doc->status == 2)
{
  $this->load->view('cancle_watermark');
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p class="pull-right top-p">
		<?php if(! empty($doc->issue_code) OR ! empty($doc->receive_code)) : ?>
			<span class="red">** เอกสารเข้าระบบ SAP แล้วไม่สามารถแก้ไขได้</span>
		<?php endif; ?>
		</p>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:990px;">
      <thead>
        <tr>
          <th class="fix-width-50 text-center">ลำดับ</th>
          <th class="fix-width-250">รหัสสินค้า</th>
          <th class="min-width-300">สินค้า</th>
          <th class="fix-width-10 text-center">จำนวน</th>
        </tr>
      </thead>
      <tbody id="detail-table">
<?php if(!empty($details)) : ?>
<?php   $no = 1;    ?>
<?php 	$total = 0; ?>
<?php   foreach($details as $rs) : ?>
      <tr class="font-size-12 rox" id="row-<?php echo $rs->id; ?>">
        <td class="middle text-center no">
          <?php echo $no; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_code; ?>
        </td>
        <td class="middle">
          <?php echo $rs->product_name; ?>
        </td>

        <td class="middle text-center">
          <?php echo number($rs->qty); ?>
        </td>
      </tr>
<?php     $no++; ?>
<?php 		$total += $rs->qty; ?>
<?php   endforeach; ?>
			<tr>
				<td colspan="3" class="middle text-right">รวม</td>
				<td class="middle text-center"><?php echo number($total); ?></td>
			</tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform.js"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/adjust_transform/adjust_transform_add.js"></script>
<?php $this->load->view('include/footer'); ?>
