<?php
   /**
   * it should take all sql operate, 
   * should known all sql , before execute them 
   * should support multiple db later
   */
   class Frd_Db
   {
      protected $db=null;
      protected $is_default=false;

      function __construct($adapter=false, $config = array())
      {
         if($config != false)
         {
            $this->setDefaultDb($adapter,$config);
         }
      }

      function init($adapter,$config=array(),$default=false)
      {
         $this->db=Zend_Db::factory($adapter,$config);
         $this->db->query('set names utf8');

         if($default == true)
         {
            Zend_Db_Table::setDefaultAdapter($this->db);
            $this->is_default=true;
         }
      }

      function query($sql)
      {
         return $this->db->query($sql);
      }

      function __call($name,$params)
      {
         return call_user_func_array(array($this->db,$name),$params);
      }

      /*
      function getDb()
      {
         return $this->db;
      }
      */
      function getDb()
      {
         return $this->is_default;
      }
   
   }
