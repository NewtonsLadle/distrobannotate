<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');

$config_local_pagename = "Instructions - Kidney Cancer";
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

          <div id="instructions_cont">
            <h2>Miscellaneous</h2>

            <h4>Bug Reporting</h4>
            Please contact <a href="mailto:helle246@umn.edu">helle246@umn.edu</a>
            in the event of any unexpected or erroneous behavior. If possible
            and relevant, please provide a screenshot of what is happening.

            <h4>Dashboard</h4>
            Below is a screenshot of the landing page when you first enter the site.
            This contains various information about the status of the project.
            <img src="/kidneycancer/resources/images/instructions/dashboard.png"></img>

            <h2>General Workflow</h2>

            <h4>Choosing an Image Set</h4>
            Below is another screenshot of the landing page. In the top left, I've
            highlighted the "Annotate" button.
            <img src="/kidneycancer/resources/images/instructions/dashboard_annotate.png"></img>
            Clicking this will bring you to the imageset list, as shown below.
            <img src="/kidneycancer/resources/images/instructions/choose.png"></img>
            In the above image, each image set has two buttons on its right side.
            The top one is for annotation, the bottom for validation. Just to the
            left of this is the space where the site will notify you if that image
            set has been active in the past hour, as shown below.
            <img src="/kidneycancer/resources/images/instructions/minutes.png"></img>

            <h4>Zooming In/Out</h4>
            Below is the page you land on after clicking the "Annotate" button on
            a given image set. There are two ways to zoom once on this page. the
            first is to press the "ctrl" key simultaneously with the "equals" key.
            The other is to simply click the "+" in the magnifying glass highlighted
            below.
            <img src="/kidneycancer/resources/images/instructions/annotate.png"></img>
            Similarly, use "ctrl -" or click the "-" in the magnifying glass to
            zoom out.

            <h4>Navigating Through Images</h4>
            In the below image, I've highlighted a scrollable element containing
            a number of images. These are each of the images in this particular
            image set. You may click on any of these small images to jump to that
            image. Otherwise, you can use the "previous" or "next" buttons in the
            toolbar above it.
            <img src="/kidneycancer/resources/images/instructions/annotate_slider.png"></img>

            <h4>Labeling a Feature</h4>
            In order to label a feature, simply draw with your cursor much like
            you would with any other painting tool. If you draw a closed shape,
            right click inside of it to fill it in. Use "ctrl z" to undo and
            "ctrl shift z" to redo.
            <img src="/kidneycancer/resources/images/instructions/annotation.png"></img>

            <h4>Submitting a Feature</h4>
            In order to submit your current drawing as a feature, click the blue
            button corresponding to that feature, highlighted below.
            <img src="/kidneycancer/resources/images/instructions/annotate_submit.png"></img>
            In order to redo a feature, simply redraw your label and click that
            same button again. The most recent submission is always considered
            to be the true label. Once a feature is submitted, you will see that
            a thumbnail of your annotation will appear in its appropriate location
            below the image.
            <img src="/kidneycancer/resources/images/instructions/thumbnails.png"></img>

            <h4>Mark an Image Set as Done</h4>
            Identify the "info" link shown below and click it.
            <img src="/kidneycancer/resources/images/instructions/info.png"></img>
            This will open the lightbox shown below. At the bottom of that window,
            click the green button "Mark as Done". Clicking this again will undo
            the action.

          </div>
          <style type="text/css">
            div#instructions_cont {
              max-width: 800px;
              padding-bottom:250px;
            }
            div#instructions_cont img {
              max-width:750px;
              display:block;
              padding:25px;
            }
            div#instructions_cont h4 {
              font-weight: bold;
              padding-top:25px;
            }
            div#instructions_cont h2 {
            }

          </style>

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

    <!-- Morris Charts JavaScript -->
    <script src="../vendor/raphael/raphael.min.js"></script>
    <script src="../vendor/morrisjs/morris.min.js"></script>
    <script src="../data/morris-data.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>
