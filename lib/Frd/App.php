<?php
   /** 
   * @status try
   * each app should have a main object (App)
   * which can do everything of the app
   * for this lib, you can use it , or do not use
   * but for a real app, it is should have an object 
   * this is good for manage and test !
   * it should enable add module dynamicly
   *  
   *  app and page relationship
   * 
   */

   class Frd_App
   {
      //this is depend root path
      /*
      function loadFile($file)
      {
         ;
      }
      */

      /**
      * for all request , GET, POST,COOKIE , handle it and return
      */
      function handleRequestValue($value)
      {
         if(is_string($value))
         {
            return trim($value);
         }
         else
         {
            return $value;
         }
      }

      function getGet($key,$default=false)
      {
         if(isset($_GET[$key]))
         {
            return  $this->handleRequestValue($_GET[$key]);
         }
         else
         {
            return $default; 
         }
      }

      function setGet($key,$value)
      {
         $_GET[$key]=$value;
      }

      function getPost($key,$default=false)
      {
         if(isset($_POST[$key]))
         {
            return  $this->handleRequestValue($_POST[$key]);
         }
         else
         {
            return $default; 
         }
      }

      function setPost($key,$value)
      {
         $_POST[$key]=$value;
      }

      function getCookie($key,$default=false)
      {
         if(isset($_COOKIE[$key]))
         {
            return  $this->handleRequestValue($_COOKIE[$key]);
         }
         else
         {
            return $default; 
         }
      }

      /**
      *
      * ONLY FOR TEST
      * NOT READY FOR USE
      */
      function setCookie($key,$value)
      {
         setcookie($key,$value,time()+30,'/');
      }

      function getRequest($key,$default=false)
      {
         $value=$this->getGet($key);
         if($value == false)
         {
            $value=$this->getPost($key);
            if($value == false)
            {
               $value=$this->getCookie($key);

               if($value == false)
               {
                  return $default;
               }
            }
         }

         return $value;
      }

      function getServer($key,$default=false)
      {
         if(isset($_SERVER[$key]))
         {
            return  $this->handleRequestValue($_SERVER[$key]);
         }
         else
         {
            return $default; 
         }
      }

      function setServer($key,$value)
      {
         $_Server[$key]=$value;
      }

   }
