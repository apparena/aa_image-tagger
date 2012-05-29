<?php

   //require_once 'PHPUnit/Autoload.php';
   require_once dirname(__FILE__).'/../../../../lib/Frd/Frd.php';
   Frd::setTimeZone('Asia/Shanghai');

   Frd::addIncludePath("../../../../lib/");
   Frd::addIncludePath("../../../modules");


   Frd::enableAutoload();
   //$global=Frd::getGlobal();
   Frd::setRootPath(dirname(__FILE__));
   //echo Frd::getRootPath();

   Frd::handleException();

   //Frd::setSetting("exception_output",false);
   Frd::setSetting("exception_output",true);
   Frd::setSetting("module_path",realpath(dirname(__FILE__).'/../../../modules'));

   Frd::addDb(array(
      'adapter'=>'MYSQLI',
      'host'=>'localhost',
      'username'=>'root',
      'password'=>'123',
      'dbname'=>'test',
   ));

   $db=Frd::getDb();


   //$table=Frd::getClass("frd/html/table",'base');
   //echo 'aaa';
   //$app=new Frd_App();
   //print_r(get_include_path());

