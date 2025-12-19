<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/pick_list/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
				<?php $channels = empty($rs->channels_code) ?  "ไม่ระบุ" : $this->channels_model->get_name($rs->channels_code); ?>
        <div class="list-block" onclick="goProcess('<?php echo $rs->code; ?>')">
          <div class="list-link" >
            <div class="list-link-inner">
              <div class="margin-right-10 no <?php echo status_color($rs->status); ?>"><?php echo $no; ?></div>
              <div class="display-inline-block">
                <span class="display-block font-size-14"><?php echo $rs->code; ?> &nbsp;&nbsp; - &nbsp;&nbsp; <?php echo status_text($rs->status); ?></span>
                <span class="display-block font-size-11">วันที่ :  <?php echo thai_date($rs->date_add, FALSE, '/'); ?>&nbsp;&nbsp;&nbsp; ช่องทาง : <?php echo $channels; ?></span>
                <span class="display-block font-size-11">ต้นทาง :  <?php echo $rs->warehouse_code; ?>  &nbsp;&nbsp;&nbsp; ปลายทาง : <?php echo $this->zone_model->get_name($rs->zone_code); ?></span>
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

<?php $this->load->view('include/pg_footer_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
