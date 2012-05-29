<?php
   /**
   *
   * @status  not yet
   */

   class Frd_Grid
   {
      protected $paginator=null;
      protected $columns=array();


      //format:e.g. array('Id','Title','Name','Action') 
      protected $titles=array();
      protected $operates=array(); //operates html

      protected $caption='';

      protected $debug=false;

      protected $primary=false;

      protected $grid_id='';


      protected $sortname=array(
         ''=>'---choose--', 
      );

      protected $sortorder=array(
         'desc'=>'Desc', 
         'asc'=>'Asc', 
      );

      protected $cur_sortname='';
      protected $cur_sortorder='';

      //page
      protected $perpages=array(
         5=>5,
         10=>10,
         15=>15,
         20=>20, 
      );

      protected $default=array(
         'page'=>1,
         'perpage'=>10,
         'sortname'=>'',
         'sortorder'=>'',
      );

      function  setRequest($data)
      {
         $this->request= array_merge($this->default,$data);
         $this->paginator->setRequest($this->request);
      }
      //filter
      protected $filter=array();
      function addSort($key,$value)
      {
         $this->sortname[$key]=$value;
      }


      function setPerpages($arr)
      {
         $this->perpages=$arr; 
      }

      function createTemplate()
      {
         $html='<div id="'.$this->grid_id.'">';
            $html.='</div>';
         $html.='<div id="'.$this->grid_id.'_add">';
            $html.='</div>';
         $html.='<div id="'.$this->grid_id.'_edit">';
            $html.='</div>';

         return $html;
      }

      function createJs()
      {
         $js='<script type="text/javascript">';
            $js.="\n";
            $js.='var grid1=cloneAll(FrdGrid);';
            $js.="\n";
            $js.='grid1.setSelector("#'.$this->grid_id.'");';
            $js.="\n";
            $js.='grid1.setUrl("/index.php/plan/getdata");';
            $js.="\n";
            $js.='grid1.delete_url="/index.php/plan/dodelete";';
            $js.="\n";

            $js.='grid1.setPage('.$this->paginator->getPage().');';
            $js.="\n";
            $js.='grid1.setPerPage('.$this->paginator->getPerPage().');';
            $js.="\n";

            $js.='jQuery(document).ready( function(){';
            $js.="\n";
            $js.='  //grid1.debug();';
            $js.="\n";
            $js.='  grid1.getData();';
            $js.="\n";

            $js.='});';
            $js.="\n";
            $js.='</script>';
         $js.="\n";

         return $js;
      }

      function setId($grid_id)
      {
         $this->grid_id=$grid_id;  
      }

      function getId()
      {
         return $this->grid_id;
      }

      function setPrimary($primary)
      {
         $this->primary=$primary;  
      }

      function setCaption($title)
      {
         $this->caption=$title;  
      }

      function addOperate($html)
      {
         $this->operates[]=$html;  
      }

      function setPaginator($paginator)
      {
         $this->paginator=$paginator;  

         if($this->debug == true)
         $this->paginator->setDebug(true);
      }

      /**
      * @param Mix  $handle  can be: null (no handle), string (function callback), array ( 0=>class,1=>method )
      */
      function addColumn($column,$handle=null)
      {
         $this->columns[$column]=$handle; 
      }

      function setTitle($titles)
      {
         $this->titles=$titles;  
      }
      /**
      * set style for check if correct
      */
      function setDebug($debug=true)
      {
         $this->debug=$debug;
      }

      function render()
      {
         //$table=new Frd_Table(array('class'=>'grid','id'=>$this->grid_id));
         $table=new Frd_Table(array('class'=>'grid'));

         if($this->debug == true)
         $table->setDebug();

         if($this->caption != false)
         {
            $table->setRow(2);  //row 1 for caption
         }

         //set grid title
         $table->setRowClass("title");
         foreach($this->titles as $title)
         {
            $table->col($title); 
         }
         //operate bar
         $table->nextRow();
         $table->setRowClass("operate");
         $cols=$table->getMaxCol();

         $operate_html='';
         foreach($this->operates as $operate)
         $operate_html.=$operate.'&nbsp;&nbsp;';

         $table->colspan($operate_html,$cols);

         $table->nextRow();

         //set grid content
         $table->setRowClass("list");

         $rows= $this->paginator->getList();
         foreach($rows as $row)
         {
            $onclick=$this->paginator->getJsGrid().'.select('.$row[$this->primary].',this)';
            $table->setRowAttr(array("onclick"=>$onclick));

            foreach($this->columns as $column => $handle)
            {
               if( isset($row[$column] ))
               $param1=$row[$column];
               else
               $param1='';


               if($handle == false )
               {
                  $value=$param1;
               }
               else if(is_string($handle))
               {
                  $value=$handle($param1,$row);
               }
               else if(is_array($handle) && count($handle) > 0)
               {
                  $class=new $handle[0]; 
                  $value=$class->$handle[1]($param1,$row);
               }
               else
               {
                  $value=$param1;
               }

               $table->col($value);

            }

            $table->nextRow();
         }


         //operate bar
         $table->setRowClass("operate");
         $cols=$table->getMaxCol();

         $operate_html='';
         foreach($this->operates as $operate)
         $operate_html.=$operate.'&nbsp;&nbsp;';

         $table->colspan($operate_html,$cols);

         //set order
         $table->nextRow();
         $order_html=" Sort By &nbsp;&nbsp;";

         $form=new Frd_Form();

         if(isset($this->request['sortname']))
         $this->cur_sortname=$this->request['sortname'];
         else
         $this->cur_sortname='';

         if(isset($this->request['sortorder']))
         $this->cur_sortorder=$this->request['sortorder'];
         else
         $this->cur_sortorder='';

         $order_html.=$form->select('sortname',$this->cur_sortname,$this->sortname);
         $order_html.=$form->select('sortorder',$this->cur_sortorder,$this->sortorder);


         $table->colspan($order_html,$cols);

         //filter

         //show debug message
         if($this->debug == true)
         {
            $table->nextRow();
            $debug_html='<div style="color:red">';
               $debug_html.='<ul>';

                  foreach($this->paginator->getDebugMessage() as $msg)
                  {
                     $debug_html.='<li>';
                     $debug_html.=$msg;
                     $debug_html.='</li>';
                  }

                  $debug_html.='</ul>';
               $debug_html.='</div>';


            $table->colspan($debug_html,$cols);
         }
         //set grid paginator info
         $table->nextRow();
         $table->setRowClass("paginator");

         $cols=$table->getMaxCol();

         //paginator contain 3 parts:
         //page info, notice message, paginator
         $pages=$form->select("perpage",$this->request['perpage'],$this->perpages);

         $html='<div class="pageinfo">'.$pages.'</div>';
         $html.='<div class="message">notice message</div>';
         $html.= '<div class="pagelist>'.$this->paginator->toHtml().'</div>';
         $html.= '<div style="clear:both"></div>';

         $table->colspan($html,$cols);

         //set caption
         if($this->caption != false)
         {
            $table->setRow(1);
            $table->setCol(1);
            $table->setRowClass("caption");

            $cols=$table->getMaxCol();

            $table->colspan($this->caption,$cols);
         }


         $html=$table->render();

         return $html;
      }
   }
