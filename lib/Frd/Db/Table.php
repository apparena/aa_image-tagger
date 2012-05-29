<?php
/**
 * for table which only have a column as primary key (e.g. id)
 *
 * functions:
 *  load :  load a record
 *  loadBy:  load record(s) by column
 *  save:   update or insert
 *  delete:  delete a record
 *  getColumns:  get column names
 *
 * Usage:
 *  $table=new Frd_Table_Common('users','id');
 *  $table->name='fred';
 *  echo $table->save();
 *
 *  if($table->load(1) == true)
 *    echo $table->name; 
 *
 *  $table->delete(1);
 *
 *  @version 2011-12-14
 *  @update  add simple select function,add before/after for add/edit/delete
 */
class Frd_Db_Table  extends Zend_Db_Table
{
  protected $_data=array();
  protected $has_load=false;

  protected $primary_key=null; //primary key ,can be string or array (for multi primary keys)
  protected $primary_value=null; //primary key is primary_key
  protected $last_insert_id=0;

  protected $columns=array(); //tables

  protected $created_at_field=null; //if set this field, will set this value aotomatic
  protected $modified_at_field =null; //if set this field, will set this value aotomatic
  protected $extra_fields=array(); //extra fields will always set ,so user do not need  set the value again
  /**
   * @param table   string   table name
   * @param primary string   primary key
   * @param boolean getcolumns if get columns, in set,save will filter the column which not in  this table automatic,but will need more resource ,because it need query the dabase to get columns,so if have cache , then it will be always true,now you need change , it can also be array , which is the column's config
   */
  function __construct($table,$primary,$getcolumns=false)
  {
    parent::__construct();

    $this->_name=$table;
    $this->_primary=$primary;

    $this->primary_key=$primary;

    /*
    //check if table name is correct
    try{
      $this->find(1);
    }
    catch(Zend_Db_Table_Exception $e)
    {
      echo "Table or Primary key not exists ( $table.$primary )";
      echo "<br/>\n";
      echo __FILE__;
      echo " : ";
      echo __LINE__;
      echo "<br/>\n";
      echo "Detail:".$e->getMessage(); 
    }
     */

    //if necessary ,get columns
    if($getcolumns === true)
    {
      $this->columns=$this->getColumns();
    }
    else if(is_array($getcolumns))
    {
      $columns=$getcolumns;
      if(isset($columns[0]))
      {
        foreach($columns as $column)
        {
          $this->columns[$column]=array();
        }
      }
      else
      {
        $this->columns=$columns; 
      }
    }
    else
    {
      $this->columns=array();
    }
  }

  /**
  * table's name
  */
  function getName()
  {
     return $this->_name; 
  }

  /**
  * table's primary
  */
  function getPrimary()
  {
     return $this->_primary; 
  }

  /**
   * if a recored exists
   * usage:
   *   $table->exists($id);
   *   $table->exists($key1,$key2,....)
   */
  function exists()
  {
    $select=$this->_db->select();
    $select->from($this->_name,'count(*)');

    $args=func_get_args();

    if(is_array($this->primary_key))
    {
      $where=array();
      foreach($args as $k=>$v)
      {
        $select->where($this->primary_key[$k]."=?",$v);
      }
    }
    else
    {
      $select->where($this->primary_key."=?",$args[0]);
    }

    $count=$this->_db->fetchOne($select);
    if($count > 0) 
      return true;
    else
      return false;
  }

  function __set($key,$value)
  {

    if(count($this->columns) > 0 && !isset($this->columns[$key]) )
    {
      echo "field $key not exists in columns (".implode(",",array_keys($this->getColumns())).")";

      //throw new Exception("field $key not exists in columns (".implode(",",$this->getColumns()).")");
      exit();
    }
    else
    {
       $this->_data[$key]=$value;	
    }
  }

  /**
   * set many data in once
   *
   * @param Array  columns data like (COLUMN1=>'',COLUMN2=>'')
   */
  function setData($data)
  {
    if(!is_array($data))
    {
      echo "function setData's parameter must be array!";
    }
    foreach($data as $k=>$v)  
    {
      $this->$k=$v; 
    }
  }

  function getData()
  {
     return $this->_data; 
  }


  /**
   * get column
   *
   * @param key column name
   * @return the column's value  or null if not exists (must use  === null to check it)
   */
  function __get($name)
  {
    if(isset($this->_data[$name]))
    {
      return $this->_data[$name];
    }
    else
    {
      return null;
    }
  }


