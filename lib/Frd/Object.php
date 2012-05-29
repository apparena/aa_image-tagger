<?php
   /**
   * base object , the  data is private, so subclass can not visit it directly, that's important, 
   *  if subclass also  have the attribate  $data, there will not conflict
   */
   class Frd_Object 
   {
      //main attribute
      private $data=array(); //must be private, so will not conflict with subclass

      /*
      public function __construct() 
      {
         //
      }
      */


      function __set($key,$value)
      {
         $this->set($key,$value);
      }

      function set($key,$value)
      {
         $this->data[$key]=$value;	
      }

      function __get($key)
      {
         return $this->get($key);
      }

      function __unset($key)
      {
         if($this->has($key))
         {
            unset($this->data[$key]);
         }
      }

      function __isset($key)
      {
         return $this->has($key);
      }

      function get($key)
      {
         if(isset($this->data[$key]))
         {
            return $this->data[$key];
         }
         else
         {
            return null;
         }

      }

      function has($key)
      {
         if( isset($this->data[$key]) )
         {
            return true; 
         }
         else
         {
            return false; 
         }
      }

      function setData($data)
      {
         if(!is_array($data))
         {
            echo "function setData's parameter must be array!";
         }

         foreach($data as $k=>$v)  
         {
            $this->set($k,$v);
         }
      }

      function getData()
      {
         return $this->data;
      }

   }
