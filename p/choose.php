<?php

require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/annotation.php');

require_once('../annotate/main.php');
require_once('../annotate/api.php');
require_once('../annotate/choice.php');


$config_local_pagename = "Select an Image Set";

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
    <link rel="stylesheet" href="/kidneycancer/resources/style/choose.css" />

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
                      $config_local_pagename <a class="auto" href="annotate.php">auto</a>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>

EOT;

?>
        <?php echo annotate_choice_get_patients();?>


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
