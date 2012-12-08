<?php

require('config.php');

$smarty = new Homescape;

$smarty->assign('name','Nate');

$smarty->display('index.tpl');

?>
