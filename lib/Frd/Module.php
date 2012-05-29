<?php 
   /**
   * module ,each module's main class should extends from this class
   *
   * @version 0.0.1
   * @status  try
   */
   class Frd_Module
   {
      protected $is_enable=true;

      protected $_folder=''; //the module's folder,used for get other class of the module 

      final function init($folder)
      {
         $this->_folder=ltrim($folder,'/');
      }

      function isDisable()
      {
         if( $this->is_enable == true )
         {
            return true;
         }
         else
         {
            return false;
         }
      }
      function disable()
      {
         $this->is_enable=false;

      }
      function enable()
      {
         $this->is_enable=true;
      }

      /**
      * must pass module test then can use
      */
      /*
      function test()
      {

         ;
      }
      */

      /*
      function register()
      {

         ;
      }
      */
      function install()
      {
         ;

      }

      function uninstall()
      {
         ;

      }

      function isInstalled()
      {
         ;

      }

      function getInstaller($path=false)
      {
         if($path == false)
         {
            //default is Install.php
            return $this->getClass('install','Install');
         }
         else
         {
            return $this->getClass($path,'Install');
         }
      }

      function getTable($path)
      {
         return $this->getClass($path,'Table','Table');
      }

      function getForm($path)
      {
         $params=func_get_args();
         array_shift($params); //unset path

         return $this->getClass($path,'Form','Form',$params);
      }

      function getTemplatePath($template_path)
      {
         $module_path=getSetting("module_path");

         $file=rtrim($module_path).'/'.$this->_folder."/$template_path";

         return $file;
      }

      function getBlock($path)
      {
         return $this->getClass($path,'Block','Block');
      }

      function getConfig()
      {
         return $this->getClass('config','');
      }

      function getModel($path)
      {
         return $this->getClass($path,"Model");
      }

      /**
      * class are under  Model
      */
      function getClass($path,$prefix='Model',$class_prefix=false,$params=array())
      {
         $name=Frd_Loader::pathToClass($path);
         $realpath=Frd_Loader::pathToRealpath($path);


         if($prefix != false)
         {
            $file=$this->_folder."/$prefix/$realpath.php";
         }
         else
         {
            $file=$this->_folder."/$realpath.php";
         }

         require_once($file);

         //add module name for class name
         $class_name=get_class($this);
         if($class_prefix != false)
         {
            $class_name.='_'.$class_prefix.'_'.$name;
         }
         else
         {

            $class_name.='_'.$name;
         }

         $class=Frd::_getClass($class_name,$params);

         return $class;
      }

   }
