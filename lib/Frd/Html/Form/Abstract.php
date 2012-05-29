<?php
class Frd_Html_Form_Abstract
{
  protected $params=array();  //array
  protected $notices=array();  //array
  protected $filters=null;  //Zend_Filter
  protected $validators=array();  //validators array
  protected $valid_messages=array(); //validate message array

  protected $filter_map=array(
    'int'=>'Zend_Filter_Int', 
    'alnum'=>'Zend_Filter_Alnum',
    'alpha'=>'Zend_Filter_Alpha',
    'basename'=>'Zend_Filter_BaseName',
    'digits'=>'Zend_Filter_Digits',
    'dir'=>'Zend_Filter_Dir',
    'htmlentities'=>'Zend_Filter_HtmlEntities',
    'int'=>'Zend_Filter_Int',
    'stripnewLines'=>'Zend_Filter_StripNewlines',
    'realpath'=>'Zend_Filter_RealPath',
    'lower'=>'Zend_Filter_StringToLower',
    'upper'=>'Zend_Filter_StringToUpper',
    'trim'=>'Zend_Filter_StringTrim',
    'striptags'=>'Zend_Filter_StripTags',
  );

  function __construct($name,$value,$params=array())
  {
    if( $value != false)
      $params['value']=$value;
   else
      $params['value']='';

    $params['name']=$name;
    $this->params=$params;
    //
    $this->filters=new Zend_Filter();
  }

  function getName()
  {
    return $this->params['name']; 
  }

  function addNotice($notice)
  {
    $this->notices[]=$notice; 
  }

  function getNotices()
  {
    if(count($this->notices) == 0 )
      return '';
    else if(count($this->notices) ==1 )
      return '<span class="notice">'.$this->notices[0].'</span>';
    else
    {
      return arrayToUl($this->notices,array('class'=>'notice'));
    }
  }


  function setValue($value)
  {
    $this->params['value']=$value; 
  }

  function addValidator($validator,$message=array())
  {
    if(is_array($message))
    {
      foreach($messages as $k=>$message)
        $validator->setMessage($message,$k);
    }
    $this->validators[]=$validator; 
  }

  function getValidMessages()
  {
    $name=$this-> getName();

    if(count($this->valid_messages) == 0 )
      //return '';
      return '<span class="validate '.$name.'_validate"></span>';
    else if(count($this->valid_messages) ==1 )
      return '<span class="validate '.$name.'_validate">'.$this->valid_messages[0].'</span>';
    else
    {
      return arrayToUl($this->valid_messages,array('class'=>'validate '.$name.'_validate'));
    }
  }

  function isValid()
  {
    $valid=true;

    $value=$this->getValue();

    foreach($this->validators as $validator)
    {
      if($validator->isValid($value) == false)
      {
        foreach($validator->getMessages() as $message)
          $this->valid_messages[]=$message;

        $valid=false;
      }
    }

    return $valid;
  }

  function getValue()
  {
    $value=trim($this->params['value']);

    $value=$this->filters->filter($value);
    return $value;
  }

  function filter()
  {
    $value=trim($this->params['value']);

    $value=$this->filters->filter($value);
    return $value;
  }

  function addFilter($filter)
  {
    if(is_string($filter) && isset($this->filter_map[strtolower($filter)]) )
    {
      $filter=new  $this->filter_map[strtolower($filter)];
    }

    $this->filters->addFilter($filter); 
  }

  function getFilter()
  {
  
  }

  function __toString()
  {
    $element=new Frd_Html_Element('input',$this->params);
    $html= $element->toHtml();

    return $html;
  }
}

?>
