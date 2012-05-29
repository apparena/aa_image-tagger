<?php
   class AppLog_Table_Fb extends Frd_Db_Table
   {
      function __construct()
      {
         $table="app_log_fb";
         $primary="id";

         parent::__construct($table,$primary);
      }
   }
