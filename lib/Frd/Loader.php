<?php
   /**
   * frd's loader, it support different version
   * 
   * @version 0.0.1
   * @status  try
   */
   class Frd_Loader extends Zend_Loader
   { 
      protected static $version=false;

      public static function setVersion($v)
      {
         self::$version=$v;
      }

      public static function getVersion()
      {
         return self::$version;
      }

      public static function autoload($classname)
      {
         $path=str_replace("_","/",$classname);
         $filename=($path.".php");

         //check version
         $v=self::getVersion();
         if($v != false)
         {
            $filename=str_replace("Frd/","Frd{$v}/",$filename);
         }

         require_once($filename);
      } 

      /*
      public static function loadFile()
      {
         ;
      }
      */
      /**
      * aaa/bbb/cCC =>  Aaa_Bbb_CCC
      */
      public static function pathToClass($path)
      {
         $values=explode('/',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $class_name=implode("_",$values);

         return $class_name;
      }

      /**
      * aaa/bbb/cCC =>  Aaa/Bbb/CCC
      */
      public static function pathToRealpath($path)
      {
         $values=explode('/',$path); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         $realpath=implode("/",$values);

         return $realpath;
      }
   }

