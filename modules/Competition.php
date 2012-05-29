<?php
/**
 *
 */
class Competition
{
	
  //get competition by appid
  function getCompetition()
  {
  	global $global,$appID;
  	
  	if($appID == false)
  	{
  		return false;	
  	}
  	
  	$select=$global->db->select();
  	$select->from('competition','id');
  	$select->where('app_id=?',$appID);
  	
  	$id=$global->db->fetchOne($select);
  	
  	if($id == false)
  	{
  		return false;	
  	}
  	
  	$competition=new Frd_Table_Common('competition','id');
  	$competition->load($id);
  	
  	return $competition;
  }
	/* return array or array() 
	 * 
	 * 
	 */
  function getJoin($instid,$params)
  {
  	global $global;
  	$db=$global->db;
  	$select=$db->select();
  	$select->from('competition_join','*');
  	$select->where('instid=?',$instid);
    $select->where('created_at >?',$this->getStartDatetime($instid));

  	
  	//set sort
  	if(isset($params['sort']))
  	{
  		$sort=$params['sort'];
  		if($sort == "votes")
  		{
  			$select->order('vote_tickets desc');
  		}
  		else if($sort == "date")
  		{
  			$select->order('created_at desc');
  		}
  	}
  	//echo $select;
  	$rows=$db->fetchAll($select);
  	
  	return $rows;
  }
  
  /* check fb user has joined this competition
   * return true or false
   * 
   * 
   */
  function hasJoin($instid,$user_id)
  {
  	global $global;
  	$db=$global->db;
  	$select=$db->select();
  	$select->from('competition_join','id');
  	$select->where('instid=?',$instid);
  	$select->where('fb_userid=?',$user_id);
    $select->where('created_at >?',$this->getStartDatetime($instid));
  	
  	
  	//echo $select;
 	$result=$db->fetchOne($select) ;
 	if($result > 0)
	 	return true;
 	else 
	 	return false;
  }

  //get competition join by useid
  function getJoinByUserid($instid,$user_id)
  {
    global $global;
    $db=$global->db;
    $select=$db->select();
    $select->from('competition_join','*');
    $select->where('instid=?',$instid);
    $select->where('fb_userid=?',$user_id);
    $select->where('created_at >?',$this->getStartDatetime($instid));

    //echo $select;
    $result=$db->fetchRow($select) ;
    if($result  == false)
      return false;
    else 
      return $result;
  }
  
  /*
   * 
   * get a uploader's been vote times (how many times a photo  ,the user voted )
   */
   function getPhotoVotetime($instid,$vote_user_id,$join_id)
   {
      global $global;
      $db=$global->db;
      $select=$db->select();
      $select->from('competition_vote','count(id)');
      $select->where('instid=?',$instid);
      $select->where('join_id=?',$join_id);
      $select->where('vote_userid=?',$vote_user_id);
      $select->where('created_at >?',$this->getStartDatetime($instid));

      //echo $select;
      $result=$db->fetchOne($select) ;
      if($result > 0 )
         return $result;
      else 
         return 0;
   }

  /**
   * how many votes the user voted
   */
  function getUserVotetime($instid,$vote_userid)
  {
    global $global;
    $db=$global->db;
    $select=$db->select();
    $select->from('competition_vote','count(id)');
    $select->where('instid=?',$instid);
    $select->where('vote_userid=?',$vote_userid);
    $select->where('created_at >?',$this->getStartDatetime($instid));

    //echo $select;
    $result=$db->fetchOne($select) ;
    if($result > 0 )
      return $result;
    else 
      return 0;
  }
  
  /**
   * get a user's uploaded time (how many photoes he uploaded)
   */
  function getUploadedTime($instid,$fb_userid)
  {
    global $global;
    $db=$global->db; 

    $select=$db->select();
    $select->from('competition_join','count(*)');
    $select->where('instid=?',$instid);
    $select->where('fb_userid=?',$fb_userid);
    $select->where('created_at >?',$this->getStartDatetime($instid));
    //echo $select; 
    $ret=$db->fetchOne($select);

    return intval($ret);
  }

  //get if the same appid  competition has exists
  function isAppExists($app_id,$id)
  {
  	global $global;
  	$db=$global->db;

  	$select=$db->select();
  	$select->from('competition','id');
  	$select->where('app_id=?',$app_id);

    if($id >0 )
      $select->where('id !=?',$id);
  	
  	//echo $select;
    $result=$db->fetchOne($select) ;
    if($result > 0 )
      return true;
    else 
      return false;
  }

  /**
  * get the competition start time
  */
  function getStartDatetime($instid)
  {
     $db=Frd::getDb();
     $app_config=new App_Config($db,$instid);

     $start_time=$app_config->round_reset_timestamp;

     if($start_time == false)
     {
        $start_time='1970-01-01';
     }

     return $start_time;
  }

  /**
  * reset competition, start with new competition
  */
  function reset($instid)
  {
     $db=Frd::getDb();

     $datetime=date("Y-m-d H:i:s");

     $app_config=new App_Config($db,$instid);
     $app_config->round_reset_timestamp=$datetime;

     return $datetime;
  }
}

