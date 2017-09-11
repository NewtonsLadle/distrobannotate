<?php
require_once('../annotate/meta.php');


function config_framework_get_meta($loc_description, $loc_author) {
  return <<<EOT
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="$loc_description">
    <meta name="author" content="$loc_author">
EOT;
}

function config_framework_get_links($loc_relative_to_root) {
  return <<<EOT
<!-- Bootstrap Core CSS -->
    <link href="$loc_relative_to_root/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="$loc_relative_to_root/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="$loc_relative_to_root/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="$loc_relative_to_root/vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="$loc_relative_to_root/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
EOT;
}

function config_framework_get_fancybox() {
  return <<<EOT
  <!-- Add jQuery library -->
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>

  <!-- Add mousewheel plugin (this is optional) -->
  <script type="text/javascript" src="/fancybox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

  <!-- Add fancyBox -->
  <link rel="stylesheet" href="/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
  <script type="text/javascript" src="/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

  <!-- Optionally add helpers - button, thumbnail and/or media -->
  <link rel="stylesheet" href="/fancybox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" type="text/css" media="screen" />
  <script type="text/javascript" src="/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
  <script type="text/javascript" src="/fancybox/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>

  <link rel="stylesheet" href="/fancybox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" type="text/css" media="screen" />
  <script type="text/javascript" src="/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

  <script type="text/javascript">
  $(document).ready(function() {
    $(".fancybox").fancybox({
    "autoDimensions" : false,
    "autoscale"      : false,
    "maxHeight"      : 670,
    "width"          : 800,
    "transitionIn"   :  "fade",
    "transitionOut"  :  "fade",
    "speedIn"        :  300,
    "speedOut"       :  300,
    "opacity"        : true,
    "padding"        : 7,
    "type"           :'iframe',
    helpers : {
        overlay : {
            css : {
                "background" : "rgba(0,0,0,.1)"
            }
        }
    }
    });
  });
</script>
EOT;
}

function get_notification_icon($loc_type) {
  if ($loc_type === 0) {
    return 'fa-smile-o';
  }
  else if ($loc_type === 1) {
    return 'fa-file';
  }
  else {
    return 'fa-file';
  }
}

function notification_timestamp_tostring($loc_timestamp) {
  $today = localtime(time(), TRUE);
  $stamp = localtime($loc_timestamp/1000, TRUE);
  //return implode(" ",$stamp) . " | " . implode(" ",$today);
  //return $today["tm_yday"] . " | " . $stamp["tm_yday"];
  $num_years = $today["tm_year"] - $stamp["tm_year"];
  $num_months = $today["tm_mon"] - $stamp["tm_mon"];
  $num_days = $today["tm_yday"] - $stamp["tm_yday"];
  if ($num_years > 0) {
    if ($num_years == 1) {
      if ($num_days >= 365) {
        return "a year ago";
      }
      else {
        if ($num_days+365 < 31) {
          $num_days = $num_days + 365;
          return "$num_days days ago";
        }
        elseif ($num_days+365 < 62) {
          return "a month ago";
        }
        else {
          $num_months = $num_months + 12;
          return "$num_months months ago";
        }
      }
    }
    else {
      return "$num_years years ago";
    }
  }
  elseif ($num_months > 0) {
    if ($num_months == 1 && $num_days < 30) {
      if ($num_days == 1) {
        return "$num_days day ago";
      }
      else {
        return "$num_days days ago";
      }
    }
    else {
      if ($num_months == 1) {
        return "1 month ago";
      }
      else {
        return "$num_months months ago";
      }
    }
  }
  elseif ($num_days > 0) {
    if ($num_days == 1) {
      return "1 day ago";
    }
    else {
      return "$num_days days ago";
    }
  }
  else {
    return "today";
  }
}

function config_framework_get_navbar($loc_sitename, $loc_notifications_data) {
  if (!isset($loc_notifications_data)) {
    $loc_notifications_data = annotate_meta_get_notifications($GLOBALS["config_global_username"], 5)["data"];
  }
  $ret = <<<EOT
<!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/p/index.php">$loc_sitename</a>
            </div>
            <!-- /.navbar-header -->

            <ul class="nav navbar-top-links navbar-right">
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-alerts">
EOT;
                    foreach ($loc_notifications_data as $notification) {
                      $ret = $ret . '
                      <li>
                          <a href="/p/notifications.php?id=' . $notification->id . '">
                              <div>
                                  <i class="fa ' . get_notification_icon($notification->type) . ' fa-fw"></i> ' . $notification->content . '
                                  <span class="pull-right text-muted small">' . notification_timestamp_tostring($notification->timestamp) . '</span>
                              </div>
                          </a>
                      </li>
                      <li class="divider"></li>
                      ';
                    }
                    $ret = $ret . <<<EOT
                        <li>
                            <a class="text-center" href="/p/notifications.php">
                                <strong>See All Notifications</strong>
                                <i class="fa fa-angle-right"></i>
                            </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-alerts -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="/p/user.php"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="https://www.myu.umn.edu/psp/psprd/EMPLOYEE/EMPL/?cmd=logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li>
                            <a href="/p/index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="/p/choose.php"><i class="fa fa-edit fa-fw"></i> Annotate</a>
                        </li>
                        <li>
                            <a href="/p/choose.php"><i class="fa fa-check fa-fw"></i> Validate</a>
                        </li>
                        <li>
                            <a href="/p/projects.php"><i class="fa fa-table fa-fw"></i> Projects</a>
                        </li>
                        <li>
                            <a href="/p/kidneycancerinstructions.php"><i class="fa fa-book fa-fw"></i> Instructions</a>
                        </li>
                        <li>
                          <a href="/p/bug.php">Report a Bug</a>
                        </li>
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
EOT;
  return $ret;
}

