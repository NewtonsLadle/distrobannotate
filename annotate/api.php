<?php
function isnt_finished($patient) {
  $annotated = count($patient->annotated->pending) + count($patient->annotated->valid) + count($patient->annotated->flagged);
  return ($annotated < $patient->num_images);
}

// takes username of person making request
// returns patient object for that person to annotate
function annotate_api_get_patient($username, $loc_patient_id) {
  $users = json_decode(file_get_contents($GLOBALS['config_global_webroot'] . "data/users.json"));
  $patients = json_decode(file_get_contents($GLOBALS['config_global_webroot'] . "data/patients.json"));
  if (($loc_patient_id != "") && isset($loc_patient_id)) {
    echo "<!-- patient given -->";
    $users_i = 0;
    foreach ($users as $user) {
      if ($user->x500 == $username) {
        $users_fd = $users_i;
        $patients_i = 0;
        foreach ($patients as $patient) {
          if ($patient->id == $loc_patient_id) {
            $patients_fd = $patients_i;
            #found user and patient. Will return
            $patients[$patients_fd]->user = $username;
            $patients[$patients_fd]->last_active = time();
            $users[$users_fd]->current_patient = $patient->id;
            file_put_contents($GLOBALS['config_global_webroot'] . "data/users.json", json_encode($users));
            file_put_contents($GLOBALS['config_global_webroot'] . "data/patients.json", json_encode($patients));
            return $patient;
          }
          $patients_i = $patients_i + 1;
        }
        #didn't find patient. Will return
        return "notfound";
      }
      $users_i = $users_i + 1;
    }
    #didn't find user. Will return
    return "error";
  }
  else {
    $found = 0;
    $users_i = 0;
    $users_fd = 0;
    $patients_i = 0;
    $other_found = 0;
    $check_found = 0;
    foreach($users as $value) {
      if ($value->x500 == $username) {
        $found = 1;
        $users_fd = $users_i;
        if ($value->current_patient != "") {
          $ret_id = $value->current_patient;
          foreach ($patients as $value1) {
            if (($value1->id == $ret_id) && ($value1->user == $username)) {
              $check_found = 1;
              echo '<!-- patient found -->';
              if (isnt_finished($value1)) {
                $other_found = 1;
                $ret = $value1;
                break;
              }
            }
            else {
              //echo $value1->id . '|' . $ret_id;
              //echo $value1->user . '|' . $username;
            }
            $patients_i = $patients_i + 1;
          }
          if ($check_found == 0) {
            echo "<!-- A problem occurred (1) -->";
            return "error";
          }
        }
        if ($other_found == 0) {
          $patients_i = 0;
          foreach ($patients as $value) {
            if ($value->user == "") {
              $other_found = 1;
              $ret = $value;
              //TODO Need to set and flush new info here
              $patients[$patients_i]->user = $username;
              $patients[$patients_fd]->last_active = time();
              $users[$users_fd]->current_patient = $value->id;
              file_put_contents($GLOBALS['config_global_webroot'] . "data/users.json", json_encode($users));
              file_put_contents($GLOBALS['config_global_webroot'] . "data/patients.json", json_encode($patients));
              echo '<!-- patient assigned -->';
              break;
            }
            $patients_i = $patients_i + 1;
          }
          if ($other_found == 0) {
            return "none";
          }
        }
        break;
      }
      $users_i = $users_i + 1;
    }
    if ($found == 0) {
      echo "<!-- A problem occurred (2) -->";
      return "error";
    }
    return $ret;
  }
}

function annotate_api_fix_flag($patient, $image, $feature) {
  $file = $GLOBALS['config_global_webroot'] . "data/flags.json";
  if (($flags = json_decode(file_get_contents($file))) !== FALSE) {
    $flag_ind = 0;
    foreach ($flags as $flag) {
      if (($flag->patient == $patient) && ($flag->image == $image) && ($flag->feature == $feature)) {
        $flags[$flag_ind]->fixed = TRUE;
      }
      $flag_ind = $flag_ind + 1;
    }
    // write flag data to file
    file_put_contents($file, json_encode($flags));
  }
}

function annotate_api_new_annotation($username, $feature, $patient, $image) {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    $patient_ind = 0;
    foreach($patients as $value) {
      if ($value->id == $patient) {
        break;
      }
      $patient_ind = $patient_ind + 1;
    }
    if ($key = array_search($image, $patients[$patient_ind]->annotated->pending)) {
      // Nothing to be done.
    }
    else if ($key = array_search($image, $patients[$patient_ind]->annotated->valid)) {
      // Move image to pending
      array_push($patients[$patient_ind]->annotated->pending, $image);
      unset($patients[$patient_ind]->annotated->valid[$key]);
    }
    else if ($key = array_search($image, $patients[$patient_ind]->annotated->flagged)) {
      // Move image to pending
      array_push($patients[$patient_ind]->annotated->pending, $image);
      unset($patients[$patient_ind]->annotated->flagged[$key]);
    }
    else if ($feature == "left_kidney") {
      // Add image to partial_lk
      array_push($patients[$patient_ind]->annotated->partial_lk, $image);
    }
    else if ($feature == "right_kidney") {
      // Add image to partial_rk
      array_push($patients[$patient_ind]->annotated->partial_rk, $image);
    }
    else if ($feature == "left_cancer") {
      // Add image to partial_lc
      array_push($patients[$patient_ind]->annotated->partial_lc, $image);
    }
    else if ($feature == "right_cancer") {
      // Add image to partial_rc
      array_push($patients[$patient_ind]->annotated->partial_rc, $image);
    }
    else if ($feature == "aorta") {
      // Add image to partial_ao
      array_push($patients[$patient_ind]->annotated->partial_ao, $image);
    }

    // Mark any flag as fixed
    annotate_api_fix_flag($patient, $image, $feature);

    // Move full partials to pending
    if (in_array($image, $patients[$patient_ind]->annotated->partial_lk) &&
        in_array($image, $patients[$patient_ind]->annotated->partial_lc) &&
        in_array($image, $patients[$patient_ind]->annotated->partial_rk) &&
        in_array($image, $patients[$patient_ind]->annotated->partial_rc) &&
        in_array($image, $patients[$patient_ind]->annotated->partial_ao)) {
      // Add image to pending
      array_push($patients[$patient_ind]->annotated->pending, $image);
      // Remove image from partials
      $key_lk = array_search($image ,$patients[$patient_ind]->annotated->partial_lk);
      $key_lc = array_search($image ,$patients[$patient_ind]->annotated->partial_lc);
      $key_rk = array_search($image ,$patients[$patient_ind]->annotated->partial_rk);
      $key_rc = array_search($image ,$patients[$patient_ind]->annotated->partial_rc);
      $key_rc = array_search($image ,$patients[$patient_ind]->annotated->partial_ao);
      unset($patients[$patient_ind]->annotated->partial_lk[$key_lk]);
      unset($patients[$patient_ind]->annotated->partial_lc[$key_lc]);
      unset($patients[$patient_ind]->annotated->partial_rk[$key_rk]);
      unset($patients[$patient_ind]->annotated->partial_rc[$key_rc]);
      unset($patients[$patient_ind]->annotated->partial_ao[$key_ao]);
    }
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
?>
