<?php
// Check if user is Admin
if (is_admin()){
?>
<div id="admin_panel">
	<header>
		<h1>Admin-Panel</h1>
	</header>
	<div class="panel-content">
		<div class="export-participants">
			<span class="btn btn-large" onclick="return popup('admin/index.php?p=exportparticipants&aa_inst_id=<?php echo $session->instance['aa_inst_id']; ?>');"><i class="icon-download-alt"></i> <?php __p("Export participants")?></span>
		</div>
		<div class="select-winner">
			<span class="btn btn-large" onclick="return popup('admin/index.php?p=getwinner&aa_inst_id=<?php echo $session->instance['aa_inst_id']; ?>');"><i class="icon-gift"></i> <?php __p("Select winner")?></span>
		</div>

		<?php if ($session->config['admin_reset']['value']) {?>
			<div class="reset-lottery">
				<span class="btn btn-large" onclick="return popup('admin/index.php?p=reset&aa_inst_id=<?php echo $session->instance['aa_inst_id']; ?>');"><i class="icon-repeat"></i> <?php __p("Reset")?></span>
			</div>
		<?php }?>
	</div>
	
	<footer>
		<div class="configure-app">
			<span class="btn btn-large btn-primary" onclick="top.location.href='/manager/index.php/user/apps/';"><i class="icon-cog icon-white"></i> <?php __p("Configure App")?></span>
		</div>
	</footer>
</div>
	
	<script type="text/javascript">
	function popup (url) {
	 fenster = window.open(url, "Admin panel", "width=900,height=650,resizable=no");
	 fenster.focus();
	 return false;
	}
	
	</script>

<?php 
}
?>
