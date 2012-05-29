<?php 
class Frd_Paginator
{
  protected $_db=null;

  protected $_page=1;
  protected $_perpage=10;
  protected $_sortname='';
  protected $_sortorder='asc';
  protected $_qtype='';
  protected $_query='';

  protected $_param=array();
  protected $_data=null;

  //result ,will used for paginator list
  protected $_total=0;
  protected $_pagecount=0;
  protected $_pagenum=5; //will show how much pages in page list 


  function __construct($db=false)
  {
    $this->_db=$db;

    if($db == false)
    {
      $db=Frd::getDb(); 
    }

    $this->_db=$db;
  }

  /**
   * set current page
   */
  function setPage($number)
  {
    $number=intval($number);
    if($number <=0 )
      $number =1;

    $this->_page=$number;	
  }

  function setPerPage($number)
  {
    $this->_perpage=$number;	
  }

  /**
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
      $this->_sortorder='desc';

  }

  /**
   * set query type and query value
   */
  function setQuery($qtype,$query)
  {
    $this->_qtype=$qtype;
    $this->_query=$query; 
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

  function getTotal()
  {
    if($this->_total != null)
      return $this->_total;

    $this->_total=$this->_getTotal();

    return $this->_total;
  }

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
        'aa_inst_id'=>$global->aa_inst_id,
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
        'aa_inst_id'=>$global->aa_inst_id,
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
      'aa_inst_id'=>$global->aa_inst_id,
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
        'aa_inst_id'=>$global->aa_inst_id,
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
        'aa_inst_id'=>$global->aa_inst_id,
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
