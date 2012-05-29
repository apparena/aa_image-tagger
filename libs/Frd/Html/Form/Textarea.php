<?php
class Frd_Html_Form_Textarea extends Frd_Html_Form_Abstract
{
  function __construct($name,$value,$params=array())
  {
    $params['type']='textarea'; 

    parent::__construct($name,$value,$params);

  }

  function __toString()
  {
    $element=new Frd_Html_Element('textarea',$this->params);

    $value=$this->params['value'];
    unset($this->params['value']);

    //special ,but it is text for textarea value
    $element->appendText($value);

    $html= $element->toHtml();
    return $html;
  }
}
