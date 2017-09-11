<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/annotation.php');

require_once('../annotate/main.php');
require_once('../annotate/api.php');
require_once('../config/validation.php');


$config_local_pagename = "Hi, " . $config_global_user_info->first_name . "!";

$patient = $_GET["patient"];
$image = $_GET["image"];
$feature = $_GET["feature"];
$scale = $_GET["scale"];

$image_data = config_validation_get_next($patient, $image, $feature);
if ($image_data != 0) {
  $scaled_width = 800;
  $scaled_height = intval(800.0*$image_data["width"]/$image_data["height"]);
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

    <link rel="stylesheet" href="/kidneycancer/resources/style/annotate.css" />


</head>

<body>
    <script type="text/javascript">
    imgdata = <?php echo stripslashes(json_encode($image_data)); ?>;
    </script>

    <div id="wrapper">
    <?php echo config_framework_get_navbar($config_global_sitename); ?>

        <div id="page-wrapper">
            <?php echo config_framework_get_pagelabel($config_local_pagename); ?>

            <?php

            if ($image_data != 0) {
              $feature = $image_data["featurename"];
              $slider = annotate_main_get_slider($image_data["patient"], 3, $image_data["image"], 0, "validation");
              echo <<<EOT
              <div id="validation_toolbar">
                <a href="#" id="val_button" class="btn btn-primary">Validate {$feature}</a>
                <a href="#" id="flag_button" class="btn btn-danger">Flag</a>
                <input id="flag_notes" type="text" placeholder="Flag notes" />
                $slider
              </div>
              <div id="to_validate" class="val_image" style="height:{$scaled_height}px;width:{$scaled_width}px;">
                <img src="{$image_data["imagefile"]}" class="process_me val_pos" height="{$scaled_height}px" width="{$scaled_width}px"></img>
                <img src="{$image_data["annotationfile"]}" class="process_me val_pos" height="{$scaled_height}px" width="{$scaled_width}px"></img>
              </div>
EOT;
            }
            else {
              if ($patient == "" || !isset($patient)) {
                echo "<h1 class='error_msg'>No images are available for validation at this time. Please <a href='/p/bug.php'>report this bug</a> if you believe this is an error.</h1>";
              }
              else {
                echo "<h1 class='error_msg'>Patient '$patient' doesn not have any images available for validation at this time. Please <a href='/p/bug.php'>report this bug</a> if you believe this is an error.<br><br>
                <a href='/p/choose.php'>Choose another patient</a><br>
                <a href='/p/validate.php'>Assign automatically</a></h1>";
              }
            }
            ?>

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

    <!-- Validation JavaScript -->
    <script src="../kidneycancer/resources/js/validate.js"></script>

</body>

</html>
