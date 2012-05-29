<?php

function parse_signed_request($signed_request)
{
	if($signed_request == false)
	{
		return array();
	}

	//$signed_request = $_REQUEST["signed_request"];
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

	return $data;

}

/**
 * get fb page id in fanpage
 */
function get_page_id()
{
  if(isset($_GET['page_id']))
  {
    $page_id=intval($_GET['page_id']);
  }
  else if(isset($_POST['fb_sig_page_id']))
  {
    $page_id=$_POST['fb_sig_page_id'];
  }
  else if(isset($_REQUEST['signed_request']))
  {
     $signed_request = $_REQUEST["signed_request"];
     list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
     $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);


     if(isset($data['page']))
       $page_id=$data['page']['id'];
     else
       $page_id=false;
  }
  else
  {
    $page_id=false;
  }

  return $page_id;
}

function is_fan()
{
  if(isset($_REQUEST['signed_request']))
  {
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    $is_fan=$data['page']['liked'];
  }
  else
  {
    $is_fan=false;
  }

  return $is_fan;
}


function is_admin()
{
  if(isset($_REQUEST['signed_request']))
  {
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if(isset($data['page']['admin']))
      $is_admin=$data['page']['admin'];
    else
      $is_admin=false;
  }
  else
  {
    $is_admin=false;
  }

  return $is_admin;
}


function get_user_id()
{
  if(isset($_REQUEST['signed_request']))
  {
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if(isset($data['user_id']))
      $user_id=$data['user_id'];
    else
      $user_id=false;
  }
  else
  {
    $user_id=false;
  }

  return $user_id;
}

/* useful functions only for this app 
 *
 * 
 */
/* germany character html 
 * &auml;
 * &uuml;
 * &ouml;
 */

/*
 * add js to page
 */
function addJs($file,$base='js/',$version='')
{
  global $global;

  if($base == false)
    $base='js/';

  $file=str_replace(".js","",$file);
  if($version != false)
    $file.='.js?v='.$version;
  else
    $file.='.js';

  $html='<script type="text/javascript" src="'.$base.$file.'"></script>'."\n";
  echo $html;
}

/*
 * add css to page
 */
function addCss($file,$base='css/',$version='')
{
  global $global;

  if($base == false)
    $base ='css/';

  $file=str_replace(".css","",$file);
  if($version != false)
    $file.='.css?v='.$version;
  else
    $file.='.css';

  $html='<link href="'.$base.$file.'" media="screen" rel="stylesheet" type="text/css" />'."\n";
  echo $html;
}




/*
 * get current uri
 */
function app_current_uri()
{
  $url='http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'];
  return $url;
}

/**
 * if is https , replace url's link with https://
 */
function handle_link($link)
{
  if(is_https() == true)
  {
    $link=str_replace("http://","https://",$link); 
  }

  return $link;
}

//after user vote photo ,post this message to his facebook
function postto_facebook($link,$photo_url,$description,$caption="",$name="")
{
  global $global;

  $params=array(
    'link'=>$link,
    'picture'=>$photo_url,
    'name'=>$name,
    'caption'=>$caption,
    'description'=>$description,
  ); 
  $global->facebook->api('/me/feed','post',$params);

}
/*
 * redirect to other url ,form iframe app
 * @param url string 
 */
function redirect($url)
{
  global $global;

  //$url=$global->base_url.$url;

  echo '<script>top.location="' . $url . '";</script>';
  exit();
}




//template functions
/**
 * load php file
 */
//render a template file,and return the html
function baseurl()
{
  return '';
  return "http://apps.facebook.com/new_quiz_app/";
}


/*  useful functions */
//get current url
//this url is  rewrited uri
//like  appmodel/index/...
//do not have host and baseurl
function get_current_url()
{
  $uri= $_SERVER['REQUEST_URI'];

  $uri=str_replace(baseurl(false).'index.php',"",$uri);

  return $uri;
}


/**
 * must with instid in the  key ,otherwise will conflict
 */
function generateSessionKey($instid)
{
  return "instance_".$instid;
}

