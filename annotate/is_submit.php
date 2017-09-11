<?php
require_once("../config/global.php");
require_once("api.php");
// Takes POST['id']             (imageset id)
//       POST['user']           (username of person who made the change)
//       POST['non_contrast']   (true if non-contrast box is checked)
//       POST['mri']            (true if mri box is checked)
//       POST['no_kidney']      (true if No-Kidney box is checked)
//       POST['done']           (true if user clicked button to mark is as done)
//       POST['notes']          (contains value of textarea)
// Saves changed meta to patients.json file
// Returns a value for "error" if something goes wrong

$id = $_POST["id"];
$user = $_POST["user"];
$nc = $_POST["non_contrast"];
$mri = $_POST["mri"];
$nk = $_POST["no_kidney"];
$donebtn = ($_POST["done"] == "true");
$notes = $_POST["notes"];
$msg = "id = $id\nuser = $user\nno kidney = $nk\nmri = $mri\nnon-contrast = $nc\ndonebtn = $donebtn\nnotes = $notes";
session_start();
$response = array(
  "error" => "",
  "msg" => $msg
);

$patients = json_decode(file_get_contents($GLOBALS['config_global_webroot'] . "data/patients.json"));
$i = 0;
foreach ($patients as $patient) {
  if ($patient->id == $id) {
    $done = (($donebtn && !$patient->finished) || (!$donebtn && $patient->finished));
    $patients[$i]->notes = $notes;
    $patients[$i]->finished = ($done == "true");
    $patients[$i]->mri = ($mri == "true");
    $patients[$i]->noncontrast = ($nc == "true");
    $patients[$i]->nokidney = ($nk == "true");
  }
  $i = $i + 1;
}
file_put_contents($GLOBALS['config_global_webroot'] . "data/patients.json", json_encode($patients));

echo json_encode($response);
exit();


?>
