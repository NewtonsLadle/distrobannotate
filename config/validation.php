<?php
function config_validation_get_next_patient($loc_patient_id) {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    if (isset($loc_patient_id) && ($loc_patient_id != "")) {
      foreach ($patients as $patient) {
        if ($patient->id == $loc_patient_id) {
          if ((count($patient->annotated->pending) > 0)) {
            return $patient;
          }
        }
        else {
          return 0; // Patient not valid
        }
      }
      return 0; // Patient not found
    }
    else {
      foreach ($patients as $patient) {
        if ((count($patient->annotated->pending) > 0)) {
          return $patient;
        }
      }
      return 0; // No patients to annotate
    }
  }
  else {
    return 0; // Error reading file
  }
}

function config_validation_get_next_image($patient, $loc_image) {
  if (in_array($loc_image, $patient->annotated->pending) ||
      in_array($loc_image, $patient->annotated->valid) ||
      in_array($loc_image, $patient->annotated->flagged)) {
    return $loc_image;
  }
  else {
    return $patient->annotated->pending[0];
  }
}

function config_validation_get_next_imagepath($patient, $image) {
  $imagenum = $patient->first_image + $image;
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
  $imagefile = $cat . $imagenum;
  $fullimagefile = '/data/patient_data/' . $patient->id . '/raw_png/' . $imagefile . '.png';
  return $fullimagefile;
}

function is_flagged($patient, $image, $feature, $flags) {
  foreach ($flags as $flag) {
    //echo $flag->feature;
    //echo $flag->patient;
    //echo $flag->image;
    //echo $flag->handled;
    //echo "|";
    if (($flag->patient == $patient->id) &&
        ($flag->image == $image) &&
        ($flag->feature == $feature) &&
        ($flag->handled == FALSE)) {
      //echo $feature . " IS FLAGGED";
      return TRUE;
    }
  }
  //echo $feature . " IS NOT FLAGGED";
  return FALSE;
}

function config_validation_get_featureid($feature) {
  if ($feature == 0) {
    return "left_kidney";
  }
  elseif ($feature == 1) {
    return "right_kidney";
  }
  elseif ($feature == 2) {
    return "left_cancer";
  }
  elseif ($feature == 3) {
    return "right_cancer";
  }
  elseif ($feature == 4) {
    return "aorta";
  }
  return "";
}

function config_validation_get_next_feature($patient, $image, $loc_feature) {
  $loc_feature = config_validation_get_featureid($loc_feature);
  if (isset($loc_feature) && ($loc_feature != "")) {
    return $loc_feature;
  }
  $file = $GLOBALS['config_global_webroot'] . "data/flags.json";
  if (($flags = json_decode(file_get_contents($file))) !== FALSE) {
    if (!in_array($image, $patient->annotated->valid_lk) && !is_flagged($patient, $image, "left_kidney", $flags)) {
      return "left_kidney";
    }
    if (!in_array($image, $patient->annotated->valid_rk) && !is_flagged($patient, $image, "right_kidney", $flags)) {
      return "right_kidney";
    }
    if (!in_array($image, $patient->annotated->valid_lc) && !is_flagged($patient, $image, "left_cancer", $flags)) {
      return "left_cancer";
    }
    if (!in_array($image, $patient->annotated->valid_rc) && !is_flagged($patient, $image, "right_cancer", $flags)) {
      return "right_cancer";
    }
    if (!in_array($image, $patient->annotated->valid_ao) && !is_flagged($patient, $image, "aorta", $flags)) {
      return "aorta";
    }
  }
  return "error";
}

# http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
function config_validation_endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}

function config_validation_get_recent_annotation($suffix, $directory) {
  $full_directory = $GLOBALS["config_global_webroot"] . $directory;
  $contents = scandir($full_directory);
  $maxtime = 0;
  foreach ($contents as $file) {
    if (config_validation_endsWith($file, $suffix)) {
      $time = (int)explode('_', $file)[0];
      if ($time > $maxtime) {
        $maxtime = $time;
      }
    }
  }
  return $maxtime;
}

function config_validation_get_next_annotation($patient, $image, $feature) {
  $imagenum = $patient->first_image + $image;
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
  $imagefile = '_' . $patient->user . '_' . $feature . '_' . $cat . $imagenum . ".png";
  $most_recent = config_validation_get_recent_annotation($imagefile, 'data/patient_data/' . $patient->id . '/annotated/');
  return "/data/patient_data/" . $patient->id . "/annotated/" . $most_recent . $imagefile;
}

function config_validation_get_featurename($feature) {
  $feature = trim($feature);
  if ($feature == "left_kidney") {
    return "Left Kidney";
  }
  if ($feature == "right_kidney") {
    return "Right Kidney";
  }
  if ($feature == "left_cancer") {
    return "Left Mass";
  }
  if ($feature == "right_cancer") {
    return "Right Mass";
  }
  if ($feature == "aorta") {
    return "Aorta";
  }
  return "";
}

