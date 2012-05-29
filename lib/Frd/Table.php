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

  function __set($key,$value)
  {
     $this->_data[$key]=$value;	
     return $value;
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

  /**
  * before add/update table, filter data, only return valid columns
  */
  function getFilterData($new_data=false)
  {
     $data=array();

     if($new_data == false)
     {
        $new_data=$this->_data;
     }

     if($new_data == false)
     {
        return $data;
     }

     if(count($this->columns) > 0  )
     {
        foreach($this->columns as $k=>$v)
        {
           if(isset($new_data[$k]))
              $data[$k]=$new_data[$k];
        }

     }
     else
     {
        return $new_data;
     }

     return $data;
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
      $where=$this->buildWhere($this->primary_key,$this->primary_value);


      //set update modify field
      if($this->modified_at_field != false)
      {
         $this->_data[$this->modified_at_field]=date("Y-m-d H:i:s");
      }

      //extra fields
      foreach($this->extra_fields as $field=>$value)
      {
         $this->_data[$field]=$value;
      }

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

       //extra table with id
       $data=$this->_data;
       if(is_string($this->primary_key ))
       {
          $data[$this->primary_key]=$this->last_insert_id;
       }

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

    if (is_array($this->primary_key))
    {
       if(is_array($keys))
       {
          $where=$this->buildWhere($this->primary_key,$keys);
       }
       else
       {
          $where=$this->buildWhere($this->primary_key,func_get_args());
       }
    }
    else
    {
       //$where=$this->buildWhere($this->primary_key,$keys);
       $where=array($this->primary_key=>$keys);
    }

    $row=$this->getRow($where);

    if($row == false)
    {
       return false;
    }

    $this->_load($row);

    //return true;
    return $this;
  }

  function buildWhere($key,$value)
  {
     $where=array();

     if(is_array($key))
     {
        $amount=count($key);

        for($i=0;$i<$amount;++$i)
        {
           $k=$key[$i];
           $v=$value[$i];

           if(strpos($k,"?") !== false)
           {
              $where[]=$this->_db->quoteInto($k,$v);
           }
           else
           {
              $where[]=$this->_db->quoteInto($k."=?",$v);
           }
        }
     }
     else
     {
        if(strpos($key,"?") !== false)
        {
           $where[]=$this->_db->quoteInto($key,$value);
        }
        else
        {
           $where[]=$this->_db->quoteInto($key."=?",$value);

        }
     }

     return $where;
  }


  /**
  * load data ,return true for success  or false 
  */
  protected function _load($row)
  {
     if(empty($row))
     {
        $this->_data=array();
        $this->has_load=false;
     }
     else
     {
        $this->_data=$row;
        $this->has_load=true;

        if (is_array($this->primary_key))
        {
           $this->primary_value=array();
           foreach($this->primary_key as $key)
           {
              $this->primary_value[$key]=$this->_data[$key];
           }
        }
        else
        {
           $this->primary_value=$this->_data[$this->primary_key];
        }
     }

  }

  /**
  * @param Array where ,format:  array('name'=>'test','age'=>'11',...)
  */

  function loadWhere($where)
  {
     $row=$this->getRow($where);
     if($row == false)
     {
        return false;
     }

     $this->_load($row);

     return $true;
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
  * will execute after mehod delete
  *
  * @return boolean true or false
  */
  function afterDelete()
  {
    return true;
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
  function getOne($column,$where=array())
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,$column);
     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $select->where($k,$v);
        }
        else
        {
           $select->where($k.'=?',$v);
        }
     }

     $select->limit(1);

     //echo $select;

     $rows=$this->_db->fetchOne($select);

     return $rows;
  }

  function getRow($where=array())
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $select->where($k,$v);
        }
        else
        {
           $select->where($k.'=?',$v);
        }
     }

     $select->limit(1);

     $rows=$this->_db->fetchRow($select);

     return $rows;
  }

  function getAll($where=array(),$order=false)
  {
     if($where == false)
     {
        $where=array(); 
     }

     $select=$this->_db->select();
     $select->from($this->_name,'*');

     if(is_string($where))
     {
        $select->where($where);
     }
     else
     {
        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $select->where($k,$v);
           }
           else
           {
              $select->where($k.'=?',$v);
           }
        }
     }

     if($order != false)
     {
        $select->order($order);
     }

     $rows=$this->_db->fetchAll($select);

     return $rows;
  }

  function editWhere($where,$data)
  {
     if($this->existsWhere($where))
     {
        $real_where=array();

        foreach($where as $k=>$v)
        {
           if(strpos($k,"?") !== false)
           {
              $real_where[$k]=$v;
           }
           else
           {
              $real_where[$k.'=?']=$v;
           }
        }

        return $this->update($data,$real_where);
     }
  }

  function deleteWhere($where)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }
     return $this->delete($real_where);
  }

  function addWhere($where,$data)
  {
     $real_where=array();

     foreach($where as $k=>$v)
     {
        if(strpos($k,"?") !== false)
        {
           $real_where[$k]=$v;
        }
        else
        {
           $real_where[$k.'=?']=$v;
        }
     }

     if($this->existsWhere($real_where))
     {
        return $this->update($data,$real_where);
     }
     else
     {
        $this->add($data);
     }
  }

  function existsWhere($where)
  {
     $data=$this->getRow($where);
     if($data != false)
     {
        return true; 
     }
     else
     {
        return false;
     }
  }

  /* 
  * relation methods  
  * change this table also effect other tables
  */

  /**
  *
  */
  function update(array $data, $where)
  {
     //$this->doUpdateRelation($data,$where);
     $data=$this->getFilterData($data);

     if($data == false)
     {
        return false;
     }

     parent::update($data,$where);
  }

  function insert(array $data)
  {
     $data=$this->getFilterData($data);
     $pkData=parent::insert($data);
     return $pkData;
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

     /*
    if(is_array($key))
    {
      foreach($key as $k)
      {
        $where=$this->_db->quoteInto($this->primary_key."=?",$key);

        //$this->doDeleteRelation($where);
        $count=parent::delete($where);
      }
    }
    else
    */

    if(is_array($this->primary_key))
    {
       $args=func_get_args();
       $where=array();
       foreach($args as $k=>$v)
       {
          $where[]=$this->_db->quoteInto($this->primary_key[$k]."=?",$v);
       }
    }
    else
    {
       if(is_array($key))
       {
          foreach($key as $k=>$v)
          {
             $where[]=$this->_db->quoteInto($k,$v);
          }
       }
       else
       {
          $where=$this->_db->quoteInto($this->primary_key."=?",$key);
       }
    }

    //$this->deleteRelation($where);
    $count=parent::delete($where);

     if( $this->afterDelete() == false)
     {
        //return false;
     }

    return $count;
  }

  /*** ***/
  /*
  protected $_delete_relations=array();
  function addDeleteRelation($key,$table)
  {
     $this->_delete_relations[]=array(
        'key'=>$key,
        'table'=>$table,
     );
  }

  function deleteRelation($where)
  {
     if ($this->_delete_relations == false)
     {
        return false;
     }

     $data=$this->getRow($where);
     if($data == false)
     {
        return false;
     }

     foreach($this->_delete_relations as $relation)
     {
        $where=array();

        $key=$relation['key'];
        $table=$relation['table'];

        if(is_string($key))
        {
           $where["$key=?"]=$data[$key];
        }
        else if(is_array($key))
        {
           foreach($key as $k)
           {
              $where["$k=?"]=$data[$k];
           }
        }

        $table->deleteWhere($where);
     }

  }
  */


  /** table relation **/
  protected $relations=array();
  function addRelation($name,$table)
  {
     $this->relations[$name]=$table;
  }

  function getRelation($name)
  {
     if(!isset($this->relations[$name]))
     {
        return false;
     }

     if($this->getData() == false)
     {
        return false;
     }

     $table=$this->relations[$name];
     $primary=$table->getPrimary();

     if(is_array($primary))
     {
        foreach($primary as $k)
        {
           if(isset($this->$k) == false)
           {
              return false;
           }
        }

        if( $table->load($this->$primary) == true)
        {
           return $table;
        }

        return false;
     
     }
     else if(is_string($primary))
     {
        if(isset($this->$primary) == false)
        {
           return false;
        }

        if( $table->load($this->$primary) == true)
        {
           return $table;
        }

        return false;
     }
  
  }


}
