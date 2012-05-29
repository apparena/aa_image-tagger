<?php
   /**
   * contain most functions for framework
   *
   * @version 0.0.1
   * @status  try
   *
   */
   class Frd
   {
      public static $inited=false;  //does it inited

      public static $dbs=false;
      public static $modules=false;
      public static $app=false; //app object

      /**
      * init framework
      */
      public static function init($config)
      {
         $timezone=$config['timezone'];
         $root_path=$config['root_path'];
         $include_paths=$config['include_paths'];
         $module_path=$config['module_path'];

         //init
         self::setTimeZone($timezone);

         //auto load
         foreach($include_paths as $include_path)
         {
            self::addIncludePath($include_path);
         }
         self::enableAutoload();

         //global 
         self::initGlobal();
         self::setRootPath($root_path);

         //exception
         self::setSetting("exception_output",true);
         self::handleException();

         //module
         self::setSetting("module_path",$module_path);

         //app
         $app=new Frd_App();
         self::$app=$app;


         //include framework function interface
         require_once(dirname(__FILE__).'/framework.php');

         //set flat
         self::$inited=true;
      }


      /**
      * load frd lib functions
      * function are used in global  area
      */
      public static function loadFunctions()
      {
         require_once(dirname(__FILE__).'/Frd/functions.php'); 
      }

      public static function getClass($str)
      {
         $args=func_get_args();

         if($args == false)
         {
            throw new Exception('get class no parameter');
         }

         $str=$args[0];

         $values=explode('/',$str); 

         foreach($values as $k=>$value)
         {
            $values[$k]=ucfirst($value); 
         }

         //class __construct params
         $params=array_slice($args,1);
         $class_name=implode("_",$values);

         $class=self::_getClass($class_name,$params);

         return $class;
      }

      /**
      *
      */
      public static function _getClass($class_name,$params=array())
      {
         if(count($params) == 0)
         {
            $class=new $class_name();
         }
         else if(count($params) == 1)
         {
            $class=new $class_name($params[0]);
         }
         else if(count($params) == 2)
         {
            $class=new $class_name($params[0],$params[1]);
         }
         else if(count($params) == 3)
         {
            $class=new $class_name($params[0],$params[1],$params[2]);
         }
         else if(count($params) == 4)
         {
            $class=new $class_name($params[0],$params[1],$params[2],$params[3]);
         }
         else if(count($params) == 5)
         {
            $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4]);
         }
         else if(count($params) == 6)
         {
            $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5]);
         }
         else if(count($params) == 7)
         {
            $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6]);
         }
         else if(count($params) == 8)
         {
            $class=new $class_name($params[0],$params[1],$params[2],$params[3],$params[4],$params[5],$params[6],$params[7]);
         }
         else
         {
            throw new Exception("sorry , getClass only support max 8 params");
         }

         return $class;
      }


      public static function initGlobal()
      {
         if( Zend_Registry::isRegistered("Frd_GLOBAL") == false)
         {
            $global=new Frd_Global();

            Zend_Registry::set("Frd_GLOBAL",$global);
         }
      }

      /**
      * get $global
      *
      * @param  string $key  $global's key
      * @return Object  if $key not false, return $global->$key,else return $global
      */
      public static function getGlobal($key=null)
      {
         //$registry=Zend_Registry::getInstance();

         //var_dump($registry->isRegister("Frd_GLOBAL"));

         if( Zend_Registry::isRegistered("Frd_GLOBAL") == false)
         {
            self::initGlobal();
         }

         $global=Zend_Registry::get("Frd_GLOBAL");
         if($key != false)
            return $global->$key;
         else
            return $global;

      }

      public static function setGlobal($key,$value)
      {
         $global=self::getGlobal();

         $global->register($key,$value);
      }

      //add include path
      public static function addIncludePath($path)
      {
         //$realpath=$this->getRootPath().'/'.$path;
         $realpath=realpath($path);

         if($realpath == false)
         {
            throw new Exception("[init error] include path invalid: $path");

         }

         set_include_path($realpath. PATH_SEPARATOR.get_include_path());
      }

      public static function setTimezone($timezone)
      {
         date_default_timezone_set($timezone);
      }

      public static function enableAutoLoad()
      {
         //var_dump(get_include_path());exit();
         require_once "Zend/Loader/Autoloader.php";
         require_once dirname(__FILE__)."/Loader.php";

         $autoloader = Zend_Loader_Autoloader::getInstance();
         $autoloader->setFallbackAutoloader(true);
         $autoloader->pushAutoloader(array('Frd_Loader', 'autoload'), 'Frd');
      }

      public static function setRootPath($path)
      {
         self::setGlobal('root_path',realpath($path));
      }

      public static function getRootPath()
      {
         return self::getGlobal('root_path');
      }

      public static function handleException()
      {
         $exception=new Frd_Exception();

         if( Zend_Registry::isRegistered("Frd_Exception") == false)
         {
            Zend_Registry::set("Frd_Exception",$exception);

            $exception->setExceptionHandler();
         }
      }

      public static function getException()
      {
         if( Zend_Registry::isRegistered("Frd_Exception") == false)
         {
            Zend_Registry::get("Frd_Exception");
         }

         return Zend_Registry::get("Frd_Exception");
      }

      /*
      public static function printExceptions()
      {
         echo 'aaa';
         $exception=self::getException();

         $exception->outputExceptions();
      }
      */
      /** only for base framework setting **/
      public static function setSetting($key,$value)
      {
         Frd_Setting::$$key=$value;
      }

      public static function getSetting($key)
      {
         return Frd_Setting::$$key;
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
      /*
      public static function getSetting($key)
      {
         return Frd_Setting::$$key;
      //setVersion : auto load use different version lib
      }

      */

      /**
      * example:
      *   getModule('test','Test','param1','param2','param3'....)
      */
      public static function getModule($folder,$class_name=false)
      {
         if( self::$modules == false )
         {
            self::$modules=new Frd_Object();
         }

         if( !isset(self::$modules->$folder) )
         {
            Frd_Setting::check("module_path");

            $file_path=rtrim(Frd_Setting::$module_path,"/")."/".$folder."/main.php";

            require_once($file_path);

            //get module parameters
            $args=func_get_args();
            $params=array_slice($args,2);


            if($class_name == false)
            {
               $class_name=self::getClassNameFromModule($file_path);
            }

            $module=self::_getClass($class_name,$params);

            $module->init($folder);

            self::$modules->$folder=$module;
         }
         else
         {
            $module=self::$modules->$folder;
         }

         return $module;
      }

      /**
      * search class name from module's main file
      */
      public static function getClassNameFromModule($file_path)
      {
         $string=file_get_contents($file_path);

         $pattern='/class (\S*) *extends *Frd_Module/';

         $ret=Frd_Regexp::search($string,$pattern);
         if(count($ret) == 2)
         {
            $class_name=$ret[1];
         }
         else
         {
            $class_name=false;
         }

         if($class_name == false)
         {
            throw new Exception('can not get module class name from file: '.$file_path );
         }

         return $class_name;
      }

      public static function addDb($config,$name="default")
      {
         //init
         if(self::$dbs == false)
         {
            self::$dbs=new Frd_Object();
         }

         //can not overwrite
         if(self::$dbs->$name  !== null)
         {
            throw new Exception("db already exists: $name");
         }

         $adapter=$config['adapter'];
         unset($config['adapter']);

         $db=new Frd_Db();

         if( count(self::$dbs->getData()) == 0)
         {
            $db->init($adapter,$config,true);
         }
         else
         {
            $db->init($adapter,$config,false);
         }

         self::$dbs->$name=$db;

         return $db;
      }

      /**
      * get db 
      */
      public static function getDb($name="default")
      {
         if( self::$dbs->$name === null)
         {
            throw new Exception("db not exist: $name");
         }

        return self::$dbs->$name ;
      }

      //config
      public static function initConfig()
      {
         if( Zend_Registry::isRegistered("Frd_Config") == false)
         {
            $config=new Frd_Object();

            Zend_Registry::set("Frd_Config",$config);
         }
      }

      /**
      * get $global
      *
      * @param  string $key  $global's key
      * @return Object  if $key not false, return $global->$key,else return $global
      */
      public static function getConfig($key=null)
      {
         if( Zend_Registry::isRegistered("Frd_Config") == false)
         {
            self::initConfig();
         }

         $config=Zend_Registry::get("Frd_Config");
         if($key != false)
            return $config->$key;
         else
            return $config;
      }

      public static function setConfig($key,$value=false)
      {
         $config=self::getConfig();

         $config->$key=$value;
      }

      public static function getConfigs()
      {
         $config=self::getConfig();

         return (object) $config->getData();
      }


      public static function getApp()
      {
         return self::$app;
      }

   }