function config_validation_get_next($loc_patient_id, $loc_image, $loc_feature) {
  if (($patient = config_validation_get_next_patient($loc_patient_id)) != 0) {
    $image = config_validation_get_next_image($patient, $loc_image);
    $fullimagefile = config_validation_get_next_imagepath($patient, $image);
    $feature = config_validation_get_next_feature($patient, $image, $loc_feature);
    $annotation = config_validation_get_next_annotation($patient, $image, $feature);
    $featurename = config_validation_get_featurename($feature);
    return array(
      "patient" => $patient,
      "image" => $image,
      "feature" => $feature,
      "imagefile" => $fullimagefile,
      "annotationfile" => $annotation,
      "featurename" => $featurename,
      "height" => $patient->height,
      "width" => $patient->width
    );
  }
  else {
    return 0;
  }
}

function config_validation_mixed_valid_done($loc_patient, $loc_image) {
  $file = $GLOBALS['config_global_webroot'] . "data/flags.json";
  if (($flags = json_decode(file_get_contents($file))) !== FALSE) {
    $flag_ind = 0;
    $fcovered_features = [];
    foreach ($flags as $flag) {
      $flag_ind = $flag_ind + 1;
      if (($flag->patient == $loc_patient->id) && ($flag->image == $loc_image) && ($flag->handled == FALSE)) {
        array_push($fcovered_features, $flag->feature);
      }
    }
    $vcovered_features = [];
    if (in_array($loc_image, $loc_patient->annotated->valid_lk)) {
      array_push($vcovered_features, "left_kidney");
    }
    if (in_array($loc_image, $loc_patient->annotated->valid_rk)) {
      array_push($vcovered_features, "right_kidney");
    }
    if (in_array($loc_image, $loc_patient->annotated->valid_lc)) {
      array_push($vcovered_features, "left_cancer");
    }
    if (in_array($loc_image, $loc_patient->annotated->valid_rc)) {
      array_push($vcovered_features, "right_cancer");
    }
    if (in_array($loc_image, $loc_patient->annotated->valid_ao)) {
      array_push($vcovered_features, "aorta");
    }
    return array(
      "flagged" => $fcovered_features,
      "valid" => $vcovered_features
    );
  }
}

function config_validation_delete_from_array($array, $index) {
  $temp = $array[0];
  $array[$index] = $array[0];
  $array[0] = $temp;
  array_shift($array);
  return $array;
}

function config_validation_handle_if_done($patient, $image, $patients, $patient_ind) {
  // Move full partials to valid
  if (in_array($image, $patients[$patient_ind]->annotated->valid_lk) &&
      in_array($image, $patients[$patient_ind]->annotated->valid_lc) &&
      in_array($image, $patients[$patient_ind]->annotated->valid_rk) &&
      in_array($image, $patients[$patient_ind]->annotated->valid_rc) &&
      in_array($image, $patients[$patient_ind]->annotated->valid_ao)) {
    //echo "FULLY VALID";
    // Add image to valid
    array_push($patients[$patient_ind]->annotated->valid, (int)$image);
    // Remove image from partials
    $key_lk = array_search($image ,$patients[$patient_ind]->annotated->valid_lk);
    $key_lc = array_search($image ,$patients[$patient_ind]->annotated->valid_lc);
    $key_rk = array_search($image ,$patients[$patient_ind]->annotated->valid_rk);
    $key_rc = array_search($image ,$patients[$patient_ind]->annotated->valid_rc);
    $key_ao = array_search($image ,$patients[$patient_ind]->annotated->valid_ao);
    $patients[$patient_ind]->annotated->valid_lk =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_lk, $key_lk);
    $patients[$patient_ind]->annotated->valid_rk =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_rk, $key_rk);
    $patients[$patient_ind]->annotated->valid_lc =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_lc, $key_lc);
    $patients[$patient_ind]->annotated->valid_rc =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_rc, $key_rc);
    $patients[$patient_ind]->annotated->valid_ao =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_ao, $key_ao);
    // Remove image from pending
    $key_pending = array_search($image,
                $patients[$patient_ind]->annotated->pending);
    $patients[$patient_ind]->annotated->pending =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->pending, $key_pending);
  }
  else {
    $statuses = config_validation_mixed_valid_done($patients[$patient_ind], $image);
    //echo "STATUSES[flagged]: " . count($statuses["flagged"]);
    //echo "STATUSES[valid]: " . count($statuses["valid"]);

    if (count($statuses["flagged"]) + count($statuses["valid"]) == 5) {
      //echo "HANDLING CUZ DONE";
      // remove image from pending
      $key_pending = array_search($image, $patients[$patient_ind]->annotated->pending);
      $patients[$patient_ind]->annotated->pending =
                config_validation_delete_from_array($patients[$patient_ind]->annotated->pending, $key_pending);

      // mark partials for valid features
      foreach ($statuses["valid"] as $valid_feature) {
        if ($valid_feature == "left_kidney") {
          $key = array_search($image ,
                      $patients[$patient_ind]->annotated->valid_lk);
          $patients[$patient_ind]->annotated->valid_lk =
                      config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_lk, $key);
          array_push($patients[$patient_ind]->annotated->partial_lk, (int)$image);
        }
        if ($valid_feature == "right_kidney") {
          $key = array_search($image ,$patients[$patient_ind]->annotated->valid_rk);
          $patients[$patient_ind]->annotated->valid_rk =
                      config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_rk, $key);
          array_push($patients[$patient_ind]->annotated->partial_rk, (int)$image);
        }
        if ($valid_feature == "left_cancer") {
          $key = array_search($image ,
                      $patients[$patient_ind]->annotated->valid_lc);
          $patients[$patient_ind]->annotated->valid_lc =
                      config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_lc, $key);
          array_push($patients[$patient_ind]->annotated->partial_lc, (int)$image);
        }
        if ($valid_feature == "right_cancer") {
          $key = array_search($image ,
                      $patients[$patient_ind]->annotated->valid_rc);
          $patients[$patient_ind]->annotated->valid_rc =
                      config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_rc, $key);
          array_push($patients[$patient_ind]->annotated->partial_rc, (int)$image);
        }
        if ($valid_feature == "aorta") {
          $key = array_search($image ,
                      $patients[$patient_ind]->annotated->valid_ao);
          $patients[$patient_ind]->annotated->valid_ao =
                      config_validation_delete_from_array($patients[$patient_ind]->annotated->valid_ao, $key);
          array_push($patients[$patient_ind]->annotated->partial_ao, (int)$image);
        }
      }
      $flag_file = $GLOBALS['config_global_webroot'] . "data/flags.json";
      if (($flags = json_decode(file_get_contents($flag_file))) !== FALSE) {
        foreach ($statuses["flagged"] as $flagged_feature) {
          $flag_ind = 0;
          foreach($flags as $flag) {
            if (($flag->patient == $patient) && ($flag->image == $image) && ($flag->feature == $flagged_feature) && !($flag->handled)) {
              //echo "SETTING " . $flagged_feature . " HANDLED";
              $flags[$flag_ind]->handled = TRUE;
            }
            $flag_ind = $flag_ind + 1;
          }
          reset($flags);
        }
        file_put_contents($flag_file, json_encode($flags));
      }
    }
  }
  return $patients;
}

