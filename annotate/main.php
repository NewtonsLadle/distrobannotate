<?php
require_once("../config/global.php");

function annotate_main_get_notfound($patient_id) {
  return ("<h1 class='error_msg'>The imageset '" . $patient_id . "' was not found. <br><br><a href='/p/choose.php'>Choose a different imageset</a><br><a href='/p/annotate.php'>Select at random</a></h1>");
}

function annotate_main_get_error() {
  return "<h1 class='error_msg'>An error occurred. Please <a href='/p/bug.php'>report this bug</a>.</h1>";
}

function annotate_main_get_none() {
  return "<h1 class='error_msg'>No images are available for annotation at this time. Please <a href='/p/bug.php'>report this bug</a> if you believe this is an error.</h1>";
}

function annotate_main_get_image_name($loc_patientdata, $loc_image) {
  $imagenum = $loc_image + $loc_patientdata->first_image;
  if ($imagenum < 10) {
    $cat = "000";
  }
  elseif ($imagenum < 100) {
    $cat = "00";
  }
  elseif ($imagenum < 1000) {
    $cat = "0";
  }
  else {
    $cat = "";
  }
  return $cat . $imagenum . '.png';
}

function annotate_main_get_image_location($loc_patientdata, $loc_image) {
  return 'https://distrobannotate.cs.umn.edu/data/patient_data/'
          . $loc_patientdata->id . '/raw_png/' .
          annotate_main_get_image_name($loc_patientdata, $loc_image);
}

function annotate_main_get_image_class($loc_patientdata, $loc_image) {
  $class = "";

  if (in_array($loc_image, $loc_patientdata->annotated->pending)) { // annotation pending
    $class = "pending";
  }
  else if (in_array($loc_image, $loc_patientdata->annotated->valid)) { // annotation pending
    $class = "valid";
  }
  else if (in_array($loc_image, $loc_patientdata->annotated->flagged)) { // annotation pending
    $class = "flagged";
  }

  return $class;
}

function get_comp($loc_loc_image, $loc_loc_feature, $loc_loc_annotated) {
  if (in_array($loc_loc_image, $loc_loc_annotated->pending) ||
      in_array($loc_loc_image, $loc_loc_annotated->valid) ||
      in_array($loc_loc_image, $loc_loc_annotated->flagged)) {
    return TRUE;
  }
  if ($loc_loc_feature == "left_kidney") {
    if (in_array($loc_loc_image, $loc_loc_annotated->partial_lk)) {
      return TRUE;
    }
  }
  else if ($loc_loc_feature == "right_kidney") {
    if (in_array($loc_loc_image, $loc_loc_annotated->partial_rk)) {
      return TRUE;
    }
  }
  else if ($loc_loc_feature == "left_cancer") {
    if (in_array($loc_loc_image, $loc_loc_annotated->partial_lc)) {
      return TRUE;
    }
  }
  else if ($loc_loc_feature == "right_cancer") {
    if (in_array($loc_loc_image, $loc_loc_annotated->partial_rc)) {
      return TRUE;
    }
  }
  else if ($loc_loc_feature == "aorta") {
    if (in_array($loc_loc_image, $loc_loc_annotated->partial_ao)) {
      return TRUE;
    }
  }
  return FALSE;
}

