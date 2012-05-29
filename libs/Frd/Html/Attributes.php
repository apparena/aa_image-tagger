<?php
/**
 * http attribute
 * methodes
 *  add
 *  delete
 *  toHtml
 *
 */
 /*==================Changelog==============================
 Tue Jan 24 17:39:27 CST 2012: modify handleValue method

 ==========================================================*/

class Frd_Html_Attributes
{
  protected $attrs=array();

  function __construct($attribs=null)
  {
    if(is_array($attribs)) 
    {
      foreach($attribs as $k=>$v) 
        $this->add($k,$v);
    }
  }

  function add($key,$value)
  {
    $this->attrs[$key] =$value;
  }

  function delete($key)
  {
    unset($this->attrs[$key]);
  }

  function toHtml()
  {
    $html='';

    foreach($this->attrs as $key=>$value)
    {
      if($value!==null)
      {
        $value=$this->handleValue($value);
        //$html.=$key.'="'.$value.'" ';
        $html.=$key.'='.$value.' ';
      }
      else
      {
        $html.=$key;
      }
    }

    return " ".trim($html)."";
  }

  function __toString()
  {
    return $this->toHtml(); 
  }

  /**
   * handle value, because the value will be between ",
   * so it need handle some special characters
   */
  function handleValue($value)
  {
    //$value=htmlentities($value,ENT_QUOTES,"UTF-8"); 
    $value=Frd_String::toJsVar($value);

    return $value;
  }
}
