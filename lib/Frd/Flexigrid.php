<?php
/*
 * help to create array use in flexigrid  (jquery plugin)
 * if the data interface changes ,it can't change easily 
 */
class Frd_Flexigrid
{
  protected $_option=null;
  protected $_colModel=null;
  protected $_button=null;
  protected $_data=null;

  function __construct()
  {
    $this->_button=new Frd_Flexigrid_Button();
    $this->_col=new Frd_Flexigrid_Col();
  }

  function addButton($action,$label,$callback='',$class='')
  {
    $this->_button->add($action,$label,$callback,$class);
  }

  function removeButton($action)
  {
    $this->_button->remove($action);
  }

  function initData()
  {
    if($this->_data != null)
      throw new Exception("data has inited!");
    $this->_data=new Frd_Flexigrid_Data();
    return $this->_data;
  }
    function set($key,$value)
    {
      $key=strtolower($key);
      $this->_options[$key]=$value; 
    }

  function addCol($name,$label,$width='',$sortable='',$align='')
  {
    $this->_col->add($name,$label,$width,$sortable,$align);
  }

  function removeCol($name)
  {
    $this->_col->remove($name);
  }

  function getOption()
  {
    $option='{';
    $option.='colModel: '.$this->_col->toHtml();
    $option.='buttons: '.$this->_button->toHtml();
    if($this->_data instanceof Frd_Flexigrid_Data)
    {
      $option.='data: '.$this->_data->getJsonData(); 
    }

    foreach($this->_options as $key=>$value)
    {
      $option.="$key: '$option'";
    }
    $option.='}';


    return $option;
  }
}

