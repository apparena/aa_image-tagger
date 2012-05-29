<?php
	
	/********************************************************************
	 * This init is for the template files and where it might be needed *
	 * after the index.php has used the init.php for initializing the   *
	 * session.                                                         *
	 * This only gets the session where the aa-contents were previously *
	 * stored in the init.php.                                          *
	 * NOTE that the aa_inst_id is needed here as a GET parameter!      *
	 ********************************************************************/
	
	if( isset( $_GET['aa_inst_id'] ) ) {
		
		$aa_inst_id = $_GET['aa_inst_id'];
		
	} else {
		
		die( "invalid session! exiting..." );
		exit( -1 );
		
	}
	
	//auto load
	//set inclclude path
	define("ROOT_PATH",realpath(dirname(__FILE__)));
	set_include_path(ROOT_PATH.'/libs/' . PATH_SEPARATOR );
	
	// Initialize the Zend Autoloader
	require_once "Zend/Loader/Autoloader.php";
	Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
	
	$session = new Zend_Session_Namespace( 'aa_' . $aa_inst_id );
	
?>