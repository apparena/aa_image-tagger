<?php
   /**
   * js object, for create js object and render as js code
   * Usage: 
   *  $object=new Frd_Js_Object();
   *  $object->id='test';
   *  $object->values=array('a'=>1);
   *
   *  echo "var options=".$object->render();
   * 
   *  Notice: now only support object, if other types in object, will cause error
   */
   class Frd_Js_Object extends Frd_Object
   {
      protected $nr="\n";  //new line 
      protected $indent="  ";  //indent 

      function __construct($data=array())
      {
         $this->setData($data);
      }

      /**
      * add js array
      */
      function addArray($key,$data=array())
      {
         $array=new Frd_Js_Array($data);
         $this->set($key,$array);
      }

      function renderObject($data,$deep=1)
      {
         $script="";
         $indent='';

         for($i=1;$i<$deep;++$i)
         {
            $indent.=$this->indent;
         }

         $count=count($data);
         $i=0;
         foreach($data as $k=>$v)
         {
            ++$i;

            if(is_array($v) == false)
            {
               if($v == false )
               {
                  $script.="$indent $k:''";
               }
               else if(is_numeric($v) )
               {
                  $script.="$indent $k:$v";
               }
               else if(is_string($v) )
               {
                  $script.="$indent $k:'$v'";
               }
               else
               {
                  $script.="$indent $k:".$v->render();
               }

               //not last one, do not need , for Supid IE!
               if($i != $count)
               {
                  $script.=",";
               }

               $script.=$this->nr;

            }
            else
            {
               $script.="$indent $k:";
               $script.="$indent {";
               $script.=$this->nr;
               $script.=$this->renderObject($v,$deep+1);

               //because befor } is a  NR, so it is special
               if($i != $count)
               {
                  $script.=$this->nr;
               }

               $script.="$indent }";
               if($i != $count)
               {
                  $script.=",";
               }

               $script.=$this->nr;
            }
         }

         return $script;
      }

      /**
      *  print js
      */
      function render()
      {
         if($this->getData()== false)
         {
            return "{}";
         }

         $data=$this->renderObject($this->getData());

         $script="{";
         $script.=$this->nr;
         $script.=$data;

         $script.="}";

         return $script;
      }
   }