/** translate functions **/
/*
 *translate , may be for the future
* use __('name')  replace 'name'
*/
function __t()
{
	//$translate=Frd::getGlobal("translate");
	global $global;
	$translate=$global->translate;
	
	$args=func_get_args();
	$num=func_num_args();

	if($num == 0)
	return '';

	$str=$args[0];
	if($num == 1)
	{
		return  $translate->_($str);
	}

	unset($args[0]);
	//$param='"'.implode('","',$args).'"';

	//$str='$ret=sprintf("'.$translate->_($str).'",'.$param.');';
	//eval($str);

$str=$translate->_($str);
	//return  $ret;
	foreach($args as $parameter)
	{
	$str=Frd_Regexp::replace($str,"%s",$parameter,1);
	}
	
	return  $str;
}
/*
 *translate ,but print directly
*/
function __p()
{
	//$translate=Frd::getGlobal("translate");
	global $global;
	$translate=$global->translate;

	$args=func_get_args();
	$num=func_num_args();

	if($num == 0)
	return '';

	$str=$args[0];
	if($num == 1)
	{
		echo  $translate->_($str);
		return ;
	}

	unset($args[0]);
	//$param='"'.implode('","',$args).'"';

	//$str='$ret=sprintf("'.$translate->_($str).'",'.$param.');';
	//eval($str);

$str=$translate->_($str);
	//echo  $ret;
	foreach($args as $parameter)
	{
	$str=Frd_Regexp::replace($str,"%s",$parameter,1);
	}
	
	echo   $str;

}

/**
* get client's ip
*/
function getClientIp()
{
   // Get client ip address
   if ( isset($_SERVER["REMOTE_ADDR"]))
   $client_ip = $_SERVER["REMOTE_ADDR"];
   else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
   $client_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
   else if ( isset($_SERVER["HTTP_CLIENT_IP"]))
   $client_ip = $_SERVER["HTTP_CLIENT_IP"];

   return $client_ip;
}


//string to js var
function to_js_var($str)
{
   return json_encode($str); 
}
/**
*
*/
function  initFacebook(&$global)
{
   global $aa_app_id,$aa_app_secret;

   // Initialize App-Manager connection
   $aa = new AA_AppManager(array(
      'aa_app_id'  => $aa_app_id,
      'aa_app_secret' => $aa_app_secret,
      'aa_inst_id' => '',
   ));

   $instance=$aa->getInstance();

   $params=array(
      'appId' => $instance['fb_app_id'],
      'secret' => $instance['fb_app_secret'],
   );

   $facebook = new Facebook($params);
   $global->FB_client=$facebook;

}

/**
* create html 
*/
function createHtml($tag,$text=false,$attrs=array())
{
   $attributes='';
   foreach($attrs as $key=>$value)
   {
      if($value !== null)
      {
         $value=json_encode($value);
         $attributes.=$key.'='.$value.' ';
      }
      else
      {
         $attributes.=$key;
      }
   }

   if($text == false)
   {
      $html='<'.$tag.' '.$attributes.'/>';
   }
   else
   {
      $html='<'.$tag.' '.$attributes.'>'.$text.'</'.$tag.'>';
   }
   return $html;
}


function Input($params=array())
{
   return createHtml('input',false,$params);
}

function A($params=array())
{
   $value=$params['value'];
   unset($params['value']);

   return createHtml('a',$value,$params);
}


/**
* log fb parameters
*/
function app_log_fb()
{
   $fb=getModule("app_log")->getTable("fb");

   $data=array();
   $data['get']=array();

   foreach($_GET as $k=>$v)
   {
      if(strpos($k,"fb_") === 0)
      {
         $data['get'][$k]=$v;
      }
   }

   //if no fb_*  parameter, do not log
   if($data['get'] ==  false)
   {
      return false;
   }

   //$data['post']=print_r($_POST,true);
   $data['signed_request']='';

   $signed_request=getRequest('signed_request',false);

   if($signed_request != false)
   {
      list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
      $signed_data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
      $data['signed_request']=print_r($signed_data,true);
   }

   $data=array('data'=>print_r($data,true));


   $fb->add($data);
}
function get_fb_app_info($fb_page_url)
{
   return false;
   global $aa_app_id,$aa_api_key;

   $params=array(
      'aa_app_id'=>$aa_app_id,
      'aa_app_secret'=>$aa_api_key,
      'aa_inst_id'=>'',
      'fb_page_id'=>'',
   );


   $fluttery = new Fluttery();
   $params['fb_page_url']=$fb_page_url;


   $fluttery->init($params);
   $data=$fluttery->getFbApp($fb_page_url);

   return $data;
}
?>
