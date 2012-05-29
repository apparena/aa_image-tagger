<?php
   /**
   * @version 0.0.1
   * @status close
   * @info do not use it, use Frd_Layout replace
   * for create html page
   */
   class Frd_Page  extends Frd_Template
   {
      protected $_jses=array();
      protected $_csses=array();
      protected $_footerjs=array();
      protected $_content=null;

      //config

      //only render content
      protected $only_content=false;

      function __construct()
      {
      
      }

      function renderJsFiles(){}
      function renderCssFiles(){}
      function renderFooterJsFiles(){}

      /**
      *  render a block 
      */
      /*
      function render($template_path=false)
      {
         if($template_path != false)
         {
            $this->setTemplate($template_path);
         }

         //render all tempates
         $html='';

         //render css
         $html.=$this->renderCss();

         //render html
         $html.=$this->renderHtml();

         //render js
         $html.=$this->renderJs();

         return $html;
      }
      */

   }
