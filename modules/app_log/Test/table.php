<?php
   require_once 'PHPUnit/Autoload.php';
   require_once dirname(__FILE__).'/../../../init.php';

   class TableTest extends PHPUnit_Framework_TestCase
   {
      public function setUp()
      {
         $db=Frd::getDb();
         $creater=new Frd_Db_Creater($db);

         $creater->deleteTable("app_log_user");
         $creater->deleteTable("app_log_fb");
         $creater->deleteTable("app_log_admin");

         $installer=Frd::getModule("app_log",'AppLog')->getInstaller();
         $installer->install();
      }

      public function testInstall()
      {
         $db=Frd::getDb();

         $admin_log=Frd::getModule("app_log",'AppLog')->getTable("admin");
         $now= date("Y-m-d H:i:s");

         $data=array(
            'fb_user_id'=>100,
            'aa_inst_id'=>10,
            'action'=>'add',
            'ip'=>'10.10.10.10',
            'timestamp'=>$now,
         );
         $admin_log->add($data);

         $sql="select count(*) from app_log_admin where fb_user_id=100 and aa_inst_id=10 and action='add' and ip='10.10.10.10' and timestamp = '$now'";

         $ret=$db->fetchOne($sql);

         $this->assertEquals(1,$ret);



         //$admin_log=Frd::getModule("app_log",'AppLog')->getTable("admin");
         //$admin_log=Frd::getModule("app_log",'AppLog')->getTable("admin");
      }

   }

   //$test=new InstallTest();
   //$test->testInstall();

