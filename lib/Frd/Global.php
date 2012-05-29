<?php
   /**
   * Frd_Global is for maintain global variables
   * for use global variables,  
   * it can  be :
   *  global $var;
   * the problems is  the variabls can be everywhere, 
   * when there has many global variables, 
   * at last you may forget where there are
   * so Frd_Global is for fix this
   * 
   * with this , you can only has one global variable, maybe it is $global,
   * or other variable, just as your wish,
   * so the usage maybe:
   *  global $global;
   *  $global->VARIABLE
   *  ...
   *  
   *  this is more clear ,and also Frd_Global support readonly variables
   *  Usage:
   *     $global=new Frd_Global();
   *     $global->register("db",DB_OBJECT,true);  //then $global->db can not modify, if edit it ,will throw an exception
   *     $global->register("config",CONFIG_OBJECT); 
   *     $global->js; //if not register js before, this will throw exception
   */
   class Frd_GLobal extends Frd_Object
   {
      protected $readonly_list=array();

      function __construct()
      {

      }

      /**
      * register a variable, then it can use
      */
      function register($key,$value,$readonly=false)
      {
         if($readonly == true)
         {
            $this->readonly_list[$key]='readonly';
         }

         parent::set($key,$value);
      }

      /**
      * check if a variable is readonly
      */
      protected function isReadonly($key)
      {
         if(isset($this->readonly_list[$key]))
         {
            return true;
         }
         else
         {
            return false; 
         }

      }

      /**
      * change a variable
      */
      function set($key,$value)
      {
         if(parent::has($key) == false)
         {
            throw new Exception("should use register to add to global !");
         }

         if($this->isReadonly($key) )
         {
            throw new Exception('can not edit readly data in global');
         }

         parent::set($key,$value);
      }

      /**
      * get a registered variable
      */
      function get($key)
      {
         $ret=parent::get($key);

         if($ret === null)
         {
            throw new Exception("$key not exists");
         }

         return $ret;
      }

      /**
      * for developer debug
      */
      function info()
      {
         var_dump(parent::getData());
         var_dump($this->readonly_list);
      }
   }
