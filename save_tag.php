<?
	include_once( 'init_session.php' );
	include_once( 'config.php' );
	
	$connection = mysql_connect( $database_host, $database_user, $database_pass );
	
	if ( !$connection ) {
	
		die( 'sql connection failed: ' . mysql_error() );
		 
	}
	
	$aa_inst_id = 0;
	
	if( isset( $_GET[ 'aa_inst_id' ] ) ) {
	
		$aa_inst_id = $_GET[ 'aa_inst_id' ];
	
	} else {
	
		die( "invalid session! exiting..." );
		exit( -1 );
	
	}
	
	$fb_user_id = 0;
	
	if( isset( $_POST[ 'fb_user_id' ] ) ) {
	
		$fb_user_id = $_POST[ 'fb_user_id' ];
	
	} else {
	
		die( "invalid session! exiting..." );
		exit( -1 );
	
	}
	
	$fb_user_name = "";
	$fb_user_name = $_POST[ 'fb_user_name' ];
	
	$x_coord = $_POST[ 'x_coord' ];
	$y_coord = $_POST[ 'y_coord' ];
	
	// Get client ip address
	if ( isset($_SERVER["REMOTE_ADDR"]))
		$client_ip = $_SERVER["REMOTE_ADDR"];
	
	else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
		$client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	
	else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
		$client_ip = $_SERVER["HTTP_CLIENT_IP"];
	
	$db = mysql_select_db( $database_name, $connection );
	
	// check if the user already has tagged the image
	$checkSql = "SELECT * FROM `tags` WHERE `fb_user_id` = '" . $fb_user_id . "' AND `aa_inst_id` = " . $aa_inst_id;
	
	$checkResult = mysql_query( $checkSql );
	
	if( $checkResult ) {
		
		// the user already tagged the image
		if( mysql_num_rows( $checkResult ) > 0 ) {
			
			echo "already tagged;false";
			exit( 0 );
			
		}
		
	}
	
	// the user didnt tag the image yet, so save his tag
	$saveSql = "INSERT INTO `tags` 
				SET `aa_inst_id` = " . $aa_inst_id . ", 
				    `fb_user_id` = '" . $fb_user_id . "', 
					`fb_user_name` = '" . utf8_encode( $fb_user_name ) . "', 
					`x_coord` = " . $x_coord . ", 
					`y_coord` = " . $y_coord . ", 
					`ip_address` = '" . $client_ip . "'";
	
	$saveResult = mysql_query( $saveSql );
	
	if( mysql_affected_rows() <= 0 ) {
		
		echo "save error;false";
		exit( 0 );
		
	}
	
	echo "true";
	
?>