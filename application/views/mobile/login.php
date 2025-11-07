<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Login</title>
		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>assets/images/logo.png">
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/template.css" />
		<link rel="manifest" href="<?php echo base_url(); ?>manifest.json"/>
		<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
		<style>
			input.input-lg {
				border-radius: 5px !important;
			}

			#user-name {
				padding-left: 30px;
				padding-right: 35px;
			}

			#pwd {
				padding-left: 30px;
				padding-right: 35px;
			}

			#user-btn {
				position: absolute;
				top: 15px;
				right: 12px;
				color:#999;
				z-index: 2;
			}

			#pwd-btn {
				position: absolute;
				top: 15px;
				right: 12px;
				color:#999;
				z-index: 2;
			}
		</style>
	</head>

	<body>
		<div class="main-container">
			<div class="main-content">
				<div class="">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="login-container" style="padding-left:20px; padding-right:20px;">
							<div class="center" style="margin-top:30px;">
								<img src="<?php echo base_url(); ?>assets/images/logo.png" width="150px" />
							</div>

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main" style="border:solid 1px #ccc; border-radius:15px;">
											<h4 class="header blue lighter bigger text-center">Login to your account</h4>

											<div class="space-6"></div>

											<form>
												<fieldset>
													<label class="block clearfix">
														<span class="block input-icon input-icon-left">
															<i class="ace-icon fa fa-user" style="top:8px; bottom:8px; left:5px;"></i>
															<input type="text" name="user_name" id="user-name" class="form-control input-lg" placeholder="Username" autocomplete="off" required />
															<i id="user-btn" class="fa fa-times-circle fa-lg hide" onclick="clearUser()"></i>
														</span>
													</label>
													<div class="space-6"></div>
													<label class="block clearfix">
														<span class="block input-icon input-icon-left">
															<i class="ace-icon fa fa-lock" style="top:8px; bottom:8px; left:5px;"></i>
															<input type="password" name="password" id="pwd" class="form-control input-lg" placeholder="Password" required />
															<i id="pwd-btn" class="fa fa-eye fa-lg" onclick="showPwd()"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix hide">
														<label class="inline" id="rem-label">
															<input type="checkbox" name="remember" id="rem-box" class="ace" value="1" checked />
															<span class="lbl"> Remember Me</span>
														</label>
													</div>
													<div class="space"></div>
													<div class="clearfix">
														<button type="button" id="btn-login" class="btn btn-lg btn-primary btn-block" style="border-radius:5px;" onclick="doLogin()">
															<span class="bigger-110">Login</span>
														</button>
													</div>

													<div class="space-4"></div>
													<div class="clearfix">
														<div class="space-4"></div>
														<div class="space-4"></div>
														<p class="text-center red" id="err-message"></p>
													</div>
												</fieldset>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>

	<script>
		var BASE_URL = '<?php echo base_url(); ?>';

		$('#user-name').keyup(function(e) {
			if(e.keyCode === 13) {
				let pwd = $('#pwd').val().trim();

				if(pwd.length) {
					doLogin();
				}
				else {
					$('#pwd').focus();
				}
			}

			if($(this).val().length) {
				$('#user-btn').removeClass('hide');
			}
			else {
				$('#user-btn').addClass('hide');
			}
		})

		$('#pwd').keyup(function(e){
			if(e.keyCode === 13) {
				doLogin();
			}
		});

		function clearUser() {
			$('#user-name').val('');
			$('#user-btn').addClass('hide');
		}

		function showPwd() {
			var x = document.getElementById("pwd");
			var y = document.getElementById("pwd-btn");

			if(x.type === "password") {
				x.type = "text";
				y.classList.remove('fa-eye');
				y.classList.add('fa-eye-slash');
			}
			else {
				x.type = "password";
				y.classList.remove('fa-eye-slash');
				y.classList.add('fa-eye');
			}
		}


		function doLogin() {
			$('#user-name').clearError();
			$('#pwd').clearError();

			let uname = $('#user-name').val().trim();
			let pwd = $('#pwd').val().trim();
			let rem = $('#rem-box').is(':checked') ? 1 : 0;

			if(uname.length == 0) {
				$('#user-name').hasError();
				return false;
			}

			if(pwd.length == 0) {
				$('#pwd').hasError();
				return false;
			}

			if(uname.length && pwd.length) {
				load_in();

				$.ajax({
					url:BASE_URL + 'mobile/authentication/validate_credentials',
					type:'POST',
					cache:false,
					data:{
						'user_name' : uname,
						'password' : pwd,
						'remember' : rem
					},
					success:function(rs) {
						load_out();

						if(rs.trim() === "success") {
							window.location.href = BASE_URL + 'mobile/main';
						}
						else {
							$('#err-message').text(rs);
						}
					},
					error:function(rs) {
						load_out();
						$('#err-message').text(rs.responseText);
					}
				})
			}
		}
	</script>

	<script src="<?php echo base_url(); ?>scripts/template.js?v=<?php echo date('Ymd'); ?>"></script>
</html>
