<?php
$aa_inst_id = 0;
if( isset( $_POST[ 'aa_inst_id' ] ) ) {
	$aa_inst_id = $_POST[ 'aa_inst_id' ];
} else if( isset( $session->instance['aa_inst_id']) ){
	$aa_inst_id = $session->instance['aa_inst_id'];
} else {
	echo "invalid session! exiting...";
	exit(0);
}
$_GET['aa_inst_id'] = $aa_inst_id;
include_once(dirname(__FILE__).'/../../init_session.php');

$db = getDb();

// store the config values for looping later
$config_values = array();

if( isset( $_POST[ 'monday' ] ) )
	$config_values[ 0 ] = $_POST[ 'monday' ];
else
	$config_values[ 0 ] = 0;

if( isset( $_POST[ 'tuesday' ] ) )
	$config_values[ 1 ] = $_POST[ 'tuesday' ];
else
	$config_values[ 1 ] = 0;
	
if( isset( $_POST[ 'wednesday' ] ) )
	$config_values[ 2 ] = $_POST[ 'wednesday' ];
else
	$config_values[ 2 ] = 0;
	
if( isset( $_POST[ 'thursday' ] ) )
	$config_values[ 3 ] = $_POST[ 'thursday' ];
else
	$config_values[ 3 ] = 0;
	
if( isset( $_POST[ 'friday' ] ) )
	$config_values[ 4 ] = $_POST[ 'friday' ];
else
	$config_values[ 4 ] = 0;
	
if( isset( $_POST[ 'saturday' ] ) )
	$config_values[ 5 ] = $_POST[ 'saturday' ];
else
	$config_values[ 5 ] = 0;
	
if( isset( $_POST[ 'sunday' ] ) )
	$config_values[ 6 ] = $_POST[ 'sunday' ];
else
	$config_values[ 6 ] = 0;
	
/****************************************************************
 * first get the saved weekdays for this instance if available. *
 ****************************************************************/
$sql = "SELECT * FROM `app_config` WHERE `aa_inst_id` = '".$aa_inst_id."'";
	
//echo $sql."<br />";
	
	try{
		
		$result = $db->fetchAll( $sql );
		
	} catch(Exception $e) {
			
		// the deal should be in the db!
		echo "exception: no data found: ".$sql;
		exit(0);
		
	}
	
	
	/****************************************************
	 * check if there are config_keys for the weekdays. *
	 * (checking monday should do...)                   *
	 ****************************************************/
	$hasWeekdays = false;
	
	for( $index = 0; $index < count( $result ); $index++ ) {
		
		if( $result[ $index ][ 'config_key' ] == 'monday' ) {
			
			$hasWeekdays = true;
			break;
			
		}
		
	}
	
//echo "hasWeekdays: ".$hasWeekdays."<br />";
	
	/******************************************************
	 * this array is for the config keys to loop through. *
	 ******************************************************/
	$config_keys = array();
	
	$config_keys[ 0 ] = 'monday';
	$config_keys[ 1 ] = 'tuesday';
	$config_keys[ 2 ] = 'wednesday';
	$config_keys[ 3 ] = 'thursday';
	$config_keys[ 4 ] = 'friday';
	$config_keys[ 5 ] = 'saturday';
	$config_keys[ 6 ] = 'sunday';
	
	
	
	/*********************************************
	 * if there is no weekday key, do an insert. *
	 *********************************************/
	if( $hasWeekdays == false ) {
		
		// loop through weekdays
		for( $index = 0; $index < 7; $index++ ) {
			
			/*******************************************
			 * set the fields for inserting a weekday. *
			 *******************************************/
			$fields = "`aa_inst_id` = '"        .$aa_inst_id."', ".
					  " `config_key` = '"   .$config_keys[ $index ]."', ".
					  " `config_value` = '" .$config_values[ $index ]."'";
			
			// insert a weekday
			$sql = "INSERT INTO `app_config` SET ".$fields;

//echo $sql."<br />";
			
		    try {
		      	
		      	$result = $db->query( $sql );
		      	
		    } catch (Exception $e) {
		      	
		    	echo "!exception!";
		      	// should not fail...
		      	exit(0);
		      	
		    }
			
		}
		
	} else {
		
		/***********************************************
		 * there are already config keys for weekdays, *
		 * do an update.                               *
		 ***********************************************/
		
		// loop through weekdays
		for( $index = 0; $index < 7; $index++ ) {
			
			/******************************************
			 * set the fields for updating a weekday. *
			 ******************************************/
			$fields = "`aa_inst_id` = '"        .$aa_inst_id."', ".
					  " `config_key` = '"   .$config_keys[ $index ]."', ".
					  " `config_value` = '" .$config_values[ $index ]."'";
			
			// update a weekday
			$sql = "UPDATE `app_config` SET ".$fields." WHERE `aa_inst_id` = ".$aa_inst_id." AND `config_key` = '".$config_keys[ $index ]."'";
			
//echo $sql."<br />";
			
		    try {
		      	
		      	$result = $db->query( $sql );
		      	
		    } catch (Exception $e) {
		      	
		    	echo "!exception!";
		      	// should not fail...
		      	exit(0);
		    }
			
		}
		
	}
	
	echo "true";
	
?>