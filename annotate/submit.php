<?php
require_once("../config/global.php");
require_once("api.php");
// Takes POST['imagdat']   (base64 encoded url)
//       POST['patient']   (patient id)
//       POST['image']     (image that was annotated - string)
//       POST['image_ind'] (image that was annotated - int)
//       POST['feature']   (feature name [left_kidney, right_cancer, ...])
//       POST['user']      (username of person who did the annotations)
//       POST['height']    (height of unscaled image)
//       POST['width']     (width of unscaled image)
// Saves png file of annotation
session_start();
$response = array(
  "error" => ""
);

// Save posted data locally
$data = $_POST["imgdat"];
$patient = $_POST["patient"];
$image = $_POST["image"];
$image_ind = intval($_POST["image_ind"]);
$feature = $_POST["feature"];
$user = $_POST["user"];
$height = intval($_POST["height"]);
$width = intval($_POST["width"]);

// Check to make sure meta data exists
if (((!isset($patient)) || ($patient == "")) || ((!isset($feature)) || ($feature == "")) ||
    ((!isset($user)) || ($user == "")) || ((!isset($image)) || ($image == ""))) {
  $response["error"] = "incomplete_meta";
  echo json_encode($response);
  exit();
}

// Check to make sure image data exists
if (((!isset($data)) || ($data == "")) || ((!isset($height)) || ($height == 0)) ||
    ((!isset($width)) || ($width == 0)) || ((!isset($image_ind)) || ($image_ind < 0))) {
  $response["error"] = "incomplete_data";
  echo json_encode($response);
  exit();
}

// Decode base64 annotation data
if ($decoded_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data))) {
  // Set new file location
  $fileloc = '../data/patient_data/' . $_POST['patient'] . '/annotated' . '/';

  // Set new file name
  $filename = time() . '_' . $user . '_' . $feature . '_' . $image;

  // Save file
  if (file_put_contents($fileloc . $filename, $decoded_data) === FALSE) { // save failed
    $response["error"] = "write_error";
    $response["filename"] = $fileloc . $filename;
    $response["posix"] = posix_strerror(posix_get_last_error());
    echo json_encode($response);
    exit();
  }

  // Save succeeded, update database, return filename and empty error
  if (($new_ret = annotate_api_new_annotation($user, $feature, $patient, $image_ind)) === 0) {
    $response["filename"] = $fileloc . $filename;
    echo json_encode($response);
    exit();
  }
  else {
    $response["error"] = "meta_write_error";
    if ($new_ret === 1) {
      $response["error_details"] = "read";
    }
    else if ($new_ret === 2) {
      $response["error_details"] = "write";
    }
    else if ($new_ret === 3) {
      $response["error_details"] = "interpret";
    }
    else {
      $response["error_details"] = "something crazy";
    }
    echo json_encode($response);
    exit();
  }
}
else { // decode failed
  $response["error"] = "invalid_data";
  echo json_encode($response);
  exit();
}
?>
