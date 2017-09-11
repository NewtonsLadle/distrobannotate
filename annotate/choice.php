<?php
require_once('../config/security.php');
require_once('../config/global.php');
require_once('../config/framework.php');
require_once('../config/annotation.php');

function get_patients() {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    return $patients;
  }
  else {
    return "error";
  }
}

function get_current_patient($username) {
  $users = json_decode(file_get_contents($GLOBALS['config_global_webroot'] . "data/users.json"));
  foreach ($users as $user) {
    if ($user->x500 == $username) {
      return $user->current_patient;
    }
  }
  return "error";
}

function begin_patients_list() {
  return <<<EOT
    <div id="patients_list">
EOT;
}

function end_patients_list() {
  return <<<EOT
    </div>
EOT;
}

function get_last_time($etime, $username) {
  if ($username) {
    if (($tdif = time() - $etime) < 3600)
    return intval($tdif/60) . " minutes ago";
  }
  return '';
}

function get_last_active($patient) {
  $name = '';
  if (($time = get_last_time($patient->last_active, $patient->user)) != '') {
    $user = config_users_get_data($patient->user);
    $name = $user->first_name . ' ' . $user->last_name;
  }
  return <<<EOT
  <div id="last_active">
    <div id="last_active_name">
      $name
    </div>
    <div id="last_active_time">
      $time
    </div>
  </div>
EOT;
}

function encode_patient($patient, $current_patient) {
  $pending = count($patient->annotated->pending);
  $valid = count($patient->annotated->valid);
  $flagged = count($patient->annotated->flagged);
  $unannotated = $patient->num_images - $pending - $valid - $flagged;
  $current_class = "";
  if ($current_patient == $patient->id) {
    $current_class = " cur";
  }
  $ret = <<<EOT
  <div href="#" class="patient{$current_class}" id="1">
    <h1 class="patient_id">
      {$patient->id}
    </h1>
    <div class="patient_stat unannotated">
      <div class="patient_stat_num">{$unannotated}</div>
      <div class="patient_stat_label">Unannotated</div>
    </div>
    <div class="patient_stat pending">
      <div class="patient_stat_num">{$pending}</div>
      <div class="patient_stat_label">Pending</div>
    </div>
    <div class="patient_stat valid">
      <div class="patient_stat_num">{$valid}</div>
      <div class="patient_stat_label">Valid</div>
    </div>
    <div class="patient_stat flagged">
      <div class="patient_stat_num">{$flagged}</div>
      <div class="patient_stat_label">Flagged</div>
    </div>
EOT;

    $ret = $ret . get_last_active($patient);

    $ret = $ret . <<<EOT
    <div class="patient_buttons">
      <a href="/p/annotate.php?patient={$patient->id}" class="btn-primary btn-outline annotate">Annotate</a>
      <a href="/p/validate.php?patient={$patient->id}" class="btn-primary btn-outline validate">Validate</a>
    </div>
  </div>
EOT;
    return $ret;
}

function annotate_choice_get_patients() {
  if (($patients = get_patients()) != "error") {
    if (($current_patient = get_current_patient($GLOBALS["config_global_username"])) != "error") {
      $ret = begin_patients_list();
      foreach ($patients as $patient) {
        $ret = $ret . encode_patient($patient, $current_patient);
      }
      $ret = $ret . end_patients_list();
      return $ret;
    }
    else {
      echo '<!-- Unable to find user in db -->';
      return "error";
    }
  }
  else {
    echo '<!-- Unable to find patients in db -->';
    return "error";
  }
}

?>
