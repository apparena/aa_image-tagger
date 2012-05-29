<?php
   /**
   *
   */
   class Fluttery_Config
   {
      protected $data=array();

      function __construct($data)
      {
         $this->data=$data;
      }

      function __get($key)
      {
         return $this->getValue($key);

      }

      /**
      * get config's value
      */
      function getValue($key)
      {
         if(isset($this->data[$key]))
         {
            return $this->data[$key]['value'];

         }
         else
         {
            //when not exists, return null
            //not throw exception 
            return null;
            
          }
      }

      /**
      * get config's type ( text,date,html,image...)
      */
      function getType($key)
      {
         if(isset($this->data[$key]))
         {
            return $this->data[$key]['type'];
         }
         else
         {
            return null;
         }

      }
   }
