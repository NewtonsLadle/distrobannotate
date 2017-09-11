<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');

$config_local_pagename = "Report a Bug";
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Get Meta -->
    <?php echo config_framework_get_meta("Interface for reporting a bug", "Nick Heller"); ?>

    <title><?php echo $config_global_sitename . " | " . $config_local_pagename; ?></title>

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
            Please send an email to
            <a href="mailto:helle246@umn.edu">helle246@umn.edu</a> with a
            detailed description of your issue. Screenshots are helpful when
            relevant.
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
