<?php
require_once("../config/global.php");

function config_users_get_data($uid) {
  $file = $_GLOBALS['config_global_webroot'] . "../data/users.json";
  if (($users = json_decode(file_get_contents($file))) != FALSE) {
    foreach ($users as $user) {
      if ($user->x500 == $uid) {
        return $user;
      }
    }
  }
  else {
    return NULL;
  }
}

?>
