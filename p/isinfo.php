<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/annotation.php');

require_once('../annotate/main.php');
require_once('../annotate/api.php');

$meta = config_framework_get_meta("Imageset info", "Nick Heller");
$links = config_framework_get_links("..");

echo <<<EOT
<html>
  <head>
    $meta
    $links

    <!-- Add jQuery library -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-latest.min.js"></script>

  </head>
  <body>
  <style type="text/css">
    h1.error {
      font-size:30px;
      text-align:center;
    }
    body {
      text-align:center;
      padding:50px 0;
    }
    .attribute {
      width:620px;
      margin:0 auto;
    }
    h1.attribute {
      padding-bottom:20px;
    }
    h2.attribute {
      padding-bottom:10px;
    }
    h3.attribute.flagged {
      padding-bottom:20px;
    }
    h3.attribute.unannotated {
      color: #337ab7;
    }
    h3.attribute.pending {
      color: #f0ad4e;
    }
    h3.attribute.valid {
      color:#5cb85c;
    }
    h3.attribute.flagged {
      color:#d9534f;
    }
    .attribute .left {
      width:50%;
      display:inline-block;
      text-align:right;
    }
    .attribute .right {
      width:50%;
      display:inline-block;
      text-align:left;
      padding-left:20px;
    }
    .attribute.g {
      color:grey;
    }
    .attribute.stage {
      padding-bottom:20px;
    }
    .attribute.marker.btm {
      padding-bottom:20px;
    }
    .attribute textarea {
      border-color:lightgrey;
    }
    .attribute.notes * {
      vertical-align:middle;
    }
    .attribute.notes{
      padding-bottom:20px;
    }
    textarea {
      font-size:15px;
      min-height:60px;
    }
    .btn.markfinished {
      background-color:#5cb85c;
      border-color:#5cb85c;
    }
    .btn.markunfinished {
      color:#5cb85c;
      border-color:#5cb85c;
    }
  </style>
EOT;
if (isset($_GET["imageset"]) && ($_GET["imageset"] != "")) {
  $patientdata = annotate_api_get_patient($GLOBALS["config_global_username"], $_GET["imageset"]);
  $pending = count($patientdata->annotated->pending);
  $valid = count($patientdata->annotated->valid);
  $flagged = count($patientdata->annotated->flagged);
  $unannotated = $patientdata->num_images - $pending - $valid - $flagged;
  $pathology = $patientdata->pathology;//"Papillary RCC";
  $side = $patientdata->side;//"Right";
  $stage = $patientdata->stage;//"pT1b";
  $notes = $patientdata->notes;
  $nc_checked = "";
  $mri_checked = "";
  $nk_checked = "";
  $finished_cls = "btn-primary markfinished";
  $finished_btn = "Mark as Done";

  if ($patientdata->noncontrast) {
    $nc_checked = " checked";
  }
  if ($patientdata->mri) {
    $mri_checked = " checked";
  }
  if ($patientdata->nokidney) {
    $nk_checked = " checked";
  }
  if ($patientdata->finished) {
    $finished_cls = "btn-outline markunfinished";
    $finished_btn = "Mark as Not Done";
  }
  $user = $GLOBALS["config_global_username"];



  echo <<<EOT
  <h1 class="attribute"><span class="left">Imageset ID: </span><span class="right">{$patientdata->id}</span></h1>

  <h2 class="attribute num"><span class="left">Images: </span><span class="right">{$patientdata->num_images}</span></h2>
  <h3 class="attribute unannotated"><span class="left">Unannotated: </span><span class="right">{$unannotated}</span></h3>
  <h3 class="attribute pending"><span class="left">Pending: </span><span class="right">{$pending}</span></h3>
  <h3 class="attribute valid"><span class="left">Valid: </span><span class="right">{$valid}</span></h3>
  <h3 class="attribute flagged"><span class="left">Flagged: </span><span class="right">{$flagged}</span></h3>

  <h2 class="attribute pathology"><span class="left">Pathology: </span><span class="right">{$pathology}</span></h2>
  <h3 class="attribute side g"><span class="left">Tumor Side: </span><span class="right">{$side}</span></h3>
  <h3 class="attribute stage g"><span class="left">Stage: </span><span class="right">{$stage}</span></h3>

  <h3 class="attribute marker"><span class="left">Non-Contrast: </span><span class="right"><input id="nc_check" class="marker" type="checkbox"$nc_checked /></span></h3>
  <h3 class="attribute marker"><span class="left">MRI: </span><span class="right"><input id="mri_check" class="marker" type="checkbox"$mri_checked /></span></h3>
  <h3 class="attribute marker btm"><span class="left">No Kidney: </span><span class="right"><input id="nk_check" class="marker" type="checkbox"$nk_checked /></span></h3>
  <h3 class="attribute notes"><span class="left">Notes: </span><span class="right"><textarea id="notes_ta">{$notes}</textarea></span></h3>

  <h3 class="attribute buttons"><span class="left">
    <a href="javascript:parent.jQuery.fancybox.close();" class="btn btn-danger close_fancy" >Cancel</a>
    <a href="#" id="if_save_btn" class="btn btn-primary">Save</a>
    <a href="#" id="if_finish_btn" class="btn $finished_cls">$finished_btn</a>
  </h3>

  <script type="text/javascript">
    function _(id) {return document.getElementById(id);}
    function submit_update(type) {
      v_non_contrast = (_("nc_check").checked);
      v_no_kidney = (_("nk_check").checked);
      v_mri = (_("mri_check").checked);
      v_done = (type == "done");
      v_notes = $("#notes_ta").val();
      data = {
        id:"{$patientdata->id}",
        user:"{$user}",
        non_contrast:v_non_contrast,
        no_kidney:v_no_kidney,
        mri:v_mri,
        done:v_done,
        notes:v_notes
      };
      $.post('/annotate/is_submit.php', data).done(function(response){
        resp = $.parseJSON(response);
        if (resp.error == "") { // successfully updated
          console.log(resp.msg);
          parent.jQuery.fancybox.close();
        }
        else { // an error occurred
          console.log("an error occurred");
          parent.jQuery.fancybox.close();
        }
        return false;
      });
    }
    $(document).on("click","a#if_save_btn", function() {
      submit_update("save");
    });
    $(document).on("click","a#if_finish_btn", function() {
      submit_update("done");
    });
  </script>

EOT;
}
else {
  echo <<<EOT
  <h1 class="error">An Error Occurred</h1>
EOT;
}
echo <<<EOT
  </body>
</html>
EOT;
?>