function config_framework_get_pagelabel($loc_pagename) {
  return <<<EOT
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">$loc_pagename</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
EOT;
}

function config_framework_get_blkstats($loc_data) {
  return <<<EOT
  <div class="row">
      <div class="col-lg-3 col-md-6">
          <div class="panel panel-primary">
              <div class="panel-heading">
                  <div class="row">
                      <div class="col-xs-3">
                          <i class="fa fa-image fa-5x"></i>
                      </div>
                      <div class="col-xs-9 text-right">
                          <div class="huge">{$loc_data["unannotated"]}</div>
                          <div>Unannotated Images</div>
                      </div>
                  </div>
              </div>
              <a href="/p/choose.php">
                  <div class="panel-footer">
                      <span class="pull-left">View Details</span>
                      <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                      <div class="clearfix"></div>
                  </div>
              </a>
          </div>
      </div>
      <div class="col-lg-3 col-md-6">
          <div class="panel panel-yellow">
              <div class="panel-heading">
                  <div class="row">
                      <div class="col-xs-3">
                          <i class="fa fa-edit fa-5x"></i>
                      </div>
                      <div class="col-xs-9 text-right">
                          <div class="huge">{$loc_data["pending"]}</div>
                          <div>Pending Annotations</div>
                      </div>
                  </div>
              </div>
              <a href="/p/choose.php">
                  <div class="panel-footer">
                      <span class="pull-left">View Details</span>
                      <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                      <div class="clearfix"></div>
                  </div>
              </a>
          </div>
      </div>
      <div class="col-lg-3 col-md-6">
          <div class="panel panel-green">
              <div class="panel-heading">
                  <div class="row">
                      <div class="col-xs-3">
                          <i class="fa fa-check-square-o fa-5x"></i>
                      </div>
                      <div class="col-xs-9 text-right">
                          <div class="huge">{$loc_data["valid"]}</div>
                          <div>Validated Annotations</div>
                      </div>
                  </div>
              </div>
              <a href="/p/choose.php">
                  <div class="panel-footer">
                      <span class="pull-left">View Details</span>
                      <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                      <div class="clearfix"></div>
                  </div>
              </a>
          </div>
      </div>
      <div class="col-lg-3 col-md-6">
          <div class="panel panel-red">
              <div class="panel-heading">
                  <div class="row">
                      <div class="col-xs-3">
                          <i class="fa fa-warning fa-5x"></i>
                      </div>
                      <div class="col-xs-9 text-right">
                          <div class="huge">{$loc_data["flagged"]}</div>
                          <div>Flagged Annotations</div>
                      </div>
                  </div>
              </div>
              <a href="/p/choose.php">
                  <div class="panel-footer">
                      <span class="pull-left">View Details</span>
                      <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                      <div class="clearfix"></div>
                  </div>
              </a>
          </div>
      </div>
  </div>
EOT;
}

function config_framework_get_areachart($annotations_data) {
  $annotations_data_str = json_encode($annotations_data);
  return <<<EOT
  <div class="panel panel-default">
      <div class="panel-heading">
          <i class="fa fa-bar-chart-o fa-fw"></i> Annotations Over Time
          <div class="pull-right">
              <div class="btn-group">
                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                      Kidney Cancer - All
                      <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu pull-right" role="menu">
                      <li><a href="#">Kidney Cancer - All</a></li>
                      <li class="divider"></li>
                      <li><a href="#">Kidney Cancer - Me</a></li>
                  </ul>
              </div>
          </div>
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
          <div id="morris-area-chart"></div>
      </div>
      <!-- /.panel-body -->
  </div>
  <script type="text/javascript">
  annotations_data = {$annotations_data_str};
  var d = new Date();
  month = d.getMonth() + 1;
  day = d.getDate();
  etime = d.getTime();
  $(function() {
      $.getJSON('/data/stats/kidneycancer.json', function(dat) {
        thedata = dat.totals.progress.push({
                "month": month,
                "day": day,
                "etime": etime,
                "unannotated_images": annotations_data.unannotated,
                "pending_annotations": annotations_data.pending,
                "validated_annotations": annotations_data.valid,
                "flagged_annotations": annotations_data.flagged
              });
        Morris.Area({
          element: 'morris-area-chart',
          data: dat.totals.progress,
          xkey: 'etime',
          ykeys: ['unannotated_images', 'pending_annotations', 'validated_annotations', 'flagged_annotations'],
          labels: ['Unannotated', 'Pending', 'Validated', 'Flagged'],
          lineColors: ['#337ab7','#f0ad4e','#5cb85c','#d9534f'],
          pointSize: 2,
          hideHover: 'auto',
          resize: true
        });
      });
  });
  </script>
EOT;
}

