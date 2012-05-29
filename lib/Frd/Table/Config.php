<?php
/**
 * for config table, only for simple config table
 * a config table must have a column for key, and a column for value
 *
 */
class Frd_Table_Config 
{
  protected $data=array();  //config data
  protected $db=null;   //zend db instance

  protected $table=null;   //config table name
  protected $key_name=null;  //config table key column name
  protected $value_name=null;  //config table value column name

  function __construct($db,$table,$key,$value)
  {
    $this->db=$db;
    $this->table=$table;
    $this->key_column=$key;
    $this->value_column=$value;


    $select=$db->select();
    $select->from($table,array($key,$value));

    $data=$db->fetchPairs($select);

    if($data == false)
      $data=array();

    $this->data=$data;
  }

  /**
   * get config data
   */
  function getData()
  {
    return $this->data; 
  }

  /**
   * merge data
   */
  function setData($data)
  {
    if(is_array($data))
    {
      $this->data=array_merge($data,$this->data);  
    }
  }

  /**
   * get config value,if not exists, return default value
   */
  function get($key,$default=null)
  {
    if($this->data == false)
      return $default;

    if(isset($this->data[$key]))
      return $this->data[$key];
    else
      return $default;
  }

  /**
   * check if has this config key 
   *
   * @return true for has ,otherwise  false
   */
  function has($key)
  {
    if($this->data == false)
      return false;

    if(isset($this->data[$key]))
      return true;
    else
      return false;
  }

  /**
   * set config value
   */
  function set($key,$value)
  {
    if($key == false)
      return false;

    if($this->has($key))
    {
      $where=$this->db->quoteInto("{$this->key_column}=?",$key);
      $data=array(
        $this->value_column => $value,
      );

      $this->db->update($this->table,$data,$where);

    }
    else
    {
      $data=array(
        $this->key_column=>$key,
        $this->value_column => $value,
      );

      $this->db->insert($this->table,$data);
    }

  }

  /**
   * delete config key
   */
  function delete($key)
  {
    if($this->has($key))
    {
      $where=$this->db->quoteInto("{$this->key_column}=?",$key);

      $this->db->delete($this->table,$where);
    }
  }
}
