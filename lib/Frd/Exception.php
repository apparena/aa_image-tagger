<?php
   class Frd_Exception 
   {
      //protected $messages=array();

      /**
      * display exception, the default exception display is not good
      */
      public function handle($exception)
      {
         $table=new Frd_Html_Table();


         $table->col("Message:");
         $table->col(nl2br($exception->getMessage()));
         $table->nextRow();

         $table->col("Code:");
         $table->col($exception->getCode());
         $table->nextRow();

         $table->col("File:");
         $table->col($exception->getFile());
         $table->nextRow();

         $table->col("Line:");
         $table->col($exception->getLine());
         $table->nextRow();

         $table->col("Trace:");
         $table->col($exception->getTraceAsString());
         $table->nextRow();


         $msg=$table->render();

         if(Frd::getSetting("exception_output") == true)
         {
            echo $msg;
         }
         //$this->messages[]=$msg;
      }

      function setExceptionHandler()
      {
         /**
         * function is  global  ,even it defined in class's method
         * so use frd as prefix , it should not called in other place
         */
         function frd_handle_exception($exception)
         {
            $handler=Frd::getException();
            $handler->handle($exception);
         }

         set_exception_handler("frd_handle_exception");
         //restore_error_handler();
      }

      /*
      function outputExceptions()
      {
         foreach($this->messages as $message)
         {
            echo $message;
         }
      }

      function hasException()
      {
         if($this->messages == false)
         {
            return false;

         }
         else
         {
            return true;
         }
      }
      */

   }
