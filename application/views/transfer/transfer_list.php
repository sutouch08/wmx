<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-sm btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
			<label>ERP No.</label>
			<input type="text" class="form-control input-sm search" name="doc_num"  value="<?php echo $doc_num; ?>" />
		</div>

		<div class="col-lg-4 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
			<label>คลังปลายทาง</label>
			<select class="width-100 filter" name="to_warehouse" id="to-warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_warehouse($to_warehouse); ?>
			</select>
		</div>

		<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
			<label>พนักงาน</label>
			<select class="width-100 filter" name="user" id="user">
				<option value="all">ทั้งหมด</option>
				<?php echo select_user($user); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm filter" name="status">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Draft</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-2 col-xs-3 padding-5">
			<label>Exported</label>
			<select class="form-control input-sm filter" name="is_export">
				<option value="all">ทั้งหมด</option>
				<option value="1" <?php echo is_selected('1', $is_export); ?>>Yes</option>
				<option value="0" <?php echo is_selected('0', $is_export); ?>>No</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-daterange input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label class="not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1210px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">ลำดับ</th>
					<th class="fix-width-80 middle text-center">วันที่</th>
					<th class="fix-width-100 middle">เลขที่เอกสาร</th>
					<th class="fix-width-80 middle text-center">สถานะ</th>
					<th class="fix-width-80 middle text-center">Exported</th>
					<th class="fix-width-100 middle text-center">ERP No.</th>
					<th class="fix-width-200 middle">ต้นทาง</th>
					<th class="min-width-300 middle">ปลายทาง</th>
					<th class="fix-width-100 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($data)) : ?>
          <?php $no = $this->uri->segment($this->segment) + 1; ?>
					<?php $whsName = []; ?>
					<?php $fromWhsName = warehouse_name(getConfig('DEFAULT_WAREHOUSE')); ?>
          <?php foreach($data as $rs) : ?>

						<?php if(empty($whsName[$rs->to_warehouse])) : ?>
							<?php $whsName[$rs->to_warehouse] = warehouse_name($rs->to_warehouse); ?>
						<?php endif; ?>

            <tr class="font-size-11" id="row-<?php echo $rs->code; ?>" style="<?php echo trStatusBgColor($rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" title="View Detail" onclick="goDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if(($rs->status == 'P' OR $rs->status == 'O') && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" title="Edit Document" onclick="edit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($rs->status != 'C' && $rs->status != 'D' && $this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" title="Cancel Document" onclick="goDelete('<?php echo $rs->code; ?>', <?php echo $rs->status; ?>)"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo trStatusText($rs->status); ?></td>
							<td class="middle text-center"><?php echo $rs->is_export == 1 ? 'Yes' : 'No'; ?></td>
							<td class="middle text-center"><?php echo $rs->DocNum; ?></td>
							<td class="middle"><?php echo $rs->from_warehouse.' | '.$fromWhsName; ?></td>
							<td class="middle"><?php echo $rs->to_warehouse.' | '.$whsName[$rs->to_warehouse]; ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
$('#from-warehouse').select2();
$('#to-warehouse').select2();
$('#user').select2();

function sendToSAP(code)
{
	load_in();
	$.ajax({
		url:HOME + 'export_transfer/' + code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
				$('#row-'+code).remove();
			}else{
				swal({
					title:'Error!',
					text:rs,
					type:'error'
				});
			}
		}
	});
}
</script>

<?php $this->load->view('include/footer'); ?>
