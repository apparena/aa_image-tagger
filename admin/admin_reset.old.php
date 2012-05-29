<?php 
require_once(dirname(__FILE__).'/../init.php');
require_once(dirname(__FILE__).'/../include.php');


if($user == false)
{
	echo '<span style="color:red"> Sorry, you could not be authenticated.</span>';
	exit();
}

if( $user->isAdmin == false)
{
	echo '<span style="color:red">Sorry, you could not be authenticated.</span>';
	exit();
}

if( $session->instance['aa_inst_id'] == false)
{
	echo '<span style="color:red">Sorry, cannot get any Instance</span>';
	exit();
}


$aa_inst_id=$session->instance['aa_inst_id'];	

if(count($_POST) > 0)
{
	//get post
	$del_p=$_POST['del_participation'];
	$del_t=$_POST['del_tracking'];

	if($aa_inst_id == false){
		echo  '<span style="color:red">missing parameter aa_inst_id</span>';
		exit();
	}

	$msg= __t("Data has been deleted successfully.");
	if($del_p == 1){
		$table="app_participation";
		$where=$db->quoteInto("aa_inst_id=?",$aa_inst_id);
		$db->delete($table,$where);

    	if($del_t != 1)
    		$msg=__t('All participants (and tickets) have been deleted');
	}

	if($del_t == 1)	{
		$table="app_tracking";
		$where=$db->quoteInto("aa_inst_id=?",$aa_inst_id);
		$db->delete($table,$where);

    	if($del_p != 1)
       		$msg=__t("All tickets have been deleted");
	}

 	ui_info_msg($msg);
}

/************************************************************
 * get saved days for app reset from db @ table app_config. *
 * (to check checkboxes if needed)                          *
 ************************************************************/
$config_keys = array();

$config_keys[ 0 ] = 'monday';
$config_keys[ 1 ] = 'tuesday';
$config_keys[ 2 ] = 'wednesday';
$config_keys[ 3 ] = 'thursday';
$config_keys[ 4 ] = 'friday';
$config_keys[ 5 ] = 'saturday';
$config_keys[ 6 ] = 'sunday';

// get all config vars according to this instance
$sql = "SELECT * FROM `app_config` WHERE `aa_inst_id` = '".$aa_inst_id."'";
try{
	$result = $global->db->fetchAll( $sql );
} catch(Exception $e) {
	// the deal should be in the db!
	echo "exception: no data found: ".$sql;
	exit(0);
}

// save all (un-)checked days / filter the days out of the config vars from the db
$checkedDays = array();
for( $index = 0; $index < count( $result ); $index++ ) {
	switch( $result[ $index ]['config_key'] ) {
		// monday
		case $config_keys[ 0 ]:
			if( $result[ $index ]['config_value'] == '1' ) {				 
				// add a checked attribute to the checkbox for monday
				$checkedDays['monday'] = 1;				
			} else {				
				$checkedDays['monday'] = 0;				
			}			
			break;
		// tuesday
		case $config_keys[ 1 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['tuesday'] = 1;
			} else {
				$checkedDays['tuesday'] = 0;
			}
			break;
		// wednesday
		case $config_keys[ 2 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['wednesday'] = 1;
			} else {
				$checkedDays['wednesday'] = 0;
			}
			break;
		// thursday
		case $config_keys[ 3 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['thursday'] = 1;
			} else {
				$checkedDays['thursday'] = 0;
			}
			break;
		// friday
		case $config_keys[ 4 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['friday'] = 1;
			} else {
				$checkedDays['friday'] = 0;
			}
			break;		
		// saturday
		case $config_keys[ 5 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['saturday'] = 1;
			} else {
				$checkedDays['saturday'] = 0;
			}
			break;
		// sunday
		case $config_keys[ 6 ]:
			if( $result[ $index ]['config_value'] == '1' ) {
				// add a checked attribute to the checkbox for monday
				$checkedDays['sunday'] = 1;
			} else {
				$checkedDays['sunday'] = 0;
			}
			break;
	}
}

