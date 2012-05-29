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
   $is_fan=false;

  if(isset($_REQUEST['signed_request']))
  {
    $signed_request = $_REQUEST["signed_request"];
    list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    if(isset($data['page']))
    {
       $is_fan=$data['page']['liked'];
    }
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
  $table=new Frd_Db_Table($table,$primary);
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


/**
* Returns all available app_data parameters by decoding facebooks signed_request.
* The function tries to json_decode as well several parameters.
* @return Array Array with decoded parameters
*/
function get_app_data() {
	if(isset($_REQUEST['signed_request']))
	{
		$signed_request = $_REQUEST["signed_request"];
		list($encoded_sig, $payload) = explode('.', $signed_request, 2);
		$data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	
		if(isset($data['app_data']))
			$app_data=$data['app_data'];
		else
			$app_data=false;
	}
	else
	{
		$app_data=false;
	}
	return $app_data;
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
	$join=new Frd_Db_Table('competition_join','id');
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

/**
* save question
*/
function save_question($aa_inst_id,$fb_user_id,$data)
{
   if($data == false)
   {
      return false;
   }

   global $session;

   $award_selection =$data['award'];
   $question1_answer=$data['question1'];
   $question2_answer=$data['question2'];
   $question3_answer=$data['question3'];


   // get app_participation id
   $lottery = new iCon_Lottery($aa_inst_id,getConfig('aa_app_id'));
   $id=$lottery->isUserParticipating($fb_user_id, $aa_inst_id) ;

   $table=new Table_Participation();
   $table->load($id);

   $table->fb_user_id=$fb_user_id;
   $table->aa_inst_id=$aa_inst_id;
   //check if answers is correct
   //if questions > 0 && == correct answer , it means  correct
   $answers_correct=true;

   if($question1_answer > 0 && $question1_answer != $session->config['question1_correct_answer']['value'])
   {
      $answers_correct = false;
   }

   if($question2_answer > 0 && $question2_answer != $session->config['question2_correct_answer']['value'])
   {
      $answers_correct = false;
   }

   if($question3_answer > 0 && $question3_answer != $session->config['question3_correct_answer']['value'])
   {
      $answers_correct = false;
   }

   if($question1_answer == false && $question2_answer == false && $question3_answer == false)
   {
      $answers_correct=false;
   }

   $table->answers_correct =$answers_correct;


   $table->award_selection =$award_selection;
   $table->question1_answer=$question1_answer;
   $table->question2_answer=$question2_answer;
   $table->question3_answer=$question3_answer;

   $table->ip= getClientIp();

   $lastinsert_id=$table->save();

   return $lastinsert_id;
}

/**
* change a datetime to stand datetime format: Y-m-d H:i:s 
*/
function format_datetime($datetime,$format="Y-m-d H:i:s")
{
   return date($format,strtotime($datetime)) ;
}


?>
