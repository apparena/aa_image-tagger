<?php
/**
 *  create  css tag,
 *  merge css files
 *  use cache version to refresh new css file 
 *  Usage:
 *     $baseurl="http://frd.frd.info";
 *     $folder="css/frd";
 *     $cache_baseurl="http://frd.frd.info/cache";
 *     $cache_folder="cache";

 *     $cache=new Frd_Css_Cache($baseurl,$folder,$cache_baseurl,$cache_folder);

 *     $cache->addCss("base");
 *     $cache->addCss("menu");
 *     $cache->addCss("paginator");

 *     //$cache->clearCache(2);
 *     $cache->cache(2);
 *     $cache->output();
 *
 */
class Frd_Js_Cache
{
  protected $cache_baseurl='';  //cached file 's baseurl
  protected $cache_folder=''; //cached file's folder

  protected $files=array();  //js files

  protected $is_cached=false;

  protected $version=0; //current version ,can be integer or string (do not with any special characters or blank)

  /**
   * init,should provide js folder and js baseurl
   */
  function __construct($cache_folder,$cache_baseurl)
  {

    checkString($cache_baseurl);
    checkString($cache_folder);

    $this->cache_baseurl=rtrim($cache_baseurl,'/').'/'; 
    $this->cache_folder=rtrim($cache_folder,'/').'/'; 

    // need check if writeable  of cache folder, is readable js folder
    /*
      if(!is_writeable($path))
        throw new Exception('File '.$this->cache_folder.' is not writeable,can not cache.');
     */
  }

  /**
   * add a js file, without .js
   *
   * @param  boolean $cache , cache or not cache in cache
   */
  function addJs($filename,$type="file")
  {
    $this->files[]=$filename;
  }

  /**
   * merge 
   */
  function cache($version=0)
  {
    if($this->version == $version)
      return false;

    $this->version=$version;
    $this->is_cached=true;


    $path=$this->cache_folder.'all_'.$version.'.js';

    if(!file_exists($path))
    {
      //add last modified datetime
      $content='/*Last Modified : ' .date("Y-m-d H:i:s").'*/';
      $content.="\r\n";


      foreach($this->files as $file)
      {
        $content.=file_get_contents($file);  
        $content.="\r\n";
        $content.="\r\n";
      }

      if(file_put_contents($path,$content) == false)
      {
        throw new Exception('File '.$path.' write no character.');
      }

    }

    $this->cache_link=$this->cache_baseurl.'all_'.$version.'.js';
  }

  /**
   * remove the cache file, so can create new cache
   */
  function clearCache($version)
  {
    $path=$this->cache_folder.'/all_'.$version.'.js';
    unlink($path);
  }

  /**
   * output the js script html,
   * maybe you do not need output directly ,will set a variable,and outptu in template, so  make  $output = false
   *
   */
  function output($version=1,$output=true,$force_cache=false)
  {
    $html='';

    if($force_cache == true)
    {
      $this->clearCache($version);
     $this->cache($version); 
    }

    if($this->is_cached == false)
      $this->cache($version); 

    $html= '<script src="'.$this->cache_link.'" type="text/javascript" ></script>';

    if($output == true)
      echo $html;
    else
      return $html;
  }
}
