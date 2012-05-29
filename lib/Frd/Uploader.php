<?php
   /**
   *
   * @version 0.0.1
   * @status try
   *
   $uploader=new Frd_Uploader(array(
      'file'=>$_FILES["photo"],
      'dest'=>"/uploads",
      'domain'=>"http://test.com",
   ));

   $uploader->setAllowType(array("img","jpg"));
   $uploader->setDenyType(array("png");
   $uploader->setSizeLimit(4000);

   $ret=$uploader->upload();

   if($ret == false)
   {
      echo $uploader->getErrorCode(); 
      echo $uploader->getErrorMessage(); 
   }
   else
   {
      echo $ret['url'];
   }

   //$ret=$uploader->uploadAll();
   //var_dump($ret['urls']);


   *@version  2011-10-26
   *@update more easy, only pass config in construct and set some limit if you want

   */
/**
 *
 *
 */
class Frd_Uploader
{
  protected $file=null;
  protected $dest='';
  protected $domain='';

  //error_codes
  const TOO_SMALL='uploader_too_small';
  const TOO_LARGE='uploader_too_large';
  const TYPE_NOT_ALLOW='uploader_type_not_allow';
  const TYPE_DENY='uploader_type_deny';
  const UPLOAD_FAILED='uploader_upload_failed';
  const FILE_NOT_EXISTS='uploader_file_not_exists';
  const DEST_NOT_WRITEABLE='uploader_dest_not_writeable';
  const DEST_PERMISSION_DENIED='uploader_dest_permission_denied';

  //limits
  protected $size_max=0;   //0 means no limit
  protected $size_min=0;   //0 means no limit

  protected $allow_types=array();
  protected $deny_types=array();


  function __construct($params)
  {
    $this->init();

    if(!isset($params['file']))
    {
      throw new Exception("missing parameter file"); 
    }

    if(!isset($params['dest']))
    {
      throw new Exception("missing parameter dest"); 
    }

    if(!isset($params['domain']))
    {
      throw new Exception("missing parameter domain"); 
    }

    $this->file=$params['file'];
    $this->dest=$params['dest'];
    $this->domain=$params['domain'];
  }

  /**
   * init method for instances to config variable here
   */
  function init() { }

  /**
   *
   */
  function setAllowType($types)
  {
     $this->allow_types=$types;
  }

  /**
   *
   */
  function setDenyType($types)
  {
     $this->deny_types=$types;
  }

  /**
   *the size is   > and < not  >= or <=
   */
  function setSizeLimit($size_max,$size_min='')
  {

    $this->size_max=intval($size_max);	
    $this->size_min=intval($size_min);	
  }

  /**
   *
   * get filename's suffix
   */
  function getFileType($name)
  {
    if(strpos($name,".") === false)
    {
      return "";
    }
    else
    {
      $arr=explode('.', $name);
      return strtolower(array_pop($arr));
    }
  }

  function getErrorCode()
  {
    return $this->error_code;
  }


  function validate()
  {
    if($this->size_max != false)
    {
       if(ceil($this->file['size']/1000) > $this->size_max)		
      {
        $this->error_code=Frd_Uploader::TOO_LARGE;
        return false;
      }
    }

    if($this->size_min != false)
    {
       if(ceil($this->file['size']/1000) < $this->size_min)
      {
        $this->error_code=Frd_Uploader::TOO_SMALL;
        return false;
      }
    }

    $type=$this->getFileType($this->file['name']);

    if(!empty($this->allow_types))
    {
       if(in_array($type,$this->allow_types) == false)
       {
          $this->error_code=Frd_Uploader::TYPE_NOT_ALLOW;
          return false; 
       } 
    }
    else if(!empty($this->deny_types))
    {
      if(in_array($type,$this->deny_types))
      {
         $this->error_code=Frd_Uploader::TYPE_DENY;
         return false;
      }

    }

    return true;
  }

  /**
   * @param  string  $file  $_FILES's  key,  all for all files
   * @param  string  $dest  where upload to 
   */
  function upload()
  {
     if($this->validate() == false)
     {
        return false;
     }

     //var_dump(is_writeable($this->dest));

     if(file_exists($this->dest) == false)
     {
        /*
        if(is_writeable($this->dest) == false)
        {
           $this->error_code=Frd_Uploader::DEST_PERMISSION_DENIED;
           return false;
        }
        */

        if(mkdir($this->dest,0777,true) == false)
        {
           $this->error_code=Frd_Uploader::DEST_PERMISSION_DENIED;
           return false;
        }

        if(is_writeable($this->dest) == false)
        {
           $this->error_code=Frd_Uploader::DEST_NOT_WRITEABLE;
           return false;
        }
     }

     $file_path=rtrim($this->dest,"/")."/".$this->file['name'];


     if(move_uploaded_file($this->file['tmp_name'], $file_path) == false)
     {
        $this->error_code=Frd_Uploader::Upload_Failed;
        return false;
     }

     $result=array(
        'url'=>rtrim($this->domain,"/")."/".$this->file['name'],
        'path'=>$file_path,
     );

     return $result;
  }
}
