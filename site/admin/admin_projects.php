<?php

require_once('config.php');
require_once('../include/db.php');
require_once('../include/contact_util.php');
require_once('../include/media_util.php');

$smarty = new Homescape_Admin;
if (!isset($db)) {
  $db = new DB;
}

if (isset($_GET['action']) && $_GET['action'] == 'SCAN') {

  # print "1 begin:" . date("Y/m/d-H:i:s") . "<br>";
  # print "2 end:" . date("Y/m/d-H:i:s") . "<br>";

  $thumbnail_list = Media_Util::build_media_thumbnails(Media_Util::scan_media());

  $tbs = $thumbnail_list;
  sort($tbs);

  $contact_list = Contact_Util::get_all_contacts();

  $smarty->assign("thumbnails", $tbs);
  $smarty->assign("contacts", $contact_list);

  $smarty->display('admin/admin_projects_scan.tpl');

} elseif (isset($_GET['action']) && $_GET['action'] == 'ADD') {

  // selected_media is a list of original filename of the selected images to add
  if (isset($_POST['selected_media'])) {
    print "<pre>";
    print_r($_POST['project_info']);
    print_r($_POST['contact_old']);
    print_r($_POST['contact_new']);
    print_r($_POST['selected_media']);
    print_r($_POST['selected_caption']);
    print_r($_POST['selected_desc']);
    print "</pre>";

    /*
    // If validation failed for any reason...

    $thumbnail_list = Media_Util::build_media_thumbnails(Media_Util::scan_media());

    $tbs = $thumbnail_list;
    sort($tbs);

    $contact_list = Contact_Util::get_all_contacts();

    $smarty->assign("thumbnails", $tbs);
    $smarty->assign("contacts", $contact_list);

    $smarty->assign("project_info", $_POST['project_info']);
    $smarty->assign("contact_old", $_POST['contact_old']);
    $smarty->assign("contact_new", $_POST['contact_new']);
    $smarty->assign("selected_media", $_POST['selected_media']);
    $smarty->assign("selected_caption", $_POST['selected_caption']);
    $smarty->assign("selected_desc", $_POST['selected_desc']);

    $smarty->display('admin/admin_projects_scan.tpl');
    */

    $media_list = $_POST['selected_media'];
    $intermediate_list = Media_Util::build_media_intermediates($media_list);

    $media_db_list = Media_Util::build_media_lib($media_list);

    print "<pre>";
    print_r($media_db_list);
    print "</pre>";

    // create the item entries here based on the selection

    // create the project entry here based on the selection and information from the form

  }

} else {

  $projects = $db->query("select * from project");

  print "Found " . mysql_num_rows($projects) . " projects.<br>";

  $smarty->display('admin/admin_projects.tpl');

}

?>
