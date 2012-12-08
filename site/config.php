<?php

error_reporting(E_ALL);

define('BASEDIR', '/home/kal/homescape/smarty/');
define('SMARTY_DIR', BASEDIR.'smarty/');

// load Smarty library
require(SMARTY_DIR.'Smarty.class.php');

class Homescape extends Smarty {

  function Homescape () {
   
    // Class Constructor. These automatically get set with each new instance.
    $this->Smarty();

    $this->template_dir = BASEDIR.'templates/';
    $this->compile_dir  = BASEDIR.'templates_c/';
    $this->config_dir   = BASEDIR.'configs/';
    $this->cache_dir    = BASEDIR.'cache/'; 

    $this->caching = false;
    $this->debugging = false;
    $this->assign('app_name','Homescape');

  }
}

?>
