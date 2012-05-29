<?php
require_once(dirname(__FILE__).'/../init.php');


$aa_inst_id=intval($_GET['aa_inst_id']);

if($aa_inst_id <= 0 )
{
   echo "missing parameter aa_inst_id";
   exit();
}

//update app cached time
$cache=new Cache();
$cache->updateCachedTime($aa_inst_id);
?>

<p>
  <?php __p('Cache will update for all user when they visit any page next time'); ?>
</p>



