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

   if($result == false)
   {
      echo curl_error($ch);
   }
   //close connection
   curl_close($ch);

   return $result;
}

function postFile($url,$name,$filepath,$post_array=array())
{
   if(empty($url)){ return false;}


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

   //close connection
   curl_close($ch);

   return $result;
}

