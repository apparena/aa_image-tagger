<?php

class Frd_Html_Form_File extends Frd_Html_Form_Abstract
{
  function __construct($name,$value,$params=array())
  {
    $params['type']='file';

    parent::__construct($name,$value,$params);
  }


  function __toString()
  {
    $element=new Frd_Html_Element('input',$this->params);
    $html= $element->toHtml();

    return $html;
  }
}
?>
