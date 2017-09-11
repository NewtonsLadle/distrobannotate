<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../annotate/meta.php');

$config_local_pagename = "Dashboard - Kidney Cancer";

$annotations_data = annotate_meta_get_annotations();
$notifications_data = annotate_meta_get_notifications($GLOBALS["config_global_username"], 10)["data"];
$timeline_data = annotate_meta_get_timeline(0);
if ($timeline_data["error"]) {
  echo '<!-- TIMELINE DATA ERROR -->';
}
else {
  $timeline_data = $timeline_data["data"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Get Meta -->
    <?php echo config_framework_get_meta("Authenticated homepage", "Nick Heller"); // Nick is Author ?>

    <title><?php echo $config_global_sitename; ?></title>

    <!-- Get Links -->
    <?php echo config_framework_get_links(".."); ?>

    <!-- Page Icon -->
    <?php echo $config_global_icon_link; ?>

    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>


</head>

<body>

    <div id="wrapper">
    <?php echo config_framework_get_navbar($config_global_sitename, $notifications_data); ?>

        <div id="page-wrapper">
            <?php echo config_framework_get_pagelabel($config_local_pagename); ?>

            <!-- /.row -->
            <?php echo config_framework_get_blkstats($annotations_data); ?>

            <!-- /.row -->
            <div class="row">
                <div class="col-lg-8">
                  <?php echo config_framework_get_areachart($annotations_data); ?>
                  <?php echo config_framework_get_timeline($timeline_data); ?>
                </div>
                <!-- /.col-lg-8 -->
                <div class="col-lg-4">
                  <?php echo config_framework_get_notifications($notifications_data, 10, 0); ?>
                  <?php echo config_framework_get_progressbar($annotations_data); ?>
                </div>
                <!-- /.col-lg-4 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <!-- Replaced by inline script
    <script src="../data/morris-data.js"></script>
    -->
    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
