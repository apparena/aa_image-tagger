<?php
   /**
   * widget:
   *  widget is an object, which have it's own html,css,javascript,php code
   *  and the css,html ,js can output respectively ( this will for layout or block to handle in the future
   *   widget is for extends, so the subclass can add some custom methods 
   1, Frd_Form should support widget
   2, support auto methods to bind with it's object
     $widget->addElememnt('input',$input);

     then:
     $widget->setValue('input','test');
      
   */
   class Frd_Widget extends Frd_Block
   {
      /**

      */
   }
