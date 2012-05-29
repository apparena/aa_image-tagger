<?php
   /**
   *  subclass of Zend_View , support  {VAR} , the same as <?php echo $this->VAR ?>
   *  and support  render without set script path first
   *  and it's  subclass can access it's method !
   *  because it has already assign  $this  to  this for template 
   *  so  $this->METHOD  can call the template's method
   *  featura:
   *    support  {VAR} 
   *    support  {VAR.Attr.attr}  the VAR can be object or array 
   * 
   * @version 0.0.1
   * @status  try
   */
   class Frd_Template extends Zend_View
   {
      //has set script path 
      protected $_isset_script_path=false;

      protected $_content=''; //html content

      function __construct()
      {
         parent::__construct();
         $this->_isset_script_path=false;
      }

      /**
      * set script path
      */
      function setScriptPath($path)
      {
         $this->_isset_script_path=true;
         parent::setScriptPath($path);
      }

      /**
      * set html content , then it will not render template
      */
      function setContent($content)
      {
         $this->_content=$content;
      }

      function getContent()
      {
         return $this->_content;
      }

      /**
      * render,  if want add advance tag  {if} {endif} ,like this, 
      * should rewrite _run  method, 
      * compile the template, saved as php file, 
      * then include 
      *
      */
      function render($file,$vars=array())
      {
         //if not set script path, 
         //set the file's dir path ad script path

         if($this->_isset_script_path == false)
         {
            $path=realpath(dirname($file));

            //$this->setScriptPath($path);
            parent::setScriptPath($path);

            $file=basename($file);
         }

         if($vars != false)
         {
            //use extra vars
            $old_vars=parent::getVars();
            $vars=array_merge($old_vars,$vars);
            parent::clearVars();
            parent::assign($vars);

            //get content first, render template second 
            $content=$this->getContent();
            if($content == false)
            {
               $content=parent::render($file);
            }

            $content=$this->handle($content);

            //set old vars
            parent::clearVars();
            parent::assign($old_vars);
         }
         else
         {
            //get content first, render template second 
            $content=$this->getContent();
            if($content == false)
            {
               $content=parent::render($file);
            }

            $content=$this->handle($content);
         }

         return $content;
      }

      /**
      * this is the last step for render template
      * all php tag in template are executed , 
      * now it got result content
      * this can be rewrited, if you want do more thing
      * current , it is only replace {variable}  to real variable
      *
      */
      protected function handle($content)
      {
         //variable format
         $var_format='{%s}';

         //$vars=$this->getVars();
         $vars=parent::getVars();

         $pattern="{([\w\.]+)}"; //match  a-zA-Z _ .
         $matches=Frd_Regexp::searchAll($content,$pattern);
         $matches=$matches[1];

         foreach($matches as $match)
         {
            if($match == false)
            {
               containue;
            }

            $values=explode(".",$match);

            if(count($values) == 1 && isset($vars[$match]))
            {
               //for variable {variable}
               $search="{".$match."}";
               $replace=$vars[$match];
               $content=str_replace($search,$replace,$content);
            }
            else
            {
               //for variable {variable.attr.attr}
               $value=$values[0];
               //unset($values[0]);

               if(isset($vars[$value]))
               {
                  $curvalue=$vars[$value]; //current value

                  $amount=count($values);
                  for($i=1;$i < $amount ; $i++)
                  {
                     $value=$values[$i];

                     if(is_array($curvalue) && isset($curvalue[$value]))
                     {
                        $curvalue=$curvalue[$value];
                     }
                     else if(is_object($curvalue) && isset($curvalue->$value))
                     {
                        $curvalue=$curvalue->$value;
                     }
                     else
                     {
                        break;
                     }

                     //got last value
                     if($i == $amount-1)
                     {
                        $search="{".$match."}";
                        $replace=$curvalue;
                        if(is_string($replace) || is_numeric($replace) || is_bool($replace) )
                        {
                           $content=str_replace($search,$replace,$content);
                        }
                     }
                  }
               }
            
            }


         }

         return $content;
         //print_r($matches);
         exit();
         /*
         //create replace array
         $search=array();
         $replace=array();
         foreach($vars as $k=>$v)
         {
            if(is_string($v) || is_numeric($v) || is_bool($v) )
            {
               $search[]=sprintf($var_format,$k);
               $replace[]=$v;
            }
         }

         //replace now
         $content=str_replace($search,$replace,$content);
         */

         return $content;
      }

      /**
       *  set params for template
       *  for subclass
       * 
       */
      function setParams($params)
      {
      }
   }