  /**
   * check isset column
   *
   * @return boolean true if isset
   */
  function __isset($name)
  {
    if(isset($this->_data[$name]))
    {
       return true;
    }
    else
    {
       return false;
    }
  }
  /**
   * save recored 
   *
   * @return  lastinertid  for update is 0 , for insert is the last insert id
   */
  function save()
  {
    //if has table's columns,
    //remove value not columns from data
    if(count($this->columns) > 0)
    {
      $value='';
      $type=false;
    }

    if($this->has_load == true)
    {
       //update

      if(is_array($this->primary_key))
      {
        $where=array();
        foreach($this->primary_value as $k=>$v)
        {
          $where[]=$this->_db->quoteInto($this->primary_key[$k]."=?",$v);
        }
      }
      else
      {
        $where=$this->_db->quoteInto($this->primary_key."=?",$this->primary_value);
      }

      //set update modify field
      if($this->modified_at_field != false)
        $this->_data[$this->modified_at_field]=date("Y-m-d H:i:s");

      $count=$this->update($this->_data,$where);
      return $count;
    }
    else
    {
       //insert
      if($this->created_at_field != false)
        $this->_data[$this->created_at_field]=date("Y-m-d H:i:s");
      if($this->modified_at_field != false)
        $this->_data[$this->modified_at_field]=date("Y-m-d H:i:s");

        //extra fields
        foreach($this->extra_fields as $field=>$value)
        {
           $this->_data[$field]=$value;
        }


      $this->last_insert_id=$this->insert($this->_data);
      return $this->last_insert_id;
    }
  }


  /**
   * load recored 
   *
   * @param id int   primary key
   *
   * @return  true if load success, false if failed (not exists or exception)
   */
  function load($keys=array())
  {
    $success=false;

    if(is_array($keys))
    {
      $args=$keys;
      $nums=count($keys);
    }
    else
    {
      $args=func_get_args();
      $nums=func_num_args();
    }


    //pass not known number parameters, can only with hardcode,
    //maybe can dynamic ,i do not known
    //so that's a ugly way,and only support 5 numbers, 
    //if more, need add by manual
    if($nums == 1)
      $rows=$this->find($args[0]);
    else if($nums == 2)
      $rows=$this->find($args[0],$args[1]);
    else if($nums == 3)
      $rows=$this->find($args[0],$args[1],$args[2]);
    else if($nums == 4)
      $rows=$this->find($args[0],$args[1],$args[2],$args[3]);
    else if($nums == 5)
      $rows=$this->find($args[0],$args[1],$args[2],$args[3],$args[4]);
    else
    {
      echo "Sorry,i can not support more  primary key numbers,please add more number to support it";
      echo "\n<br/>";
      echo __FILE__;
      echo ":";
      echo __LINE__;
      exit();
    }

    $rows=$rows->toArray(); 
    if($rows == false)
    {
       return false;
    }
    $row=$rows[0];

    $success=$this->_load($row);

    return $success;
  }

  /**
  * load data ,return true for success  or false 
  */
  protected function _load($rows)
  {
     if(empty($rows))
     {
        $this->_data=array();
        $success=false;
        $this->has_load=false;
     }
     else
     {
        $success=true;
        $this->_data=$rows;
        $this->has_load=true;

        if (is_array($this->primary_key))
        {
           $this->primary_value=array();
           foreach($this->primary_key as $key)
           {
              $this->primary_value[]=$this->_data[$key];
           }
        }
        else
        {
           $this->primary_value=$this->_data[$this->primary_key];
        }
     }

     return $success;
  }
  /**
   * load by another column
   *
   * @return array   recored which column = $value , if do not have any recored, return array()
   */
  function loadBy($name,$value)
  {
    $success=false;

    //handle column name
    $name=$this->handleColumnName($name);

    $select=$this->_db->select();

    $select->from($this->_name);
    $select->where($name."=?",$value);
    $select->limit(1);

    try
    {
      //if not exists, result is array()
      //so it can used in 
      //foreach($rows as $row)
      // ....
      // or == false

      $row=$this->_db->fetchRow($select);

      $success=$this->_load($row);

      return $success;
    }
    catch(Exception $e)
    {
      echo $e->getMessage(); 
      echo "<br/>\n";
      echo __FILE__;
      echo ' : ';
      echo __LINE__;
    }
  }


  /**
  * @param Array where ,format:  array('name'=>'test','age'=>'11',...)
  */

  function loadWhere($wheres)
  {
     $select=$this->_db->select();
     $select->from($this->_name,'*');
     foreach($wheres as $k=>$v)
     {
        $select->where($k.'=?',$v);
     }

     $row=$this->_db->fetchRow($select);

     $success=$this->_load($row);

     return $success;
  }

