<?php
require_once('../config/users.php');
$config_global_sitename = "Distrob Annotation Hub";
$config_global_icon_link = '<link rel="shortcut icon" href="http://distrob.cs.umn.edu/University%20of%20Minnesota.ico">';
if (isset($_SERVER['AUTH_TYPE']) && $_SERVER['AUTH_TYPE'] === 'shibboleth' && isset($_SERVER['uid'])) {
  $config_global_username = $_SERVER['uid'];
  $config_global_webroot = "/web/research/airvl/distrobannotate/distrobannotate.cs.umn.edu/";
}
else {
  $config_global_username = "helle246";
  $config_global_webroot = "/home/" . $config_global_username . "/code/production/distrobannotate/";
}
//$config_global_username = "stani078";
$config_global_user_info = config_users_get_data($config_global_username);
$config_global_dataroot = "https://distrobannotate.cs.umn.edu/data/";
?>
