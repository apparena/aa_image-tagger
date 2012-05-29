<?php
   /**
   * status:  DEVELOPPING
   * use facebook's php sdk 
   * for easy use facebook to loign/do api call/fql and other
   */
   class Frd_Facebook
   {
      protected $fb_class="Facebook"; 

      protected $facebook=null; //facebook object

      protected $app_id='';
      protected $app_secret='';

      //error handle
      protected $error=array();

      function __construct($config)
      {
         $this->app_id=$config['appId'];
         $this->app_secret=$config['secret'];

         $this->isFacebookLibLoaded();

         $this->facebook=new $this->fb_class($config);
      }

      /**
      * this is for check if facebook lib is loaded, 
      * it only check if  class "Facebook" exists, 
      */
      function isFacebookLibLoaded()
      {
         if(class_exists($this->fb_class) == false )
         {
            throw new Exception("class ".$this->fb_class." not exists, please check if facebook lib loaded.");
         }

         if( class_exists("FacebookApiException")== false )
         {
            throw new Exception("class FacebookApiException not exists, please check if facebook lib loaded.");
         }
      }

      /**
      * check if user login 
      */
      function isLogin()
      {
         if( $this->facebook->getUser() )
         {
            return true; 
         }
         else
         {
            return false; 
         }
      }

      /**
      * check if user denied auth 
      * response will be
      * : &error_reason=user_denied&error=access_denied&error_description=The+user+denied+your+request.#_=_
      */
      function isUserDenied()
      {
         if(isset($_GET['error']) && $_GET['error'] == 'access_denied')
         {
            return true;
         }

         return false;
      }

      /**
      * redirect 
      * @params string  $method  php_header|js_location|js_top
      */
      function redirect($method,$url)
      {
         switch($method)
         {
            case 'php_header':
               header("Location:$url");
               break;
            case 'js_location':
               echo '<script type="text/javascript">location.href="' . $url . '";</script>';
               break;
            case 'js_top':
               echo '<script type="text/javascript">top.location="' . $url . '";</script>';
               break;
            default:
               throw new Exception("unknown redirect method");
               break;

            }

         }
         /**
         * login 
         * @params string  $method  php_header|js_location|js_top
         */
         function login($method,$perms,$params=array())
         {
            //if has login and has permissions , do not login again
            if($perms != false)
            {
               if($this->hasPermission($perms) == true)
               {
                  return true;
               }
            }
            else
            {
               if($this->isLogin() == true)
               {
                  return true;
               }
            }

            $login_url=$this->getLoginUrl($perms,$params);
            $this->redirect($method,$login_url);

            /*
            if($this->isLogin() == false)
            {
               $login_url=$this->getLoginUrl($perms,$params);
               $this->redirect($method,$login_url);
            }
            */
         }
         /**
         * request permission
         * 
         */
         function requestPermission($perms,$params=array())
         {
            $url = $this->facebook->getLoginUrl($perms,$params);

            $this->redirect("php_header",$url);
         }

         function logout($method)
         {
            if($this->isLogin() == false)
            {
               $logout_url=$this->getLogoutUrl();

               $this->redirect($method,$logout_url);
            }
         }

         function getLoginUrl($perms="publish_stream",$params=array())
         {
            $default=array(
               'scope' => $perms,
               'redirect_uri' => '',
               'display' => 'page',
            );

            /*
            if(!is_array($perms))
            {
               $perms=explodeString($perms);
            }
            */


            $params=array_merge($default,$params);
            //set default redirect uri
            if($params['redirect_uri'] == false)
            {
               $params['redirect_uri']=$_SERVER['HTTP_REFERER'];
            }

            $url = $this->facebook->getLoginUrl($params);

            return $url;
         }

         function getLogoutUrl()
         {
            return $this->facebook->getLogoutUrl();
         }

         /**
         * get user id
         */
         function getUserId()
         {
            return $this->facebook->getUser(); 
         }

         /**
         *  get user information with api call
         */
         function getUser()
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call getUser method");
            }

            $path="/".$this->getUserId();
            $data=$this->api($path);
            return $data;
         }

         /**
         * get exists permissions
         *
         * @return Array $data  format: array(permission1=>1,permission2=>1...)
         */
         function getPermissions()
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call getUser method");
            }

            $path="/".$this->getUserId()."/permissions";
            $result=$this->api($path);

            $data=$result['data'][0];

            return $data;
         }

         /**
         * check if has permssion, can check one or multi permissions,
         * for multi permission, if one not exists, return false
         * 
         * @param  string $perms
         * 
         */
         function hasPermission($perms)
         {
            if($this->isLogin() == false)
            {
               return false;
               //throw new Exception("not login when call hasPermission method");
            }

            $permissions=$this->getPermissions();

            $perms=explodeString($perms);

            foreach($perms as $perm)
            {
               if(!isset($permissions[$perm]) || $permissions[$perm] != 1) 
               {
                  return false;
               }
            }

            return true;
         }

         function getAccessToken()
         {
            return $this->facebook->getAccessToken(); 
         }

         function setAccessToken($token)
         {
            if($token != false)
            {
               $this->facebook->setAccessToken($token); 
            }
         }

         /**
         * api call
         */
         function api($path,$method="GET",$params=array())
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call fql method");
            }

            try{
               $path="/".ltrim($path,"/");
               $result=$this->facebook->api($path,$method,$params);
            }
            catch(Exception $e)
            {
               $result=$e->getResult();
               $error=$result['error'];

               $this->setError($error);

               return false;
            }

            return $result;
         }

         /**
         * fql call
         */
         function fql($fql)
         {
            if($this->isLogin() == false)
            {
               throw new Exception("not login when call fql method");
            }

            try {
               $result = $this->facebook->api(array(
                  'method' => 'fql.query',
                  'query' => $fql,
               ));

               return successReturn('',array('data'=>$result));

            }
            catch(FacebookApiException $e)
            { 
               //error_log($e->getType());
               //error_log($e->getMessage());

               return errorReturn($e->getMessage());
            }
         }


         /** fql helper functions **/
         function insights($object_id,$metric,$end_date,$period)
         {
            $fql="SELECT object_id,metric,end_time,period,value";
            $fql.=" FROM insights";
            $fql.=" WHERE object_id=$object_id";
            $fql.=" AND metric='$metric' ";
            $fql.=" AND end_time=end_time_date('$end_date') ";
            $fql.=" AND period=period('$period')";

            $data=$this->fql($fql);
            return $data;
         }

         /**
         * get user's pages
         */
         function getUserPages()
         {
            $uid=$this->getUserId();

            if($uid == false)
            {
               throw new Exception("can not get user id,please login first.");
            }

            //$fql="SELECT page_id, type from page_admin WHERE uid='$uid'";
            $fql="SELECT page_url,page_id,name,pic,type,has_added_app";
            $fql.=" FROM page WHERE page_id IN ";
            $fql.="(";
            $fql.="SELECT page_id From page_admin where uid=".$uid;
            $fql.=")";
            $fql.="order by name";

            $data=$this->fql($fql);

            return $data;
         }

         /**
         * get insights  values
         *
         * @param  string since_date  start date (included)
         * @param  string until_date  end date (included)
         * @return  Array  format:   array(
            array(
               value=>
               end_time=>2011-12-12T08:00:00+0000      
            )

            */
            function insightsValues($object_id,$metric,$since_date,$until_date)
            {
               $since=strtotime($since_date);
               $until=strtotime($until_date)+86400;

               $graph="/$object_id/insights/$metric?format=json&since=$since&until=$until";

               $data=$this->api($graph);

               if(is_array($data))
               {
                  $values=array();
                  foreach($data['data'] as $v)
                  {
                     $values[$v['period']]=$v['values']; 
                  }

                  return $values;
               }


               return false;
            }

            /******** action methods **********/

            /**
            * add fb action
            * 
            * @param string  $path  format:  /{USER_ID|me}/{APP NAME SPACE}:{ACTION}
            * @param array   $params
            *
            * @return  integer  action_id or false
            */
            function addAction($path,$params)
            {
               $result=$this->api($path,"POST",$params);

               if($result == false)
               {
                  return false;
               }

               $action_id=$result['id'];
               return $action_id;
            }

            /**
            * get action's information
            *
            * @param integer  $action_id  
            * 
            * @return array   action's information or false for error
            */
            function getAction($action_id)
            {
               $path="/".ltrim($action_id,"/");
               $result=$this->api($path,"GET");

               return $result;
            }

            /**
            * edit action's information
            */
            function editAction($action_id,$params=array())
            {
               $path="/".ltrim($action_id,"/");
               $result=$this->api($path,"POST",$params);

               return $result;
            }

            /**
            * delete action
            *
            * @return  true for success or false
            */
            function delteAction($action_id)
            {
               $path="/".ltrim($action_id,"/");
               $result=$this->api($path,"DELETE");

               return $result;
            }

            /*
            function likeAction()
            {
               ;
            }

            function commentAction()
            {
               ;
            }
            */

            /**
            * set fb error msg
            */
            function setError($error)
            {
               $this->error=$error;
            }

            /**
            * get fb error msg: 
            * format: array(
               'message'=>..
               'type'=>...
               'code'=>...
               )
            */
            function getError()
            {
               return $this->error;
            }

            /*******************/

            function getFriends($user_id='me')
            {
               $result=$this->api("$user_id/friends","GET");

               if($result != false)
               {
                  return $result['data'];
               }

               return $result;
            }


         }

