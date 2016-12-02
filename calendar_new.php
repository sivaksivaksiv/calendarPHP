<?php

include_once '../phpsys/core/init.inc.php';
include_once '../phpsys/class/class.calendar.inc.php';

/*
 * Load the calendar for January
 */
$cal = new Calendar($dbo);

/*
 * Set up the page title and CSS files
 */
$page_title = "Booked Stations";
$css_files = array('calendar_style.css');

//include_once 'inc/header_new.inc.php';
?>

<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <title><?php echo $page_title; ?></title>
<link href="http://cdn.alloyui.com/3.0.1/aui-css/css/bootstrap.min.css" rel="stylesheet"></link>
<link rel="stylesheet" href="css/new_cal_style.css"></link>
</head>

<body>


<div id="wrapper">
	<div id="myScheduler"></div>
</div>
<?php
/*
 * Include the footer
 */
//include_once 'inc/footer_new.inc.php';
?>

<script type="text/javascript" src="http://cdn.alloyui.com/3.0.1/aui/aui-min.js"></script>
<script type="text/javascript">

YUI().use(
  'aui-scheduler',
  function(Y) {
    var events = [
      {
        content: 'Station One',
        endDate: new Date(2016, 6, 25),
        startDate: new Date(2016, 6, 25)
      },
      {
	color: "#8d9",
        content: 'Station Two',
        disabled: true,
        endDate: new Date(2016, 6, 11),
        meeting: true,
        reminder: true,
        startDate: new Date(2016, 6, 8)
      }];

    var dayView = new Y.SchedulerDayView();
    var weekView = new Y.SchedulerWeekView();
    var monthView = new Y.SchedulerMonthView();

    var eventRecorder = new Y.SchedulerEventRecorder();
    
    new Y.Scheduler(
      {
	activeView: monthView,
        boundingBox: '#myScheduler',
        date: new Date(2016, 6, 25),
	eventRecorder: eventRecorder,
        items: events,
        render: true,
        views:  [dayView, weekView, monthView]
      }
    );
  }
);

</script>
</body>

</html>

