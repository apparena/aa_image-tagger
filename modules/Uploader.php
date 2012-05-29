<?php
/**
 * uploader
 */
class Uploader extends Frd_Uploader
{
  function init()
  {

   $uploader=new Frd_Uploader(array(
      'file'=>$_FILES["photo"],
      'dest'=>"/uploads",
      'domain'=>"http://test.com",
   ));
  }

  function pathToUrl()
  {
    return $this->dest_path;
  }
}
?>
