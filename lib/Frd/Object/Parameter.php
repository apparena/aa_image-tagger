<?php
   /**
   * this is an object for HTTP REQUEST PARAMETER
   * with this object, can defined necessary paramter, defautl paramter,
   *  check parameter if valid and try to filter it to get valid parameter
   *  information:
   rules : 
     to check if parameter valid with rules
     this should only keep simple rules, complex rules is for Form
     because if parameter is valid, if that's user's fault,
     the form can render again to show messages, 
     but for request , it does not how to do
     request's check is for developer's mistake 

  default:
     default values

  filter: 
     filter rules can handle data
     filter seems also form's job, now just keep it
      
        
   */
   /*
   TEST:
   $object=new ParameterObject();
   $object->setRule("app_id","required",'int');
   $object->setRule("is_default",'required','boolean');
   $object->setDefault("is_default",false);

   $object->fromQuery($_POST);
   $object->toQuery()

   $object->isvalid()
   */
   class Frd_ParameterObject
   {
      /**
      array(
         'required'=>true,
         'int'=>array(
            'not'=>0,
         )
         'string'=>array(
            'min_length'=>3,
            'max_length'=>20,
            'not'=>'abc',
         )
         */
         protected $rules=null;
         protected $default=null;
         //protected $filter=null;

         function __construct()
         {
            $this->rules=new Frd_Object();
            $this->default=new Frd_Object();
            //$this->filter=new Frd_Object();
         }

         function setRule($name,$type,$extra_parameter=true)
         {
            if( $this->rules->has($name) == false)
            {
               $this->rules->$name=array();
            }

            $this->rules->$name[$type]=$extra_parameter;
         }

         function get($name)
         {
            if($this->has($name) == false)
            {
               if( $this->has($name ) &&  $this->hasDefault($name)  )
               {
                  return $this->getDefault($name);
               }

               return null;
            }

            return parent::get($name);
         }

         function setRequired($name)
         {
            $this->setRule($name,"required");
         }

         function isRequired($name)
         {
            if($this->rules->has($name) == false)
            {
               return false;
            }

            if($this->rules->$name->has("required") )
            {
               return true;
            }

            return false;
         }

         function hasDefault($name)
         {
            if($this->default->has($name) ==  false)
            {
               return false;
            }
            else
            {
               return true;
            }

         }

         function getDefault()
         {
            if($this->default->has($name) ==  false)
            {
               return null;
            }

            return $this->default->$name;
         }

         function setDefault($name,$default)
         {
            $this->default->$name=$default;
         }

         function toQuery()
         {
            $query=array();
            $data=$this->getData();
            foreach($data as $k=>$v)
            {
               $query[]="$k=$v";
            }

            $query=implode("&",$query);

            $query=urlencode($query);

            return $query;
         }

         function fromQuery($query)
         {
            $query=urldecode($query);
            //substr after ?

            //
            $query=explode("&",$query);
            foreach($query as $v)
            {
               list($key,$value)=explode("=",$v);
               $this->$key=$value;
            }

            return $this->getData();
         }

         function isvalid($data)
         {
            /*
            array(
               'required'=>true,
               'int'=>array(
                  'not'=>0,
               )
               'string'=>array(
                  'min_length'=>3,
                  'max_length'=>20,
                  'not'=>'abc',
               )
               */
               foreach($this->rules as $name=>$rule)
               {
                  if(isset($

               }

            }

      }
