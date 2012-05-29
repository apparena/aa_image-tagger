<?php
/* 本模块试图在 (model,controller) 和 (view) 中间实现数据转换
 * 从php 原始数据转换成 html代码
 * 试图保持 view 层代码的清晰
 * 方便样式，javascript的更高级实现
 * 本模块非必须
 *
 */
class Frd_View_Convert
{
  protected $_view=null;
  function __construct($view)
  {
    $this->_view=$view;
  }

}

