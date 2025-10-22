<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 padding-5 text-right">
    <?php if( $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()">
        <i class="fa fa-plus"></i> Add New
      </button>
    <?php endif; ?>
  </div>
</div>
<hr/>

<form id="searchForm" method="post">
  <div class="row">
    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>เลขที่</label>
      <input type="text" class="form-control input-sm search text-center" name="code" id="code" value="<?php echo $code; ?>" autofocus />
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>ลูกค้า</label>
      <input type="text" class="form-control input-sm search text-center" name="customer" id="customer" value="<?php echo $customer; ?>" />
    </div>

    <div class="col-lg-4 col-md-4-harf col-sm-4-harf col-xs-12 padding-5">
      <label>คลัง</label>
      <select class="width-100 filter" name="warehouse" id="warehouse">
        <option value="all">ทั้งหมด</option>
        <?php echo select_consign_warehouse($warehouse); ?>
      </select>
    </div>

    <div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-12 padding-5">
      <label>User</label>
      <select class="width-100 filter" name="user" id="user">
        <option value="all">ทั้งหมด</option>
        <?php echo select_user($user); ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>วันที่</label>
      <div class="input-daterange input-group width-100">
        <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>">
        <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>">
      </div>
    </div>

    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>สถานะ</label>
      <select class="form-control input-sm filter" name="status">
        <option value="all">ทั้งหมด</option>
        <option value="P" <?php echo is_selected($status, 'P'); ?>>Draft</option>
        <option value="A" <?php echo is_selected($status, 'A'); ?>>Approval</option>
        <option value="C" <?php echo is_selected($status, 'C'); ?>>Closed</option>
        <option value="D" <?php echo is_selected($status, 'D'); ?>>Canceled</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>Export</label>
      <select class="form-control input-sm filter" name="is_exported">
        <option value="all">ทั้งหมด</option>
        <option value="N" <?php echo is_selected($is_exported, 'N'); ?>>No</option>
        <option value="Y" <?php echo is_selected($is_exported, 'Y'); ?>>Yes</option>
        <option value="E" <?php echo is_selected($is_exported, 'E'); ?>>Failed</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">search</label>
      <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getSearch()">ค้นหา</button>
    </div>

    <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
      <label class="display-block not-show">Reset</label>
      <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
    </div>
  </div>
</form>
<hr class="margin-top-15 margin-bottom-15"/>

<?php echo $this->pagination->create_links(); ?>
<div class="row">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
     <table class="table table-striped border-1" style="min-width:1340px;">
       <thead>
         <tr class="font-size-11">
           <th class="fix-width-100"></th>
           <th class="fix-width-40 text-center">#</th>
           <th class="fix-width-80">วันที่</th>
           <th class="fix-width-100">เลขที่</th>
           <th class="fix-width-60 text-center">สถานะ</th>
           <th class="fix-width-60 text-center">Export</th>
           <th class="fix-width-100">ERP No.</th>
           <th class="min-width-300">ลูกค้า</th>
           <th class="fix-width-200">คลัง</th>
           <th class="fix-width-80 text-right">จำนวน</th>
           <th class="fix-width-100 text-right">มูลค่า</th>
           <th class="fix-width-100">User</th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($docs)) : ?>
 <?php  $no = $this->uri->segment($this->segment) + 1; ?>
 <?php  $whsName = []; ?>
 <?php  foreach($docs as $rs) : ?>
  <?php  if(empty($whsName[$rs->warehouse_code])) : ?>
    <?php $whsName[$rs->warehouse_code] = warehouse_name($rs->warehouse_code); ?>
  <?php   endif; ?>
        <tr class="font-size-11 <?php echo status_color($rs->status); ?>" id="row-<?php echo $rs->code; ?>">
          <td class="middle">
            <button type="button" class="btn btn-minier btn-info" title="view detail" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
            <?php if(($rs->status == 'P' OR $rs->status == 'A') && $this->pm->can_edit) : ?>
              <button type="button" class="btn btn-minier btn-warning" title="edit" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
            <?php endif; ?>
            <?php if(($rs->status == 'P' OR $rs->status == 'A' OR ($rs->status == 'C' && $rs->is_exported != 'Y')) && $this->pm->can_delete) : ?>
              <button type="button" class="btn btn-minier btn-danger" title="cancel" onclick="confirmCancel('<?php echo $rs->code; ?>')"><i class="fa fa-times"></i></button>
            <?php endif; ?>
          </td>
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo thai_date($rs->date_add, FALSE); ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
          <td class="middle text-center"><?php echo status_text($rs->status); ?></td>
          <td class="middle text-center"><?php echo $rs->is_exported == 'Y' ? 'Yes' : ($rs->is_exported == 'E' ? 'Failed' : 'No'); ?></td>
          <td class="middle"><?php echo $rs->DocNum; ?></td>
          <td class="middle"><input type="text" class="form-control input-xs text-label font-size-11" value="<?php echo $rs->customer_code.' | '.$rs->customer_name; ?>" readonly /></td>
          <td class="middle"><input type="text" class="form-control input-xs text-label font-size-11" value="<?php echo $rs->warehouse_code. ' | '. $whsName[$rs->warehouse_code]; ?>" readonly /></td>
          <td class="middle text-right"><?php echo number($rs->TotalQty, 2); ?></td>
          <td class="middle text-right"><?php echo number($rs->DocTotal, 2); ?></td>
          <td class="middle"><?php echo $rs->user; ?></td>
        </tr>
<?php    $no++; ?>
<?php   endforeach; ?>
<?php else : ?>
        <tr>
          <td colspan="10" class="text-center">---- ไม่พบรายการ ----</td>
        </tr>
<?php endif; ?>
       </tbody>
     </table>
   </div>
 </div>
 <?php $this->load->view('cancle_modal'); ?>

<script>
  $('#warehouse').select2();
  $('#user').select2();
</script>

<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
