<?php

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
	$param='"'.implode('","',$args).'"';

	$str='$ret=sprintf("'.$translate->_($str).'",'.$param.');';
	eval($str);

	return  $ret;
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
	$param='"'.implode('","',$args).'"';

	$str='$ret=sprintf("'.$translate->_($str).'",'.$param.');';
	eval($str);

	echo  $ret;
}

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
//check if is in fan page
function is_fan_page()
{
  if(isset($_REQUEST['signed_request']))
  {
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if(isset($data['page']))
    {
       return true;
    }
  }


  return false;
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

/**
 * check if current user is admin of this app
 */
function app_is_admin($aa_inst_id)
{
  $session=new Zend_Session_Namespace("instance_".$aa_inst_id);
  if(!isset($session->is_admin) || $session->is_admin == false )
    return  false;
  else
    return true;
}

function get_user_id($app_id=false,$app_secret=false)
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

  if($user_id == false && $app_id != false && $app_secret != false )
  {
	  $facebook = new Facebook(array(
				  'appId' => $app_id,
				  'secret' => $app_secret,
				  'cookie' => true));

	  $user_id=$facebook->getUser();
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
 * load a record from table
 *
 * @return table object of this recored, or false if not exists
 */
function app_load_record($table,$primary,$value)
{
  $table=new Frd_Table_Common($table,$primary);
  if( $table->load($value) == false)
    return false;
  else
    return $table;
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
 * does it use https or http 
 */
function is_https()
{
  if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on')
    return true;
  else
    return false;
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

/**
 * cut the description ,make it no more then 150 characters
 *
 * @param description  string 
 * @return string  less then 150 character
 */
function app_cut_description($description)
{
  $description=mb_substr($description,0,150);

  return $description;
}
/*
 *  handle description  ,delete all \n \r for js function
 *
 * @param description  string 
 * @return string  
 */
function app_handle_description($description)
{
  $description=str_replace("\n","",$description);
  $description=str_replace("\r","",$description);
  $description=str_replace("'","",$description);
  $description=str_replace('"',"",$description);
  $description=htmlentities($description,ENT_QUOTES,"UTF-8");

  return $description;
}
/**
 *
 */
function app_string_to_singleline($str)
{
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);

  return $str;
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

//create thumbnail image
function create_thumbnail($newfile,$tmpname,$width)
{
  $image = new Image();
  $image->load($tmpname);
  $image->resizeToWidth($width);
  $image->save($newfile);
}

//render a template file,and return the html
function render($file,$params=array())
{
  global $global;
  $path=dirname(__FILE__);

  $view=new Zend_View();
  $view->setBasePath($path);
  $view->setScriptPath('template');
  $view->params=$params;
  $view->global=$global;

  echo $view->render($file);
}

function baseurl()
{
  return '';
  return "http://apps.facebook.com/new_quiz_app/";
}

function image_src($file)
{
  $src=baseurl().'img/'.$file;
  return $src;
}

function addImage($file,$attrstr='')
{
  $src=baseurl().'img/'.$file;
  $html='<img '.$attrstr.' src="'.$src.'">';

  echo $html;
}

/*
//get competition
function load_competition($id)
{
  $table=new Frd_Table_Common("competition","id");

  if($table->load($id) == false)
    return false;
  else
    return $table;
}
 */

/**
 * when has exception, will display message
 */
function error($msg)
{
  if($msg == false)
    var_dump($msg);
  else
    print_r($msg);

  exit();
}


/*  useful functions */
/**
 *
 */
function getValue($arr,$key,$default=false)
{
  if(isset($arr[$key]))
  {
    return  $arr[$key];
  }
  else
  {
    return $default; 
  }
}

/**
 *
 */
function getpost($key,$default=false)
{
  if(isset($_POST[$key]))
  {
    return  $_POST[$key];
  }
  else
  {
    return $default; 
  }

}

function getget($key,$default=false)
{
  if(isset($_GET[$key]))
  {
    return  $_GET[$key];
  }
  else
  {
    return $default; 
  }
}
function getrequest($key,$default=false)
{
  $value=getpost($key);
  if($value == false)
    return getget($key);
  else
    return $value;

}

function getcookie($key,$default=false)
{
  if(isset($_COOKIE[$key]))
  {
    return  $_COOKIE[$key];
  }
  else
  {
    return $default; 
  }
}

function iftrue($condition,$truevalue,$falsevalue)
{
  if($condition == true)
    return $truevalue;
  else
    return $falsevalue;
}

function iffalse($condition,$falsevalue,$truevalue)
{
  if($condition == false)
    return $falsevalue;
  else
    return $truevalue;
}

/**
 * create json error msg, format:
 * array(
 *  'error'=>1,
 *  'error_msg'=>'....',
 *  .....
 * )
 */
function errorMsg($error_msg,$params=array())
{
  $result=array(
    'error'=>1,
    'error_msg'=>$error_msg,
  );

  $result=array_merge($result,$params);

  return json_encode($result);
}

/**
 * create json success msg, format:
 * array(
 *  'error'=>0,
 *  'success_msg'=>'....',
 *  .....
 * )
 */
function successMsg($success_msg='',$params=array())
{
  $result=array(
    'error'=>0,
  );

  if($success_msg != false)
    $result['success_msg']=$success_msg;

  $result=array_merge($result,$params);

  return json_encode($result);
}
/**
 *
 */
function getMsg($json_result)
{
  $result=json_decode($json_result);
  $result=(object) $result;

  return $result;
}
/**
 * change errorReturn to errorMsg
 */
function  returnToMsg($return)
{
  $result=(array)$return;
  return json_encode($result);
}
/**
 *errorMsg to errorReturn
 */
function  msgToReturn($msg)
{
  $msg=json_decode($msg);

  return $msg;
}


/*
 * get today's datetime
 * @format string  date format
 */
function today($format="Y-m-d H:i:s")
{
  return date($format);
}
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
 * string to html attr
 * so can not contain  ' " > < \n \r 
 */
function tohtmlattr($str)
{
  $str=str_replace("\n","",$str);
  $str=htmlentities($str,ENT_QUOTES,"utf-8");
  return $str;
}

/**
* filter  ' "  for used in js code
*/
function filter_for_js($str)
{
   $str=str_replace("\n","",$str);
   $str=str_replace("\r","",$str);
   $str=str_replace("'","",$str);
   $str=str_replace('"',"",$str);
   $str=htmlentities($str,ENT_QUOTES,"utf-8");
   return $str;
}

/**
 * check if a variable is string
 */
function checkString($str,$msg='invalid string')
{
  if(!is_string($str))
  {
    error("invalid string ".$str);
  }

  $str=trim($str);
  if($str == false)
  {
    error("invalid string ".$str);
  }
}


/**
* check if  user can upload image 
*/
function  can_upload($aa_inst_id,$user_id)
{
	global $global;
	$db=$global->db; 

	$select=$db->select();
	$select->from('competition_join','count(*)');
	$select->where('aa_inst_id=?',$aa_inst_id);
	$select->where('fb_userid=?',$user_id);
	//echo $select; 
	$ret=$db->fetchOne($select);
	if($ret >= 2)
		return false;
	else
		return true;
}
//get a user  information record from table participants 
function get_user_info_byjoinid($join_id)
{
	$join=new Frd_Table_Common('competition_join','id');
	$join->load($join_id);
	$fbuserid=$join->fb_userid;

  if($fbuserid == false)
    return false;

	global $global;
	$db=$global->db; 

	$select=$db->select();
	$select->from('participants','*');
	$select->where('userId=?',$fbuserid);

	$ret=$db->fetchRow($select);

  return $ret;
}

/**
 * must with aa_inst_id in the  key ,otherwise will conflict
 */
function generateSessionKey($aa_inst_id)
{
  return "instance_".$aa_inst_id;
}

function parse_signed_request($signed_request)
{
	//$signed_request = $_REQUEST["signed_request"];
	list($encoded_sig, $payload) = explode('.', $signed_request, 2);
	$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

	return $data;

}

/**
* auth facebook
*/
function authFacebook($app_id,$app_secret,$next='',$cancel_url='')
{

	if($app_id == false || $app_secret == false)
	{
		throw new Exception("app id or app secret not exists");
	}

	//$facebook=new Facebook($api_key,$secret);
	$facebook = new Facebook(array(
    'appId' => $app_id,
    'secret' => $app_secret,
    'cookie' => true));

	try
	{
		$fbme = $facebook->api('/me');
	}
	catch (FacebookApiException $e)
	{
		$fbme = false;
		error_log($e);
		//echo $e->getMessage();
	}

	if ($fbme == false)
	{
		$params['canvas'] =1;
		$params['fbconnect'] = 0;
		$params['req_perms'] = 'publish_stream, email,user_likes';

		if($next != false)
		$params['next'] = $next;

		if($cancel_url != false)
		$params['cancel_url'] = $cancel_url;

		$login_url = $facebook->getLoginUrl($params);

		echo '<script>top.location="' . $login_url . '";</script>';
		//header("Location:$login_url");
		exit();
	}

	return true;
}

function app_is_fan($aa_inst_id)
{
	$session=new Zend_Session_Namespace("instance_".$aa_inst_id);
	if(!isset($session->is_fan) || $session->is_fan == false )
	return  false;
	else
	return true;
}

/** jquery ui helper functions **/
function ui_info_msg($msg,$id='')
{
   if($id != false)
   {
      $id='id="'.$id.'"';
   }

  $html=<<<HTML
     <div class="ui-widget" $id>
        <div style="padding: 0 .7em;" class="ui-state-highlight ui-corner-all"> 
           <p style="padding:10px;">
           <span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"></span>
           <strong>
           {$msg}
           </strong>
           </p>
        </div>
     </div>
HTML;

  echo $html;
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
?>
