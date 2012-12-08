<?php

require_once('db.php');


if (!isset($db)) {
  $db = new DB;
}

class Contact_Util {
  // returns a list of all contacts in DB
  function get_all_contacts () {
    global $db;

    $contacts = array();

    $result = $db->query("select * from contact");
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
      $contacts[] = array (
        'id' => $row['contact_id'],
        'content' => "{$row['name']}, {$row['street']}, {$row['city']}, {$row['state']}, {$row['zip']}, {$row['phone']}"
      );
    }

    return $contacts;
  }

}

?>
