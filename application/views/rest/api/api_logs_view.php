<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2-harf col-xs-6 padding-5">
		<label>เลขที่/รหัส</label>
		<input type="text" class="form-control input-sm search-box" name="code" value="<?php echo $code; ?>"/>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<select class="width-100 filter" name="status">
			<option value="all">All</option>
			<option value="success" <?php echo is_selected('success', $status); ?>>Success</option>
			<option value="failed" <?php echo is_selected('failed', $status); ?>>Failed</option>
			<option value="test" <?php echo is_selected('test', $status); ?>>Test</option>
		</select>
	</div>

	<div class="col-lg-2-harf col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
    <label>Type</label>
    <select class="width-100 filter" name="type" id="type">
			<option value="all">ทั้งหมด</option>
			<option value="INT10" <?php echo is_selected('INT10', $type);?>>INT10 | (Products Master)</option>
			<option value="INT11" <?php echo is_selected('INT11', $type);?>>INT11 | (Price List)</option>
			<option value="INT17" <?php echo is_selected('INT17', $type);?>>INT17 | (Adjust Stock)</option>
			<option value="INT20" <?php echo is_selected('INT20', $type); ?>>INT20 | (Orders)</option>
			<option value="INT21" <?php echo is_selected('INT21', $type); ?>>INT21 | (WH-Transaction) </option>
			<option value="ADD23" <?php echo is_selected('ADD23', $type); ?>>ADD23 | (Purchase Order)</option>
			<option value="ADD24" <?php echo is_selected('ADD24', $type); ?>>ADD24 | (Goods Receipt PO)</option>
			<option value="ADD90" <?php echo is_selected('ADD90', $type); ?>>ADD90 | (Return Order)</option>
			<option value="ADD91" <?php echo is_selected('ADD91', $type); ?>>ADD91 | (Return Receipt)</option>
			<option value="ADD122" <?php echo is_selected('ADD122', $type); ?>>ADD122 | (Customer Master)</option>
			<option value="ADD123" <?php echo is_selected('ADD123', $type); ?>>ADD123 | (Warehouse Master)</option>	
		</select>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Action</label>
		<select class="width-100 filter" name="action">
			<option value="all">All</option>
			<option value="create" <?php echo is_selected('create', $action); ?>>CREATE</option>
			<option value="update" <?php echo is_selected('update', $action); ?>>UPDATE</option>
			<option value="cancel" <?php echo is_selected('cancel', $action); ?>>CANCEL</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
    <label>Date</label>
    <div class="input-daterange input-group width-100">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped border-1" style="min-width:1080px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-50"></th>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">วันที่</th>
					<th class="fix-width-200 middle">เลขที่/รหัส</th>
					<th class="fix-width-80 middle text-center">Type</th>
					<th class="fix-width-80 middle text-center">Action</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="fix-width-250 middle">API Path</th>
					<th class="min-width-150 middle">Message</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($logs)) : ?>
          <?php $no = $this->uri->segment(5) + 1; ?>
          <?php foreach($logs as $rs) : ?>
            <tr class="font-size-11">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i></button>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE, '/'); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle text-center"><?php echo $rs->type; ?></td>
							<td class="middle text-center"><?php echo $rs->action; ?></td>
              <td class="middle text-center"><?php echo $rs->status; ?></td>
							<td class="middle"><?php echo $rs->api_path; ?></td>
              <td class="middle"><?php echo $rs->message; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	$('#type').select2();

	$("#fromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#toDate").datepicker("option", "minDate", ds);
		}
	});

	$("#toDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#fromDate").datepicker("option", "maxDate", ds);
		}
	});

	function viewDetail(id) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";

		var target  = HOME + '/view_detail/'+id+'?nomenu';
	 	window.open(target, '_blank', prop);
	}
</script>


<?php $this->load->view('include/footer'); ?>
