<?php
/*
 * $db=new Frd_Dbcreate_Db('test');
 * echo $db->toSql();
 */

class Frd_Dbcreate_Db
{
  protected $name=null;

  function __construct($name = null)
  {
    if(is_string($name))
      $this->name=$name;

  }

  function __set($key,$value)
  {
    $this->$key=$value; 
  }

  function __get($key)
  {
    return $this->$key;
  }

  function toSql()
  {
    $sql="create database `".$this->name."`\n";
    return $sql;
  }
}

