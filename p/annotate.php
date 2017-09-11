<?php

require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/annotation.php');

require_once('../annotate/main.php');
require_once('../annotate/api.php');


$config_local_pagename = "Hi, " . $config_global_user_info->first_name . "!";

$patient = $_GET["patient"];
$scale = intval($_GET["scale"]);
$image = intval($_GET["image"]);
$feature = intval($_GET["feature"]);


$patientdata = annotate_api_get_patient($config_global_username, $patient);
if (!$scale) {
  $mx = max($patientdata->width, $patientdata->height);
  $scale = 6;
}
if (!$feature) {
  $feature = 0;
}
if (!$image) {
  $image = 0;
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


    <!-- ANNOTATION SPECIFIC -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="/kidneycancer/resources/js/annotate.js"></script>
    <link rel="stylesheet" href="/kidneycancer/resources/style/annotate.css" />

    <?php echo config_framework_get_fancybox(); ?>

</head>

<body>

    <div id="wrapper">
    <?php echo config_framework_get_navbar($config_global_sitename); ?>
          <?php
          echo <<<EOT
          <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                      $config_local_pagename
                      <a href="#" id="zoom_in" class="zoom_icon">
                        <i class="glyphicon glyphicon-zoom-in fa-fw"></i>
                      </a>
                      <a href="#" id="zoom_out" class="zoom_icon">
                        <i class="glyphicon glyphicon-zoom-out fa-fw"></i>
                      </a>
                      <span id="image_num">Patient: $patient  &nbsp; &nbsp; Image: $image</span>
                      <a class="info fancybox" href="/p/isinfo.php?imageset={$patientdata->id}" style="font-size:25px;">info</a>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>

EOT;

?>
<?php
if ($patientdata == "notfound") {
  echo annotate_main_get_notfound($patient);
}
elseif ($patientdata == "error") {
  echo annotate_main_get_error();
}
elseif ($patientdata == "none") {
  echo annotate_main_get_none();
}
else {
  echo annotate_main_get_toolbar($patientdata, $scale, $image, $feature);
  echo annotate_main_get_payload($patientdata, $scale, $image, $feature);
}
?>


        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->


    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>


    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
