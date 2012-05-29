<?php
   /**
   * framework function interface
   */
   require_once(dirname(__FILE__).'/Frd.php');

   /**
   * init framework
   */
   /*
   function init($config)
   {
      Frd::init($config);
   }
   */


   /**
   * load frd lib functions
   * function are used in global  area
   */
   function loadFunctions()
   {
      require_once(dirname(__FILE__).'/Frd/functions.php'); 
   }

   function getClass($str)
   {
      return Frd::getClass($str);
   }

   function initGlobal()
   {
      Frd::initGlobal();
   }

   /**
   * get $global
   *
   * @param  string $key  $global's key
   * @return Object  if $key not false, return $global->$key,else return $global
   */
   function getGlobal($key=null)
   {
      return Frd::getGlobal($key);
   }

   function setGlobal($key,$value)
   {
      return Frd::setGlobal($key,$value);
   }

   //add include path
   function addIncludePath($path)
   {
      Frd::addIncludePath($path);
   }

   function setRootPath($path)
   {
      Frd::setRootPath($path);
   }

   function getRootPath()
   {
      return Frd::getRootPath();
   }

   /** only for base framework setting **/
   function setSetting($key,$value)
   {
      Frd::setSetting($key,$value);
   }

   function getSetting($key)
   {
      return Frd::getSetting($key);
   }

   /*** for modules ***/
   /**
   autoload
   loadModuleClass($module);
   * loadModuleClass("block/main");
   * getModulePath($path);

   enableModule
   disableModule
   */


   /**
   * example:
   *   getModule('test','Test','param1','param2','param3'....)
   */
   function getModule($folder,$class_name=false)
   {
      return Frd::getModule($folder,$class_name);
   }

   function addDb($config,$name="default")
   {
      return Frd::addDb($config,$name);
   }

   /**
   * get db 
   */
   function getDb($name="default")
   {
      return Frd::getDb($name);
   }

   function getConfig($key=null)
   {
      return Frd::getConfig($key);
   }

   function setConfig($key,$value=false)
   {
      if(is_array($key))
      {
         foreach($key as $k=>$v)
         {
            Frd::setConfig($k,$v);
         }
      }
      else
      {
         Frd::setConfig($key,$value);
      }
   }

   function getConfigs()
   {
      return Frd::getConfigs();
   }

   /**  request **/

   function getGet($key,$default=false)
   {
      return Frd::getApp()->getGet($key);
   }

   function setGet($key,$value)
   {
      Frd::getApp()->setGet($key,$value);
   }

   function getPost($key,$default=false)
   {
      return Frd::getApp()->getPost($key,$default);
   }

   function setPost($key,$value)
   {
      Frd::getApp()->setPost($key,$value);
   }

   function getCookie($key,$default=false)
   {
      return Frd::getApp()->getCookie($key,$default);
   }

   function getRequest($key,$default=false)
   {
      return Frd::getApp()->getRequest($key,$default);
   }

   function getServer($key,$default=false)
   {
      return Frd::getApp()->getServer($key,$default);
   }
