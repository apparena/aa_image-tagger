<?php
/* 
 *
 */
class Frd_Flexgrid_Col
{

  protected $_cols=array();

  function __construct()
  {
  }

  function add($name,$label,$width='',$sortable='',$align='')
  {
    $name=strtolower($name);
    $this->_cols[$name]=array(
      'name'=>$name,
      'display'=>$label,
      'width'=>$width,
      'sortable'=>$sortable,
      'align'=>$align,
    );
  }

  function remove($action)
  {
    $name=strtolower($name);
    unset($this->_cols[$name]);
  }

  function toHtml()
  {
    $data=array_values($this->_cols);
    return json_encode($data);
  }
}
