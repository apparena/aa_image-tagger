<?php
   class Frd_Form_Validate extends Frd_Object
   {
      protected $data=array();
      protected $messages=array(); //valid failed messages

      protected $invalid_messages=array(
         'required'=> "This field is required.",
         'remote'=> "Please fix this field.",
         'email'=> "Please enter a valid email address.",
         'url'=> "Please enter a valid URL.",
         'date'=> "Please enter a valid date.",
         'dateISO'=> "Please enter a valid date (ISO).",
         'number'=> "Please enter a valid number.",
         'digits'=> "Please enter only digits.",
         'creditcard'=> "Please enter a valid credit card number.",
         'equalTo'=> "Please enter the same value again.",
         'accept'=> "Please enter a value with a valid extension.",
         'maxlength'=> "Please enter no more than {0} characters.",
         'minlength'=> "Please enter at least {0} characters.",
         'rangelength'=> "Please enter a value between {0} and {1} characters long.",
         'range'=> "Please enter a value between {0} and {1}.",
         'max'=> "Please enter a value less than or equal to {0}.",
         'min'=> "Please enter a value greater than or equal to {0}.",
      );

      function __construct($data=array())
      {
      
      }

      function add($name,$validate)
      {
         $this->$name=$validate;
      }

      function valid($data)
      {
         $valid=true;

         foreach($this->getData() as $k=>$v)
         {
            foreach($v as $type=>$value)
            {
               $method="valid".ucfirst(strtolower($type));

               //check if valid method exists
               if(method_exists($this,$method) == false)
               {
                  trigger_error("class method not exists: $method");
               }

               //for all validate, the validate data should exists
               if(!isset($data[$k]))
               {
                  $this->addValidateMessage($k,"required");
                  $valid=false;
                  continue;
               }

               if( $this->$method($value,$data[$k]) == false)
               {
                  $this->addValidateMessage($k,$type,$value);
                  $valid=false;
               }
            
            }
         }
      
         return $valid;
      }

      function addValidateMessage($name,$type,$value=false)
      {
         if(!isset($this->messages[$name]))
         {
            $this->messages[$name]=array();
         }

         //TODO
         $msg=$this->invalid_messages[$type];

         $this->messages[$name][]=$msg;
      }

      function getValidateMessages()
      {
         return $this->messages;
      }

      /* valid methods */
      function validRequired($config,$value)
      {
         if(trim($value) == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }

      function validEmail($config,$value)
      {
         if(filter_var($value, FILTER_VALIDATE_EMAIL) == false)
         {
            return false;
         }
         else
         {
            return true;
         }
      }


      /* valid error methods */

      /*
      function renderJs()
      {
         return json_encode($this->getData());
      }
      */
   
   }