function annotate_main_get_slider($loc_patientdata, $loc_scale, $loc_image, $loc_feature, $loc_type) {
  $ret = "";
  if ($loc_type == "annotate") {
    $ret = $ret . <<<EOT
    <div id="patient_info">
      <div id="images_slider">
        <table>
          <tbody>
            <tr>
EOT;
    $i = 0;
    while ($i < $loc_patientdata->num_images) {
      $img = annotate_main_get_image_location($loc_patientdata, $i);
      if ($i != $loc_image) {
        $imgclass = annotate_main_get_image_class($loc_patientdata, $i);
      }
      else {
        $imgclass = "sel";
      }
      $urlparams = array(
        'feature' => 0,
        'patient' => $loc_patientdata->id,
        'image' => $i,
        'scale' => $loc_scale
      );
      $query = http_build_query($urlparams);
      $ret = $ret . <<<EOT
              <td>
                <a href="/p/annotate.php?{$query}" class="patient_image {$imgclass}"><img src="{$img}" height="35px" class="{$imgclass} process_me"></img></a>
              </td>
EOT;
      $i = $i + 1;
    }

    $ret = $ret . <<<EOT
            </tr>
          </tbody>
        </table>
      </div>
    </div>
EOT;
  }
  else { // validate
    $ret = $ret . <<<EOT
    <div id="patient_info">
      <div id="images_slider">
        <table>
          <tbody>
            <tr>
EOT;
    $i = 0;
    while ($i < $loc_patientdata->num_images) {
      $img = annotate_main_get_image_location($loc_patientdata, $i);
      if ($i != $loc_image) {
        $imgclass = annotate_main_get_image_class($loc_patientdata, $i);
      }
      else {
        $imgclass = "sel";
      }
      $urlparams = array(
        'feature' => 0,
        'patient' => $loc_patientdata->id,
        'image' => $i,
        'scale' => $loc_scale
      );
      $query = http_build_query($urlparams);
      $ret = $ret . <<<EOT
              <td>
                <a href="/p/validate.php?{$query}" class="patient_image {$imgclass}"><img class="process_me" src="{$img}" height="35px" class="{$imgclass}"></img></a>
              </td>
EOT;
      $i = $i + 1;
    }

    $ret = $ret . <<<EOT
            </tr>
          </tbody>
        </table>
      </div>
    </div>
EOT;
  }
  return $ret;
}

function annotate_main_get_toolbar($loc_patientdata, $loc_scale, $loc_image, $loc_feature) {
  $patientdata = json_encode($loc_patientdata);
  $loc_imagename = annotate_main_get_image_name($loc_patientdata, $loc_image);
  $scaled_width = $loc_scale*$loc_patientdata->width;
  $scaled_height = $loc_scale*$loc_patientdata->height;
  if ($loc_feature == 0) {
    $style = "";
  }
  if ($loc_feature == 1) {
    $style = "g";
  }
  if ($loc_feature == 2) {
    $style = "b";
  }
  if ($loc_feature == 3) {
    $style = "y";
  }
  $prev_urlparams = array(
    'feature' => 0,
    'patient' => $loc_patientdata->id,
    'image' => $loc_image-1,
    'scale' => $loc_scale
  );
  $prev_query = http_build_query($prev_urlparams);
  $next_urlparams = array(
    'feature' => 0,
    'patient' => $loc_patientdata->id,
    'image' => $loc_image+1,
    'scale' => $loc_scale
  );
  $next_query = http_build_query($next_urlparams);
  if (!($scrollval = $_GET["scrollval"])) {
     $scrollval = 0;
  }
	if (!($scrollvalx = $_GET["scrollvalx"])) {
     $scrollvalx = 0;
  }
	if (!($scrollvaly = $_GET["scrollvaly"])) {
     $scrollvaly = 0;
  }
  $ret = <<<EOT
  <script type="text/javascript">
    var imgdata = $patientdata;
    imgdata.scale = $loc_scale;
    imgdata.style = $loc_feature;
    imgdata.image_name = "$loc_imagename";
    imgdata.image_ind = $loc_image;
    imgdata.user = "{$GLOBALS["config_global_username"]}";
    imgdata.feature = $loc_feature;
    imgdata.scrollval = $scrollval;
		imgdata.scrollvalx = $scrollvalx;
		imgdata.scrollvaly = $scrollvaly;
  </script>
  <div id="ctrl_bar">
    <div id="toolbox">
EOT;
  if ($loc_image != 0) {
    $ret = $ret . <<<EOT
      <a id="prevbutton" href="/p/annotate.php?{$prev_query}" class="btn btn-outline btn-primary">Previous</a>
EOT;
  }
  $ret = $ret . <<<EOT
      <a href="#" id="icon10_a" class="tool"><img id="icon10_img" src="/kidneycancer/resources/images/kidneycancer/tools/{$style}icon10.png" /></a>
      <a href="#" id="icon0_a" class="tool"><img id="icon0_img" src="/kidneycancer/resources/images/kidneycancer/tools/{$style}icon0.png" /></a>
EOT;
  if ($loc_image != ($loc_patientdata->num_images-1)) {
    $ret = $ret . <<<EOT
      <a id="nextbutton" href="/p/annotate.php?{$next_query}" class="btn btn-outline btn-primary">Next</a>
EOT;
  }
    $myarr = array(
      "left_kidney" => $loc_patientdata->features->left_kidney,
      "right_kidney" => $loc_patientdata->features->right_kidney,
      "left_cancer" => $loc_patientdata->features->left_cancer,
      "right_cancer" => $loc_patientdata->features->right_cancer,
      "aorta" => $loc_patientdata->features->aorta
    );
    foreach ($myarr as $key => $value) {
      if ($key == "left_kidney") {
        $name = $GLOBALS["config_annotation_left_kidney_name"];
      }
      if ($key == "right_kidney") {
        $name = $GLOBALS["config_annotation_right_kidney_name"];
      }
      if ($key == "left_cancer") {
        $name = $GLOBALS["config_annotation_left_cancer_name"];
      }
      if ($key == "right_cancer") {
        $name = $GLOBALS["config_annotation_right_cancer_name"];
      }
      if ($key == "aorta") {
        $name = $GLOBALS["config_annotation_aorta_name"];
      }
      if ($value->exists != 0) {
        $comp = "";
        if (get_comp($loc_image, $key, $loc_patientdata->annotated)) {
          $comp = " comp";
        }
        $ret = $ret . <<<EOT
    <button type="button" id="submit_{$key}" class="submit_feature btn btn-primary{$comp}">{$name} <i class="fa fa-check fa-fw"></i></button>
EOT;
    }
  }
  $ret = $ret . "</div>";
  $ret = $ret . annotate_main_get_slider($loc_patientdata, $loc_scale, $loc_image, $loc_feature, "annotate");
  return $ret . '</div>';
}

//stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}

function get_time_from_option($option) {
  $array = explode('_',$option);
  return intval($array[0]);
}

function annotate_main_get_full($dir, $postfix, $path, $feature, $loc_image, $loc_patientdata) {
  $go = 0;
  $ret = 'https://www.atwork.ca/wp-content//uploads/2015/08/Light-Grey-Square.png';
  if (in_array($loc_image, $loc_patientdata->annotated->pending) || in_array($loc_image, $loc_patientdata->annotated->flagged) || in_array($loc_image, $loc_patientdata->annotated->valid)) {
    $go = 1;
  }
  else {
    if (($feature == "left_kidney") && (in_array($loc_image, $loc_patientdata->annotated->partial_lk))) {
      $go = 1;
    }
    if (($feature == "right_kidney") && (in_array($loc_image, $loc_patientdata->annotated->partial_rk))) {
      $go = 1;
    }
    if (($feature == "left_cancer") && (in_array($loc_image, $loc_patientdata->annotated->partial_lc))) {
      $go = 1;
    }
    if (($feature == "right_cancer") && (in_array($loc_image, $loc_patientdata->annotated->partial_rc))) {
      $go = 1;
    }
    if (($feature == "aorta") && (in_array($loc_image, $loc_patientdata->annotated->partial_ao))) {
      $go = 1;
    }
  }
  if ($go == 1) {
    $directory = dir($dir);
    $options = array();
    while (false !== ($entry = $directory->read())) {
      if (endsWith($entry, $postfix)) {
        array_push($options, $entry);
      }
    }
    $max_time = 0;
    $i = 0;
    foreach ($options as $option) {
      if (($new_time = get_time_from_option($option)) > $max_time) {
        $ret = $path . $option;
        $max_time = $new_time;
      }
      $i = $i + 1;
    }
  }
  return $ret;
}



function annotate_main_get_annotated($loc_patientdata, $loc_image) {
  $features = array("left_kidney", "right_kidney", "left_cancer", "right_cancer", "aorta");
  $ret = 'https://distrobannotate.cs.umn.edu/data/patient_data/'
          . $loc_patientdata->id . '/annotated/';
  $dir = '../data/patient_data/' . $loc_patientdata->id . '/annotated';

  $ret_array = array();
  foreach ($features as $feature) {
    $postfix = $feature . '_' . annotate_main_get_image_name($loc_patientdata, $loc_image);
    $full = annotate_main_get_full($dir, $postfix, $ret, $feature, $loc_image, $loc_patientdata);
    array_push($ret_array, $full);// . $postfix);
  }

  return $ret_array;

}

function annotate_main_get_flag_notes($patient, $image) {
  $file = $GLOBALS['config_global_webroot'] . "data/flags.json";
  if (($flags = json_decode(file_get_contents($file))) !== FALSE) {
    $ret = array();
    foreach ($flags as $flag) {
      if (!($flag->fixed) && ($flag->patient == $patient->id) && ($flag->image == $image)) {
        $ret[$flag->feature] = array(
          "notes" => $flag->notes,
          "user" => $flag->user
        );
      }
    }
    return $ret;
  }
}

