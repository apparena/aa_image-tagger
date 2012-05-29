<?php
class Frd_Html_Form_Multiselect extends Frd_Html_Form_Abstract
{
  function __construct($name,$value=null,$params=array(),$options=array())
  {
    $this->options=$options;
    $params['multiple']=null;

    parent::__construct($name,$value,$params);
  }


  function __toString()
  {
    $selected=$this->params['value'];
    unset($this->params['selected']);

    $element=new Frd_Html_Element('select',$params);
    foreach($options as $value=>$text)
    {
      if(is_array($selected)  && in_array($value,$selected))
        $element->add('option',array('value'=>$value,'selected'=>'selected'),$text);
      else
        $element->add('option',array('value'=>$value),$text);
    }

    $html= $element->toHtml();
    return $html;
  }
}
?>
