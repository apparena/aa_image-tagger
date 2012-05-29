<?php
/**
 * for youtube operate
 */
class Youtube
{

  protected $username='';
  protected $password='';
  protected $developkey='';

  protected $httpclient=null; //youtube http client
  protected $yt=null; //youtube object

  function __construct($username,$password,$developkey)
  {
    if($username == false)
    {
      throw new Exception("invalid youtube username");
    }

    if($password == false)
    {
      throw new Exception("invalid youtube password");
    }
    if($developkey == false)
    {
      throw new Exception("invalid youtube developkey");
    }

    $this->youtube_username=$username;
    $this->youtube_password=$password;
    $this->youtube_developkey=$developkey;
  }

  function getClient()
  {
     return $this->httpclient;
  }

  function authenticate()
  {

    $developerkey=$this->youtube_developkey;

    $authenticationURL= 'https://www.google.com/youtube/accounts/ClientLogin';
    $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
      $username = $this->youtube_username,
      $password = $this->youtube_password,
      $service = 'youtube',
      $client = null,
      $source = 'multimedia', // a short string identifying your application
      $loginToken = null,
      $loginCaptcha = null,
      $authenticationURL);


    $httpClient->setHeaders('X-GData-Key', "key=${developerkey}");

    $this->httpclient=$httpClient;

