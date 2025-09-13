</div><!-- /.page-content -->
</div><!-- /.main-content-inner -->
</div><!-- /.main-content -->
</div><!-- /.main-container -->

<!-- page specific plugin scripts -->

<!-- ace scripts -->
<script type="text/javascript">
window.jQuery || document.write("<script src='<?php echo base_url(); ?>assets/js/jquery.js'>"+"<"+"/script>");
</script>

<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar.js"></script>
<script src="<?php echo base_url(); ?>assets/js/ace/ace.sidebar-scroll-1.js"></script>
<script src="<?php echo base_url(); ?>assets/js/ace/ace.submenu-hover.js"></script>
<script src="<?php echo base_url(); ?>scripts/beep.js"></script>
<script src="<?php echo base_url(); ?>scripts/template.js?v=2<?php echo date('Ymd'); ?>"></script>
<script>

function changeUserPwd(uname)
{
	window.location.href = BASE_URL +'user_pwd/change/'+uname;
}
</script>

</body>

</html>
