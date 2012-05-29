<?php
   class Frd_Form
   {
      protected $render='base';
      protected $elements=array();
      protected $form_attrs=array();
      protected $hidden_elements=array();

      protected $validater=null; //validater
      protected $validates=array(); //validate messages

      protected $form_data=array(); //form's data

      protected $template=false; //custom template to render form

      function __construct($render='bootstrap',$attrs=array())
      {
         $this->form_attrs=$attrs;

         $this->render=$render;

         $this->elements=array(
            'title'=>'',
            'fields'=>array(),
            'buttons'=>array(),
         );

         $this->validater=new Frd_Form_Validate();
      }

      function setAction($action)
      {
         $this->form_attrs['action']=$action;
      }

      function setAttr($name,$value)
      {
         $this->form_attrs[$name]=$value;
      }


      function setTitle($title)
      {
         $this->elements['title']=$title;
      }

      function addField($type,$name,$value,$label,$attrs=array(),$extra_info=array())
      {
         $html_form=new Frd_Html_Form();

         if($type  == 'select' && isset($attrs['options']))
         {
            $options=$attrs['options'];
            unset($attrs['options']);

            $field=$html_form->$type($name,$value,$options,$attrs);
         }
         else 
         {
            $field=$html_form->$type($name,$value,$attrs);
         }

         if(isset($extra_info['before']))
         {
            $before=$extra_info['before'];
         }
         else
         {
            $before=false;
         }

         if(isset($extra_info['after']))
         {
            $after=$extra_info['after'];
         }
         else
         {
            $after=false;
         }

         if(isset($extra_info['last']))
         {
            $last=$extra_info['last'];
         }
         else
         {
            $last=false;
         }

         if(isset($extra_info['info']))
         {
            $info=$extra_info['info'];
         }
         else
         {
            $info=false;
         }


         $this->elements['fields'][$name]=array(
            'label'=>$label,
            'field'=>$field,
            'before'=>$before,
            'after'=>$after,
            'last'=>$last,
            'info'=>$info,
         );
      }

      function addHiddenField($name,$value)
      {
         $form=new Frd_Html_Form();
         $hidden=$form->hidden($name,$value);

         $this->hidden_elements[$name]=$hidden->render();
      }

      function addSubmitButton($value)
      {
         $this->addButton($value,array('type'=>'submit'));
      }

      function addButton($value,$attrs=array())
      {
         $attrs['value']=$value;
         if(!isset($attrs['type']))
         {
            $attrs['type']="button";
         }

         $this->elements['buttons'][]=$attrs;
      }

      function setTemplate($template)
      {
         $this->template=$template;
      }

      function render()
      {
         $classname="Frd_Form_".ucfirst($this->render);

         $data=array(
            'form_attrs'=>$this->form_attrs,
            'elements'=>$this->elements,
            'hidden_elements'=>$this->hidden_elements,
            'validates'=>$this->validates,
         );



         $render=new $classname($data);
         if($this->template != false)
         {
            $render->setTemplate($this->template);
         }
         return $render->render();
      }


      /** validate methods **/
      function addValidate($name,$validate)
      {
         $this->validater->add($name,$validate);
      }

      function valid($data)
      {
         $this->setFormData($data);

         if( $this->validater->valid($this->form_data) == false)
         {
            $this->validates=$this->validater->getValidateMessages();
            return false;
         }
         else
         {
            return true;
         }
      }

      function renderJs()
      {
         $classname="Frd_Form_Validate_".ucfirst($this->render);

         if(!isset($this->form_attrs['id']))
         {
            trigger_error("please set form id for js validate.");
         }

         $form_id=$this->form_attrs['id'];

         $js_validate=new $classname($form_id,$this->validater->getData());

         $js=$js_validate->render();

         return $js;
      }

      function populate($data=array())
      {
         if($data != false)
         {
            $this->setFormData($data);
         }

         //populate
         foreach($this->form_data as $name=>$v)
         {
            if(isset($this->elements['fields'][$name]))
            {
               $this->elements['fields'][$name]['field']->setValue($v);
            }

         }
      }

      function setFormData($data)
      {
         foreach($this->elements['fields'] as $name=>$v)
         {
            if(isset($data[$name]))
            {
               if(is_string($data[$name]))
               {
                  $this->form_data[$name]=trim($data[$name]);
               }
               else
               {
                  $this->form_data[$name]=$data[$name];
               }
            }
            /*
            else
            {
               $this->form_data[$name]='';
            }
            */
         }

         //hidden fields
         foreach($this->hidden_elements as $name=>$v)
         {
            if(isset($data[$name]))
            {
               if(is_string($data[$name]))
               {
                  $this->form_data[$name]=trim($data[$name]);
               }
               else
               {
                  $this->form_data[$name]=$data[$name];
               }
            }
            /*
            else
            {
               $this->form_data[$name]='';
            }
            */
         }
      }

      function getFormData()
      {
         return $this->form_data;
      }


      function getValue($name,$default=false)
      {
         if(isset($this->form_data[$name]))
         {
            return $this->form_data[$name];
         }
         else
         {
            return $default;
         }
      }

      function setValue($name,$value)
      {
         if(isset($this->elements['fields'][$name]))
         {
            $this->elements['fields'][$name]['field']->setValue($value);
         }
      }
   }
