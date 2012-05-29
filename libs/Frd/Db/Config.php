<?php
/**
 * for config table,if config has extra fields, 
 * 2 ways to use this:
 *
 * A ,you should create a class extends this ,
 *     and rewrite  __get/__set/has
 *     this use 
 *       $config->name='test';
 *       $config->has('name');
 *       echo $config->name;
 * B, use itself directly
 *       $config->set($data,$where)
 *       $config->get($where);
 *       $config->has($where)
 */
class Frd_Db_Config 
{
  protected $db='';  //Zend Db Object
  protected $table=''; //table name

  function __construct($db,$table)
  {
    $this->db=$db; 
    $this->table=$table; 
  }

  function __get($key)
  {
  
  }

  function __set($key,$value)
  {
  
  }

  /**
   * get key  value
   */
  function get($column,$wheres)
  {
    $select=$this->db->select();
    $select->from($this->table,$column);

    foreach($wheres as $k=>$v)
    {
      $select->where($k."=?",$v); 
    }

    $row=$this->db->fetchOne($select);
    return $row;
  }

  /**
   * get all config values
   */
  function getAll($wheres)
  {
    $select=$this->db->select();
    $select->from($this->table,$key);

    foreach($wheres as $k=>$v)
    {
      $select->where($k."=?",$v); 
    }

    $row=$this->db->fetchAll($select);
    return $row;
  }

  /**
   * set config value
   */
  function set($data,$wheres)
  {

    if(self::has($wheres))
    {
      $where=array();
      foreach($wheres as $k=>$v)
      {
        $where[]=$this->db->quoteInto($k."=?",$v); 
      }

      $this->db->update($this->table,$data,$where);
    }
    else
    {
      $data=array_merge($data,$wheres);
      $this->db->insert($this->table,$data);
    }

		return true;
  }

  /**
   * check if record exists
   */
  function has($wheres)
  {
    $select=$this->db->select();
    $select->from($this->table,'*');

    foreach($wheres as $k=>$v)
    {
      $select->where($k."=?",$v); 
    }

    $row=$this->db->fetchOne($select);

    if($row == false)
      return false;
    else
      return true;
  }
}
