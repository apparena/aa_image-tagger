<?php
   /**
   * @category   AA
   * @version    2012-02-09-version 1
   * 
   * current version is for soap4.php, this version support locale
   */
   /**
   * example:
   $params=array(
      'aa_app_id'=>{APP_ID}
      'aa_app_secret'=>{APP_SECRET},
      'aa_inst_id'=>{INSTID},
      //'fb_page_id'=>{FB_PAGE_ID},  
      'locale'=>false,
   );

   $manager=AA_AppManger($params);

   $manager->getData();
   $manager->getInstanceId();
   $manager->getConfig();
   $manager->getConfigByType('html');
   $manager->getConfigById('fb_color');
   */

   //define("ROOT_PATH",realpath(dirname(__FILE__)));
   //set_include_path(ROOT_PATH.'/../lib/' . PATH_SEPARATOR );
   //require(dirname(__FILE__).'/Zend/Soap/Client.php');
   require('Zend/Soap/Client.php');

   class AA_AppManager 
   {
      //soap client
      protected $client=null;

      //soap server url
      //default: http://www.app-arena.com/manager/server/soap.php
      //protected $server_url=false;
      protected $server_url='http://www.app-arena.com/manager/server/soap3.php';

      //this params will transport each call 
      protected $soap_params=array(
         'aa_app_id'=>false,
         'aa_app_secret'=>false,
         'aa_inst_id'=>false,
         'fb_page_id'=>false,
         'locale'=>false,
      );

      //last error message
      //if call server faild will set this message
      protected $error_msg=''; 

      /**
      * construct
      * 
      * @param array  $params should set  necessary for client
      *
      */
      function __construct($params) 
      {
         //init all necessary params
         $keys=array(
            'aa_app_id','aa_app_secret','aa_inst_id','fb_page_id'
         );

         foreach($keys as $key)
         {
            if( isset($params[$key]) )
            {
               $this->soap_params[$key]=trim($params[$key]);
            }
         }

         //check params 

         if( $this->soap_params['aa_app_id'] == false)
         {
            throw new Exception("missing parameter  aa_app_id");
         }

         if( $this->soap_params['aa_app_secret'] == false)
         {
            throw new Exception("missing parameter  aa_app_secret");
         }


         if( $this->soap_params['aa_inst_id'] == false && $this->soap_params['fb_page_id'] == false)
         {
            //try get  fb page_id automaticly
            $this->soap_params['fb_page_id'] = $this->getFbPageId();

            //check again
            /*
            if( $this->soap_params['aa_inst_id'] == false && $this->soap_params['fb_page_id'] == false)
            {
               throw new Exception("missing parameter aa_inst_id  or  fb_page_id ");
            }
            */
         }

         if(isset($params['server_url']) && $params['server_url'] != false)
         {
            $this->setServerUrl($params['server_url']);
         }

         //set locale
         if(isset($params['locale']))
         {
            $this->setLocale($params['locale']);
         }

         //now init 
         $this->init();
      }

      /**
      * set current locale
      */
      function setLocale($locale)
      {
         $this->soap_params['locale']=$locale;
      }

      /**
      * try get fb page id from $_REQUEST['signed_request']
      * this will only work in fan page
      *
      * @return string|boolean   fb_page_id for success and false for failed
      */
      private function getFbPageId()
      {
         if(isset($_GET['page_id']))
         {
            $fb_page_id=intval($_GET['page_id']);
         }
         else if(isset($_POST['fb_sig_page_id']))
         {
            $fb_page_id=$_POST['fb_sig_page_id'];
         }
         else if(isset($_REQUEST['signed_request']))
         {
            $signed_request = $_REQUEST["signed_request"];
            list($encoded_sig, $payload) = explode('.', $signed_request, 2); 
            $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);


            if(isset($data['page']))
            {
               $fb_page_id=$data['page']['id'];
            }
            else
            {
               $fb_page_id=false;
            }
         }
         else
         {
            $fb_page_id=false;
         }

         return $fb_page_id;
      }


      /**
      * this depend developer how to save aa_inst_id 
      *
      protected function getInstid()
      {
         ;
      }
      */

      /**
      * init 
      * you can create subclass of this class, and  rewrite the init method
      * then you can use different soap server url
      */
      private function init()
      {
         /*
         if($this->getServerUrl() == false)
         {
            $server_url='http://www.app-arena.com/manager/server/soap3.php';
            $this->setServerUrl($server_url);
         }
         */
         $this->initClient();
      }

      /**
      * change server url
      */
      public function setServerUrl($url)
      {
         $this->server_url=$url;
         $this->initClient();
      }

      /**
      * change server url
      */
      function getServerUrl()
      {
         return $this->server_url;
      }

      /**
      * init client 
      */
      private function initCLient()
      {
         //init soap client
         $options = array(
            'location' => $this->server_url,
            'uri'      => $this->server_url,
         );

         $this->client = new Zend_Soap_Client(null, $options);  
      }

      /**
      * call a method of server
      * if failed, return false, and set error_msg
      * 
      * @param  string $method 
      * @param  array|boolean  $params  which for the $method
      * 
      * @return boolean  true or false, when false,you can call  getErrorMsg
      */
      private  function call($method,$params=array())
      {
         try
         {
            $result=$this->client->call($method,$this->soap_params,$params);

            if($result !== false)
            {
               return $result;
            }
            else
            {
               $this->error_msg="call method $method return false";
               return false;
            }
         }
         catch(Exception $e)
         {
            $this->error_msg=$e->getMessage();  
            return false;
         }
      }

      /**
      * get error msg, but should only call it after call method from server faild
      *
      * @return string  
      */
      function getErrorMsg()
      {
         return $this->error_msg;
      }


      //====================== server's methods ==============

      /**
      * get app's current aa_inst_id, if you only known the fb_page_id
      *
      * @return int
      */
      function getInstanceId()
      {
         $aa_inst_id = $this->call('getInstanceId');
         return $aa_inst_id;
      }

      /**
      * get all data  ,(instance and config)
      *
      * @return array
      */
      /*
      function getData()
      {
         $result = $this->call('getData');
         return $result;
      }
      */

      /**
      * only get instance data
      *
      * @return array
      */
      function getInstance()
      {
         $result = $this->call('getInstance');
         return $result;
      }

      /**
      * only get config data
      *
      * @params Mix identifiers , if false , mean get all config data, if is config identifiers array, only get the value of these identifiers
      *
      * @return array
      */
      function getConfig($identifiers=false,$locale=false)
      {
         $result = $this->call('getConfig',array('identifiers'=>$identifiers,'locale'=>$locale));
         return $result;
      }


      /**
      * get config by type
      *
      * @return array
      */
      function getConfigByType($type)
      {
         $result = $this->call('getConfigByType',$type);
         return $result;
      }

      /**
      * get config by config identifier 
      *
      * @return array
      */
      function getConfigById($identifier)
      {
         $result = $this->call('getConfigById',$identifier);
         return $result;
      }

      /**
      * active app instance , will deactive another actived app if exists
      * 
      */
      /*
      function activeAppInstance()
      {
         $result = $this->call('activeAppInstance',$identifier);
         return $result;
      }
      */

      /**
      * get fb app's info
      *
      * @return int
      */
      function getFbApp($fb_page_url)
      {
         $result=$this->client->getFbApp($fb_page_url);
         return $result;
      }

      /**
      * get Translate
      * 
      * @param string  $locale false for app model's default locale
      */
      function getTranslation($locale=false)
      {
         $result = $this->call('getTranslation',$locale);
         return $result;
      }

   }
