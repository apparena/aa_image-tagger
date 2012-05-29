<?php

   /**
   * this should be an interface ! 
   * but now just make it work
   */
   class Fluttery
   {
      protected  $app_secret='';
      protected  $app_id='';
      protected  $instid='';
      protected  $fb_page_id='';

      protected $client=null;

      function __construct()
      {
      }

      function init($params)
      {
         /*
         $this->app_secret=$params['app_secret'];
         $this->app_id=$params['app_id'];
         $this->instid=$params['instid'];
         $this->fb_page_id=$params['fb_page_id'];
         */

         $this->client=new AA_AppManager($params);
      }

      function getData()
      {
         $instance=$this->client->getInstance();


         $config=$this->client->getConfig();

      
         //to object
         $config=new Fluttery_Config($config);
         $instance=new Fluttery_Instance($instance);

         return array('config'=>$config,'instance'=>$instance);
      }
   }

   class Fluttery2
   {
      protected $_server_url='http://www.app-arena.com/manager/server/soap.php';
      //protected $_server_url='http://fbapps.frd.info/app_arena/server/soap.php';
      //protected $_server_url='http://dev.app-arena.com/manager/server/soap.php';
      protected $app_secret;

      protected $_app_id=null;
      protected $_instance_id=null;
      protected $_page_id=null;

      protected $session=null; // instance of Zend_Session_Namespace

      protected $allow_cache=false;

      //soap client
      protected $_client=null;

      /*
      * init class , need app_secret to auth
      */
      function __construct($app_secret)
      {
         if($app_secret == false)
         throw new Exception('invalid app secret');

         $this->app_secret=$app_secret;

         //init soap client
         $options = array(
            'location' => $this->_server_url,
            'uri'      => $this->_server_url,
         );

         $this->_client = new Zend_Soap_Client(null, $options);  

      }

      function setUri($uri)
      {
         if($uri != false)
         {
            $this->_server_url=$uri;

            //init soap client
            $options = array(
               'location' => $this->_server_url,
               'uri'      => $this->_server_url,
            );

            $this->_client = new Zend_Soap_Client(null, $options);  
         }
      }

      /**
      * set session store
      */
      function setSession($session,$allow_cache=true)
      {
         if($this->_instance_id == false)
         throw new Exception("you have not set instance, please use the method setInstace(APP_ID,INSTANCE_ID,PAGE_ID) set it first ");

         $this->session=$session;
         if(!isset($this->session->instances[$this->_instance_id]))
         $this->session->instances[$this->_instance_id]=new ArrayObject();;

         $this->allow_cache=$allow_cache;
      }


      function setInstanceId($app_id,$instance_id)
      {
         $this->_app_id=$app_id;
         $this->_instance_id=$instance_id;
      }
      /*
      * set instance , each app have an app instance  in fluttery,
      * and use instance_id to identify it
      * get instance id have two ways:
      *   1, use ic_app_id and instance_id (ic_app_id is for identify the instance, that make sure you  are not  give the error instance id by mistake
      *   2, use  ic_app_id and page_id
      *
      * @param interger  app_id  app model's id ,it's instance is app instance
      * @param integer   instance_id app instance's primary key
      * @param integer   facebook's page id   like  in fanpage, it will have page id, but canvas page  do not
      *
      */
      function setInstanceIdByPageId($app_id,$page_id)
      {
         $this->_app_id=$app_id;
         $this->_page_id=$page_id;

         try
         {
            $instance_id = $this->_client->getInstanceId($app_id,$page_id,'');

            if($instance_id != false)
            {
               $this->_instance_id=$instance_id;
            }
            else
            {
               throw new Exception("sorry ,can not get instance id: [ic_app_id:$ic_app_id],[page_id:$page_id]");
            }
         }
         catch(Exception $e)
         {
            echo $e->getMessage();  
         }


         return $this->_instance_id;
      }

      /*
      * get all data from fluttery
      * 
      * @return array(
         *            'config'=>array(...),
         *            'content'=>array(...),
         *            'instance'=>array(...))
         *
         *          if do not have data , will return 
         *         array(
            *            'config'=>array(),
            *            'content'=>array(),
            *            'instance'=>array())
            *
            *          of false, will return false,and throw Exception,that mean you code may not right, like  give a wrong instance id ,and so on.
            */
            function getData()
            {
               if($this->_instance_id == false)
               throw new Exception("you have not set instance, please use the method setInstace(APP_ID,INSTANCE_ID,PAGE_ID) set it first ");

               if($this->allow_cache== true && isset($this->session->instances[$this->_instance_id]->data))
               {
                  return $this->session->instances[$this->_instance_id]->data;
               }


               //try
               {
                  $result = $this->_client->getData($this->app_secret,$this->_app_id,$this->_instance_id);

                  if(is_object($result) && $result->error !=0)
                  {
                     throw new Exception($result->error_msg); 
                  }

                  if($result != false)
                  {
                     $content=$result['content'];

                     foreach($content as $k=>$v)
                     {
                        if(is_https() == true)
                        {
                           $content[$k]=str_replace("http://","https://",$v); 
                        }

                        $content[$k]=str_replace("{instid}",$this->_instance_id,$v); 
                     }

                     $result['content']=$content;

                     if($this->allow_cache== true )
                     $this->session->instances[$this->_instance_id]->data=$result;
                     return $result;
                  }
               }
               /*
               catch(Exception $e)
               {
                  echo $e->getMessage();  
               }
               */
            }

            /* get insatnceid, 
            * if you do not known this, 
            * should use app_id and page_id get it first ,
            * like:
            *   $client->setInstanceId();
            *   $instance_id=$client->getInsatnceId();
            */
            function getInstanceId()
            {
               if($this->_instance_id == false)
               throw new Exception("you have not set instance, please use the method setInstace(APP_ID,INSTANCE_ID,PAGE_ID) set it first ");

               return $this->_instance_id;
            }
         }
