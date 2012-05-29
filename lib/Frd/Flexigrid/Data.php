<?php
/*
 * help to create array use in flexigrid  (jquery plugin)
 * if the data interface changes ,it can't change easily 
 */
class Frd_Flexigrid_Data
{
  protected $_data=array(
    'total'=>0,
    'page'=>1,
    'rows'=>array(),
  );
  /*
  'rows'=>array(
   array('cell' =>array('1','12','13')),
   array('cell' =>array('2','22','23')),
    )
   */
  function  __construct()
  {
  }
  function setTotal($total)
  {
    $this->_data['total']=$total; 
  }

  function setPage($page)
  {
    $this->_data['page']=$page; 
  }
  function addRow(array $cell)
  {
    $cell=array_values($cell);
    $this->_data['rows'][]=array('cell'=>$cell);
  }

  function addRows(array $cells)
  {
    foreach($cells as $cell)
    {
      $cell=array_values($cell);
      $this->_data['rows'][]=array('cell'=>$cell);
    }
  }
  function addCol(array $col,$pos=1)
  {
    $pos=$pos-1;
    foreach($this->_data['rows'] as $k=>$row)
    {
      $this->_data['rows'][$k]['cell']=Func::array_insert($row['cell'],$pos,$col[$k]);
    }
  }

  function getData()
  {
    return $this->_data;
  }

  function getJsonData()
  {
    return json_encode($this->_data); 
  }
}

