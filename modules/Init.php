<?php
/**
 * this is for init db,fluttery,global varialbes, aut facebook and so on
 * so in other page can only call method which it needed, but do not init all the things as before
 */
class Init
{

  /**
   * init db connection
   * after that $db=Frd::getDb()  can work,or $global->db
   */
  function initDb()
  {
    global $database_host, $database_name, $database_user,$database_pass;

    $global=Frd::getGlobal();

    $db_config=array(
      'host'=>$database_host,
      'username'=>$database_user,
      'password'=>$database_pass,
      'dbname'=>$database_name
    );

    $db = Zend_Db::factory('pdo_mysql',$db_config);
    $db->query('set names utf8');
    Zend_Db_Table::setDefaultAdapter($db);
    $global->db=$db;

    $registry = Zend_Registry::getInstance();
    $registry->set("db_default",$global->db);
  }

  /**
   * use soap to get instance data from server
   * this need instance id or page id
   */
  function initFluttery($instid=null,$page_id=null)
  {
    global $aa_app_id;
    global $aa_api_key;
    global $session_expiration;
    global $allow_cache;


    $global=Frd::getGlobal();


    $params=array(
       'aa_app_id'=>$aa_app_id,
       'aa_app_secret'=>$aa_api_key,
       'aa_inst_id'=>'',
       'fb_page_id'=>'',
    );

    if($instid == false && $page_id == false)
      return false;

    $fluttery = new Fluttery();
    if($instid >0 )
      $params['aa_inst_id']=$instid;
    else
      $params['fb_page_id']=$page_id;


    $fluttery->init($params);

    //$instid=$fluttery->getInstanceId();

    //global  variables


    /*
    if($allow_cache == true)
    {
      $session_key=generateSessionKey($instid);
      $session=new Zend_Session_Namespace($session_key);
      $session->setExpirationSeconds($session_expiration);

      if(!isset($session->instances) || !is_array($session->instances) )
        $session->instances=array();

      $fluttery->setSession($session);
    }
    */


    $data=$fluttery->getData();

    /*
    if(is_object($data) && $data->error == 1)
    {
      echo $data->error_msg;
      exit(); 
    }
    */

    //var_dump($data);exit();
//  print_r($data);
    $config = $data['config'];
    //$content = $data['content'];
    $instance = $data['instance'];
    //$design = $data['design'];

    //$global->config = new Frd_Array_Object($config);
    $global->config = $config;

    //$global->content = new Frd_Array_Object($content);
    //$global->instance = new Frd_Array_Object($instance);
    $global->instance = $instance;
    //$global->design = new Frd_Array_Object($design);
    //$global->design = $design;

    //$global->instid=$fluttery->getInstanceId();
    $global->instid=$global->instance->aa_inst_id;
    $global->page_id=$page_id; //page id may not exists
    $global->fb_app_id= $global->instance->fb_app_id;

    $global->app_secret= $global->instance->fb_app_secret;

    $global->baseurl=$global->instance->fb_canvas_url;
    $global->appurl=handle_link('http://apps.facebook.com/'.$global->instance->fb_app_url.'/');


    $global->instance->fb_page_url=str_replace("http://www.facebook.com","",$global->instance->fb_page_url);
    $global->instance->fb_page_url=str_replace("https://www.facebook.com","",$global->instance->fb_page_url);


    $global->pageurl=handle_link("http://www.facebook.com".$global->instance->fb_page_url."?sk=app_".$global->fb_app_id);

    $global->instance->fb_like_url=handle_link("http://www.facebook.com".$global->instance->fb_page_url);

    $global->instance->fb_page_url=handle_link("http://www.facebook.com".$global->instance->fb_page_url."?sk=app_".$global->fb_app_id);


  }

  /**
   * get page id ,only work in fanpage
   *
   * @return string  $page_id , if not exists, return false
   */
  function initPageId()
  {
    $global=Frd::getGlobal();

    if(isset($_REQUEST['signed_request']))
    {
      $global->page_id=get_page_id();
      return $global->page_id;
    }

    return false;
  }

  /**
   * debug object for global
   */
  function initDebug()
  {
    $global=Frd::getGlobal();
    $global->debug=new Debug();
  }

  /**
   * view object for render pages
   */
  function initView($basepath,$global)
  {
    $view=new Zend_View();
    $view->setBasePath($basepath);
    $view->setScriptPath($basepath.'/page');

    $view->global=$global;

    return $view;
  }

  /**
    * view object for render layout,
    * layout will contain view
   */
  function initLayout($basepath,$global)
  {
    $view=new Zend_View();
    $view->setBasePath($basepath);
    $view->setScriptPath($basepath.'/layout');

    $view->global=$global;

    return $view;
  }

  /**
   * auth facebook use php 
   * this need next url and cancel url,
   * if missed, may have problem
   */
  function authFacebook($next='',$cancel_url='')
  {
    //global $global;
    $global=Frd::getGlobal();

    require_once dirname(__FILE__).'/../lib/fbapi/facebook.php';

    if($global->fb_app_id == false || $global->app_secret == false)
    {
      throw new Exception("app id or app secret not exists"); 
    }
    //$facebook=new Facebook($api_key,$secret);
    $facebook = new Facebook(array(
      'appId' => $global->fb_app_id,
      'secret' => $global->app_secret,
      'cookie' => true));

    $global->facebook=$facebook;
    $session = $facebook->getSession();

    try
    {
      $global->fbme = $facebook->api('/me');  
    } 
    catch (FacebookApiException $e) 
    {
      $global->fbme = false;
      error_log($e);
      //echo $e->getMessage();
    }  

    if ($global->fbme == false) 
    {
      $params['canvas'] =1;
      $params['fbconnect'] = 0;
      $params['req_perms'] = 'email';

      if($next != false)
        $params['next'] = $next;

      if($cancel_url != false)
        $params['cancel_url'] = $cancel_url;

      $login_url = $facebook->getLoginUrl($params);

      echo '<script>top.location="' . $login_url . '";</script>';
      //header("Location:$login_url");
      exit();
    }

    return true;
  }

  /**
   * debug object for global
   */
  function initTranslate()
  {
    $global=Frd::getGlobal();

    $root_path=dirname(__FILE__).'/..';
    //set translate
    $translate = new Zend_Translate('csv', $root_path.'/locale/de.csv', 'de',array('delimiter' => ';'));

    $translate->addTranslation($root_path.'/locale/zh.csv', 'zh');

    $translate->setLocale('de');
    $global->translate=$translate;
  }

}