?>
	<div id="header" class="fix">
		<div id="admin-popup">
			<div class="header">
				<h1><?php __p("Admin panel")?></h1>
			</div>
			<div class="content">
				<div id="form_reset_lottery">
					<h3><?php __p("Manual reset")?>:</h3>
					<span class="reset_button"><button class="jquery-button" onclick="resetLottery();"><span class="ui-button-text"><?php __p("form_reset_lottery")?></span></button></span>
				</div>
				<div id="form_set_days">
					<h3><?php __p("Automatic reset")?>:</h3>
					<table>
					
						<tr>
							<th colspan="8" id="form_set_days_headline"><span><?=__p("form_set_days_headline");?></span></th>
						</tr>
						<tr>
							
							<td id="monday" class="weekday">
								<span><?=__p("form_monday");?></span>
								<br />
								<input value="1" id="check_monday" Name="check_monday" type="checkbox" title="<?=__p("form_monday");?>" <? if( $checkedDays['monday'] == 1 ) {?>checked<?}?>/>
							</td>
							<td id="tuesday" class="weekday">
								
								<span><?=__p("form_tuesday");?></span>
								<br />
								<input
								 	value="1"
									id="check_tuesday" 
									Name="check_tuesday" 
									type="checkbox"
									title="<?=__p("form_tuesday");?>"
									<?
									
										if( $checkedDays['tuesday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
											
									?>
								/>
								
							</td>

							<td id="wednesday" class="weekday">
								
								<span><?=__p("form_wednesday");?></span>
								<br />
								<input  
								 	value="1"
									id="check_wednesday" 
									Name="check_wednesday" 
									type="checkbox"
									title="<?=__p("form_wednesday");?>"
									<?
									
										if( $checkedDays['wednesday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
										
									?>
								/>
								
							</td>

							<td id="thursday" class="weekday">
								
								<span>
								<?=__p("form_thursday");?>
								</span>
								<br />
								<input  
								 	value="1"
									id="check_thursday" 
									Name="check_thursday" 
									type="checkbox"
									title="<?=__p("form_thursday");?>"
									<?
									
										if( $checkedDays['thursday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
										
									?>
								/>
								
							</td>
							
							<td id="friday" class="weekday">
								
								<span>
								<?=__p("form_friday");?>
								</span>
								<br />
								<input  
								 	value="1"
									id="check_friday" 
									Name="check_friday" 
									type="checkbox"
									title="<?=__p("form_friday");?>"
									<?
									
										if( $checkedDays['friday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
										
									?>
								/>
								
							</td>
							
							<td id="saturday" class="weekday">
								
								<span>
								<?=__p("form_saturday");?>
								</span>
								<br />
								<input  
								 	value="1"
									id="check_saturday" 
									Name="check_saturday" 
									type="checkbox"
									title="<?=__p("form_saturday");?>"
									<?

										if( $checkedDays['saturday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
										
									?>
								/>
								
							</td>
							
							<td id="sunday" class="weekday">
								
								<span>
								<?=__p("form_sunday");?>
								</span>
								<br />
								<input  
								 	value="1"
									id="check_sunday" 
									Name="check_sunday" 
									type="checkbox"
									title="<?=__p("form_sunday");?>"
									<?

										if( $checkedDays['sunday'] == 1 ) {
										    
										    // add a checked attribute to the checkbox
										    ?>checked<?
										    
										}
										
									?>
								/>
								
							</td>
							<td><span class="reset_button"><button class="jquery-button" onclick="setWeekdays();"><span class="ui-button-text"><?php __p("form_set_weekdays_button")?></span></button></span></td>
						</tr>
					</table>
				</div>
				
				<h3><?php __p("Delete data of lottery")?>:</h3>
				<form id="reset_form" action="" method="post">
					<table>
						<tr>
							<td width="75%"><input value="1" name="del_participation" id="del_participation" type="checkbox" /> <?php __p("Delete participants data")?></td>
							<td></td>
						</tr>
						<?php 
						// Hide delete extra tickets for Basic version
						if ($session->config['referral_mode'] == "1") {
						?>
						<tr>
							<td><input value="1" name="del_tracking" id="del_tracking" type="checkbox" /> <?php __p("Delete extra tickets")?> </td>
							<td>
								<span class="reset_button"><button class="jquery-button" onclick="reset_data();"><span class="ui-button-text"><?php __p("Delete")?></span></button></span>
							</td>
						</tr>
						<?php }?>
					</table>
					
				</form>
			</div>
			<div class="footer">
				
			</div>
		</div>
	</div>
	
	<div id="mPopup">
	</div>


	<script type="text/javascript">
  function reset_data()
  {

    if(jQuery("#del_participation").is(':checked') == true )
      var del_p= 1;
    else
      var del_p= 0;

    if(jQuery("#del_tracking").is(':checked') == true )
      var del_t= 1;
    else
      var del_t= 0;

    if(del_p == 0 && del_t == 0)
    {
    	jQuery("#select-options").dialog({
    		modal: true
    	});
      return false;
    }

    if(del_p == 1 && del_t == 1)
    	jQuery("#delete-participants-tickets").dialog({
    		modal: true
    	});
    else if(del_p == 1 && del_t == 0)
    	jQuery("#delete-participants").dialog({
    		modal: true
    	});
    else if(del_p == 0 && del_t == 1)
    	jQuery("#delete-tickets").dialog({
    		modal: true
    	});
    jQuery("#reset_form").submit();
  };
  jQuery(document).ready(function(){
	  jQuery(".jquery-button").button();
	});

	

  /****************************************************
   * Save the checkbox values to app_config db-table. *
   ****************************************************/
function setWeekdays() {

	$("#mMessage").remove();
	
	$.ajax({
		
		type : 'POST',
		async: true,
		url  : 'store_weekdays.php',
		data : ({
			
			aa_inst_id:    '<?=$session->instance['id']?>',
			monday:    $('#check_monday:checked').val(),
			tuesday:   $('#check_tuesday:checked').val(),
			wednesday: $('#check_wednesday:checked').val(),
			thursday:  $('#check_thursday:checked').val(),
			friday:    $('#check_friday:checked').val(),
			saturday:  $('#check_saturday:checked').val(),
			sunday:    $('#check_sunday:checked').val()
			
		}),
		
		
		
		/*****************************************************************
		 * this function gets executed when the ajax call above returns. *
		 *****************************************************************/
		success: function( data ){
		
			if( data.indexOf( "true" ) >= 0 ) {
				
				$('<div id="mMessage" class="ui-widget">' +
		        		'<div style="padding: 0.7em;" class="ui-state-highlight ui-corner-all">' + 
		            		'<p style="padding:10px;">' +
		            			'<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>' +
		            			'<strong>' +
		           					'<?=__p("message_days_saved_success");?>' +
		            			'</strong>' +
		            		'</p>' +
		           		'</div>' +
		     		'</div>').prependTo("#header");
				
			} else {

				$('<div id="mMessage" class="ui-widget">' +
		        		'<div style="padding: 0.7em;" class="ui-state-highlight ui-corner-all">' + 
		            		'<p style="padding:10px;">' +
		            			'<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>' +
		            			'<strong>' +
		           					'<?=__p("message_days_saved_fail");?>' +
		            			'</strong>' +
		            		'</p>' +
		           		'</div>' +
		     		'</div>').prependTo("#header");
				
			}
		
		}
		
	});
	
}

  /*********************************************************
   * Show popup to question if deletion shall be executed. *
   *********************************************************/
function resetLottery() {

	$("#mMessage").remove();
		
	var mResult = dopopup( '<?=__p("form_reset_lottery_question");?>' );
	
}



/***************************************************
 * Creates a jquery dialog-popup.                  *
 * Calls success() or fail().                      *
 * @param msg the message to display               *
 ***************************************************/
 function dopopup( msg, success, fail ){

		$( "#mPopup" ).html( msg );
		$( "#mPopup" ).dialog({
			
			close:   function(event, ui){


			},
		
			height:   'auto',
			width:    'auto',
			position: 'top',
			
			buttons: {
		
				"Ok": function(){
					
					//$(this).dialog('close');

					reset();
					
				},

				"Cancel": function(){

					$(this).dialog('close');
					
				}
			
			}
			
		});

	}

function reset(){

	// asynch ajax call
	$.ajax({
		
		type : 'POST',
		async: true,
		url  : 'resetRound.php',
		data : ({
			
			aa_inst_id: '<?=$session->instance['id']?>'
			
		}),
		
		
		
		/*****************************************************************
		 * this function gets executed when the ajax call above returns. *
		 *****************************************************************/
		success: function( data ){
		
			if( data.indexOf( "true" ) > 0 ) {
				
				$('<div id="mMessage" class="ui-widget">' +
		        		'<div style="padding: 0.7em;" class="ui-state-highlight ui-corner-all">' + 
		            		'<p style="padding:10px;">' +
		            			'<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>' +
		            			'<strong>' +
		           					'<?=__p("form_reset_lottery_success");?>' +
		            			'</strong>' +
		            		'</p>' +
		           		'</div>' +
		     		'</div>').prependTo("#header");
				
			} else {

				$('<div id="mMessage" class="ui-widget">' +
		        		'<div style="padding: 0.7em;" class="ui-state-highlight ui-corner-all">' + 
		            		'<p style="padding:10px;">' +
		            			'<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>' +
		            			'<strong>' +
		           					'<?=__p("form_reset_lottery_fail");?>' +
		            			'</strong>' +
		            		'</p>' +
		           		'</div>' +
		     		'</div>').prependTo("#header");
				
			}

			// close the popup
			$( "#mPopup" ).html("");
			$( "#mPopup" ).dialog('close');
		
		}
		
	});
	
}


</script>
</body>
</html>

<div id="select-options" style="display:none;">
	<p><?php __p("Please select what you want to reset.")?></p>
</div>
<div id="delete-tickets" style="display:none;">
	<p><?php __p("Do you really want to delete all extra tickets?")?></p>
</div>
<div id="delete-participants" style="display:none;">
	<p><?php __p("Do you really want to delete all participation data?")?></p>
</div>
<div id="delete-participants-tickets" style="display:none;">
	<p><?php __p("Do you really want to delete all participation data and extra tickets?")?></p>
</div>
