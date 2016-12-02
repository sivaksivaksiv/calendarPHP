<?php

/*
 * Make sure the event ID was passed
 */
if ( isset($_GET['id']) )
{
    /*
     * Make sure the ID is an integer
     */
    $id = preg_replace('/[^0-9]/', '', $_GET['id']);

    /*
     * If the ID isn't valid, send the user to the main page
     */
    if ( empty($id) )
    {
        header("Location: ./");
        exit;
    }
}
else
{
    /*
     * Send the user to the main page if no ID is supplied
     */
    header("Location: ./");
    exit;
}

/*
 * Include necessary files
 */
include_once '../phpsys/core/init.inc.php';
include_once '../phpsys/class/class.calendar.inc.php';

/*
 * Output the header
 */
$page_title = "Station Detail";
$css_files = array("calendar_style.css");
include_once 'inc/header.inc.php';

/*
 * Load the calendar
 */
$cal = new Calendar($dbo);

?>

<div id="content">
<?php echo $cal->displayStation($id) ?>

    <a href="calendar.php">&laquo; Back to the calendar</a>
</div><!-- end #content -->

<?php

/*
 * Output the footer
 */
include_once 'inc/footer.inc.php';

?>
