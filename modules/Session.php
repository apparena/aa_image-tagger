<?php
   //app's session
   //now only for develop
   class Session
   {
      function __construct($key="develop")
      {
         $session=new Zend_Session_Namespace($key);
         $session->messages=array();

         if(!isset($session->login))
         $session->login=false;

         $this->session=$session;
      }
      function isLogin()
      {
         if($this->session->login==true)
         {
            return true;
         }
         else
         {
            return false;
         }
      }

      function login($password)
      {
         if(md5($password) == "1a9e35b226e614b260452c8b2b377ce6")
         {
            $this->session->login=true; 
            return true;
         }
         else
         {
            return false; 
         }
      }

      function logout()
      {
         $this->session->login=false; 
      }
   }
