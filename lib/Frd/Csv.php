<?php 
   /**
   * csv class ,for create csv file and import csv 
   * should: 
      create new csv file
      import csv content as array
      modify csv content
      error handle
     problems:
       
   * @version 0.1
   *  
   */
   class Frd_Csv
   {
      //data
      protected $header=array(); //csv header
      protected $rows=array(); //csv rows

      //content setting
      protected $col_split=',';
      protected $row_split="\n";
      protected $quote='"';

      //content
      protected $content='';

      //set col split
      function setColSplit($split=",")
      {
         if($split != false)
         {
            $this->col_split=$split; 
         }
      }

      /**
      * add csv header
      */
      function setHeader($row)
      {
         if(!is_array($row))
         {
            $row=func_get_args();
         }

         $this->header=array_merge($this->header,$args);
      }

      function getHeader()
      {
         return $this->header;
      }

      /**
      * out csv header
      */
      function outputHttpHeader($filename="data.csv")
      {
         $filename=str_replace(".csv","",$filename);
         $filename.=".csv";

         #header("Content-Type: application/csv");
         #header ("Content-disposition: attachment; filename=$filename") ;

         header('Content-type: text/csv; charset=utf-8');
         header("Cache-Control: no-store, no-cache");  
         header('Content-Disposition: attachment; filename="'.$filename.'"');

         //echo "\xEF\xBB\xBF"; // UTF-8 BOM
      }

      function addRows($rows)
      {
         foreach($rows as $row)
         $this->addRow($row);
      }

      /**
      * add csv content
      */
      function addRow($row)
      {
         if(!is_array($row))
         {
            $row=func_get_args();
         }

         $this->rows[]=$row;

         /*
         foreach($row as $k=>$v)
         {
            $str=htmlentities($v,ENT_QUOTES,"utf-8"); 

            //except special character
            $chars=array(
               '&auml;',
               '&ouml;',
               '&uuml;',
               '&Auml;',
               '&Ouml;',
               '&Uuml;',
               '&szlig;',
            );
            $replaces=array(
               'ä',
               'ö',
               'ü',
               'ß',
               'Ä',
               'Ö',
               'Ü',
               'ß',
            );

            //$row[$k]=str_replace($chars,$replaces,$str);
         }

         $str=$this->quote.implode($this->quote.$this->col_split.$this->quote,$row).$this->quote;


         $this->data.=$str.$this->row_split;
         */
      }

      function getRows()
      {
         return $this->rows;
      }

      /**
      * save csv data to file
      */
      function save($path)
      {
         $this->toContent();

         file_put_contents($path,$this->data); 
      }

      /**
      * to csv content which can be saved  in file
      */
      function toContent()
      {
         if($this->header != false)
         {
            $rows=array_merge(array($this->header),$this->rows);
         }
         else
         {
            $rows= $this->rows;
         }

         foreach($rows as $row)
         {
            foreach($row as $k=>$v)
            {
               $ros[$k]=htmlentities($v,ENT_QUOTES,"utf-8"); 

            }

            $str=$this->quote.implode($this->quote.$this->col_split.$this->quote,$row).$this->quote;

            $this->content.=$str.$this->row_split;
         }


         return $this->content;
      }

      /**
      * print csv data
      */
      function __toString()
      {
         $this->toContent();
         return $this->content; 
      }

      function output()
      {
         $this->toContent();
         echo $this->content; 
      }


      /**
      * import 
      *
      * @param boolean  $has_header, use the first line as header or not
      */
      function import($path,$has_header=true)
      {
         $rows=Frd_File::fileToArray($path);
         if(count($rows) == false)
         {
            return false;
         }

         //handle header
         if($has_header == true)
         {
            $this->header=$this->parseString($rows[0]);
            unset($rows[0]);
         }

         //handle rows
         foreach($rows as $row)
         {
            $this->addRow($this->parseString($row));
         }

      }

      /**
      * import a line from string
      */
      function parseString($string)
      {
         $string=trim($string,$this->quote); //remove quote at head and tail

         $split=$this->quote.$this->col_split.$this->quote; //create split

         $row=explode($split,$string);

         return $row;
      }


      /**************  modify  *******************/
      /**
      * to assoc array , must have set header
      * format: 
         array(
            array(
               header1=>value,
               header2=>value2
               ...
      */
      function toAssocArray()
      {
         if($this->header == false)
         {
            trigger_error("method failed:"."csv header not exists");
         }

         $data=array();

         foreach($this->rows as $row)
         {
            $item=array( );
            foreach($row as $k=>$v)
            {
               $item[$this->header[$k]]=$v;
            }

            $data[]=$item;
         }

         return $data;
      }

      /*
       TODO:
       insert row
       edit row
       delete row
       modify header
      */
   }