  /**
   * after save , get lastinsertid
   */
  function lastinsertid()
  {
    return $this->last_insert_id;
  }

  /**
  * will execute before mehod delete
  *
  * @return boolean true or false
  */
  function beforeDelete()
  {
    return true;
  }

  /**
   * delete recored
   *
   * @param key    primary key ,or array of primary key
   *
   *
   */
  function delete($key)
  {
     if( $this->beforeDelete() == false)
     {
        return false;
     }

    if(is_array($key))
    {
      foreach($key as $k)
      {
        $where=$this->_db->quoteInto($this->primary_key."=?",$key);
        $count=parent::delete($where);
      }
    }
    else
    {
      $args=func_get_args();

      if(is_array($this->primary_key))
      {
        $where=array();
        foreach($args as $k=>$v)
        {
          $where[]=$this->_db->quoteInto($this->primary_key[$k]."=?",$v);
        }
      }
      else
      {
        $where=$this->_db->quoteInto($this->primary_key."=?",$key);
      }
      $count=parent::delete($where);
    }

     if( $this->afterDelete() == false)
     {
        //return false;
     }

    return $count;
  }

  /**
  * will execute after mehod delete
  *
  * @return boolean true or false
  */
  function afterDelete()
  {
    return true;
  }

  /**
   * return column data 
   */
  function toArray()
  {
    return $this->_data;
  }

  /**
   * handle  column's name for safe
   * column name can not have (',",\,/,?...)
   *
   * @param name string column name
   * @return name string handled  column name
   */
  function handleColumnName($name)
  {
    $filter=array('"',"'",'/','\\','?',';');
    $replace=array('','','','','','');
    return str_replace($filter,$replace,$name);
  }

  function beforeAdd()
  {
     return true; 
  }

  /**
   * add a record
   */
  function add($data)
  {
     if( $this->beforeAdd() == false)
     {
        return false; 
     }

    $this->setData($data);

    $ret=$this->save();

     if( $this->afterAdd() == false)
     {
        return false; 
     }

    return $ret;
  }

  function afterAdd()
  {
     return true; 
  }

  function beforeEdit()
  {
    return true;
  }

  /**
   * edit a recored
   */
  function edit($key,$data)
  {
     if( $this->beforeEdit() == false)
     {
        return false; 
     }

    $this->load($key);
    $this->setData($data);
    $ret=$this->save();

    $this->afterEdit();
    return $ret;
  }

  function afterEdit()
  {
    return true;
  }
  /**
   * helper method
   */

  function setCreatedAt($field_name='created_at')
  {
    $this->created_at_field=$field_name;
  }

  function setModifiedAt($field_name='modified_at')
  {
    $this->modified_at_field=$field_name;
  }

  function addExtraField($field,$value)
  {
     $this->extra_fields[$field]= $value;
  }

  function removeExtraField($field,$value)
  {
     unset($this->extra_fields[$field]);
  }

  /** fetch methods **/
  function getOne($column,$wheres=array())
  {
     if($wheres == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,$column);
     foreach($wheres as $k=>$v)
     {
        $select->where($k,$v);
     }

     $select->limit(1);

     //echo $select;

     $rows=$this->_db->fetchOne($select);

     return $rows;
  }

  function getRow($wheres=array())
  {
     if($wheres == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');
     foreach($wheres as $k=>$v)
     {
        $select->where($k,$v);
     }

     $select->limit(1);

     $rows=$this->_db->fetchRow($select);

     return $rows;
  }

  function getAll($wheres=array(),$order=false)
  {
     if($wheres == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');
     foreach($wheres as $k=>$v)
     {
        $select->where($k,$v);
     }

     if($order != false)
     {
        $select->order($order);
     }

     $rows=$this->_db->fetchAll($select);

     return $rows;
  }

  function editWhere($wheres,$data)
  {
     return $this->_db->update($this->_name,$data,$wheres);
  }

  function deleteWhere($wheres)
  {
     return $this->_db->delete($this->_name,$wheres);
  }

  function addWhere($wheres,$data)
  {
     if($this->existsWhere($wheres))
     {
        return $this->_db->update($this->_name,$data,$wheres);
     }
     else
     {
        $this->add($data);
     }
  }

  function existsWhere($wheres)
  {
     $data=$this->getRow($wheres);
     if($data != false)
     {
        return true; 
     }
     else
     {
        return false;
     }
  }
}