    $this->yt = new Zend_Gdata_YouTube($httpClient);
  }

  function createUploadForm($videoTitle, $videoDescription, $videoCategory, $videoTags, $nextUrl = null)
  {

    $httpClient = $this->httpclient;
    //$httpClient = getAuthSubHttpClient();
    $youTubeService = new Zend_Gdata_YouTube($httpClient);

    $newVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

    $newVideoEntry->setVideoTitle($videoTitle);
    $newVideoEntry->setVideoDescription($videoDescription);

    //make sure first character in category is capitalized
    $videoCategory = strtoupper(substr($videoCategory, 0, 1))
      . substr($videoCategory, 1);
    $newVideoEntry->setVideoCategory($videoCategory);

    // convert videoTags from whitespace separated into comma separated
    $videoTagsArray = explode(' ', trim($videoTags));
    $newVideoEntry->setVideoTags(implode(', ', $videoTagsArray));

    $tokenHandlerUrl = 'http://gdata.youtube.com/action/GetUploadToken';
    try {
      $tokenArray = $youTubeService->getFormUploadToken($newVideoEntry, $tokenHandlerUrl);
        /*
        if (loggingEnabled()) {
            logMessage($httpClient->getLastRequest(), 'request');
            logMessage($httpClient->getLastResponse()->getBody(), 'response');
        }
         */
    } catch (Zend_Gdata_App_HttpException $httpException) {
      print 'ERROR ' . $httpException->getMessage()
        . ' HTTP details<br /><textarea cols="100" rows="20">'
        . $httpException->getRawResponseBody()
        . '</textarea><br />'
        . '<a href="session_details.php">'
        . 'click here to view details of last request</a><br />';
      return;
    } catch (Zend_Gdata_App_Exception $e) {
      print 'ERROR - Could not retrieve token for syndicated upload. '
        . $e->getMessage()
        . '<br /><a href="session_details.php">'
        . 'click here to view details of last request</a><br />';
      return;
    }

    $tokenValue = $tokenArray['token'];
    $postUrl = $tokenArray['url'];

    $vars=array(
      'posturl'=>$postUrl,
      'nexturl'=>$nextUrl,
      'tokenvalue'=>$tokenValue
    );

    return $vars;
  }

  //get video 's falsh url
  function getVideoUrl($id)
  {
    $httpClient = $this->httpclient;
    $yt = new Zend_Gdata_YouTube($httpClient);

    $entry=$yt->getVideoEntry($id);
    return $entry->getFlashPlayerUrl();
  }

  //get video 's data,as array so can extend in the future
  function getVideoData($id)
  {
    $httpClient = $this->httpclient;
    $yt = new Zend_Gdata_YouTube($httpClient);

    //$entry=$yt->getVideoEntry($id);
    try
    {
      //  $entry=$yt->getVideoEntry($id,null,true);
      $entry=$yt->getVideoEntry($id);
    }
    catch(Exception $e)
    {
      //echo $e->getMessage();
      return false;
    }

    //set data
    $data=array();

    $thumbnails=$entry->getVideoThumbnails();
    if(count($thumbnails) >0 )
    {
      $data['thumbnail']=$thumbnails[0];
      $data['thumbnails']=$thumbnails;
    }

    $data['flash_url']=$entry->getFlashPlayerUrl();
    $data['is_embeddable']=$entry->isVideoEmbeddable();

    //var_dump(get_class_methods($entry));

    return $data;
  }

  //delete video in youtube
  function delete($id)
  {
    $httpClient = $this->httpclient;
    $yt = new Zend_Gdata_YouTube($httpClient);

    //must have null,true parameters for delete
    try
    {
      $entry=$yt->getVideoEntry($id,null,true);
    }
    catch(Exception $e)
    {
      return false;
    }

    $yt->delete($entry);
    return true;
  }

  //check if this video exists
  function checkCode($id)
  {
    if($id ==false)
      return false;

    $httpClient = $this->httpclient;
    $yt = new Zend_Gdata_YouTube($httpClient);

    try
    {
      $entry=$yt->getVideoEntry($id,null,true);
    }
    catch(Exception $e)
    {
      return false;
    }

    return $entry->getFlashPlayerUrl();
  }

  //get code from url
  function getCode($url)
  {
    $url=trim($url);
    //$url="http://www.youtube.com/watch?v=h1ZYT97prP8";
    $baseurl="http://www.youtube.com/watch?v=";

    if(strpos($url,$baseurl) === 0)
    {
      $code =str_replace($baseurl,"",$url);
    }
    else
    {
       $baseurl="www.youtube.com/watch?v=";
       if(strpos($url,$baseurl) === 0)
       {
          $code =str_replace($baseurl,"",$url);
       }
       else
       {
          $code= ""; 
       }
    }

    //if watch?v=CODE&param1=value1&...., 
    //drop the string before &
    if(strpos($code,"&")!= false)
    {
      $pos= strpos($code,"&");

      $code=substr($code,0,$pos);
    }

    //for pattern "http://youtu.be/1qfESSjPnAc"
    if($code == false)
    {
       $pattern="/http:\/\/youtu.be\/(\w+)/";
      $matches=array();

      $count=preg_match($pattern,$url,$matches);
      if($count > 0)
      {
         $code= $matches[1];
      }
    }

    return $code;
  }

  //youtube_id in database
  function getThumbnails($id)
  {
    if($id ==false)
      return false;

    $httpClient = $this->httpclient;
    $yt = new Zend_Gdata_YouTube($httpClient);

    try
    {
      $entry=$yt->getVideoEntry($id,null,true);
    }
    catch(Exception $e)
    {
      return false;
    }

    //will return array of thumbnails
    return $entry->getVideoThumbnails();
  }

  //playlist
  function playlistAdd($title,$description)
  {
     $newPlaylist = $this->yt->newPlaylistListEntry();
     $newPlaylist->title = $this->yt->newTitle()->setText($title);
     $newPlaylist->summary = $this->yt->newDescription()->setText($description);
     // post the new playlist
     $postLocation = 'http://gdata.youtube.com/feeds/api/users/default/playlists';
     try 
     {
        $this->yt->insertEntry($newPlaylist, $postLocation);
     } catch (Zend_Gdata_App_Exception $e) {
        echo $e->getMessage();
     }
     //get entry
     $this->yt->setMajorProtocolVersion(2);

     $playlistListFeed = $this->yt->getPlaylistListFeed($this->youtube_username);

     foreach ($playlistListFeed as $playlistListEntry) 
     {
        if( $playlistListEntry->title->text == $title )
        {
           return $playlistListEntry;
        }
     }

  }

  function playlistExists($title)
  {
     // optionally set version to 2 to retrieve a version 2 feed
     $this->yt->setMajorProtocolVersion(2);

     $playlistListFeed = $this->yt->getPlaylistListFeed($this->youtube_username);

     foreach ($playlistListFeed as $playlistListEntry) 
     {
        if( $playlistListEntry->title->text == $title )
        {
           return $playlistListEntry;
        }
     }

     return false;
  }

  function playlistAddEntry($playlistListEntry,$entry_id)
  {
     $postUrl = $playlistListEntry->getPlaylistVideoFeedUrl();
     // video entry to be added
     $videoEntryToAdd = $this->yt->getVideoEntry($entry_id);

     // create a new Zend_Gdata_PlaylistListEntry, passing in the underling DOMElement of the VideoEntry
     $newPlaylistListEntry = $this->yt->newPlaylistListEntry($videoEntryToAdd->getDOM());

     // post
     try {
        $this->yt->insertEntry($newPlaylistListEntry, $postUrl);
     } 
     catch (Zend_App_Exception $e) 
     {
        echo $e->getMessage();
     }
  }

}
