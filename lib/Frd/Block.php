<?php
   /**
   * @level  core
   *   this is a very good class, even now it is simple
   *
   * a block's feature :
   *   in template, $this->METHOD can access the block's method , very important feature! then you do not limit in  Zend_View
   *   one block , one template !
   *   can include other block
   *  
   *  example:
        $template=new Frd_Block();

        $params=array(
           'name'=>'fred',
        );

        $template->assign($params);

        //$template->test();

        //$template->addBlock('test/t.phtml');

        $header=new Frd_Block();
        $header->setTemplate("test/header.phtml");


        $template->addBlock('header',$header);
        //$template->addBlock($header);
        echo $template->render('test/t.phtml');

   * 
   */
   // how about block  add name for construct ??
   class Frd_Block extends Frd_Template
   {
      protected $_blocks=null;  //children blocks 
      protected $_templates=array(); //block's template file path
      protected $_disabled_templates=array(); //disabled template names

      function __construct($template_path=false)
      {
         //parent::__construct();

         $this->_blocks=new Frd_Object();

         if($template_path != false)
         {
            $this->setTemplate($_template_path);
         }
      }

      /**
      * add block 
      */
      function addBlock($name,$block=false)
      {
         if($block == false && is_object($name))
         {
            throw new Exception("please add block by  block name, block name for get block ");
         }
         $this->_blocks->$name=$block;
      }


      function getBlock($name)
      {
         return $this->_blocks->$name;
      }

      function renderBlock($name)
      {
         return $this->_blocks->$name->render();
      }

      /**
      * set block's template
      *
      *@param string $name  for identifier the tempalte, optional
      */
      function setTemplate($template_path)
      {
         //the main default template is main
         //$this->addTemplate($template_path,'main');
         $this->_templates['main']=$template_path;
      }

      /**
      *
      *@param string $name  for identifier the tempalte, optional
      */
      function addTemplate($name,$template_path)
      {
         if($name != false)
         {
            if(isset($this->_templates[$name]))
            {
               throw new Exception("the same name ($name)  template exists");
            }

            $this->_templates[$name]=$template_path;
         }
         else
         {
            $this->_templates[]=$template_path;
         }
      }

      /**
      *
      *@param string $name  for identifier the tempalte, optional
      */

      function disableTemplate($name)
      {
         if(isset($this->_templates[$name]))
         {
            $this->_disabled_templates[$name]='disable'; //the value have no meaning
         }
      }

      function enableTemplate($name)
      {
         if(isset($this->_templates[$name]))
         {
            if(isset($this->_disabled_templates[$name]))
            {
               unset($this->_disabled_templates[$name]);
            }
         }
      }

      /**
      *  render a block 
      */
      function render($template_path=false,$vars=array())
      {
         //render child template
         if(isset($this->_templates[$template_path]))
         {
            $html=$this->renderTemplate($template_path,$vars);
            return $html;
         }


         //set as main template
         if($template_path != false )
         // && !isset($this->_templates['main']) )
         {
            //   $this->setTemplate($template_path);
            $html=$this->renderTemplate($template_path,$vars);
         }
         else
         {
            //render all tempates
            if($this->getContent() != false)
            {
               $html=parent::render(false,$vars);
            }
            else
            {
               $html=$this->renderTemplate($this->_templates['main'],$vars);
            }
         }

         return $html;
      }

      /**
      * render other template
      * which also can use this block's variables and methods
      *
      * @param string alias  template alias name
      */
      function renderTemplate($template_path,$vars)
      {
         if(isset($this->_templates[$template_path]))
         {
            if(isset($this->_disabled_templates[$template_path]) )
            {
               $html=''; //disabled template
            }
            else
            {
               $html=parent::render($this->_templates[$template_path],$vars);
            }
         }
         else
         {
            $html=parent::render($template_path,$vars);
         }

         return $html;
      }
   }
