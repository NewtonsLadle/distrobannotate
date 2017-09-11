<?php
require_once("../config/global.php");
class stdObject{};

/* setters */

// Reads data from patients.json
// Updates data in stats/kidneycancer.json
// Is the function that sets historical points in the areachart
// To be called periodically
function annotate_meta_update_annotations ($current_annotations) {
  $file = $_GLOBALS['config_global_webroot'] . "../data/stats/kidneycancer.json";

  if (($stats = json_decode(file_get_contents($file))) != FALSE) {
    while ((getdate()[0]*1000 - $stats->updated) >= 86400000) {
      $new_time = $stats->updated + 86400000;
      $new_status = new stdObject();
      $new_status->month = getdate($new_time/1000)["mon"];
      $new_status->day = getdate($new_time/1000)["mday"];
      $new_status->etime = getdate($new_time/1000)[0]*1000;
      $new_status->unannotated_images = $current_annotations["unannotated"];
      $new_status->pending_annotations = $current_annotations["pending"];
      $new_status->validated_annotations = $current_annotations["valid"];
      $new_status->flagged_annotations = $current_annotations["flagged"];
      array_push($stats->totals->progress, $new_status);
      $stats->updated = $new_time;
    }
    file_put_contents($file,json_encode($stats));
  }
  else {
    //echo "DAFUQ";
  }
}

// Makes new notification for user specified
// To be called synchronously
function new_notification($loc_user, $loc_type, $loc_message) {

}

// Makes new milestone on timeline of events
// To be called synchronously
function new_milestone($loc_title, $loc_type, $loc_message) {

}


/* getters */

// returns
// array(
//      "unannotated" => $total_unannotated,
//      "pending" => $total_pending,
//      "valid" => $total_valid,
//      "flagged" => $total_flagged
// );
function annotate_meta_get_annotations() {
  $file = $GLOBALS['config_global_webroot'] . "data/patients.json";
  if (($patients = json_decode(file_get_contents($file))) !== FALSE) {
    $total = 0;
    $total_pending = 0;
    $total_valid = 0;
    $total_flagged = 0;
    foreach ($patients as $patient) {
      $total = $total + $patient->num_images;
      $total_pending = $total_pending + count($patient->annotated->pending);
      $total_valid = $total_valid + count($patient->annotated->valid);
      $total_flagged = $total_flagged + count($patient->annotated->flagged);
    }
    $total_unannotated = $total - ($total_pending + $total_valid + $total_flagged);
    $ret = array(
      "unannotated" => $total_unannotated,
      "pending" => $total_pending,
      "valid" => $total_valid,
      "flagged" => $total_flagged
    );
    annotate_meta_update_annotations($ret);
    return $ret;
  }
  else {
     return array(
       "error" => TRUE
     );
  }
}

// returns
// array(
//      "error" => TRUE/FALSE,
//      "data" => array(
//                     obj->id
//                     obj->timestamp
//                     obj->type
//                     obj->content
//                )
// )
function annotate_meta_get_notifications($user, $max) {
  if (!isset($max)) {
    $max = 1000;
  }
  $file = $GLOBALS['config_global_webroot'] . "data/notifications.json";
  if (($notifications = json_decode(file_get_contents($file))) !== FALSE) {
    $ret = array(
      "error" => FALSE,
      "data" => array()
    );
    $i = 0;
    foreach ($notifications as $notification) {
      if (in_array($user, $notification->users) && $i < $max) {
        array_push($ret["data"], $notification);
        $i = $i + 1;
      }
    }
    return $ret;
  }
  else {
     return array(
       "error" => TRUE
     );
  }
}

// returns
// array(
//      "error" => TRUE/FALSE,
//      "data" => array(
//                     obj->timestamp
//                     obj->type
//                     obj->title
//                     obj->content
//                )
// )
function annotate_meta_get_timeline($loc_project) {
  $file = $GLOBALS['config_global_webroot'] . "data/projects.json";
  if (($projects = json_decode(file_get_contents($file))) !== FALSE) {
    $ret = array(
      "error" => FALSE,
      "data" => array()
    );
    foreach ($projects as $project) {
      if ($project->id == $loc_project) {
        $ret["data"] = $project->timeline;
      }
    }
    return $ret;
  }
  else {
     return array(
       "error" => TRUE
     );
  }
}
?>
