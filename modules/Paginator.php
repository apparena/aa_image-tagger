<?php
/*
 * paginator 
 */
class Paginator
{
  protected $db=false;

  protected $_curpage=1;
  protected $_pagecount=15;
  protected $_sortname='';
  protected $_sortorder='asc';
  protected $_qtype='';
  protected $_query='';

  protected $_total=null;

  protected $_param=array();

  function __construct($db = false)
  {
    if($db == false)
    {
      $db=Frd::getDb(); 
    }

    $this->_db=$db;
  }
  /*
   * set current page
   */
  function setPage($page)
  {
    $page=intval($page);
    if($page <= 0)
      $page=1;
    else
      $this->_curpage=$page;
  }

  /*
   * set each page has how many items
   */
  function setPageCount($count)
  {
    $count=intval($count);

    if($count > 0)
      $this->_pagecount=$count;
  }

  /*
   * set params which willl used in get list ,as condition of sql
   */
  function setParam($param=array())
  {
    foreach($param as $k=>$v)
    {
      $this->_param[$k]=$v; 
    }
  }

  /*
   * set order
   *
   * @param sortname  sort column's name
   * @param sortorder sort order , must be 'asc' or 'desc','asc' is the default value
   *
   */
  function setOrder($sortname,$sortorder='asc')
  {
    $this->_sortname=$sortname;

    $sortder=strtolower($sortorder);

    if($sortorder == 'desc')
      $this->_sortorder='desc';
    else if($sortorder == 'asc')
      $this->_sortorder='asc';
    else
      $this->_sortorder='asc';

  }

  function setQuery($qtype,$query)
  {
    $this->_qtype=$qtype;
    $this->_query=$query;
  }

  /*
   * get total items , the sql must the same as get list,but use count()
   *
   *
   */
  public function getTotal()
  {
    if($this->_total != null)
      return $this->_total;

    $this->_total=$this->_getTotal();

    return $this->_total;
  }

  /*
  protected function _getTotal()
  {
   
  }

   */

  /*
   * get recoreds 
   */
  function getList()
  {
  }

  /*
   * get html of page link , like  'newest pre 1 2 3 ...next last'
   */
  function getPageLink()
  {
    global $global;
    $totalpage=ceil($this->_total/$this->_pagecount);
    if($totalpage == false)
      return "";

    $html='';
    //newest
    if($this->_curpage > 1)
    {
      $param=array(
        'instid'=>$global->instid,
        'curpage'=>1,
        'sortname'=>$this->_sortname ,
        'sortorder'=>$this->_sortorder,
      );
      $link=$global->link->createLink('overview.php',$param);
      $html.='&nbsp;&nbsp;<a href="'.$link.'" target="_top">newest</a>';
    }
    else
    {
      $html.='&nbsp;&nbsp;newest';
    }

    //previous
    if($this->_curpage > 1)
    {
      $param=array(
        'instid'=>$global->instid,
        'curpage'=>($this->_curpage-1),
        'sortname'=>$this->_sortname ,
        'sortorder'=>$this->_sortorder,
      );
      $link=$global->link->createLink('overview.php',$param);
      $html.='&nbsp;&nbsp;<a href="'.$link.'" target="_top">previous</a>';
    }
    else
    {
      $html.='&nbsp;&nbsp;previous';
    }

    //curpage
    $param=array(
      'instid'=>$global->instid,
      'curpage'=>$this->_curpage,
      'sortname'=>$this->_sortname ,
      'sortorder'=>$this->_sortorder,
    );

    $link=$global->link->createLink('overview.php',$param);
    $html.='<li class="active"><a class="curpage" href="'.$link.'" target="_top">'.$this->_curpage.'</a></li>';

    //nextpage
    if($this->_curpage < $totalpage)
    {
      $param=array(
        'instid'=>$global->instid,
        'curpage'=>($this->_curpage+1),
        'sortname'=>$this->_sortname ,
        'sortorder'=>$this->_sortorder,
      );
      $link=$global->link->createLink('overview.php',$param);
      $html.='&nbsp;&nbsp;<a href="'.$link.'" target="_top">next</a>';
    }
    else
    {
      $html.='&nbsp;&nbsp;next';
    }
    //last 
    if($this->_curpage < $totalpage)
    {
      $param=array(
        'instid'=>$global->instid,
        'curpage'=>$totalpage,
        'sortname'=>$this->_sortname ,
        'sortorder'=>$this->_sortorder,
      );
      $link=$global->link->createLink('overview.php',$param);
      $html.='&nbsp;&nbsp;<a href="'.$link.'" target="_top">last</a>';
    }
    else
    {
      $html.='&nbsp;&nbsp;last';
    }

    return $html;
  }
}
