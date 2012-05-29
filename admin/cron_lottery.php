<?php
	
	/**************************************************************
	 * cronjob script for executing daily resets of all lotteries *
	 * where days are checked for resetting.                      *
	 * i placed a copy of this into /etc/cron.d/                  *
	 *                                                            *
	 * !this is only for testing!                                 *
	 **************************************************************/
	
	require_once('/var/www/apps/iconsultants/lottery/v15/config.php');

	mysql_connect( "localhost", $database_user, $database_pass );
	mysql_select_db( $database_name );
	
	date_default_timezone_set('Europe/Berlin');
	// get the name of the day now and convert it to lower case chars
	$today = strtolower ( date( 'l', time() ) );
	
	// this will count all changed entries
	$results_all = 0;
	
	/***************************************
	 * check instances for desired resets. *
	 * select all results which have       *
	 * 'today' set as a config key and     *
	 * its value is '1'.                   *
	 ***************************************/ 
	$sql = "SELECT * FROM `app_config` 
	        WHERE `config_key` = '".$today."' 
	        AND `config_value` = '1'";
	
	//echo "querying:\n".$sql."\n";
	
	// query db connected above
	$result = mysql_query( $sql );
	
	// create a current time stamp to insert/update
	$timestamp = date( 'Y-m-d H:i:s', time() );

	// loop through results
	$aa_instances = array();
	while( $row = mysql_fetch_array( $result )) {
	
		$aa_instances[] = $row[ 'aa_inst_id' ];
		
		// Check if round reset recordset already exists in DB
		$sql = "SELECT `config_value`
				FROM `app_config` 
				WHERE aa_inst_id=" . $row[ 'aa_inst_id' ] . 
				" AND `config_key`='round_reset_timestamp'
				LIMIT 1";
		
		//echo "1querying: ".$sql."<br /><br />";
		
		$result_check = mysql_query( $sql );
		
		if ( mysql_fetch_array( $result_check ) ){
			$sql = "UPDATE `app_config`
					SET `config_value` = '$timestamp'
					WHERE `config_key` = 'round_reset_timestamp' 
					AND `aa_inst_id` = '".$row[ 'aa_inst_id' ]."'
					LIMIT 1";
			
		} else {
			$sql = "INSERT INTO `app_config`
					       (`aa_inst_id`,
					        `config_key`,
					        `config_value`)
					VALUES ('".$row[ 'aa_inst_id' ]."',
					        'round_reset_timestamp',
					        '$timestamp')";
			
		}
	
		//echo "2querying: ".$sql."<br /><br />";
		
		// INSERT / UPDATE db
		$result_update = mysql_query( $sql );

		$results_all += $result_update;
		
	}
	echo "Automatic Reset of instances\n";
	echo "Date: " . $today. ", " . $timestamp . "\n";
	echo "Total number of instances reseted: " . $results_all . "\n";
	echo "Instances affected:\n";
	foreach ($aa_instances as $aa_inst_id) {
		echo "\t" . $aa_inst_id . "\n";
	}
	
	//echo "changed / updated >".$results_all."< entries...<br /><br />";
	
?>