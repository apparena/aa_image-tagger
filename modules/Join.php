<?php
   /**
   * class for competiton join
   */

   class Join
   {
      protected $tablename="competition_join";
      protected $primary="id";

      /**
      *
      */
      function getPhotoTitles($instid)
      {
         $db=Frd::getDb();

         $select=$db->select();
         $select->from($this->tablename." as a",
         array('a.id as join_id','a.*','concat(b.first_name," ",b.middle_name," ",b.last_name) as username')
         );

         $select->where('a.instid=?',$instid);
         $select->where('a.is_available=?',1);
         $select->where('a.activated=?',1);

         $select->joinLeft("user_data as b","a.fb_userid=b.user_id");


         $rows=$db->fetchAll($select);



         return $rows;
      }

      /**
      * get photo titles for js , as auto complete's paramater
      */
      function getPhotoTitlesForJs($instid,$rows=false)
      {
         $data=array();

         if($rows == false)
         {
            $rows=$this->getPhotoTitles($instid);
         }

         foreach($rows as $row)
         {
            $data[]=array(
               'label'=>$row['title'],
               'title'=>$row['title'],
               'username'=>$row['username'],
               'id'=>$row['join_id'],
            );
         }

         if(count($data) <= 0)
         {
            return '[]'; 
         }
         else
         {

            return json_encode($data);
         }
      }

   }
