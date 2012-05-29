<?php
   /** 
   *
   * data methods 
   */
   class Frd_Date
   {
      /**
      *  add day form the date
      */
      function dateAdd($date,$days=1,$format="Y-m-d")
      {
         $time=strtotime($date);
         $time+=$days*86400;

         $new_date=date($format,$time);

         return $new_date;
      }

      function dateReduce($date,$days=1,$format="Y-m-d")
      {
         $time=strtotime($date);
         $time-=$days*86400;

         $new_date=date($format,$time);

         return $new_date;
      }

      /**
      * compare date, 
      * if date1 > date2 ,return >, otherwise return < or =
      */
      function dateCompare($date1,$compare_type,$date2)
      {
         $time1=strtotime($date1);
         $time2=strtotime($date2);

         $compare_type=trim($compare_type);

         $types=array( '>','=','<','>=','<=' );

         if(in_array($compare_type,$types) == false)
         {
            throw new Exception("unknown compare type: $compare_tpe");
         }

         if($compare_type == "=")
         {
            $compare_type="==";
         }

         $code="return $time1 $compare_type $time2;";

         return eval($code);
      }


      /**
      * get yesterday's datetime
      */
      function yesterday($format="Y-m-d")
      {
         $today=today($format);
         return dateReduce($today);
      }

      /**
      * get date range, include current day
      *  if  day_number is 2, it will include "today,yesterday"
      * 
      * 
      * @param string  $type  before will calculate date before today, after for days after today
      */
      function dateRange($date,$day_number,$type="before")
      {
         if($day_number < 1)
         {
            throw new Exception("days can not < 1"); 
         }

         $days=array();

         $start=today("Y-m-d");

         $days[]=$start;

         for($i=1;$i<$day_number;++$i)
         {
            if($type="before")
            {
               $days[]=dateReduce($start,$i);
            }
            else 
            {
               $days[]=dateAdd($start,$i);
            }
         }

         return $days;
      }
      /**
      * return last 7 days as array
      */
      function last7Days()
      {
         $start=today("Y-m-d");
         $days=dateRange($start,7);

         return $days;
      }

      /**
      * change date format
      */
      function dateToFormat($date,$format="Y-m-d")
      {
         $date=date($format,strtotime($date)); 

         return $date;
      }

      function currentMonth($format="Y-m-d")
      {
         $format=str_replace("d","01",$format);
         return date($format);
      }

      function monthFirstDay($date,$format="Y-m-d")
      {
         $format=str_replace("d","01",$format);


         return date($format,strtotime($date));
      }

      function monthAdd($date,$month=1,$format="Y-m-d")
      {
         $time=strtotime($date." + $month month");

         $new_date=date($format,$time);

         return $new_date;

      }

      function monthReduce($date,$month=1,$format="Y-m-d")
      {
         $time=strtotime($date." - $month month");

         $new_date=date($format,$time);

         return $new_date;
      }

      function previousMonth($date=false,$format="Y-m-d")
      {
         if($date == false)
         {
            $date=currentMonth($format);
         }

         return monthReduce($date,1,$format);
      }

      function nextMonth($date=false,$format="Y-m-d")
      {
         if($date == false)
         {
            $date=currentMonth($format);
         }

         return monthAdd($date,1,$format);
      }
   }
