<?php
   /**
   * facebook batch 
   * this do not need session support,only need access_token,
   * the access_token should get from other place , maybe will permission "offline_access"
   * Usage:
      $access_token="";

      $batch=new Frd_Facebook_Batch();
      $batch->setAccessToken($access_token);

   */
   class Frd_Facebook_Batch
   {
      protected $client=null; //http client
      protected $server_url="https://graph.facebook.com";
      protected $access_token=''; //access token, very important
      protected $params=array();  //request params

      function __construct($access_token=false)
      {
         $this->client=new Zend_Http_Client();
         $this->client->setUri($this->server_url);
         $this->client->setConfig(array(
            'maxredirects'=>0,
            'timeout'=>30,
         ));

         if($access_token != false)
         {
            $this->setAccessToken($access_token);
         }
      }

      /**
      * set access token
      */
      function setAccessToken($access_token)
      {
         $this->params['access_token']=$access_token;
      }

      /**
      * graph api call
      */
      function api($path)
      {
         $path=ltrim($path,"/");

         $this->params['batch']=array(
            'method'=>'GET',
            'relative_url'=>$path,
         );

         $this->params['batch'] =json_encode(array($this->params['batch']));

         $data=$this->request();

         return $this->getResponse($data);
      }

      /**
      * only get one response,actually it support multiple request
      */
      function getResponse($data)
      {
         $data=json_decode($data,true);
         $data=$data[0];

         if($data['code'] == 200)
         {
            return json_decode($data['body'],true);
         }
         else
         {
            return false; 
         }
      }

      /**
      * request server uri
      */
      function request()
      {
         $this->client->setParameterPost($this->params);

         $response=$this->client->request('POST');

         $content=$response->getBody();

         return $content;
      }

      /**
      * get insights  data
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

   }


?>
