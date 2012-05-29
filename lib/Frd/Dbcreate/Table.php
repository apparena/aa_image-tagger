<?php
/*
 * $table=new Frd_Dbcreate_Table("test","z_data");
 * $table->addColumn("id");
 * $table->getColumn("id")->datatype="int";
 * $table->getColumn("id")->len=11;
 * echo $table->toSql();
 */

require_once("Frd/Dbcreate/Column.php");
class Frd_Dbcreate_Table
{
  protected $db='';
  protected $table='';
  protected $primarykey=null;
  protected $engine='MyISAM';
  protected $columns=array();
  protected $defaultCharset='utf8';

  function __construct($tablename = null,$dbname = null)
  {
    if(is_string($tablename))
      $this->table=$tablename;

    if(is_string($dbname))
      $this->db=$dbname;
  }

  function addColumn($name)
  {
    return $this->columns[$name]=new Frd_Dbcreate_Column($name); 
  }

  function getColumn($name)
  {
    return $this->columns[$name];
  }

  function __set($key,$value)
  {
    $this->$key=$value; 
  }

  function __get($key)
  {
    return $this->$key;
  }

  /*
   * $table=new Frd_Dbcreate_Table();
   * $table->fromIni(dirname(__FILE__).'/config/config.ini','create','Frd_Dbcreate_Table');
   */
  function fromIni($iniFilePath,$section,$iniClass="Zend_Config_Ini")
  {
    if(file_exists($iniFilePath)== false)
      throw new Exception("ini file[$iniFilePath] not exists!"); 

    $config=new $iniClass($iniFilePath,$section);

    if(method_exists($config,'toArray')== false)
      throw new Exception("configClass need toArray method! ");

    $data=$config->toArray();
    $this->fromArray($data);
  }
  /* 
   * $table=new Frd_Dbcreate_Table();
   * $table->fromYaml(dirname(__FILE__).'/config/config.yaml','Frd_Yaml');
   * echo $table->toSql();
   */
  function fromYaml($yamlFilePath,$yamlClass="Frd_Yaml")
  {
    $config=new $yamlClass();

    if(method_exists($config,'toArray')== false)
      throw new Exception("configClass need toArray method! ");

    $data=$config->toArray($yamlFilePath);
    $this->fromArray($data);
  }
  function fromArray($data)
  { 
    $columns=$data['columns'];
    unset($data['columns']);

    foreach($data as $k=>$v)
      $this->$k=$v;

    foreach($columns as $name=>$data)
    {
      $this->columns[$name]=new Frd_Dbcreate_Column($name,$data); 
    }

  }
  /*
  function fromTable($database,$table)
  {
    return ; 
  }
  function _sqlFromTable($database,$table)
  {
    $sql="show create table `$database`.`$table`";
    $ret=db_query1($sql);
    foreach($ret as $k=>$v)
    {
      if($k=='Create Table') 
        $create_sql=$v; 
    }
    //var_dump($ret);
    $pattern="/(AUTO_INCREMENT=\d+)/i";
    $create_sql=preg_replace($pattern,"",$create_sql);
    db_execute("use $to");
    if(db_execute($create_sql)===false)
    {
      echo "SQL Error: ".$create_sql."\n";
    }
    else
    {
      ;//echo "create   table $from \n";
    }
  
  }
   */

  function toSql()
  {
    $columns=array();
    foreach($this->columns as $v)
      $columns[]=$v->toSql();

    $columns=implode(",\n",$columns);

    $sql="CREATE TABLE `".$this->db."`.`".$this->table."` (\n";
    $sql.=$columns;
    if($this->primarykey!=null)
    {
      $sql.=",\n";
      $sql.="primary key pk(".$this->primarykey.")\n";
    }
    else
    {
      $sql.="\n";
    }

    $sql.=") ENGINE=".$this->engine." DEFAULT CHARSET=".$this->defaultCharset;

    return $sql;
  }

  /*
   * is sql valid 
   */
  function isValid($dbClass)
  {
    $deleteSql="drop table `".$this->db."`.`".$this->table."`";

    $sql=$this->toSql();
    try
    {
      $dbClass->query($sql);
      $dbClass->query($deleteSql);
      return true;
    }
    catch(Exception $e)
    {
      return false;
    }
  }

  /*
  function toFile($fileClass,$method,$params=null,$checkIsValid=true)
  {
    if($checkIsValid === true)
    {
      if($this->isValid() === false)
        throw new Exception("SQL is not valid,do not write to file");
    } 

    $fileClass->$method($params);
  }
   */
}

