<?php

class Frd_Html_Form_Checkbox extends Frd_Html_Form_Abstract
{
  function __construct($name,$value,$params=array())
  {
    $params['type']='checkbox';

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
