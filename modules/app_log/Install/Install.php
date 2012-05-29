<?php
   class AppLog_Install
   {
      function install()
      {
         if($this->isInstalled() == false)
         {
            Frd::getModule("app_log")->install();
            $this->doInstall();
         }
      }

      function isInstalled()
      {
         $db=Frd::getDb();

         $creater=new Frd_Db_Creater($db);
         $ret=$creater->tableExists("app_log_user");

         return $ret;
      }

      function uninstall()
      {
         $db=Frd::getDb();

         $creater=new Frd_Db_Creater($db);
         $ret=$creater->deletetable("app_log");

         Frd::getModule("app_log")->uninstall();
      }

      function doInstall()
      {
         $db=Frd::getDb();

         //app log user
         $sql="create table app_log_user(
            id int(10) auto_increment primary key,
            aa_inst_id int(10) not null ,
            fb_user_id char(30) not null,
            action  char(100) not null,
            ip  char(50) not null,
            timestamp  datetime 
         )";
         $db->query($sql);

         //app log fb
         $sql="create table app_log_fb(
            id int(10) auto_increment primary key,
            data text,
            timestamp  datetime 
         )";
         $db->query($sql);

         //app log admin
         $sql="create table app_log_admin(
            id int(10) auto_increment primary key,
            fb_user_id int(10) not null ,
            aa_inst_id int(10) not null,
            action  char(100) not null,
            ip  char(50) not null,
            timestamp  datetime 
         )";
         $db->query($sql);

      }
   }
