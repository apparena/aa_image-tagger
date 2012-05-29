<?php
require_once(dirname(__FILE__).'/../init.php');
ini_set('display_errors', 0);

//Register Admin
$lottery = new iCon_Lottery($aa_app_id);
$action = "CSVExport";
$lottery->registerAdmin($session->user["id"], $session->instance['aa_inst_id'], $action);

//Export of the User Data
$exporter = new iCon_Export();

// Add Nr of tickets if pro version
if (isset($session->config['referral_mode']) && $session->config['referral_mode']="1"){
	
	// Create userId array --> take first column of participants list
	
	foreach ($arrData as $participant)
		$uids[] = $participant['user_id'];
	
	// if the questions are activated
	if( isset( $session->config[ 'questions_activated' ] ) && $session->config[ 'questions_activated' ] = "1" ) {
		
		// if newsletter is activated
		if( isset( $session->config[ 'newsletter_registration' ] ) && $session->config[ 'newsletter_registration' ] = "1" ) {
			
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => __t("Gender"), 5 => 'Timestamp', 6 => 'IP', 7 => __t("Newsletter registration"), 8=> __t("Tickets"), 9 => __t("answers_correct"), 10 => __t("question1_answer"), 11 => __t("question2_answer"), 12 => __t("question3_answer"), 13 => __t("award_selection"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `tickets`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `tickets`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`");
			}
			
			// questions are not activated
		} else {
			
			// leave out timestamp, ip and newsletter
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => __t("Gender"), 5=> __t("Tickets"), 6 => __t("answers_correct"), 7 => __t("question1_answer"), 8 => __t("question2_answer"), 9 => __t("question3_answer"), 10 => __t("award_selection"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `tickets`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `tickets`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`");
			}
			
		}
		
		// no questions activated
	} else {
		
		// if newsletter is activated
		if( isset( $session->config[ 'newsletter_registration' ] ) && $session->config[ 'newsletter_registration' ] = "1" ) {
			
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => __t("Gender"), 5 => 'Timestamp', 6 => 'IP', 7 =>__t("Newsletter registration"), 8=>__t("Tickets"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `tickets`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `tickets`");
			}
			
			// newsletter is not activated
		} else {
			
			// leave out timestamp, ip and newsletter
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 =>__t("Gender"), 5=>__t("Tickets"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `tickets`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `tickets`");
			}
			
		}
		
		
		
	}
	
	/*
	// Get tickets for each user id
	$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => 'Timestamp', 5 => 'IP', 6 =>__t("Gender"), 7 =>__t("Newsletter registration"), 8=>__t("Tickets"));
	$tickets = $lottery->getNrOfTickets($uids, $session->instance['id']);
	$i = 0;
	foreach ($arrData as $participant){
		$participant['tickets'] = $tickets[$i];
		$i++;
	}
	*/
	
} else {
	
	// if the questions are activated
	if( isset( $session->config[ 'questions_activated' ] ) && $session->config[ 'questions_activated' ] = "1" ) {
		
		// if newsletter is activated
		if( isset( $session->config[ 'newsletter_registration' ] ) && $session->config[ 'newsletter_registration' ] = "1" ) {
			
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => __t("Gender"), 5 => 'Timestamp', 6 => 'IP', 7 =>__t("Newsletter registration"), 8 => __t("answers_correct"), 9 => __t("question1_answer"), 10 => __t("question2_answer"), 11 => __t("question3_answer"), 12 => __t("award_selection"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`, `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`");
			}
			
			// newsletter is not activated
		} else {
			
			// leave out timestamp, ip and newsletter
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 =>__t("Gender"), 5 => __t("answers_correct"), 6 => __t("question1_answer"), 7 => __t("question2_answer"), 8 => __t("question3_answer"), 9 => __t("award_selection"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `answers_correct`, `question1_answer`, `question2_answer`, `question3_answer`, `award_selection`");
			}
			
		}
		
		// no questions activated
	} else {
		
		// if newsletter is activated
		if( isset( $session->config[ 'newsletter_registration' ] ) && $session->config[ 'newsletter_registration' ] = "1" ) {
			
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 => __t("Gender"), 5 => 'Timestamp', 6 => 'IP', 7 =>__t("Newsletter registration"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], ", `timestamp`, `ip`, `newsletter_registration`", $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id']);
			}
			
			// newsletter is not activated
		} else {
			
			// leave out timestamp, ip and newsletter
			$arrTitle = array(0=>__t("FB User Id"), 1 =>__t("First name"), 2 =>__t("Last name"), 3 =>__t("Email-address"), 4 =>__t("Gender"));
			
			if (isset($_POST['from']) && isset($_POST['to'])){
				$arrData = $lottery->getWinnerList($session->instance['id'], $_POST['from'], $_POST['to']);
			} else {
				$arrData = $lottery->getWinnerList($session->instance['id']);
			}
			
		}
		
	}
	
}




if(isset($arrData) && count($arrData) != "0"){
	// Get participants
	// `user_id`, `first_name`, `last_name`, `email`, `timestamp`, `ip`, 'gender', `newsletter_registration`
	$exporter->arrayToCsv($arrData, $arrTitle);
} else
	__p("During this time nobody participated.");
	
?>
