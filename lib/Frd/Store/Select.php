<?php
class Frd_Store_Select extends Frd_Store
{
  protected $_queue=array();

  function store($select)
  {
    $this->_queue[]=$select;   
  }

  function getLast()
  {
    $index=count($this->_queue);

    return $this->_queue[$index];
  }

  function clear()
  {
    $this->_queue=array(); 
  }
}
