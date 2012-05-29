<?php
/**
 * initialize  database for Fb
 * Usage: 
 *  $install=new Install();
 *  $install->inistall();
 */
class Install{
  protected $db=null;

  /**
   * initialize db
   */
  function __construct($db=null)
  {
    if($db == null)
    {
      global $global;
      if (isset($global->db))
        $this->db = $global->db;
    }
    else
    {
      $this->db=$db; 
    }
  }

  /**
   * check the database if has installed
   */
  function isInstalled()
  {
    $config=new Frd_Table_Config($this->db,"app_config","config_key","config_value"); 

    $config->set("installed","no",array("instid"=>118));
    return $config->getAll();
  }

  /**
   * create fb user tables
   */
  function installFb()
  {
    $sql="CREATE TABLE IF NOT EXISTS `user_data` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) NOT NULL,
      `first_name` varchar(255) CHARACTER SET latin1 NOT NULL,
      `middle_name` varchar(255) CHARACTER SET latin1 NOT NULL,
      `last_name` varchar(255) CHARACTER SET latin1 NOT NULL,
      `email` varchar(255) CHARACTER SET latin1 NOT NULL,
      `gender` varchar(6) CHARACTER SET latin1 NOT NULL,
      `about` text CHARACTER SET latin1 NOT NULL,
      `hometown` varchar(255) CHARACTER SET latin1 NOT NULL,
      `location` varchar(255) CHARACTER SET latin1 NOT NULL,
      `link` varchar(255) CHARACTER SET latin1 NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin  ;";

    $this->db->query($sql);


    $sql=" CREATE TABLE IF NOT EXISTS `user_data_education` (
      `eduId` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) NOT NULL,
      `name` varchar(255) CHARACTER SET latin1 NOT NULL,
      `type` varchar(50) CHARACTER SET latin1 NOT NULL,
      `year` int(4) NOT NULL,
      `concentration` varchar(255) CHARACTER SET latin1 NOT NULL,
      PRIMARY KEY (`eduId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin; ";

    $this->db->query($sql);


    $sql=" CREATE TABLE IF NOT EXISTS `user_data_work` (
      `workId` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) NOT NULL,
      `employer` varchar(255) CHARACTER SET latin1 NOT NULL,
      `location` varchar(255) CHARACTER SET latin1 NOT NULL,
      `position` varchar(255) CHARACTER SET latin1 NOT NULL,
      `description` text CHARACTER SET latin1 NOT NULL,
      `start_date` date NOT NULL,
      `end_date` date NOT NULL,
      PRIMARY KEY (`workId`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;"; 
    $this->db->query($sql);

    $sql=" CREATE TABLE IF NOT EXISTS `user_friends` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `friend_name` varchar(30) CHARACTER SET utf8 NOT NULL,
      `user_id` bigint(20) NOT NULL,
      `friend_user_id` bigint(20) NOT NULL COMMENT 'Friends user id',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;";
    $this->db->query($sql);

  }

  /**
   * install config table
   */
  function installConfigTable()
  {
    //install app config 
    $sql="CREATE TABLE IF NOT EXISTS `app_config` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `instid` int(10) DEFAULT NULL,
      `config_key` varchar(50) DEFAULT NULL,
      `config_value` varchar(100) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ";

    $this->db->query($sql);
  }
  /**
   * install app tables
   */
  function installAppTable()
  {
    $sql="CREATE TABLE `competition_join` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `instid` int(10) DEFAULT NULL,
      `fb_userid` varchar(20) DEFAULT NULL,
      `description` text NOT NULL,
      `title` text NOT NULL,
      `content` text,
      `original_url` text,
      `vote_tickets` int(10) DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
      `activated` tinyint(1) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=utf8";


    $this->db->query($sql);
    $sql="CREATE TABLE `competition_vote` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `instid` int(10) DEFAULT NULL,
      `join_id` varchar(20) DEFAULT NULL,
      `vote_userid` varchar(20) DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1104 DEFAULT CHARSET=utf8";
    $this->db->query($sql);
  }
}
