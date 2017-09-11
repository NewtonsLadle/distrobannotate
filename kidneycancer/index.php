<?php session_start(); ?>
<?php
$_SESSION['person'] = 'Nick';
if (array_key_exists("scale",$_GET)) {
  $scale = (int)$_GET["scale"];
  if ($scale < 1) {
    $scale = 1;
  }
  else if ($scale > 8) {
    $scale = 8;
  }
}
else {
  $scale = 3;
}
$imgdata = array(
  "width" => 320,
  "height" => 250,
  "scale" => $scale,
  "patient" => '',
  "image" => 'kidneytumor.png',
  "features" => array(
    'left_kidney' => 0,
    'right_kidney' => 1,
    'left_cancer' => 0
  )
);
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include_once "kidney_head.php"; ?>
    <script type="text/javascript" src="/kidneycancer/resources/js/annotate.js"></script>
    <link rel="stylesheet" href="/kidneycancer/resources/style/annotate.css" />
    <title>Annotate | Kidney Cancer Detection</title>
  </head>
  <script type="text/javascript">
    var imgdata = <?php echo json_encode($imgdata); ?>;
  </script>
  <body>
    <?php include_once "kidney_nav.php"; ?>
    <div id="ctrl_island" class="island">
      <h2 id="feature_name"><?php echo $_SESSION['person']; ?></h2>
      <h3 id="image_name">Male_Left_Lower_T1_Cancer -> 17340</h3>
      <div class="island_divider"></div>
      <div id="toolbox">
        <!-- commenting out for the time being
        <a href="#" id="icon3_a" class="tool"><img id="icon3_img" src="/resources/images/kidneycancer/tools/icon3.png" /></a>
        <a href="#" id="icon5_a" class="tool"><img id="icon5_img" src="/resources/images/kidneycancer/tools/icon5.png" /></a>
        -->
        <a href="#" id="icon10_a" class="tool"><img id="icon10_img" src="/kidneycancer/resources/images/kidneycancer/tools/icon10.png" /></a>
        <a href="#" id="icon20_a" class="tool"><img id="icon20_img" src="/kidneycancer/resources/images/kidneycancer/tools/icon20.png" /></a>
        <a href="#" id="icon0_a" class="tool"><img id="icon0_img" src="/kidneycancer/resources/images/kidneycancer/tools/icon0.png" /></a>
      </div>
      <div class="island_divider"></div>
      <?php foreach ($imgdata["features"] as $key => $value) { ?>
        <?php $done = ""; ?>
        <?php if ($value == 1) {$done = " done";} ?>
      <a href="#" id="feature_<?php echo $key; ?>_a" class="feature_a<?php echo $done; ?>">submit <?php echo $key; ?></a>
      <?php } ?>
      <div id="buttons_cont">
        <button type="button" id="nextbutton" class="btn btn-primary">Next</button>
      </div>
    </div>
    <div id="annotator_cont" class="brushwidth20" oncontextmenu="return false;" style="width:<?php echo $imgdata['scale']*$imgdata['width']; ?>px;height:<?php echo $imgdata['scale']*$imgdata['height']; ?>px;">
       <img id="to_annotate" src="/kidneycancer/resources/images/<?php echo $imgdata['image']; ?>" width="<?php echo $imgdata['scale']*$imgdata['width']; ?>" height="<?php echo $imgdata['scale']*$imgdata['height']; ?>" />
       <canvas id="annotator" width="<?php echo $imgdata['scale']*$imgdata['width']; ?>" height="<?php echo $imgdata['scale']*$imgdata['height']; ?>"></canvas>
    </div>
  </body>
</html>
