<?php $this->load->view('include/header'); ?>
	<div class="row">
	  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 text-center">
	    <h1>Hello! <?php echo $this->_user->uname; ?></h1>
	    <h5>Good to see you here</h5>
	  </div>
		<div class="divider"></div>
	</div>
	<div class="row">
		<?php if( ! empty($this->e)) : ?>
			<div class="alert alert-danger"><?php echo $this->e; ?></div>
		<?php endif; ?>	
	</div>
<?php $this->load->view('include/footer'); ?>
