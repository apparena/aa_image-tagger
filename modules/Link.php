<?php

   /**
   * Link functions for facebook app
   * 
   * functions:
   *  1, craete link
   *  2, get parameter
   *  3, create link html
   *
   * @author fred
   *
   */
   class Link 
   {
      //current params
      protected $_params=array();

      protected $signed_request='';
      protected $data=array();
      protected $app_data=array(); //app_data contain params

      function __construct($signed_request=false) 
      {
         if($signed_request != false)
         {
            $this->signed_request=$signed_request;
            $this->parseRequest($signed_request);
         }
      }

      /**
      * Creates a link to a file which should be shown in the Fanpage tab
      * @param String $file Filename which should be loaded to the Fanpage-Tab
      * @return String Link to show the commited file in the Fanpage Tab
      */
      public static function createFanLink($param=array()) 
      {
         $global=Frd::getGlobal();

         $param_str='';
         foreach($param as $k=>$v)
         {
            $param_str.=$k.'='.urlencode($v).'&';
         }

         $app_data=base64_encode($param_str);

         $link = $global->instance->fb_page_url. "&app_data=" . $app_data;

         $link=handle_link($link);

         return $link;
      }

      public static function createCanvasLink($file,$param=array()) 
      {
         $global=Frd::getGlobal();

         $param_str='';
         foreach($param as $k=>$v)
         {
            $param_str.=$k.'='.($v).'&';
         }

         $param_str=trim($param_str,"&");

         $url='http://apps.facebook.com/'.$global->instance->fb_app_url.'/'.$file.'?'.$param_str;

         $url=handle_link($url);

         return $url;
      }

      public static function createBaseLink($file,$param=array()) 
      {
         $global=Frd::getGlobal();

         $param_str='';
         foreach($param as $k=>$v)
         {
            $param_str.=$k.'='.($v).'&';
         }

         $param_str=trim($param_str,"&");

         $url=$global->baseurl.$file.'?'.$param_str;

         return $url;
      }



      function display()
      {
         print_r($this->_params);
      }

      function base64_url_decode($input) 
      {
         return base64_decode(strtr($input, '-_', '+/'));
      }

      function checkIsParsed()
      {
         if($this->signed_request == false)
         {
            throw new Exception("no signed request exists");
         }
      }

      /*
      * parse params from signed_request
      */
      function parseRequest($signed_request,$secret=false)
      {
         $this->signed_request=$signed_request;

         if($signed_request== false)
         {
            return false;
         }

         $data = $this->parseSignedRequest($signed_request, $secret);

         $this->data=$data;

         if(isset($data['app_data']))
         {
            $this->parseAppData($data['app_data']);
         }

      }

      //for noew secret do not check
      function parseSignedRequest($signed_request, $secret=false) 
      {
         list($encoded_sig, $payload) = explode('.', $signed_request, 2); 

         // decode the data
         $sig = $this->base64_url_decode($encoded_sig);
         $data = json_decode($this->base64_url_decode($payload), true);

         if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') 
         {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
         }

         // check sig
         /*
         $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
         if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
         }
         */

         return $data;
      }

      function parseAppData($app_data) 
      {
         $app_data=base64_decode($app_data);

         $param_str=explode("&",$app_data);

         $params=array();
         foreach($param_str as $v)
         {
            $arr=explode("=",$v);
            if(is_array($arr) && count($arr) == 2)
            {
               list($kk,$vv)=$arr;

               if($kk != false)
               $params[$kk]=$vv;
            }
         }

         $this->app_data=$params;
      }


      /**
      * get fb page id in fanpage
      */
      function getPageId()
      {
         $this->checkIsParsed();
         if(isset($this->data['page']))
         {
            $page_id=$this->data['page']['id'];
         }
         else
         {
            $page_id=false;
         }

         return $page_id;
      }

      function isFan()
      {
         $this->checkIsParsed();
         if(isset($this->data['page']))
         {
            $is_fan=$this->data['page']['liked'];
         }
         else
         {
            $is_fan=false;
         }

         return $is_fan;
      }

      function isAdmin()
      {
         $this->checkIsParsed();
         if(isset($this->data['page']['admin']))
         {
            $is_admin=$this->data['page']['admin'];
         }
         else
         {
            $is_admin=false;
         }

         return $is_admin;
      }

      function getUserId()
      {
         $this->checkIsParsed();

         if(isset($this->data['user_id']))
         {
            $user_id=$this->data['user_id'];
         }
         else
         {
            $user_id=false;
         }

         return $user_id;
      }


      /*
      * get param from current page, the param is store in _GET['app_data']
      *
      * @param  key   string parameter key
      * @param  default  the default value if not have parameter
      */
      public function getParam($key,$default=false)
      {
         if(isset($_GET[$key]))
         {
            return $_GET[$key];
         }

         if(isset($this->app_data[$key]))
         {
            return $this->app_data[$key];
         }
         else
         {
            return $default;
         }
      }
   }
