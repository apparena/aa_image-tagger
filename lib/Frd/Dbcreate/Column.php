<?php
class Frd_Dbcreate_Column
{
  protected $count=0;
  protected $name=null;
  protected $autoincrement=false;
  protected $datatype=null;
  protected $len=0;
  protected $default=null;
  protected $notNull=true;
  protected $charset=null;
  protected $comment=null;

  function __construct($name = null,$param=null)
  {
    if(is_string($name))
      $this->name=$name;

    if(is_array($param))
    {
      $this->set($param);
    }

  }

  function set($key,$value=null)
  {
    if(is_array($key))
    {
      foreach($key as $k=>$v)
        $this->$k=$v; 
    }
    else
    {
      $this->$key=$value; 
    }
    return $this;
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
    $name="`".$this->name."`";
    if(is_numeric($this->len) && $this->len> 0)
      $len='('.$this->len.')';
    else
      $len='';

    $datatype=$this->datatype;
    
    if($this->autoincrement == true)
      $autoincrement="auto_increment";
    else
      $autoincrement="";

    if($this->default == null)
      $default='';
    else
      $default="default '".$this->default."'";

    if($this->notNull== true)
      $notNull="NOT NULL";
    else
      $notNull='';

    if($this->charset == null)
      $charset='';
    else
      $charset="character set ".$this->charset;

    $sql="$name $datatype$len $autoincrement $charset $notNull $default";

    return $sql;
  }
}