function config_framework_get_timeline($loc_timeline_data) {
  $ret = $ret . <<<EOT
  <!-- /.panel -->
  <div class="panel panel-default">
      <div class="panel-heading">
          <i class="fa fa-clock-o fa-fw"></i> Project Timeline
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
          <ul class="timeline">
EOT;
    foreach ($loc_timeline_data as $milestone) {
      $ret = $ret . '
                    <li>
                        <div class="timeline-badge"><i class="fa ' . get_notification_icon($milestone->type) . '"></i>
                        </div>
                        <div class="timeline-panel">
                            <div class="timeline-heading">
                                <h4 class="timeline-title">' . $milestone->title . '</h4>
                                <p><small class="text-muted"><i class="fa fa-clock-o"></i> ' . notification_timestamp_tostring($milestone->timestamp) . '</small>
                                </p>
                            </div>
                            <div class="timeline-body">
                                <p>' . $milestone->content . '</p>
                            </div>
                        </div>
                    </li>
      ';
    }
    $ret = $ret . <<<EOT
          </ul>
      </div>
      <!-- /.panel-body -->
  </div>
  <!-- /.panel -->
EOT;
  return $ret;
}

function config_framework_get_notifications($loc_notifications_data, $verbose) {
  $ret = <<<EOT
  <div class="panel panel-default">
      <div class="panel-heading">
          <i class="fa fa-bell fa-fw"></i> Notifications
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
          <div class="list-group">
EOT;
  foreach($loc_notifications_data as $notification) {
    if ($verbose === 1) {
      $verbose_section = '<div>' . $notification->extended . '</div>';
    }
    $ret = $ret . '
              <a href="/p/notifications.php?id=' . $notification->id . '" class="list-group-item">
                  <i class="fa ' . get_notification_icon($notification->type) . ' fa-fw"></i> ' . $notification->content . '
                  <span class="pull-right text-muted small"><em>' . notification_timestamp_tostring($notification->timestamp) . '</em></span>
                  ' . $verbose_section . '
              </a>
';
  }
  $ret = $ret . <<<EOT
          </div>
EOT;
  if ($verbose !== 1) {
    $ret = $ret . <<<EOT
          <!-- /.list-group -->
          <a href="/p/notifications.php" class="btn btn-default btn-block">View All Notifications</a>
EOT;
  }
  $ret = $ret . <<<EOT
      </div>
      <!-- /.panel-body -->
  </div>
EOT;
  return $ret;
}

function config_framework_get_progressbar_itself($loc_annotations_data) {
  $num = $loc_annotations_data["pending"] + $loc_annotations_data["valid"];
  $den = $loc_annotations_data["flagged"] + $loc_annotations_data["unannotated"] + $num;
  $val = 100.0*$num/$den;
  return <<<EOT
  <div class="progress progress-striped active">
      <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{$val}" aria-valuemin="0" aria-valuemax="100" style="width: {$val}%">
          <span class="sr-only">{$val}% Complete (success)</span>
      </div>
  </div>
EOT;
}

function config_framework_get_progressbar($loc_annotations_data) {
  $num = $loc_annotations_data["pending"] + $loc_annotations_data["valid"];
  $den = $loc_annotations_data["flagged"] + $loc_annotations_data["unannotated"] + $num;
  $val = 100.0*$num/$den;
  return <<<EOT
  <div class="panel panel-default">
      <div class="panel-heading">
          <i class="fa fa-play fa-fw"></i> Progress
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{$val}" aria-valuemin="0" aria-valuemax="100" style="width: {$val}%">
                <span class="sr-only">{$val}% Complete (success)</span>
            </div>
        </div>
      </div>
      <!-- /.panel-body -->
  </div>


EOT;
}

function config_framework_get_projects($loc_annotations_data) {
  $ret = <<<EOT
  <div class="panel panel-default">
      <div class="panel-heading">
        <a class="project_home" href="/p/index.php">
          <i class="fa fa-bar-chart-o fa-fw"></i> Kidney Cancer
        </a>
      </div>
      <!-- /.panel-heading -->
      <div class="panel-body">
EOT;
  $ret = $ret . config_framework_get_progressbar_itself($loc_annotations_data);
  $ret = $ret . <<<EOT
          <p class="center_button_p">
              <a href="/p/annotate.php" class="annotatebutton btn btn-outline btn-primary"> Annotate </a>
          </p>
      </div>
      <!-- /.panel-body -->
  </div>
EOT;
  return $ret;
}
?>
