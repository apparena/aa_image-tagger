<?php
/**
 * create html table
 *
 */
class Frd_Html_Table
{
  protected $data=array();

  protected $rownum=0;  //how much rows
  protected $colnum=0;  //how much cols

  protected $currow=1;
  protected $curcol=1;

  protected $debug=false;
  //style
  protected $row_class=array();
  protected $row_attr=array();

  function __construct($attrs=array())
  {
    $attrs=new Frd_Html_Attributes($attrs);
    $this->attrs=$attrs->toHtml(); 
  }
  /**
   * set style for check if correct
   */
  function setDebug($debug=true)
  {
    $this->debug=$debug;
  }

  function setRow($index)
  {
    $this->currow=$index; 
  }

  function setRowClass($class)
  {
    $this->row_class[$this->currow]=$class ;
  }

  function setRowAttr($attr)
  {
    $this->row_attr[$this->currow]=$attr ;
  }

  function setCol($index)
  {
    $this->curcol=$index; 
  }

  function getMaxCol()
  {
    return  $this->colnum-1;
  }


  function nextRow($col=1)
  {
    $this->currow+=1;

    if($col > 0)
      $this->curcol=$col;
    else
      $this->curcol=1;
  }

  /**
   * 
   */
  function span($value,$rows,$cols,$attrs=array())
  {
    $attrs['colspan']=$cols;
    $attrs['rowspan']=$rows;


    if(!isset($this->data[$this->currow]) ) 
      $this->data[$this->currow]=array();

    $col=$this->curcol;

    $this->colspan($value,$cols,$attrs);

    $this->setCol($col);
    $this->rowspan($value,$rows,$attrs);
  }
  /**
   *
   */
  function rowspan($value,$rows,$attrs=array())
  {
    $attrs['rowspan']=$rows;

//    var_dump($this->curcol);
    $this->col($value,$attrs);

    $col= $this->curcol-1;

    for($i=1; $i<$rows; ++$i)
    {
      $this->nextRow($col);

      $this->col(null);
    }

    $this->setCol($col+1);

  }

  function colspan($value,$cols,$attrs=array())
  {
    $attrs['colspan']=$cols;

    $this->col($value,$attrs);
    for($i=1; $i<$cols; ++$i)
    {
      $this->col(null);
    } 
  }

  function col($value,$attrs=array())
  {
    if(!isset($this->data[$this->currow]) ) 
      $this->data[$this->currow]=array();

    if($this->currow > $this->rownum )
      $this->rownum=$this->currow;

      $this->data[$this->currow][$this->curcol]=array('value'=>$value,'attrs'=>$attrs);

    $this->curcol+=1;
    if($this->curcol > $this->colnum )
      $this->colnum= $this->curcol;

  }

  function render()
  {
    return $this->toHtml();  
  }

  function toHtml()
  {
    //print_r($this->data);
    $html='';
    if($this->debug == true)
    {
      $html.='<table cellspacing=0 border=1 '.$this->attrs.' style="border:dotted 1px green">';
    }
    else
    {
      $html.='<table '.$this->attrs.'>';
    }

    for($r=1;$r<=$this->rownum;++$r)
    {
      if(isset($this->row_attr[$r]))
        $attr= $this->row_attr[$r];
      else
        $attr=array();

      if(isset($this->row_class[$r]))
        $attr['class']=$this->row_class[$r];

      $attrs=new Frd_Html_Attributes($attr);
      $attr_str=$attrs->toHtml();

      $html.='<tr '.$attr_str.'>';

      for($c=1;$c<$this->colnum;++$c)
      {

        if(isset($this->data[$r][$c]))
        {
          $col=$this->data[$r][$c];
          if($col['value'] === null)
            continue;

          $attrs=new Frd_Html_Attributes($col['attrs']);
          $attr_str=$attrs->toHtml();

          $html.='<td '.$attr_str.'>';
          $html.=$col['value'];
        }
        else
        {
          $html.='<td>';
          $html.='&nbsp;';
        }


        $html.='</td>';
      }     
      $html.='</tr>';
    }
    $html.='</table>';

    return $html;
  }
}
?>
