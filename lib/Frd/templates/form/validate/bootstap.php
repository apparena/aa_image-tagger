$("#{id}").validate({
      highlight: function(element, errorClass, validClass) {
            //alert('highlight');
            if (element.type === 'radio') {

                  //this.findByName(element.name).addClass(errorClass).removeClass(validClass);
                  this.findByName(element.name).parent().parent('.control-group').addClass(errorClass).removeClass(validClass);
               } else {

                  $(element).parent().parent(".control-group").addClass(errorClass).removeClass(validClass);
            }
      },
      unhighlight: function(element, errorClass, validClass) {
            if (element.type === 'radio') {
                  this.findByName(element.name).parent().parent('.control-group').removeClass(errorClass).addClass(validClass);
               } else {
                  $(element).parent().parent('.control-group').removeClass(errorClass).addClass(validClass);
            }
      },
      errorElement: "span",
      errorsFor: function(element) {
            alert('error for');
            var name = this.idOrName(element);
            return this.errors().filter(function() {
                  return $(this).attr('for') == name;
            });
      },
      errors: function() {
            alert('errors');
            return $( this.settings.errorElement + "." + "help-inline", this.errorContext );
      },
      showLabel: function(element, message) {
            alert('showlabe');

            var label = this.errorsFor( element );
            if ( label.length ) {
                  // refresh error/success class
                  label.removeClass( this.settings.validClass ).addClass( this.settings.errorClass );

                  // check if we have a generated label, replace the message then
                  label.attr("generated") && label.html(message);
               } else {
                  // create label
                  label = $("<" + this.settings.errorElement + "/>")
                  .attr({"for":  this.idOrName(element), generated: true})
                  //.addClass(this.settings.errorClass)
                  .addClass('help-inline')
                  .html(message || "");
                  if ( this.settings.wrapper ) {
                        // make sure the element is visible, even in IE
                        // actually showing the wrapped element is handled elsewhere
                        label = label.hide().show().wrap("<" + this.settings.wrapper + "/>").parent();
                  }
                  if ( !this.labelContainer.append(label).length )
                  this.settings.errorPlacement
               ? this.settings.errorPlacement(label, $(element) )
               : label.insertAfter(element);
         }
         if ( !message && this.settings.success ) {
               label.text("");
               typeof this.settings.success == "string"
            ? label.addClass( this.settings.success )
            : this.settings.success( label );
      }
      this.toShow = this.toShow.add(label);
},

   rules: <?php echo json_encode($this->data); ?>

});


