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

include_once 'inc/header.inc.php';

?>

<div id="content">
<?php


/*
 * Display the calendar HTML
 */
echo $cal->buildCalendar();

?>
</div><!-- end #content -->
<?php

/*
 * Include the footer
 */
include_once 'inc/footer.inc.php';

?>
