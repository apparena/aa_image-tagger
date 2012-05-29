<?php
   /**
   * save system log,exception and so on
   */
   class Logger
   {
      public  static function getLogPath()
      {
         $log_path=dirname(__FILE__).'/../var/log/system.log';
         return $log_path;
      }

      public  static function  addLog($msg)
      {
         $path=self::getLogPath();

         $log=new Frd_Log($path);
         $log->log($msg);
      }

      public  static function addException($msg)
      {
         $path=self::getLogPath();
         $log=new Frd_Log($path);
         $log->err($msg);

      }
   }

