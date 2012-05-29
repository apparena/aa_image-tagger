<?php
   class Applog_User
   {
      /**
      * other object ask me to do something
      */
      function ask($source,$action,$params)
      {
         if($source == 'app_log')
         {
            return $this->$action($params);
         }

      }

      /**
      * get instance id from app log user
      * this check the most used instance of the fb user
      * then return the instance id
      */
      function getInstanceIdFromLog($params)
      {
         $db=getDb();

         $fb_user_id=$params['fb_user_id'];

         if($fb_user_id == false)
         {
            return false;
         }

         $sql="select aa_inst_id from app_log_user where fb_user_id='$fb_user_id' group by aa_inst_id order by aa_inst_id desc limit 1";
         //echo $sql;

         $aa_inst_id=$db->fetchOne($sql);

         return $aa_inst_id;
      }

   }
