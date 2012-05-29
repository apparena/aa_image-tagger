<?php

class Frd_Html_Form_Select extends Frd_Html_Form_Abstract
{
  function __construct($name,$value=null,$options=array(),$params=array())
  {
    $this->options=$options;

    parent::__construct($name,$value,$params);
  }


  function __toString()
  {
    $selected=$this->params['value'];
    unset($this->params['selected']);

    $element=new Frd_Html_Element('select',$this->params);
    foreach($this->options as $value=>$text)
    {
      if($selected != false && $selected == $value)
        $element->add('option',array('value'=>$value,'selected'=>'selected'),$text);
      else
        $element->add('option',array('value'=>$value),$text);
    }

    $html= $element->toHtml();
    return $html;
  }
}
?>
