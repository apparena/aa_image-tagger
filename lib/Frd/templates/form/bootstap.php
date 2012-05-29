<?php
   $attrs=array();
   foreach($this->form_attrs as $k=>$v)
   {
      $attrs[]="$k=\"$v\"";
   }

   $attrs=implode(" ",$attrs);

?>

<form class="form-horizontal" <?php echo $attrs; ?>>
     <!-- hidden fields -->
     <?php foreach($this->hidden_fields as $field): ?>
          <?php echo $field; ?>

    <?php endforeach; ?>

     <!-- hidden fields end-->

  <fieldset>

     <!-- title -->
     <?php if($this->title != false): ?>
     <legend>{title}</legend>
     <?php endif; ?>

     <!-- fields -->
     <?php foreach($this->fields as $name=>$field): ?>

     <?php if(isset($this->validates[$name])): ?>
        <div class="control-group error">
     <?php else: ?>
        <div class="control-group">
     <?php endif; ?>

       <label for="input01" class="control-label">
          <?php echo $field['label']; ?>
       </label>
      <div class="controls">
         <!-- before field -->
          <?php echo $field['before']; ?>

         <?php echo $field['field']->render(); ?>

          <!-- validate erro messages -->
        <?php if(isset($this->validates[$name])): ?>
        <?php foreach($this->validates[$name] as $message): ?>
          <p class="help-block">
             <?php echo $message; ?>
          </p>
        <?php endforeach; ?>
        <?php endif; ?>

      <!-- after field -->
      <?php echo $field['after']; ?>

          <p class="help-block">
             <?php echo $field['info']; ?>
          </p>

      <!-- last field -->
      <?php echo $field['last']; ?>
      </div>
    </div>
    <?php endforeach; ?>

     <!-- buttons -->
    <div class="form-actions">
       <?php 
          foreach($this->buttons as $button)
          {
             $value=$button['value'];
             unset($button['value']);

             if($button['type'] == 'submit')
             {
                $button['class']="btn btn-primary";
             }
             else
             {
                $button['class']="btn";
             }

             //generate attr string
             $attrs=array();
             foreach($button as $k=>$v)
             {
                $attrs[]="$k=\"$v\"";
             }

             $attrs=implode(" ",$attrs);

             //create html
             echo '<button '.$attrs.'>';
             echo $value;
             echo '</button>';

          }
       ?>
    </div>
  </fieldset>
</form>
