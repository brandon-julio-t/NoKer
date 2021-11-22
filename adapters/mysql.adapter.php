<?php

class MySqlAdapter
{
  public static function get()
  {
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "noker";

    $db = new mysqli($host, $username, $password, $database);
    if ($db->connect_error)
      die("Connection failed: {$db->connect_error}");
    return $db;
  }

  private function __construct()
  {}
}
