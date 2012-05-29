<?php
require_once(dirname(__FILE__).'/../init.php');
require_once(dirname(__FILE__).'/../include.php');

//header("content-type:text/html;charset=utf8");
$aa_inst_id=$_GET['aa_inst_id'];

//Register Admin
$lottery = new iCon_Lottery($aa_inst_id,$aa_app_id);
$action = "GetWinner";
$lottery->registerAdmin($session->user["id"], $aa_inst_id, $action);

// Check if there are time boundaries and get participants
if (isset($_POST['from']) && isset($_POST['to'])){
	$participants = $lottery->getParticipantList($aa_inst_id,'',
				$_POST['from'], $_POST['to']);
} else {
	$participants = $lottery->getParticipantList($aa_inst_id);
}

// Check if several winners have to be returned
if (isset($_POST['winners'])){
	$arrWinner = $lottery->getWinner($participants, $aa_inst_id ,
				$_POST['winners']);
} else {
	$arrWinner = $lottery->getWinner($participants, $aa_inst_id);
}
?>

<div id="header" class="fix">
	<div id="admin-popup">
		<div class="header">
			<h1><?php __p("Admin panel")?></h1>
		</div>
		<div class="content">
			<h1><?php __p("Winner of the Lottery")?></h1>
			<table>
			  <tr>
			    <th><?php __p("Tickets")?></th>
			    <th><?php __p("Name")?></th>
			    <th><?php __p("Email-address")?></th>
			  </tr>
			  <?php 
			  foreach ($arrWinner as $winner) {?>
			  	<tr>
				    <td><?=$winner[5]?></td>
				    <td><?=$winner[2]?> <?=$winner[3]?></td>
				    <td><?=$winner[4]?></td>
				</tr>
			  <?php }?>
			</table>
		</div>
		<div class="footer">
			
		</div>
	</div>
</div>
