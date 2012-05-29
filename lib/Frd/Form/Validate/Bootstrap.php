<?php
class Frd_Form_Validate_Bootstrap extends Frd_Block
{
   function __construct($id,$data)
   {
      $path=Frd::getFrdLibPath().'/templates/form/validate/bootstap.php';
      $this->setTemplate($path);

      //assign values
      $this->assign("id",$id);
      $this->assign("data",$data);
   }
}
