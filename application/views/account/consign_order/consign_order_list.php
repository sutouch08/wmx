<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-lg-6 col-md-6 col-sm-8 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 padding-5 text-right">
    <?php if( $this->pm->can_add ) : ?>
      <button type="button" class="btn btn-xs btn-success top-btn" onclick="goAdd()">
        <i class="fa fa-plus"></i> สร้างใหม่
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

    <div class="col-lg-1-harf col-md-3 col-sm-3 col-xs-6 padding-5">
      <label>โซน</label>
      <input type="text" class="form-control input-sm search text-center" name="zone" id="zone" value="<?php echo $zone; ?>" />
    </div>

    <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
      <label>อ้างอิง</label>
      <input type="text" class="form-control input-sm search text-center" name="ref_code" id="ref_code" value="<?php echo $ref_code; ?>" />
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
      <select class="form-control input-sm" name="status" id="status" onchange="getSearch()">
        <option value="all" <?php echo is_selected($status, 'all'); ?>>ทั้งหมด</option>
        <option value="0" <?php echo is_selected($status, '0'); ?>>ยังไม่บันทึก</option>
        <option value="1" <?php echo is_selected($status, 1); ?>>บันทึกแล้ว</option>
        <option value="2" <?php echo is_selected($status, 2); ?>>ยกเลิก</option>
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
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <p class="pull-right top-p">
      <span>ว่าง</span><span class="margin-right-15"> = ปกติ</span>
      <span class="blue">NC</span><span class="margin-right-15"> = ยังไม่บันทึก</span>
      <span class="red">CN</span><span class=""> = ยกเลิก</span>
    </p>
  </div>
</div>

<div class="row">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
     <table class="table table-striped border-1 table-padding-3" style="min-width:1080px;">
       <thead>
         <tr class="font-size-11">
           <th class="fix-width-100"></th>
           <th class="fix-width-40 text-center">#</th>
           <th class="fix-width-80">วันที่</th>
           <th class="fix-width-100">เลขที่เอกสาร</th>
           <th class="fix-width-40 text-center">สถานะ</th>
           <th class="min-width-200">ลูกค้า</th>
           <th class="min-width-200">โซน</th>
           <th class="fix-width-100 text-right">มูลค่า</th>
           <th class="fix-width-100">อ้างอิง</th>
         </tr>
       </thead>
       <tbody>
<?php if(!empty($docs)) : ?>
<?php   $no = $this->uri->segment(4) + 1; ?>
<?php   foreach($docs as $rs) : ?>
        <tr class="font-size-11" id="row-<?php echo $rs->code; ?>">
          <td class="middle">
            <button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
            <?php if($rs->status == 0 && $this->pm->can_edit) : ?>
              <button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
            <?php endif; ?>
            <?php if($rs->status == 0 && $this->pm->can_delete) : ?>
              <button type="button" class="btn btn-minier btn-danger" onclick="getDelete('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
            <?php endif; ?>
          </td>
          <td class="middle text-center no">
            <?php echo $no; ?>
          </td>
          <td class="middle">
            <?php echo thai_date($rs->date_add, FALSE); ?>
          </td>
          <td class="middle">
            <?php echo $rs->code; ?>
          </td>
          <td class="middle text-center">
            <?php if($rs->status == 2) : ?>
              <span class="red">CN</span>
            <?php endif; ?>
            <?php if($rs->status == 0) : ?>
              <span class="blue">NC</span>
            <?php endif; ?>
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-label font-size-11" value="<?php echo $rs->customer_name; ?>" readonly />
          </td>
          <td class="middle">
            <input type="text" class="form-control input-sm text-label font-size-11" value="<?php echo $rs->zone_name; ?>" readonly />
          </td>
          <td class="middle text-right">
            <?php echo number($rs->amount, 2); ?>
          </td>
          <td class="middle">
            <?php echo $rs->is_api ? $rs->pos_ref : $rs->ref_code; ?>
          </td>
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
<script src="<?php echo base_url(); ?>scripts/account/consign_order/consign_order.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
