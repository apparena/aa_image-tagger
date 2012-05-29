<?php
   /**
   * this is for load js, and maybe can also for css, 
   * because js and css are both not have it's own load method
   * this is for load js code which will render in the page, not <script src=.... ></script>
   because most page need some special js code, these code will only for this page, 
   if save them all in js file,that will need to load a big js file for all pages,
   so the better way seems to load in the page ,
   and why just write them in the page ?
   there has several reasons
   1, html page become short , and clear
   2, just use move, then can move whole js code to other place
   3, with load, it can load several script files,and it's easy to remove and add new script


   Usage:
     $loader=new Frd_Loader_Js(dirname(__FILE__).'/js/app');
     $loader->load("test") ;  //load js: js/app/test.js
     $loader->load("test");   //will do nothing, because it has loaded
     $loader->load("config"); //load js: js/app/config.js

     $loader->loadFile(dirname(__FILE__).'/main.js'); //load absolute js file

     echo $loader->render();  //output js file content


   */
   class Frd_Loader_Js
   {
      protected $folder=false; //js folder 
      protected $files=array(); //loaded js and php file

      function __construct($folder=false)
      {
         if($folder == false)
         {
            trigger_error("missing parameter: folder ( you should defined js folder which contain js files");
         }

         $this->folder=rtrim($folder,"/").'/';
      }

      function load($name,$vars=array())
      {
         $type="js";
         //a/b/c =>  a_b_c
         $key_name=str_replace("/","_",$name); //as key

         if(strrpos($name,".php") !== false)
         {
            $file=$this->folder.$name;
            $type="php";
         }
         else
         {
            $file=$this->folder.$name.'.js';
         }

         if(isset($this->files[$key_name]) == false)
         {
            $this->files[$key_name]=array(
               'type'=>$type,
               'file'=>$file,
               'vars'=>$vars,
            );
         }
      }

      /*
      function loadFile($path)
      {
         $key_name=str_replace("/","_",$path); //as key
         if(Frd_File::exists($path) == false)
         {
            trigger_error("invalid parameter: path not exists: $path");
         
         }
         $this->files[$key_name]=$path;
      }
      */

      function render()
      {
         $js='';
         foreach($this->files as $name=>$v)
         {
            $type=$v['type'];
            $file=$v['file'];
            $vars=$v['vars'];


            $content="/**  $name  **/";
            $content.="\n";

            if($type == 'js')
            {
               $content=Frd_File::read($file);
            }
            else if($type == 'php')
            {
               $template=new Frd_Template();
               $content.=$template->render($file,$vars);
            }


            $content.="\n";

            $js.=$content;
         }

         return $js;
      }
   }
