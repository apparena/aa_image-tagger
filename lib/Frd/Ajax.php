<?php

/**
* for accept ajax request and call correct function to response
*/
class Frd_Ajax
{
   protected $_action_name="action"; //default param for action
   protected $_required_params=array(); //required params 
   protected $_folder=false; //callback files folder
   protected $_error_msg='';

   function __construct()
   {
   
   }

   function setFolder($folder)
   {
      $this->_folder=rtrim($folder,'/');
   }

   /*
   function setRequiredParams($params)
   {
   
   }
   */

   function addRequiredParam($name,$type=false)
   {
      $this->_required_params[$name]=$name;
   }

   function setActionParam($param)
   {
      $this->_action_name=$param;
   }

   function run()
   {
      $data=array_merge($_GET,$_POST);
      foreach($data as $k=>$v)
      {
         $data[$k]=trim($v);
      }

      if( $this->checkRequiredParams($data) == false)
      {
         echo errorMsg($this->getErrorMsg());
         exit();
      }

      if( $this->checkActionParams($data) == false)
      {
         echo errorMsg($this->getErrorMsg());
         exit();
      }

   
      $action=$data[$this->_action_name];
      //execute
      $callback_file=$this->_folder.'/'.$action.'.php';

      if(file_exists($callback_file) == false)
      {
         echo errorMsg("callback file not exists $callback_file");
         exit();
      }

      require_once $callback_file;

      if(function_exists($action) == false)
      {
         echo errorMsg("action not exists [$action]");
         exit();
      }

       $ret=$action($data);

       if(is_string($ret))
       {
          echo $ret;
       }
       else if(is_array($ret))
       {
          echo json_encode($ret);
       }
   }

   //run method
   function setErrorMsg($msg)
   {
      $this->_error_msg=$msg;
   }
   function getErrorMsg()
   {
      return $this->_error_msg;
   }



   function checkRequiredParams($data)
   {
      foreach($this->_required_params as $k)
      {
         if(!isset($data[$k]))
         {
            $this->setErrorMsg("missing parameter ".$k);
            return false;
         }

         if($data[$k] == false)
         {
            $this->setErrorMsg("invalid parameter ".$k);
            return false;
         }
      }

      return true;
   }

   function checkActionParams($data)
   {
      if(!isset($data[$this->_action_name]))
      {
         $this->setErrorMsg("missing parameter ".$this->_action_name);
         return false;
      }

      return true;
   }
}

/*
$ajax=new Frd_Ajax();
$ajax->setFolder(dirname(__FILE__));
$ajax->addRequiredParam('instid');
$ajax->setActionParam('action');

$ajax->run();
*/
