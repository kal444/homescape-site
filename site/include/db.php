<?php

class DB {
  var $link;

  function DB () {
    $this->connect();
  }

  function connect () {
    /* Connecting, selecting database */
    $this->link = mysql_connect("localhost", 
                          "homescape_test", 
                          "homescape_test")
      or die("Could not connect : " . mysql_error());

    // echo "Connected successfully";

    mysql_select_db("homescape_test") 
      or die("Could not select database");
  }

  function query ($query) {
    $result = mysql_query($query) 
      or die("Query failed : " . mysql_error());

    return $result;
  }

  function disconnect () {
    mysql_close($this->link);
  }
}

?>
