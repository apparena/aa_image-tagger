<?php
class Frd_Form_Bootstrap extends Frd_Block
{
   function __construct($data)
   {
      $form_attrs=$data['form_attrs'];
      $elements=$data['elements'];
      $hidden_elements=$data['hidden_elements'];
      $validates=$data['validates'];

      $path=Frd::getFrdLibPath().'/templates/form/bootstap.php';
      $this->setTemplate($path);

      //assign values
      $this->assign("form_attrs",$form_attrs);
      $this->assign("hidden_fields",$hidden_elements);
      $this->assign("validates",$validates);

      foreach($elements as $key=>$value)
      {
         $this->assign($key,$value);
      }

   }
}
