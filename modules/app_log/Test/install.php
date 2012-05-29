<?php
   require_once 'PHPUnit/Autoload.php';
   require_once dirname(__FILE__).'/../../../init.php';

   class InstallTest extends PHPUnit_Framework_TestCase
   {
      public function setUp()
      {
         $db=Frd::getDb();
         $creater=new Frd_Db_Creater($db);

         $creater->deleteTable("app_log_user");
         $creater->deleteTable("app_log_fb");
         $creater->deleteTable("app_log_admin");
      }

      public function testInstall()
      {
         $module=Frd::getModule("app_log",'AppLog');
         $installer=$module->getInstaller();

         $installer->uninstall();

         $ret=$installer->isInstalled(); 
         $this->assertFalse($ret);

         $installer->install(); 

         $ret=$installer->isInstalled(); 
         $this->assertTrue($ret);

      }

   }

   //$test=new InstallTest();
   //$test->testInstall();

