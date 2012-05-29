<?php
   /**
   * regexp helper ,for easy use regexp 
   */
   class Frd_Regexp
   {
      public static function replace($string,$pattern,$replace,$limit=-1)
      {
         $pattern="/".trim($pattern,"/")."/";

         $string=preg_replace($pattern,$replace,$string,$limit);

         return $string;
      }

      public static function search($string,$pattern)
      {
         $pattern="/".trim($pattern,"/")."/";
         $match='';


         if(preg_match($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         return $match;
      }

      public static function searchAll($string,$pattern)
      {
         $match=array();
         $pattern="/".trim($pattern,"/")."/";

         if(preg_match_all($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         return $match;
      }

      public static function isMatch($string,$pattern)
      {
         $match='';
         //$pattern="/^$pattern$/";
         $pattern="/$pattern/";

         if(preg_match($pattern,$string,$match) === false)
         {
            throw new Exception("preg_match falied ");
         }

         if($match == false)
         {
            return false; 
         }
         else
         {
            return true; 
         }
      }
   }
