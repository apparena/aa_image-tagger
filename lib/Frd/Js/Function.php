<?php
   /**
   * js function 
   * Usage: 
   *  $function=new Frd_Js_Function();
   *  $function->setCode(....);
   *  $function->render();
   *
   */
   class Frd_Js_Function 
   {
      protected $nr="\n";  //new line 
      protected $indent="  ";  //indent 

      protected $func_name="  ";  //js function name 
      protected $code="  ";  //js code 

      function __construct($func_name='')
      {
         $this->func_name=$func_name;
      }


      function setCode($code)
      {
         $this->code=$code;
      }

      /**
      *  print js
      */
      function render()
      {
         if($this->func_name == false)
         {
            $script="function()";
         }
         else
         {
            $script="function ".$this->func_name."()";
         }

         $script.=$this->nr;
         $script.=$this->indent."{";
         $script.=$this->nr;

         //js function content
         $script.=$this->code;


         $script.=$this->nr;
         $script.=$this->indent."}";

         return $script;
      }
   }
