<?php
   class AppLog_Table_User extends Frd_Db_Table
   {
      function __construct()
      {
         $table="app_log_user";
         $primary="id";

         parent::__construct($table,$primary);
      }
   }
