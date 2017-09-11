<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/users.php');
require_once('../annotate/meta.php');

$annotations_data = annotate_meta_get_annotations();
$user_data = config_users_get_data($GLOBALS["config_global_username"]);
if ($user_data != NULL) {
  $config_local_pagename = $user_data->first_name . ' ' . $user_data->last_name;
}
else {
  $config_local_pagename = $GLOBALS["config_global_username"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Get Meta -->
    <?php echo config_framework_get_meta("Authenticated homepage", "Nick Heller"); ?>

    <title><?php echo $config_global_sitename; ?></title>

    <!-- Get Links -->
    <?php echo config_framework_get_links(".."); ?>

    <!-- Page Icon -->
    <?php echo $config_global_icon_link; ?>

    <link href="../kidneycancer/resources/style/projects.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div id="wrapper">
    <?php echo config_framework_get_navbar($config_global_sitename); ?>

        <div id="page-wrapper">
            <?php echo config_framework_get_pagelabel($config_local_pagename); ?>
            <div class="row">
                <div class="col-lg-4">
                  <?php echo config_framework_get_projects($annotations_data); ?>
                </div>
            </div>

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
