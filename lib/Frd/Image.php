<?php
   /**
   *  handle image
   *
   *  TODO:
   *  wideimage  seems powerful
   *  so this class can add more powerful method
   *  the method for different image format should be easy to use
   *  *  add more methods
   *  *  be an interface
   *  *  add unit test  
   *
   *  example
   $frd_image=new Frd_Image();
   //$filepath=dirname(__FILE__).'/test/1.jpg';
   $filepath=dirname(__FILE__).'/test/1.jpg';
   #$new_filepath=dirname(__FILE__).'/test/2.jpg';
   $new_filepath=dirname(__FILE__).'/test/3.jpg';

   $frd_image->load($filepath);

   echo $frd_image->getWidth();
   echo $frd_image->getHeight();

   #$frd_image->resizeToWidth(100);
   #$frd_image->resizeToHeight(100);
   //$frd_image->resize(200,60);

   *
   */

   class Frd_Image
   {
      protected $image=null; //image object

      function __construct()
      {
         $this->loadLibs();
      }

      /**
      * load image lib, this lib is the real image handler
      */
      protected function loadLibs()
      {
         include_once(dirname(__FILE__).'/extra_libs/wideimage/WideImage.php');
      }

      /**
      * load an image
      */
      function load($filename) 
      {
         $this->image=WideImage::load($filename);
      }

      /**
      * get image, only for it self  to use
      */
      protected function getImage()
      {
         if($this->image == false)
         {
            throw new Exception("image is not loaded, please use  load(PATH) to load it ");
         }

         return $this->image;
      }

      protected function setImage($image)
      {
         $this->image=$image;
      }

      /**
      * save the image  to harddisk
      */
      function save($filename)
      {
         //create the folder first, if not exists
         Frd_File::createDirByFilePath($filename);

         $this->getImage()->saveToFile($filename);
      }

      //asString  :  image to string 

      /**
      * output to browser
      */
      function output($image_type='jpg') 
      {
         //$this->image->output($image_type, 45);
         $this->getImage()->output($image_type);
      }
      function getWidth() 
      {
         return $this->getImage()->getWidth();
      }

      function getHeight() 
      {
         return $this->getImage()->getHeight();
      }

      /**
      * resize a image
      * i guess it will work like this:
      *  it will always keep the image's proportion (w/h)
      *  if width >  resize_width
      resize by width
      else:
      resize by height
      */
      function resize($width,$height) 
      {
         $image=$this->getImage()->resize($width, $height);
         $this->setImage($image);

         return $image;
      }

      function resizeToHeight($height) 
      {
         $this->resize(null, $height);
      }

      function resizeToWidth($width) 
      {
         $this->resize($width,null);
      }

      function scale($scale) 
      {

      }

      function getImageType()
      {

      }


   }

   //do not use this , keep it just for backup 
   //after new class stable , will remove it
   class Frd_Image_OLD
   {
      var $image;
      var $image_type;
      protected $type=0;

      function load($filename) 
      {
         $image_info = getimagesize($filename);
         $this->image_type = $image_info[2];

         if( $this->image_type == IMAGETYPE_JPEG ) 
         {
            $this->image = imagecreatefromjpeg($filename);
            $this->type='jpg';
         }
         else if( $this->image_type == IMAGETYPE_GIF ) 
         {
            $this->image = imagecreatefromgif($filename);
            $this->type='gif';
         } 
         elseif( $this->image_type == IMAGETYPE_PNG ) 
         {
            $this->image = imagecreatefrompng($filename);
            $this->type='png';
         }
         elseif( $this->image_type == IMAGETYPE_BMP ) 
         {
            //$this->image = imagecreatefromwbmp($filename);
            $this->type='png';
         }
         else
         {
            $this->type='unknown';
            return false;    
         }

         return true;
      }

      function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) 
      {
         if( $image_type == IMAGETYPE_JPEG ) 
         {
            imagejpeg($this->image,$filename,$compression);
         }
         else if( $image_type == IMAGETYPE_GIF ) 
         {
            imagegif($this->image,$filename);
         }
         elseif( $image_type == IMAGETYPE_PNG ) 
         {
            imagepng($this->image,$filename);
         }
         if( $permissions != null) 
         {
            chmod($filename,$permissions);
         }
      }
      function output($image_type=IMAGETYPE_JPEG) 
      {
         if( $image_type == IMAGETYPE_JPEG ) 
         {
            imagejpeg($this->image);
         }
         else if( $image_type == IMAGETYPE_GIF ) 
         {
            imagegif($this->image);
         } 
         else if( $image_type == IMAGETYPE_PNG ) 
         {
            imagepng($this->image);
         }
      }
      function getWidth() 
      {
         return imagesx($this->image);
      }

      function getHeight() 
      {
         return imagesy($this->image);
      }

      function resizeToHeight($height) 
      {
         $ratio = $height / $this->getHeight();
         $width = $this->getWidth() * $ratio;
         $this->resize($width,$height);
      }

      function resizeToWidth($width) 
      {
         $ratio = $width / $this->getWidth();
         $height = $this->getheight() * $ratio;
         $this->resize($width,$height);
      }
      function scale($scale) 
      {
         $width = $this->getWidth() * $scale/100;
         $height = $this->getheight() * $scale/100;
         $this->resize($width,$height);
      }
      function resize($width,$height) 
      {
         $new_image = imagecreatetruecolor($width, $height);
         imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
         $this->image = $new_image;
      }

      function getImageType()
      {
         return $this->type; 
      }

   }
