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
	
/*
	$fb_user_id = 0;
	
	if( isset( $_POST[ 'fb_user_id' ] ) ) {
	
		$fb_user_id = $_POST[ 'fb_user_id' ];
	
	} else {
	
		die( "invalid session! exiting..." );
		exit( -1 );
	
	}
*/
	
	$db = mysql_select_db( $database_name, $connection );
	
	// get all tags for this aa instance
	$tagSql = "SELECT * FROM `tags` WHERE `aa_inst_id` = " . $aa_inst_id;
	
	$tagResult = mysql_query( $tagSql );
	
	$tags = array();
	
	if( $tagResult ) {
	
		while( $row = mysql_fetch_array( $tagResult ) ) {
			
			$tags[] = array(
				'fb_user_id'   => $row[ 'fb_user_id' ],
				'fb_user_name' => utf8_decode( $row[ 'fb_user_name' ] ),
				'x_coord' => $row[ 'x_coord' ],
				'y_coord' => $row[ 'y_coord' ]
			);
			
		}
		
	}
	
	if( count( $tags ) > 0 ) {
		
		echo json_encode( $tags );
		exit( 0 );
		
	}
	
	echo "empty;false";
	
?>