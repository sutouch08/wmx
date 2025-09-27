<?php $this->load->view('include/header_mobile'); ?>
<?php $this->load->view('mobile/pick_list/filter'); ?>
<div class="row">
  <div class="page-wrap">
    <?php $no = 1; ?>
    <?php if( ! empty($data)) : ?>
      <?php foreach($data as $rs) : ?>
				<?php $channels = empty($rs->channels_code) ?  "" : $this->channels_model->get_name($rs->channels_code); ?>
        <div class="list-block" onclick="goProcess('<?php echo $rs->code; ?>')">
          <div class="list-link" >
            <div class="list-link-inner">
              <div class="margin-right-10 no <?php echo status_color($rs->status); ?>"><?php echo $no; ?></div>
              <div class="display-inline-block">
                <span class="display-block font-size-11"><?php echo $rs->code; ?> [<?php echo status_text($rs->status); ?>] - [<?php echo thai_date($rs->date_add, FALSE, '/'); ?>]</span>
                <span class="display-block font-size-11"><?php echo $channels; ?> &nbsp;&nbsp;&nbsp;[<?php echo $this->zone_model->get_name($rs->zone_code); ?>]</span>
              </div>
            </div>
            <i class="fa fa-angle-right fa-2x light-grey"></i>
          </div>
        </div>
        <?php $no++; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div class="paginater">
	<?php echo $this->pagination->create_links(); ?>
</div>

<?php $this->load->view('include/pg_footer_menu'); ?>

<script src="<?php echo base_url(); ?>scripts/mobile/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer_mobile'); ?>
