<?php
// Takes POST['imagdat'] (base64 encoded url)
//       POST['patient'] (patient id)
//       POST['feature'] (feature name [left_kidney, right_cancer, ...])
//       POST['user']    (username of person who did the annotations)
session_start();
$response = array();



$data = $_POST["imgdat"];
$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data));
$filename = 'savedimgs/' . $_POST['patient'] . '_' . $_POST['feature'] . '_' . $_POST['image'];
file_put_contents($filename, $data);
$response = array(
  "success" => True,
  "filename" => $filename
);
echo json_encode($response);
?>
