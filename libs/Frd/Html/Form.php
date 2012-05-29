<?php
/*
 * @author fred<iamlosing02@gmail.com>
 *
 *
 * @version 2011-10-26
 * @update  use subclass to create elements,so in the future can extend the function
 */
class Frd_Html_Form
{
  protected $_output=false; //if true ,will echo directly ,otherwise only return the value

  function setOutput($output)
  {
    if($output == true) 
      $this->_output= true;
    else
      $this->_output=false;
  }

  function output($html)
  {
    if($this->_output == true) 
    {
      echo $html;
    }
    else
    {
      return $html;
    }
  }

  /*
   * output <br/>
   * this is very useful for test or  in working project,
   * and in form develop,before style designed, 
   * usualy need output form element for test,
   * and it is necessary
   */
  function br()
  {
    $html='<br/>';
    return $this->output($html); 
  }
  /*
   * form tag start <form ... >
   */
   function start($params=array())
   {
      $attr=new Frd_Html_Attributes($params);     

      if(isset($params['table_class']))
         $class=$params['table_class'];
      else
         $class="frd_form";

      $html= '<div class="'.$class.'"><form '.$attr->toHtml().'>';

      return $this->output($html);
   }

   /**
    * support file type element,used for upload file
    */
   function file_form_start($params=array())
   {
      $params['enctype']="multipart/form-data";
      $attr=new Frd_Html_Attributes($params);     

      $html= '<form '.$attr->toHtml().'>';

         return $this->output($html);
   }
  /*
   * form tag end </form>
   */
  function end()
  {
     $html= '</form></div>'; 
    return $this->output($html);
  }
  /*
   *
   */
  function label($name,$value)
  {
    $params=array(
      'value'=>$value, 
      'for'=>$name,
    );

    $element=new Frd_Html_Element('label',$params);

    var_dump($element->toHtml());
    return $this->output($value);
  }

  /**
   * text
   */
  function text($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Text($name,$value,$params);

    return $this->output($text);
  }

  /**
   * textarea
   */
  function textarea($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Textarea($name,$value,$params);

    return $this->output($text);
  }

  /**
   * password
   */
  function password($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Password($name,$value,$params);

    return $this->output($text);
  }
  /**
   * hidden
   */
  function hidden($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Hidden($name,$value,$params);

    return $this->output($text);
  }

  /**
   * select
   */
  function select($name,$value='',$options=array(),$params=array())
  {
    $text=new Frd_Html_Form_Select($name,$value,$options,$params);

    return $this->output($text);
  }
  /**
   *multi select
   */
  function multiselect($name,$value='',$params=array(),$options=array())
  {
    $text=new Frd_Html_Form_Multiselect($name,$value,$params=array(),$options=array());

    return $this->output($text);
  }

  /*
   *radio 
   */
  function radio($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Radio($name,$value,$params);

    return $this->output($text);
  }

  /**
   *checkbox 
   */
  function checkbox($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_Checkbox($name,$value,$params);
    return $this->output($text);
  }
  /**
   *file 
   */
  function file($name,$value='',$params=array())
  {
    $text=new Frd_Html_Form_File($name,$value,$params);
    return $this->output($text);
  }
  /**
   *submit 
   */
  function submit($value='',$params=array())
  {
    if( $value != false)
      $params['value']=$value;

    $params['type']='submit';

    $params['value']=$value;

    $element=new Frd_Html_Element('input',$params);

    $html= $element->toHtml();
    return $this->output($html);
  }
  /**
   *button 
   */
  function button($name,$value='',$params=array())
  {
    if( $value != false)
      $params['value']=$value;

    $params['type']='button';
    $params['name']=$name;
    $element=new Frd_Html('input',$params);

    $html= $element->toHtml();
    return $this->output($html);
  }
}
