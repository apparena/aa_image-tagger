<?php
require_once(dirname(__FILE__).'/../init.php');
require_once(dirname(__FILE__).'/../include.php');

$fb_app_id=$session->instance['fb_app_id'];
$fb_app_secret=$session->instance['fb_app_secret'];

if($fb_app_id == false || $fb_app_secret == false)
{
  echo "Sorry, Session Expired";
  echo "<br/>";
  echo "please flush fan page and open this page again";
  exit();
}
authFacebook($fb_app_id,$fb_app_secret);
$aa_inst_id=$session->instance['aa_inst_id'];
?>

<script>
$(function() {
	var dates = $( "#from, #to" ).datepicker({
		defaultDate: "0",
		dateFormat: 'yy-mm-dd',
		changeMonth: false,
		numberOfMonths: 1,
		onSelect: function( selectedDate ) {
			var option = this.id == "from" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
});
function chkFormular () {
	if (!document.Formular.akzeptiert.checked) {
		jQuery("#accept-terms").dialog({
			modal: true
		});
	    document.Formular.akzeptiert.focus();
	    return false;
}}
jQuery(document).ready(function(){
	jQuery(".jquery-button").button();
});
</script>
<div id="header" class="fix">
	<div id="admin-popup">
		<div class="header">
			<h1><?php __p("Admin panel")?></h1>
		</div>
		<div class="content">	
			<div class="date-range">
				<!-- 
				<form name="Formular" id="form_export" action="admin_get_winner.php?aa_inst_id=<?php //echo $aa_inst_id; ?>" method="POST" target="_self" onsubmit="return chkFormular()">
				 -->
				<form name="Formular" id="form_export" action="admin_get_winners_download.php?aa_inst_id=<?php echo $aa_inst_id; ?>" method="POST" target="_self" onsubmit="return chkFormular()">
					<div>
						<h3><?php __p("Date Range for export")?>:</h3>
						<label for="from"><?php __p("Start-Date")?></label> <input type="text" id="from" name="from" />
						<label for="to"><?php __p("End-Date")?></label> <input type="text" id="to" name="to" />
					</div>
					<div>
						<h3><?php __p("Nr of winners")?>:</h3>
						<label for="from"><?php __p("Nr of winners")?></label> <input type="text" id="winners" name="winners" size="3" value="1" />
					</div>
					<div>
						<div class="agreements"><?=$session->config['admin_export_terms']?></div>
						<span id="check"><input type="checkbox" name="akzeptiert"> </span>
						<span id="checkborder"><?php __p("I read and agree to all export terms & conditions")?></span>
					</div>
				</form>
			</div>
		</div>
		<div class="footer">
			<span class="buttons"><button class="jquery-button" onclick="jQuery('#form_export').submit()"><span class="ui-button-text"><?php __p("Submit")?></span></button></span>
		</div>
	</div>
</div>

<div id="accept-terms" style="display:none;">
	<p><?php __p("Please accept the export conditions to export data!")?></p>
</div>