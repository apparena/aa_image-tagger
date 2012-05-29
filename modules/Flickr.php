<?php
/**
 * for flickr 
 */
require_once(dirname(__FILE__).'/../lib/phpflickr/phpFlickr.php');

class Flickr
{

  protected $app_key='';
  protected $app_secret='';
  protected $token='';
  protected $f=null;  //flickr object

  function __construct($app_key,$app_secret,$token='')
  {
    $global=Frd::getGlobal();

    if($global->instid == false)
    {
      throw new Exception("invalid  instid"); 
    }

    if($app_key == false )
    {
      throw new Exception("invalid  flickr app key"); 
    }

    if( $app_secret == false)
    {
      throw new Exception("invalid  flickr app secret"); 
    }

    $this->instid=$global->instid;
    $this->app_key=$app_key;
    $this->app_secret=$app_secret;
    $this->token=$token;

    if( $this->token == false)
    {
      throw new Exception("invalid  flickr token"); 
    }

    $this->f = new phpFlickr($this->app_key,$this->app_secret);

    $this->f->setToken($this->token);
    $this->f->auth('write');
  }
  /* flickr functions */
  /*
   * the token must be valid
   * so for user ,it does not auth ,
   * because we have already did the auth
   *
   * @return image id
   */
  function upload($filename,$title,$description)
  {
    $photo_id=$this->f->sync_upload ($filename, $title,$description);

    if($photo_id != false)
    {
      $photo=$this->f->photos_getInfo ($photo_id) ;
      $photo=$photo['photo'];

      //var_dump($photo);
      $url=$this->f->buildPhotoURL($photo, "small");
      $original_url=$this->f->buildPhotoURL($photo, "large");
    }
    else
    {
      $url=""; 
      $original_url='';
    }

    return array($url,$original_url,$photo_id);
  }

  /**
   * get photo info by photo_id
   */
  function photoinfo($photo_id)
  {
    if($photo_id != false)
    {
      $photo=$this->f->photos_getInfo ($photo_id) ;
      $photo=$photo['photo'];


      return $photo;

    }
    else
    {
      return false; 
    }

  }

  /**
   *
   * @return  boolean  true if create successful, otherwise false
   */
  function createPhotoSet($title,$description,$primary_photo_id)
  {
    $db=Frd::getDb();
    if($db == false)
    {
      throw new Exception("db object not exists"); 
    }
    
    $app_config=new App_Config($db,$this->instid);
    $photoset_id=$app_config->photoset_id;

    if($photoset_id == false)
    {
      $ret=$this->f->photosets_create ($title, $description, $primary_photo_id) ;
      if(is_array($ret))
      {
        $photoset_id=$ret['id']; 
        $photoset_url=$ret['url']; 

        $app_config->photoset_id=$photoset_id;
        $app_config->photoset_url=$photoset_url;
      }
    }

    return $photoset_id;
  }

  /**
   * add photo to photoset
   */
  function photosetAddPhoto($photo_id)
  {
    $photoset_id=$this->createPhotoSet($this->instid,"instance ".$this->instid,$photo_id);
    if($photoset_id > 0)
    {
      $this->f->photosets_addPhoto($photoset_id, $photo_id);
      return true;
    }

    return false;
  }

  function buildPhoto($photo_id,$size)
  {
    $photo=$this->f->photos_getInfo ($photo_id) ;
    $photo=$photo['photo'];

    $url=$this->f->buildPhotoURL($photo, $size);

    return $url;
  }

}
