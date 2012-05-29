<?php
require_once(dirname(__FILE__).'/../init.php');
ini_set('display_errors', 1);

$aa_inst_id=$_GET['aa_inst_id'];

//Register Admin
$lottery = new iCon_Lottery($aa_app_id);
$action = "CSVExport";
$lottery->registerAdmin($session->user["id"], $aa_inst_id, $action);

//Export of the User Data
$exporter = new iCon_Export();

//add admin log
$admin=getModule("app_log")->getTable("admin");

if(isset($session->fb) && isset($session->fb['fb_user_id']))
{
   $fb_user_id=$session->fb['fb_user_id'];
}
else
{
   $fb_user_id=0;
}


$data=array(
   'fb_user_id'=>$fb_user_id,
   'aa_inst_id'=>$aa_inst_id,
   'action'=>'export',
   'ip'=>getClientIp(),
   'timestamp'=>date("Y-m-d H:i:s"),

);

$admin->add($data);



//get data
// Get participants
$arrData = $lottery->getParticipantList($aa_inst_id, ", `timestamp`, `ip`,
`newsletter_registration`,`newsletter_doubleoptin`,`tickets`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`", $_POST['from'], $_POST['to']);

$arrTitle = array(
   //0=>__t("FB User Id"), 
   1 =>__t("First name"), 
   2 =>__t("Last name"),
   3 =>__t("Email-address"), 
   4 => __t("Gender"), 
   5 => 'Timestamp',
   6 => 'IP',
   7 => __t("Newsletter registration"),
   8 => __t('Newsletter doubleoptin'),
   9 => __t("Tickets"), 
   10 => __t("answers_correct"), 
   11 => __t("question1_answer"),
   12 => __t("question2_answer"),
   13 => __t("question3_answer"),
   14 => __t("award_selection")
);


if(isset($arrData) && count($arrData) != "0")
{
   //do not export fb_user_id column
   foreach($arrData as $k=>$v)
   {
      unset($arrData[$k]['fb_user_id']);
   }

   //handle column , if not acticate , unset the column
   if( intval($session->config['questions_activated']['value']) == false)
   {
      unset($arrTitle[10]);
      unset($arrTitle[11]);
      unset($arrTitle[12]);
      unset($arrTitle[13]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['answers_correct']);
         unset($arrData[$k]["question1_answer"]);
         unset($arrData[$k]['question2_answer']);
         unset($arrData[$k]['question3_answer']);
      }
   }
   
   // Remove unused questions
   if( $session->config['questions_activated']['value'] && intval($session->config['questions_amount']['value']) < 2)
   {
      unset($arrTitle[12]);
      unset($arrTitle[13]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['question2_answer']);
         unset($arrData[$k]['question3_answer']);
      }
   } else if( $session->config['questions_activated']['value'] && intval($session->config['questions_amount']['value']) < 3)
   {
      unset($arrTitle[13]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['question3_answer']);
      }
   }

   //award selection 
   if( intval($session->config['award_selection_activated']['value']) == false)
   {
      unset($arrTitle[14]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['award_selection']);
      }
   }

   // when newsletter activated, 
   if( intval($session->config['newsletter_registration']['value']) == false)
   {
      unset($arrTitle[7]);
      unset($arrTitle[8]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['newsletter_registration']);
         unset($arrData[$k]['newsletter_doubleoptin']);
      }
   }


   //referral_tracking_activated
   if( intval($session->config['referral_tracking_activated']['value']) == false)
   {
      unset($arrTitle[9]);

      foreach($arrData as $k=>$v)
      {
         unset($arrData[$k]['tickets']);
      }
   }
   
   // Registration without Facebook 
   if( intval($session->config['use_form_registration']['value']) == true)
   {
		unset($arrTitle[2]);
		unset($arrTitle[4]);
		$arrTitle[1] = __t("Name");
		foreach($arrData as $k=>$v)
		{
			unset($arrData[$k]['last_name']);
			unset($arrData[$k]['gender']);
		}
   }


   $exporter->arrayToCsv($arrData, $arrTitle);
}
else
{
   __p("During this time nobody participated.");

}
?>
