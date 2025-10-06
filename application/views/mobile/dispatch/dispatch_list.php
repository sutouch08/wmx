<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/dispatch/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
				<?php $channels = empty($rs->channels_code) ?  "ไม่ระบุ" : $this->channels_model->get_name($rs->channels_code); ?>
        <?php $func = $rs->status == 'P' ? "goEdit('{$rs->code}')" : "viewDetail('{$rs->code}')"; ?>
        <div class="list-block" onclick="<?php echo $func; ?>">
          <div class="list-link" >
            <div class="list-link-inner width-100">
              <div class="margin-right-10 no <?php echo status_color($rs->status); ?>"><?php echo $no; ?></div>
              <div class="width-100">
                <span class="display-block font-size-14"><?php echo $rs->code; ?> &nbsp;&nbsp; - &nbsp;&nbsp; <?php echo status_text($rs->status); ?></span>
                <span class="display-block font-size-11">
                  <span class="float-left width-50">วันที่ :  <?php echo thai_date($rs->date_add, FALSE, '/'); ?></span>
                  <span class="float-left width-50">ช่องทาง : <?php echo $channels; ?></span>
                </span>
                <span class="display-block font-size-11">
                  <span class="float-left width-50">ทะเบียนรถ :  <?php echo $rs->plate_no; ?></span>
                  <span class="float-left width-50">ผู้จัดส่ง : <?php echo $rs->sender_name; ?></span>
                </span>
                <span class="display-block font-size-11">User : <?php echo $rs->user; ?></span>
              </div>
            </div>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="paginater">
  <div class="paginater-toggle"><i class="fa fa-angle-up fa-lg"></i></div>
	<?php echo $this->pagination->create_links(); ?>
</div>

<?php $this->load->view('mobile/dispatch/list_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/mobile/dispatch/dispatch.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
