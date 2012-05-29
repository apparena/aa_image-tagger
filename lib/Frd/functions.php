<?php
/**
*
* @param string  string with split char, like  AA,BB,CC,DD,
* @return array  if string is false, return empty array
*
*/
function explodeString($string,$split_char=",")
{
   $result=array();

   $string=trim($string);
   $values=explode($split_char,$string);

   foreach($values as $v)
   {
      $v=trim($v);

      if($v != false)
      $result[]=$v;
   }

   return $result;
}


function getvalue($arr,$key,$default=false)
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

function hasvalue($arr,$key)
{
   if(isset($arr[$key]))
   {
      return true;
   }
   else
   {
      return false;
   }
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

function errorReturn($error_msg,$params=array())
{
  $result=array(
    'error'=>1,
    'error_msg'=>$error_msg,
  );

  $result=array_merge($result,$params);

  return (object)$result;
}

/**
 * create json success msg, format:
 * array(
 *  'error'=>0,
 *  'success_msg'=>'....',
 *  .....
 * )
 */
function successReturn($success_msg='',$params=array())
{
  $result=array(
    'error'=>0,
  );

  if($success_msg != false)
    $result['success_msg']=$success_msg;

  $result=array_merge($result,$params);

  return (object) $result;
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
* get absolute path by current file's path and relative path
* @param string current_file_path ,this should alwyas be __FILE__ !!!!!
*
*/
function getAbsolutePath($current_file_path,$relative_path)
{
   $path=dirname($current_file_path).'/'.$relative_path;

   return $path;
}

/**
* add last slash for path
*/
function appendSlash($path)
{
   return rtrim($path,"/")."/";
}
/** date functions **/


/**
* pick several values from array
*/
function pickArray($arr,$keys)
{
   $data=array();

   $keys=explodeString($keys);
   foreach($keys as $key)
   {
      $data[$key]=$arr[$key]; 
   }

   return $data;
}

/*
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
*/

function dump($data)
{
  if($data == false)
    var_dump($data);
  else
    print_r($data);
}


/**
* create guid
*/
function guid($namespace = '') 
{    
   static $guid = '';
   $uid = uniqid("", true);
   $data = $namespace;
   $data .= $_SERVER['REQUEST_TIME'];
   $data .= $_SERVER['HTTP_USER_AGENT'];

   //these are empty
   //$data .= $_SERVER['LOCAL_ADDR'];
   //$data .= $_SERVER['LOCAL_PORT'];
   $data .= $_SERVER['REMOTE_ADDR'];
   $data .= $_SERVER['REMOTE_PORT'];
   $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
   $guid = '{' .  
   substr($hash,  0,  8) .
   '-' .
   substr($hash,  8,  4) .
   '-' .
   substr($hash, 12,  4) .
   '-' .
   substr($hash, 16,  4) .
   '-' .
   substr($hash, 20, 12) .
   '}';
   return $guid;
}


/**
* render a html content, support simple variables : {VAR}
*
* @return string  handled content
*/
function  renderContent($content,$params=array())
{
   //variable format
   $var_format='{%s}';

   //create replace array
   $search=array();
   $replace=array();
   foreach($params as $k=>$v)
   {
      if(is_string($v) || is_numeric($v) || is_bool($v) )
      {
         //{VAR}
         $search[]=sprintf($var_format,$k);
         $replace[]=$v;
      }
   }

   //replace now
   $content=str_replace($search,$replace,$content);

   return $content;
}

function post($url,$post_array)
{

   if(empty($url)){ return false;}

   $fields_string =http_build_query($post_array);

   //open connection
   $ch = curl_init();

   //set the url, number of POST vars, POST data
   curl_setopt($ch,CURLOPT_URL,$url);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


   //curl_setopt($ch, CURLOPT_HEADER, 0);
   //curl_setopt($ch, CURLOPT_VERBOSE, 0);
   //curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

   //execute post
   $result = curl_exec($ch);

   //close connection
   curl_close($ch);

   return $result;
}

function postFile($url,$name,$filepath,$post_array=array())
{
   if(empty($url))
   { 
      return false;
   }


   //open connection
   $ch = curl_init();

   //set the url, number of POST vars, POST data
   curl_setopt($ch,CURLOPT_URL,$url);
   curl_setopt($ch, CURLOPT_POST, true);

   //$fields_string =http_build_query($post_array);
   //curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

   curl_setopt($ch, CURLOPT_HEADER, 0);
   curl_setopt($ch, CURLOPT_VERBOSE, 0);
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");

   // same as <input type="file" name="file_box">
   $post = array(
      $name=>"@$filepath",
   );

   curl_setopt($ch, CURLOPT_POSTFIELDS, $post); 

   //execute post
   $result = curl_exec($ch);

   if($result == false)
   {
      return curl_error($ch);
   }
   //close connection
   curl_close($ch);


   return $result;
}

/**
* if post failed , use this  to get error
*/
/*
function postError()
{
   return curl_error();
}
*/


/**
* parse a rewrite url, return the page
* and set the GET parameter
*
* @return string  $page , the default is "index"  , if not rewrited , return false
*/
function parse_rewrite_url($baseurl='')
{
   $request_uri=$_SERVER['REQUEST_URI'];
   $request_uri=str_replace($baseurl,'',$request_uri);

   if(strpos($request_uri,".php") !== false)
   {
      return false;
   }

   if(strpos($request_uri,".phtml") !== false)
   {
      return false;
   }

   $request_uri=trim($request_uri,"/");
   //get get parameters
   $index=strpos($request_uri,"?");
   if($index !==  false)
   {
      $get_data=substr($request_uri,$index+1);
      $request_uri=substr($request_uri,0,$index);

      //parse get parameter
      $get_data=explode("&",$get_data);
      foreach($get_data as $v)
      {
         $v=explode("=",$v);
         if(count($v) == 2)
         {
            $_GET[$v[0]]=$v[1];
         }
         else
         {
            $_GET[$v[0]]='';
         }
      }
   }

   $data=explode("/",$request_uri);

   //it must be /block/BLOCK_NAME/....
   if(count($data) < 2)
   {
      return false;
   }

   if($data[0] != "block")
   {
      return false;
   }

   $page=$data[1];
   unset($data[0]);
   unset($data[1]);

   $params=array();

   for($i=1;$i<count($data)+1;$i+=2)
   {
      if(isset($data[$i+1]))
      {
         $params[$data[$i]]=$data[$i+1];
      }
      else
      {
         $params[$data[$i]]='';
      }
   }

   foreach($params as $k=>$v)
   {
      $_GET[$k]=$v;
   }

   return $page;
}
