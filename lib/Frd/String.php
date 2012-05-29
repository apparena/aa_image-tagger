<?php
   /**
   * php string object, for operate string,like to js variable,
   * to html variable
   *
   * @created_at  Tue Jan 24 15:13:30 CST 2012
   */
   /*==================Changelog==============================
   Tue Jan 24 15:13:30 CST 2012:  created 


   ==========================================================*/

   class Frd_String
   {
      /**
      * php string to js variable,with json encode, it became very easy,
      * it support multilines and html tag, \n will still be  \n , safe for used in textarea
      */
      public static function toJsVar($str)
      {
         return json_encode($str); 
      }

      //截取一个字符串，某个字符前面的内容
      function cutBefore($str,$char)
      {
         $index=strpos($str,$char);
         if($index !== false)
         $str=substr($str,0,$index);

         return $str;
      }

      /**
      * some useful functions 
      */
      function strRevert($str)
      {
         $len=strlen($str);
         for ($i=0;$i<$len/2;$i++)
         {
            $temp=$str[$i];
            $str[$i]=$str[$len-$i-1];
            $str[$len-$i-1]=$temp;
         }
         return $str;
      }
   }

