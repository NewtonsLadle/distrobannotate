<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');

require_once('../config/validation.php');


if ($_POST["validate"] == "1") {
  if($ret = (config_validation_set_valid($_POST["patient"], $_POST["image"], $_POST["feature"])) == 0) {
    echo 0;
  }
  else {
    echo $ret;
  }
}
else {
  if (config_validation_set_flag($_POST["patient"], $_POST["image"], $_POST["feature"], $_POST["notes"])) {
    echo 0;
  }
  else {
    echo 1;
  }
}
?>
