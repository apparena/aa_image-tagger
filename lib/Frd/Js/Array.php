<?php
   /**
   * create frd js array, render as js code ,like [....]
   */
   class Frd_Js_Array 
   {
      protected $data=array();

      function __construct($item=false)
      {
         if($item != false) 
         {
            $this->add($item);
         }
      }

      function getLastIndex()
      {
         $index=count($this->data)-1;

         return $index;
      }

      /**
      * get an item by index
      */
      function get($index)
      {
         if (count($this->data) <= $index)
         {
            throw new Exception("js array index out of range"); 
         }

         return $this->data[$index];
      }

      /**
      * add js object
      */
      function addObject($data=array())
      {
         $object=new  Frd_Js_Object($data);

         return $this->addItem($object);
      }

      function addItem($item)
      {
         if(is_numeric($item) )
         {
            //$this->data[]="'$item'";
            $this->data[]="$item";
         }
         else if(is_string($item) )
         {
            $this->data[]="'$item'";
         }
         else 
         {
            //$this->data[]=$item->render();
            $this->data[]=$item;
         }

         return $this->getLastIndex();
      }

      /**
      * add item for array, 
      * 
      * @return  integer index if add one item, return this item's index,if add array, return last item's index
      */
      function add($object)
      {
         if(is_array($object))
         {
            foreach($object as $v)
            {
               $this->addItem($v);
            }

            return $this->getLastIndex();
         }

         else 
         {
            return $this->addItem($object);
         }

      }

      function render()
      {
         $data=array();
         foreach($this->data as $v)
         {
            if(is_object($v))
            {
               $data[]=$v->render();
            }
            else
            {
               $data[]=$v; 
            }

         }
         $script="[ ";

         $script.=implode(",",$data);

         $script.=" ]";

         return $script;
      }

   }
