<?php
/*
 * use ecb to encode and decode string,
 * it is not for only one string but like functions,
 * so it should not have method like   setKey,setString
 * it need just send string and key as parameter and return encode/decode string
 *
 * Example:
 * $string="3ffdaeaerfae";
 * $key="222";
 * $ecb=new Frd_String_Ecb();
 * echo $decoded=$ecb->encode($string,$key);
 * echo "<br/>";
 * echo $ecb->decode($decoded,$key);
 *
 *
 */
class Frd_String_Ecb
{
  /*
   * make seed before encode and decode
   */
  function makeseed() 
  {
    list($usec, $sec) = explode(' ', microtime());
    return(float) $sec+((float) $usec * 100000);
  }

  /*
   * encode a string
   * @param str   string for encode
   * @param key   the encode key
   *
   * @return  string encrypted string
   */
  function encode($str,$key)
  {
    $seed=$this->makeseed();
    srand($seed);

    /* 开启加密算法/ */
    $td = mcrypt_module_open('rijndael-256','','ecb','');
    /* 建立 IV，并检测 key 的长度 */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    /* 生成 key */
    $key = substr(md5($key), 0, $ks);
    /* 初始化加密程序 */
    mcrypt_generic_init($td, $key, $iv);
    /* 加密, $encrypted 保存的是已经加密后的数据 */
    $encrypted = mcrypt_generic($td, $str);
    /* 检测加密句柄 */
    mcrypt_generic_deinit($td);

    return $encrypted;
  }

  /*
   * decode the string
   *
   * @param str   the decoded string
   * @param key   the encode key
   *
   * @return string , if decode failed, will return string you can not read,but not false!
   * so you may need check if the string correct in code
   *
   */
  function decode($str,$key)
  {
    $seed=$this->makeseed();
    srand($seed);
    /* 开启加密算法/ */
    $td = mcrypt_module_open('rijndael-256','','ecb','');
    /* 建立 IV，并检测 key 的长度 */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    /* 生成 key */
    $key = substr(md5($key), 0, $ks);
    /* 初始化加密程序 */

    /* 初始化加密模块，用以解密 */
    mcrypt_generic_init($td, $key, $iv);
    /* 解密 */
    $decrypted = mdecrypt_generic($td, $str);
    /* 检测解密句柄，并关闭模块 */
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    /* 显示原始字符串 */

    return trim($decrypted)." "; 
  }

}
