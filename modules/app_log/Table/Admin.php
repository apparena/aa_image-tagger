<?php
   class AppLog_Table_Admin extends Frd_Db_Table
   {
      function __construct()
      {
         $table="app_log_admin";
         $primary="id";

         parent::__construct($table,$primary);
      }
   }
