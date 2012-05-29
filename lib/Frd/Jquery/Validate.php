<?php
class Frd_Jquery_Validate
{
  protected $base='';
  protected $rules='';
  protected $messages='';

  function  __construct()
  {
    $this->base=<<<JS
      errorElement: "li",
      //错误处理
      errorPlacement: function(error, element) {
        element.parent("dd").find(".errors").html('');
        error.appendTo(element.parent("dd").find(".errors"));
      },

     //成功处理 
      success: function(label,element) {
        label.html();
        label.text('可以使用A');
        //.appendTo( element.parent("td").next("td") );
        label.removeClass('error');
        label.addClass('success');
      },

JS;
    $this->rules=array();
    $this->messages=array(); 
  }

  function addRule($element,$key,$value)
  {
    $this->rules[$element][$key]=$value; 
  }

  function addMessage($element,$key,$value)
  {
    $this->messages[$element][$key]=$value; 
  }

  function __toString()
  {
   $js= $this->base;
   $js.='rules:'."\n".json_encode($this->rules);
   $js.="\n,\n";
   $js.='messages:'."\n".json_encode($this->messages);

   return $js;
  }
}
