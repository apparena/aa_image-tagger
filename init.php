<?php
/*
 * Initial process to start the app
 */
// Load config values
date_default_timezone_set('Europe/Berlin');

//fix ie can not save cookie in iframe
header('P3P: CP=CAO PSA OUR');

require_once 'functions.php';

//auto load
//set inclclude path
define("ROOT_PATH",realpath(dirname(__FILE__)));
set_include_path(ROOT_PATH.'/lib/' . PATH_SEPARATOR .
   ROOT_PATH.'/modules/' . PATH_SEPARATOR );

/**** init ***/
require_once ROOT_PATH.'/lib/Frd/functions.php'; 
require_once ROOT_PATH.'/lib/Frd/Frd.php';
// Initialize the Zend Autoloader
require_once "Zend/Loader/Autoloader.php";
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

$config=array(
   'timezone'=>'Europe/Berlin',
   'root_path'=>ROOT_PATH,
   'include_paths'=>array(
      ROOT_PATH.'/lib',
      ROOT_PATH.'/modules',
   ),

   'module_path'=>ROOT_PATH.'/modules',
);

Frd::init($config);
//start session
Zend_Session::start();
//necessary files
require_once ROOT_PATH.'/config.php';

//db
addDb(array(
   'adapter'=>'MYSQLI',
   'host'=>$database_host,
   'username'=>$database_user,
   'password'=>$database_pass,
   'dbname'=>$database_name,
));




// Initialize App-Manager connection
$aa_inst_id = "";
if( isset( $_GET['aa_inst_id'] ) ) {
	$aa_inst_id = $_GET['aa_inst_id'];
}

//check canvas redirect
$fb_page_id=get_page_id();
if($fb_page_id == false && $aa_inst_id == false)
{
   $handle=getModule("canvas_redirect")->getModel("handle");
   $aa_inst_id=$handle->handle();

   if( $aa_inst_id == false) {
      //if not instid, redirect to www.facebook.com
      $link="http://www.facebook.com";
      redirect(handle_link($link));
      exit();
   } else  {
      $aa = new AA_AppManager(array(
         'aa_app_id'  => $aa_app_id,
         'aa_app_secret' => $aa_api_key,
         'aa_inst_id' => $aa_inst_id
      ));
	  $aa->setServerUrl('http://dev.app-arena.com/manager/server/soap4.php' );

      $aa_instance = $aa->getInstance();
      if(is_array($aa_instance)) {
         //redirect to fan page
         $fb_page_url=str_replace("http://www.facebook.com","",$aa_instance['fb_page_url']);
         $fb_page_url=str_replace("https://www.facebook.com","",$fb_page_url);
         $fb_page_url="http://www.facebook.com".$fb_page_url."?sk=app_".$aa_instance['fb_app_id'];

         redirect($fb_page_url);
         exit();
      } else {
         //can not get instance
         $link="http://www.facebook.com";
         redirect(handle_link($link));
         exit();
      }
   }
}

// Setup app-manager connection
$aa = new AA_AppManager(array(
	'aa_app_id'  	=> $aa_app_id,
	'aa_app_secret' => $aa_api_key,
	'aa_inst_id' 	=> $aa_inst_id
));
$aa->setServerUrl('http://dev.app-arena.com/manager/server/soap4.php');
$aa_instance = $aa->getInstance();
$global = new Zend_Session_Namespace( 'aa_' . $aa_instance['aa_inst_id'] );
$session = &$global;
$global->instance = $aa_instance;

// initialize Facebook data from signed_request (if available)
if (isset($_REQUEST['signed_request'])){
	$session->fb = parse_signed_request($_REQUEST['signed_request']);
}

// Try to get a the current locale from cookie
$cur_locale = $session->instance['aa_inst_locale'];
$cookie_index_locale = 'aa_' . $global->instance['aa_inst_id'] . "_locale";
$lang_switch = false;
if (isset($_COOKIE[$cookie_index_locale])) {
	$cur_locale = $_COOKIE[$cookie_index_locale];
	$session->app['testme'] = $cur_locale . "_cookie";
} else {
	if (isset($session->fb["user"]["locale"]) && $session->fb["user"]["locale"] != "de_DE") {
		$lang_switch = true;
	}
}
$aa->setLocale($cur_locale);

$global->config = $aa->getConfig();
$session->config = $global->config;
$session->instance = $global->instance;
$session->app['fb_share_url'] = "https://apps.facebook.com/" . $session->instance['fb_app_url']."/fb_share.php?aa_inst_id=".$session->instance['aa_inst_id'];

// Switch language if activated
if ( $session->config['admin_lang_activated']['value'] && $lang_switch) {
	$cur_locale = "en_US";
	$aa->setLocale($cur_locale);
	$global->config = $aa->getConfig();
}
try {
	$session->translation = array();
	$session->translation[$cur_locale] = $aa->getTranslation($cur_locale);
	//$session->translation['de_DE'] = $aa->getTranslation('de_DE');
	// Add translation management
	if (!isset($session->translation[$cur_locale])) {
		$translate = new Zend_Translate('array',$session->translation[0], $cur_locale);
	} else {
		$translate = new Zend_Translate('array',$session->translation[$cur_locale], $cur_locale);
	}
	$translate->setLocale($cur_locale);
	$global->translate=$translate;
} catch (Exception $e) {
	if ($session->config['admin_debug_mode']['value']){
		Zend_Debug::dump($session->translation);
		echo $e->getMessage();
		echo $e->getTraceAsString();
	}
}
//log
app_log_fb();
// echo "\n".$global->config["tournament"]["value"];
// echo "\n".$session->config["tournament"]["value"];
?>
