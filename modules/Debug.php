<?php
   /**
   * debug helper
   * use session  frd_debug
   */
   class Debug
   {
      protected $session=null;

      function __construct($key="debug")
      {
         $session=new Zend_Session_Namespace($key);
         $session->messages=array();

         if(!isset($session->logs))
         $session->logs=array();

         $this->session=$session;
         if(!isset($this->session->enable))
         {
            $this->session->enable=false;
         }
      }

      public function  isEnable()
      {
         if( $this->session->enable==false )
         {
            return false;
         }
         else
         {
            return true;
         }
      }

      public function enable()
      {
         $this->session->enable=true;

      }
      public function disable()
      {

         $this->session->enable=false;
      }

      /**
      * save debug message
      */
      function addMessage($message)
      {
         $this->session->messages[]=$message;
      }

      /**
      * return debug messages
      */
      function displayMessages()
      {
         $html='<ul class="debug_message">';
            foreach($this->session->messages as $message)
            {
               $html.='<li>'.$message.'</li>';; 
            }

            $html.='</ul>';
         return $html;
      }

      /**
      * save debug log
      */
      function addLog($message)
      {
         $this->session->logs[]=$message;
      }

      /**
      * return debug logs
      */
      function displayLogs()
      {
         $html='<ul class="debug_message">';
            foreach($this->session->logs as $log)
            {
               $html.='<li>'.$log.'</li>';; 
            }

            $html.='</ul>';
         return $html;
      }
   }
?>
