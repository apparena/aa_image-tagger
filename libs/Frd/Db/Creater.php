<?php
   /**
   * table creater: for  create/update/drop table,
   * so do not need execute sql by manual
   * @version 0.1
   */
   /*
   * TODO : SAFE!!
   copy total db as other db
   backup db
   restore db
   */

   class Frd_Db_Creater
   {
      protected $db=null; //db object, Zend_Db_Adapter_... instance

      protected $table=null;  //table name
      protected $primary=null; //primary key
      protected $columns=array(); //columns
      protected $engine='MyISAM'; //engine
      protected $charset='utf8';  //default charset

      function __construct($db)
      {
         if($db == false)
         {
            throw new Exception("missing parameter  db"); 
         }

         $this->db=$db;
      }

      /**
      * reset all variable to default 
      */
      function reset()
      {
         $this->table=null;
         $this->primary=null;
         $this->columns=array();
         $this->engine='MyISAM';
         $this->charset='utf8';
      }

      /**
      * check if db exists
      */
      function dbExists($dbname)
      {
         $sql=$this->db->quoteInto("show database like ?",$dbname);
         $dbname=$this->db->fetchOne($sql);

         if($dbname == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }

      /**
      * check table if exists
      */
      function tableExists($tablename,$dbname='')
      {
         //if($dbname != false)
         //$this->useDb($dbname);

         $sql=$this->db->quoteInto("show tables like ?",$tablename);
         $table=$this->db->fetchOne($sql);

         if($table == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }



      /**
      * only for in browser
      */
      /*
      function showDbs()
      {
         $sql=$this->db->quoteInto("show databases");
         $ret=$this->db->fetchAll($sql);

         $table=new Frd_Table();

         $table->setRowAttr(array('style'=>'font-weight:bold;color:gray'));
         $table->col("Database");
         $table->nextRow();
         foreach($ret as $v)
         {
            $table->col($v['Database']); 
            $table->nextRow();
         }

         echo $table->render();
      }
      */

      /**
      *
      */
      /*
      function showTables($dbname=false)
      {
         //if($dbname != false)
         //$this->useDb($dbname);

         $sql=$this->db->quoteInto("show tables");
         $ret=$this->db->fetchAll($sql);

         $table=new Frd_Table();

         $table->setRowAttr(array('style'=>'font-weight:bold;color:gray'));
         $table->col("Tables");
         $table->nextRow();
         foreach($ret as $v)
         {
            foreach($v as $k=>$tablename)
            {
               $table->col($tablename);
            }

            $table->nextRow();
         }

         echo $table->render();
      }
      */

      /**
      *
      */
      /*
      function showTableInfo($tablename,$dbname=false)
      {
         //if($dbname != false)
         //$this->useDb($dbname);

         $sql="describe `".$tablename."`";

         $ret=$this->db->fetchAll($sql);

         if($ret == false)
         var_dump($ret);
         else
         {
            $table=new Frd_Table();

            $table->setRowAttr(array('style'=>'font-size:20px;font-weight:bold;color:gray'));

            $table->colspan($tablename,6,array('style'=>'text-align:center'));
            $table->nextRow();

            $table->col("Field");
            $table->col("Type");
            $table->col("Null");
            $table->col("Key");
            $table->col("Default");
            $table->col("Extra");
            $table->nextRow();

            foreach($ret as $v)
            {
               $table->col($v['Field']);
               $table->col($v['Type']);
               $table->col($v['Null']);
               $table->col($v['Key']);
               $table->col($v['Default']);
               $table->col($v['Extra']);

               $table->nextRow();
            }

            echo $table->render();

         }
      }
      */

      /**
      * create db
      */
      function createDb($dbname)
      {
         if( $this->dbExists($dbname) == true )
         {
            throw new Exception("database $dbname alreay exists"); 
         }

         $sql=$this->db->quoteInto("create database ?",$dbname);
         $this->db->query($sql);
      }



      /**
      *
      * add column
      */
      /*
      function addColumn($name,$type,$extra='',$null='',$default='NULL')
      {
         $this->columns[$name]=array(
            'field'=>$name,
            'type'=>$type, 
            'extra'=>$extra,
            'null'=>'',
            'default'=>'NULL',
            //'key
         );
      }
      */

      /**
      *set primary
      */
      function primary($primary)
      {
         $this->primary=$primary;
      }

      /**
      *
      * create table
      */
      function save()
      {
         if($this->table == false)
         {
            throw new Exception("must set table name"); 
         }

         if($this->primary == false)
         {
            throw new Exception("must set prmary"); 
         }

         if($this->columns == false)
         {
            throw new Exception("must set columns"); 
         }

         $sql='CREATE TABLE `'.$this->table.'` (';

         foreach($this->columns as $column)
         {
            $sql.=' `'.$column['field'].'` '.$column['type'].' '.$column['extra'].' DEFAULT '.$column['default'].', '; 
         }

         $sql.='PRIMARY KEY (`'.$this->primary.'`)';
         $sql.=') ENGINE='.$this->engine.' DEFAULT CHARSET='.$this->charset;


         $this->db->query($sql);
      }

      /**
      *
      */
      function setEngine($engine="MyISAM")
      {
         $this->engine=$engine;  
      }

      /**
      *
      */
      function setCharset($charset="utf8")
      {
         $this->charset=$engine;  
      }



      /**
      * update table
      */
      function updateTable($table)
      {
         $this->reset();  
         $this->table=$table;
      }

      /**
      * updata column
      */
      function updateColumn($column,$name,$type,$extra,$null='',$default='NULL')
      {
         $sql='alter table `%s`  change `%s`  `%s` %s %s';

         $sql=sprintf($sql,$this->table,$column,$name,$type,$extra,$default); 

         $this->db->query($sql);
      }

      /**
      * drop column
      */
      function dropColumn($column)
      {
         $sql=$this->db->quoteInto('alter table ?  drop column ?',array($this->table,$column)); 

         $this->db->query($sql);
      }


      /**
      *
      */
      function dropTable($tablename)
      {
         $sql="drop  table `$tablename`";

         $this->db->query($sql);
      }

      /**
      *
      */
      function backup($dbname,$table=false)
      {

         if($table == false)
         {
            //backup all 
         }

         $shell="mysqldump ....";
      }

      /**
      *
      */
      function restore($db)
      {

         $shell="mysqldump ....";
      }

      /** change table structure **/

      /*
      * create another table
      */
      public function createTable($tablename,$columns,$engine="myisam",$default_charset='utf8')
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql=sprintf("create table `%s`.`%s`",$dbname,$tablename);
         //columns
         $sql.='(';

         $sql.=implode(",",$columns);

         $sql.=") engine=$engine default charset=$default_charset";


         //echo $sql;


         $this->db->query($sql);
         /*
         try{
            $this->_db->query($sql);
         }
         catch(Exception $e)
         {

         }
         */
      }

      /*
      function getTables($tablename)
      {

         $sql="show tables";
         $rows=$this->_db->fetchAll($sql);

         return $rows;
      }
      */


      function deleteTable($tablename)
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql="drop table  if exists `$dbname`.`$tablename`";

         $this->db->query($sql);

      }

      /**
      * get columns 
      *
      * @return array  of column's name
      */
      function getColumns($tablename=false)
      {

         //if not set , it is the current table
         if($tablename == false)
         $tablename=$this->_name;

         $columns=$this->_db->describeTable($tablename);

         foreach($columns as $k=>$column)
         {
            foreach($column as $kk=>$vv)
            {
               unset($columns[$k][$kk]);
               $columns[$k][strtolower($kk)]=$vv;
            }
         }

         return $columns;
         //return array_keys($columns);
      }
      /*
      function showColumns($tablename=false)
      {
         $columns=$this->getColumns($tablename); 

         if($tablename == false)
         $tablename=$this->_name;

         foreach($columns as $column_name=>$data)
         {
            echo "<h4>$tablename : $column_name</h4>";
            //echo "<br/>";
            //echo $data["schema_name"];
            //echo "<br/>";
            //echo $data["table_name"];
            //echo "<br/>";
            //echo    $data["column_name"];
            //echo "<br/>";
            //echo $data["column_position"];
            //echo "<br/>";
            echo $data["data_type"];
            echo "<br/>";
            //echo $data["default"];
            //echo "<br/>";
            //echo $data["nullable"];
            //echo "<br/>";
            echo $data["length"];
            //echo "<br/>";
            //echo $data["scale"];
            //echo "<br/>";
            //echo $data["precision"];
            //echo "<br/>";
            //echo $data["unsigned"];
            //echo "<br/>";
            //echo $data["primary"];
            //echo "<br/>";
            //echo $data["primary_position"];
            //echo "<br/>";
            //echo  $data["identity"];
            //echo "<br/>";
         }
      }
      */
      /**
      * add column for table

      * column(name,type
      */
      /*
      function addColumn($column,$tablename=false)
      {
         $config=$this->_db->getConfig();
         $dbname=$config['dbname'];

         if($tablename == false)
         $tablename=$this->_name;

         if(!isset($column['extra']) )
         $column['extra']='';

         if($column['not_null'] == true)
         $column['not_null']='not null';
         else
         $column['not_null']='';

         if( (strtolower($column['default']) == 'null' )
         || (!isset($column['comment']) ) )
         $column['default']='';
         else 
         $column['default']="default '".$column['default']."'";

         if(($column['collation']) == false )
         $column['collation']='';
         else 
         $column['collation']="collate '".$column['collation']."'";

         if(!isset($column['comment']) )
         $column['comment']='';
         else 
         $column['comment']="comment '".$column['collation']."'";


         $sql="alter table `$dbname`.`$tablename` add  `%s` %s %s  %s %s %s %s";

         $sql=sprintf($sql,$column['name'],$column['type'],$column['extra'],$column['not_null'],$column['default'],$column['collation'],$column['comment']);

         //echo $sql;
         $this->_db->query($sql);
      }
      */

      function editColumn($oldcolumn_name,$new_column,$tablename=false)
      {
         $config=$this->_db->getConfig();
         $dbname=$config['dbname'];

         if($tablename == false)
         $tablename=$this->_name;

         /*
         $sql="show  full columns from `$dbname`.`$tablename` like '$column'";

         $row=$db->fetchRow($sql);

         if($row['Null'] == 'YES')
         $row['Null']='not null';
         else
         $row['Null']='';

         if($row['Default'] === NULL)
         $row['Default']='';
         else
         $row['Default']=$row['Default'];
         */

         /*
         $column=array(
            'table'=>'',
            'column'=>'',
            'datatype'='',
            'extra'=>''
            'null'=>'',
            'default'=>'',
            'collation'=>'',
            'comment'=>'' ,
         );  
         */
         if(!isset($new_column['extra']) )
         $new_column['extra']='';

         if($new_column['not_null'] == true)
         $new_column['not_null']='not null';
         else
         $new_column['not_null']='';

         if( (strtolower($new_column['default']) == 'null' )
         || (!isset($new_column['comment']) ) )
         $new_column['default']='';
         else 
         $new_column['default']="default '".$new_column['default']."'";

         if(!isset($new_column['collation']) )
         $new_column['collation']='';
         else 
         $new_column['collation']="collate '".$new_column['collation']."'";

         if(!isset($new_column['comment']) )
         $new_column['comment']='';
         else 
         $new_column['comment']="comment '".$new_column['collation']."'";


         $sql="alter table `$dbname`.`$tablename` change `$oldcolumn_name` `%s` %s %s  %s %s %s %s";

         $sql=sprintf($sql,$new_column['name'],$new_column['type'],$new_column['extra'],$new_column['not_null'],$new_column['default'],$new_column['collation'],$new_column['comment']);

         //echo $sql;

         $this->_db->query($sql);
      }

      function deleteColumn($column,$table)
      {
         $config=$this->_db->getConfig();
         $dbname=$config['dbname'];

         if($tablename == false)
         $tablename=$this->_name;

         $sql="alter table  `$dbname`.`$tablename` drop column `$column`";

         $this->_db->query($sql);
      }

      /*
      create primary key
      alter table testNoPk   
      add primary key PK_testNoPK (id);  


      create  key
      CREATE [UNIQUE|FULLTEXT|SPATIAL] INDEX index_name
      [index_type]
      ON tbl_name (index_col_name,...)
      [index_type]

      index_col_name:
      col_name [(length)] [ASC | DESC]

      index_type:
      USING {BTREE | HASH}

      create foreign key
      */
      function createPrimary($keys,$tablename=false)
      {
         if($tablename == false)
         $tablename=$this->_name;

         $sql="ALTER TALBE tb_name ADD PRIMARY KEY ($keys);";

         $this->_db->query($sql);
      }

      function dropPrimary($tablename=false)
      {
         if($tablename == false)
         $tablename=$this->_name;

         $sql="ALTER TABLE `".$this->_name."` DROP PRIMARY KEY ";
         $this->_db->query($sql);
      }

      function createIndex($index_name,$type,$index_cols,$index_type=false,$tablename=false)
      {
         if($tablename == false)
         $tablename=$this->_name;

         if($index_type != false)
         $index_type="USING $index_type";

         $sql="CREATE $type INDEX $index_name $index_type ON $tablename ($index_cols)";

         $this->query($sql);
      }

      function dropIndex($key,$tablename=false)
      {
         if($tablename == false)
         $tablename=$this->_name;

         $sql="ALTER TABLE `".$this->_name."` DROP KEY $key ";
         $this->_db->query($sql);

      }


      //useful method for operate table
      function emptyTable($tablename,$dbname)
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql="delete from `$dbname`.`$tablename`";

         $this->db->query($sql);
      }

      function isEmptyTable($tablename,$dbname)
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql="select count(*) from `$dbname`.`$tablename`";

         $rows=$this->db->fetchOne($sql);

         if($rows === false)
         {
            throw new Exception("query failed");
         }
         else if($rows === 0)
         {
            return true;
         }
         else
         {
            return false;
         }
      }

      function copyTableStruct($tablename,$dbname,$newtable)
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql="create table $new_table select * from $dbname.$tablename limit 0";
         $this->db->query($sql);
      }

      function copyTable($tablename,$dbname,$newtable)
      {
         $config=$this->db->getConfig();
         $dbname=$config['dbname'];

         $sql="create table $new_table select * from $dbname.$tablename";
         $this->db->query($sql);
      }
   }

?>
