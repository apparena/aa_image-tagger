<?php
require_once(dirname(__FILE__).'/../../init.php');
//require_once(dirname(__FILE__).'/../../include.php');

//header("content-type:text/html;charset=utf8");
function get_winner($data)
{
   global $aa_app_id,$session;
   $aa_inst_id=$data['aa_inst_id'];

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
      'action'=>'get winner',
      'ip'=>getClientIp(),
      'timestamp'=>date("Y-m-d H:i:s"),

   );

   $admin->add($data);
   //add log end


   //Register Admin
   $lottery = new iCon_Lottery($aa_inst_id,$aa_app_id);
   $action = "GetWinner";
   $lottery->registerAdmin($session->user["id"], $aa_inst_id, $action);

   // Check if there are time boundaries and get participants
   if (isset($_REQUEST['from']) && isset($_REQUEST['to']))
   {
      $participants = $lottery->getParticipantList($aa_inst_id,',name',
      $_REQUEST['from'], $_REQUEST['to']);
   } 
   else 
   {
      $participants = $lottery->getParticipantList($aa_inst_id,',name');
   }

   // Check if several winners have to be returned
   if (isset($_REQUEST['winners']))
   {
      $arrWinner = $lottery->getWinner($participants, $aa_inst_id ,
      $_REQUEST['winners']);
   } 
   else 
   {
      $arrWinner = $lottery->getWinner($participants, $aa_inst_id);
   }

   //no winner
   if(count($arrWinner) == false)
   {
      $html=html_no_winner();
   }
   else
   {
      $html=html_winners($arrWinner);
   }

   echo $html;
   return false;
}

function html_no_winner()
{
   echo '<div class="alert alert-block">
        <a class="close" data-dismiss="alert">x</a>
        <h4 class="alert-heading">' . __t("No winner available") . '</h4>
        <p>' . __t("At the moment there are no participants, so the system could get no winner.") . '</p>
      </div>';
}


function html_winners($arrWinner)
{
   $winner=__t("Winner of the Lottery");
   $tickets=__t("Tickets");
   $name=__t("Name");
   $email=__t("Email-address");

   $result=<<<HTML
	<div id="winner-container">
		<div class="page-header">
			<h1>$winner</h1>
		</div>
		<div class="content">
			<table class="table table-bordered">
				<tr>
				   <th>
					  $tickets
				   </th>
				  <th>
					 $name
				  </th>
				  <th>
					 $email
				  </th>
				</tr>
HTML;
			  foreach ($arrWinner as $winner) 
        $result.=<<<HTML
			  	<tr>
					 <td>{$winner['tickets']}</td>
					 <td>{$winner['name']}</td>
					 <td>{$winner['email']}</td>
				</tr>
HTML;

$result.=<<<HTML
			</table>
		</div>
		<div class="footer">
			
		</div>
	</div>
</div>

HTML;

return  $result;
      
}


?>
