<?php
   /**
   * frd 's setting
   */
   class Frd_Setting
   {
      public static $exception_output=true;

      //modules path
      public static $module_path=false;

      public static function get($key)
      {
         ;
      }

      public static function set($key)
      {
         ; 
      }

      public static function check($key)
      {
         if(self::$$key == false)
         {
            throw new Exception('setting not set: module_path');
         }
      }

   }
