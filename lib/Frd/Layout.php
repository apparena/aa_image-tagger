<?php
   /**
   * @version 0.0.1
   * @status try
   * for maintain page layout
   */
   class Frd_Layout  extends Frd_Block
   {
      protected $_content_block=null; //block

      function setContentBlock($block)
      {
         $this->_content_block=$block;
      }

      /**
      *
      * @param string $name other template name ,if has use addTemplate added
      */
      function render($name=false,$vars=array())
      {
         //only for main layout render content
         if($name == false)
         {
            if($this->_content_block != false)
            {
               $this->content=$this->_content_block->render();
            }
            else
            {
               $this->content='';
            }
         }

         return parent::render($name,$vars);
      }
   }