function config_validation_set_valid($patient, $image, $feature) {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    $patient_ind = 0;
    foreach($patients as $value) {
      if ($value->id == $patient) {
        break;
      }
      $patient_ind = $patient_ind + 1;
    }
    if ($feature == "left_kidney") {
      // Add image to partial_lk
      array_push($patients[$patient_ind]->annotated->valid_lk, (int)$image);
    }
    else if ($feature == "right_kidney") {
      // Add image to partial_rk
      array_push($patients[$patient_ind]->annotated->valid_rk, (int)$image);
    }
    else if ($feature == "left_cancer") {
      // Add image to partial_lc
      array_push($patients[$patient_ind]->annotated->valid_lc, (int)$image);
    }
    else if ($feature == "right_cancer") {
      // Add image to partial_rc
      array_push($patients[$patient_ind]->annotated->valid_rc, (int)$image);
    }
    else if ($feature == "aorta") {
      // Add image to partial_ao
      array_push($patients[$patient_ind]->annotated->partial_ao, (int)$image);
    }

    $patients = config_validation_handle_if_done($patient, $image, $patients, $patient_ind);

    // Write new data to file
    $newJSONstring = json_encode($patients);
    if (intval($patients[$patient_ind]->width) > 0) { // keep from losing data
      if (file_put_contents($file, $newJSONstring, LOCK_EX) !== FALSE) {
        return 0;
      }
      else {
        // error writing file
        return 2;
      }
    }
    else {
      // either data is corrupt or file was not correctly understood
      return 3;
    }
  }
  else {
    // error reading file
    return 1;
  }
}

function config_validation_write_patients($patients) {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  file_put_contents($file, json_encode($patients));
  return;
}

function config_validation_get_patients() {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    return $patients;
  }
  return;
}

function config_validation_get_patient_ind($patients, $loc_patient) {
  $patient_ind = 0;
  foreach ($patients as $patient) {
    if ($patient->id == $loc_patient) {
      return $patient_ind;
    }
    $patient_ind = $patient_ind + 1;
  }
}

function config_validation_set_flag($patient, $image, $feature, $notes) {
  $flag = array(
    "patient" => $patient,
    "image" => $image,
    "feature" => $feature,
    "notes" => $notes,
    "handled" => FALSE,
    "fixed" => FALSE,
    "user" => $GLOBALS["config_global_username"]
  );
  $file = $GLOBALS['config_global_webroot'] . "data/flags.json";
  if (($flags = json_decode(file_get_contents($file))) !== FALSE) {
    array_push($flags, $flag);
    file_put_contents($file, json_encode($flags));
    $patients = config_validation_get_patients();
    $patient_ind = config_validation_get_patient_ind($patients, $patient);
    $patients = config_validation_handle_if_done($patient, $image, $patients, $patient_ind);
    config_validation_write_patients($patients);
    return TRUE;
  }
  else {
    return FALSE;
  }
}
?>
