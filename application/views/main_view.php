<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
		<h1>Hello! <?php echo get_cookie('displayName'); ?></h1>
		<h5>Good to see you here</h5>
	</div>
	<div class="divider-hidden"></div>
	<div class="divider"></div>

	<div class="col-xs-12 padding-5 text-center visible-xs">
		<button type="button" class="btn btn-lg btn-white btn-primary" onclick="goTo('mobile/main')"><i class="fa fa-mobile"></i>&nbsp; Mobile Version</button>
	</div>
</div>



<?php $this->load->view('include/footer'); ?>
