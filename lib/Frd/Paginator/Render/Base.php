<?php
/**
 * base paginator render 
 */
class Frd_Paginator_Render_Base
{
    // curpage, total, pagecount ,pagenum , pageerClass
  protected $curpage=1;
  protected $total=0;
  protected $item_total=0;
  protected $pagenum=5;

  //html option
  protected $class="paginator";
  protected $class_current="current";
  protected $page_attr=array();

  //js 
  protected $js_grid='FrdGrid';

  function setJsGrid($grid)
  {
    $this->js_grid=$grid; 
  }

  function setCurpage($curpage)
  {
    $this->curpage=$curpage; 
  }
  function setItemTotal($total)
  {
    $this->item_total=$total; 
  }
  function setPageTotal($total)
  {
    $this->total=$total; 
  }
  function setPageNum($pagenum)
  {
    $this->pagenum=$pagenum; 
  }
  function setClass($classname)
  {
    $this->class=$classname; 
  }
  function setCurrentClass($classname)
  {
    $this->current_class=$classname; 
  }

  function render()
  {
     $html='<div class="'.$this->class.'">'; 
        //statistic
     //$html.='Total:'.$this->item_total;
         //page links

      $js=$this->toPageJs(1);
      $html.='<a href="1" onclick="'.$js.'">&lt;&lt;</a>'; //first page

      if($this->curpage > 1) 
      {
        $page=$this->curpage-1;
        $js=$this->toPageJs($page);
      }
      else
      {
        $page=1;
        $js=$this->toPageJs($page);
      }
      $html.='<a href="'.$page.'" onclick="'.$js.'">&lt;</a>'; //prev page
      
      // half
      $half=floor(($this->pagenum-1) / 2 );

      //before curpage
      if(($this->curpage - $half) >0)
        $start=($this->curpage - $half);
      else
        $start=1;

      for($i= $start; $i< $this->curpage; ++$i)
      {
        $js=$this->toPageJs($i);
        $html.='<a href="'.$i.'" onclick="'.$js.'">'.$i.'</a>'; //prev page
      }

      //curage
      $js=$this->toPageJs($this->curpage);
      //$html.='<a href="'.$this->curpage.'" onclick="'.$js.'" class="'.$this->class_current.'">'.$this->curpage.'</a> / '.$this->total; 
      $html.='<a href="'.$this->curpage.'" onclick="'.$js.'" class="'.$this->class_current.'">'.$this->curpage.'</a> '; 

      if(($this->curpage + $half) < $this->total)
        $end=($this->curpage + $half);
      else
        $end=$this->total;

      for($i= $this->curpage+1; $i<= $end; ++$i)
      {
        $js=$this->toPageJs($i);
        $html.='<a href="'.$i.'" onclick="'.$js.'">'.$i.'</a>'; //prev page
      }


      if($this->curpage < ($this->total-1) ) 
      {
        $page=$this->curpage+1;
      }
      else
      {
        $page=$this->total;
      }

      $js=$this->toPageJs($page);
      $html.='<a href="'.$page.'" onclick="'.$js.'">&gt;</a>'; //next page

      $js=$this->toPageJs($this->total);
      $html.='<a href="'.$this->total.'" onclick="'.$js.'">&gt;&gt;</a>'; //last page

      $html.='</div>';

      return $html;
  }

  function toPageJs($page)
  {
    $js=$this->js_grid.'.setPage('.$page.');';
    $js.=$this->js_grid.'.getData();';
    $js.='return false';


    return $js;
  }
}
