<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../annotate/meta.php');

$notifications_data = annotate_meta_get_notifications($GLOBALS["config_global_username"], 1000)["data"];
$config_local_pagename = "Notifications";
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

</head>

<body>

    <div id="wrapper">
    <?php echo config_framework_get_navbar($config_global_sitename); ?>

        <div id="page-wrapper">
            <?php echo config_framework_get_pagelabel($config_local_pagename); ?>

            <?php echo config_framework_get_notifications($notifications_data, 1); ?>

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
