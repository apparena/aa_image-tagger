<?php
/* 
 *
 */
class Frd_Flexgrid_Button
{
  protected $_button=array();

  function __construct()
  {
    $this->add("add","Add");
    $this->add("show","Show");
    $this->add("edit","Edit");
    $this->add("del","Delete");
  }

  function add($action,$label,$callback='',$class='')
  {
    $action=strtolower($action);
    $this->_button[$action]=array(
      'name'=>$label,
      'bclass'=>$class,
      'onpress'=>$callback);
  }

  function remove($action)
  {
    $action=strtolower($action);
    unset($this->_button[$action]);
  }

  function toHtml()
  {
    $data=array_values($this->_button);

    return json_encode($data);
  }
}