function annotate_main_get_payload($loc_patientdata, $loc_scale, $loc_image, $loc_feature) {
  $scaled_width = $loc_scale*$loc_patientdata->width;
  $scaled_height = $loc_scale*$loc_patientdata->height;
  $img = annotate_main_get_image_location($loc_patientdata, $loc_image);
  $ret = <<<EOT
  <div id="annotator_cont" class="brushwidth20" oncontextmenu="return false;"
       style="width:{$scaled_width}px;height:{$scaled_height}px">
     <img id="to_annotate" src="$img" width="$scaled_width" height="$scaled_height" class="process_me"/>
     <canvas id="annotator" width="$scaled_width" height="$scaled_height"></canvas>
  </div>
EOT;
  $flags = annotate_main_get_flag_notes($loc_patientdata, $loc_image);
  $annotations = annotate_main_get_annotated($loc_patientdata, $loc_image);
  $left_kidney_flag_note = $flags["left_kidney"]["notes"];
  $right_kidney_flag_note = $flags["right_kidney"]["notes"];
  $left_cancer_flag_note = $flags["left_cancer"]["notes"];
  $right_cancer_flag_note = $flags["right_cancer"]["notes"];
  $aorta_flag_note = $flags["aorta"]["notes"];
  // Authors
  if (isset($left_kidney_flag_note))
    $left_kidney_flag_author = '-' .  config_users_get_data($flags["left_kidney"]["user"])->first_name;
  if (isset($right_kidney_flag_note))
    $right_kidney_flag_author = '-' .  config_users_get_data($flags["right_kidney"]["user"])->first_name;
  if (isset($left_cancer_flag_note))
    $left_cancer_flag_author = '-' . config_users_get_data($flags["left_cancer"]["user"])->first_name;
  if (isset($right_cancer_flag_note))
    $right_cancer_flag_author = '-' . config_users_get_data($flags["right_cancer"]["user"])->first_name;
  if (isset($aorta_flag_note))
    $aorta_flag_author = '-' . config_users_get_data($flags["aorta"]["user"])->first_name;
  $ret = $ret . <<<EOT
  <div class="finished_annotation">
    <div id="left_kidney_finished" class="fa_image">
      <img src="$img" height="180px" width="180px" class="process_me"></img>
      <img src="{$annotations[0]}" height="180px" width="180px"></img>
      <p class="fa_lbl">
        Left Kidney
        <span class="flag_note"><br>{$left_kidney_flag_note}<br>{$left_kidney_flag_author}</span>
      </p>
    </div>
    <div class="finished_divider"></div>
    <div id="right_kidney_finished" class="fa_image">
      <img src="$img" height="180px" width="180px" class="process_me"></img>
      <img src="{$annotations[1]}" height="180px" width="180px"></img>
      <p class="fa_lbl">
        Right Kidney
        <span class="flag_note">{$right_kidney_flag_note}<br>{$right_kidney_flag_author}</span>
      </p>
    </div>
    <div class="finished_divider"></div>
    <div id="left_cancer_finished" class="fa_image">
      <img src="$img" height="180px" width="180px" class="process_me"></img>
      <img src="{$annotations[2]}" height="180px" width="180px"></img>
      <p class="fa_lbl">
        Left Mass
        <span class="flag_note">{$left_cancer_flag_note}<br>{$left_cancer_flag_author}</span>
      </p>
    </div>
    <div class="finished_divider"></div>
    <div id="right_cancer_finished" class="fa_image">
      <img src="$img" height="180px" width="180px" class="process_me"></img>
      <img src="{$annotations[3]}" height="180px" width="180px"></img>
      <p class="fa_lbl">
        Right Mass
        <span class="flag_note">{$right_cancer_flag_note}<br>{$right_cancer_flag_author}</span>
      </p>
    </div>
    <div class="finished_divider"></div>
    <div id="aorta_finished" class="fa_image">
      <img src="$img" height="180px" width="180px" class="process_me"></img>
      <img src="{$annotations[4]}" height="180px" width="180px"></img>
      <p class="fa_lbl">
        Aorta
        <span class="flag_note">{$aorta_flag_note}<br>{$aorta_flag_author}</span>
      </p>
    </div>
  </div>
EOT;
  return $ret;
}
?>
