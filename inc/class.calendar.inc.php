<?php

include_once('class.db_connect.inc.php');
include_once('class.station.inc.php');

/**
 * Builds and manipulates an events calendar
 */
class Calendar extends DB_Connect
{

	private $_useDate;

	private $_m;

	private $_y;

	private $_daysInMonth;

	private $_startDay;

	public function __construct($dbo=NULL, $useDate=NULL)
    	{

		parent::__construct($dbo);

                 /*
         	* Gather and store data relevant to the month
         	*/
        	if ( isset($useDate) )
        	{
             		$this->_useDate = $useDate;
        	}
        	else
        	{
             		$this->_useDate = date('Y-m-d H:i:s');
        	}

        	/*
         	* Convert to a timestamp, then determine the month
         	* and year to use when building the calendar
         	*/
        	$ts = strtotime($this->_useDate);
        	$this->_m = (int)date('m', $ts);
        	$this->_y = (int)date('Y', $ts);

        	/*
         	* Determine how many days are in the month
         	*/
        	$this->_daysInMonth = cal_days_in_month(
                	CAL_GREGORIAN,
                	$this->_m,
                	$this->_y
            	);
        	/*
         	* Determine what weekday the month starts on
         	*/
        	$ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
        	$this->_startDay = (int)date('w', $ts);
    	}

	private function _loadData($id=NULL)
    	{
        	$sql = "SELECT
                    `id`, `name`, `dt_avail_from`, `dt_avail_to`, `rate`,
                    `rate_unit`, `date_created`, `date_modified`, `modified_by`
                FROM `station`";

        	/*
         	* If an event ID is supplied, add a WHERE clause
         	* so only that event is returned
        	 */
        	if ( !empty($id) )
        	{
            		$sql .= "WHERE `id`=:id LIMIT 1";
        	}

        	/*
         	* Otherwise, load all events for the month in use
         	*/
        	else
        	{
            		/*
             		* Find the first and last days of the month
             		*/
            		$start_ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
            		$end_ts = mktime(23, 59, 59, $this->_m+1, 0, $this->_y);
            		$start_date = date('Y-m-d H:i:s', $start_ts);
            		$end_date = date('Y-m-d H:i:s', $end_ts);

            		/*
             		* Filter events to only those happening in the
             		* currently selected month
             		*/
            		$sql .= "WHERE `dt_avail_from`
                        	BETWEEN '$start_date'
                        	AND '$end_date'
                    	ORDER BY `dt_avail_from`";
		}

		try
        	{
            		$stmt = $this->db->prepare($sql);

            		/*
             		* Bind the parameter if an ID was passed
             		*/
            		if ( !empty($id) )
            		{
                		$stmt->bindParam(":id", $id, PDO::PARAM_INT);
            		}

            		$stmt->execute();
            		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            		$stmt->closeCursor();

            		return $results;
        	}
        	catch ( Exception $e )
        	{
            		die ( $e->getMessage() );
        	}
	}

    	private function _createStationObj()
    	{
        	$arr = $this->_loadData();

        	/*
         	* Create a new array, then organize the events
         	* by the day of the month on which they occur
         	*/
        	$stations = array();
        	foreach ( $arr as $station )
        	{
            		$day = date('j', strtotime($station['dt_avail_from']));
            		try
            		{
                		$stations[$day][] = new Station($station);
            		}
            		catch ( Exception $e )
            		{
                		die ( $e->getMessage() );
            		}
        	}
        	return $stations;
    	}

	public function buildCalendar()
    	{
        	/*
         	* Determine the calendar month and create an array of
         	* weekday abbreviations to label the calendar columns
         	*/
        	$cal_month = date('F Y', strtotime($this->_useDate));
		$WEEKDAYS = array('Sun','Mon','Tue','Wed','Thu','Fri','Sat');
        	/*
         	* Add a header to the calendar markup
         	*/
        	$html = "\n\t<h2>$cal_month</h2>";
        	for ( $d=0, $labels=NULL; $d<7; ++$d )
        	{
            		$labels .= "\n\t\t<li>" . $WEEKDAYS[$d] . "</li>";
        	}
        	$html .= "\n\t<ul class=\"weekdays\">"
            		. $labels . "\n\t</ul>";

		/*
     		* Load station data
     		*/
    		$stations = $this->_createStationObj();

		/*
     		* Create the calendar markup
     		*/
    		$html .= "\n\t<ul>"; // Start a new unordered list
    		for ( $i=1, $c=1, $t=date('j'), $m=date('m'), $y=date('Y');
            		$c<=$this->_daysInMonth; ++$i )
    		{

			/*
         		* Apply a "fill" class to the boxes occurring before
         		* the first of the month
         		*/
        		$class = $i<=$this->_startDay ? "fill" : NULL;
        		/*
         		* Add a "today" class if the current date matches
         		* the current date
         		*/
        		if ( $c==$t && $m==$this->_m && $y==$this->_y )
        		{
            			$class = "today";
        		}

        		/*
         		* Build the opening and closing list item tags
         		*/
        		$ls = sprintf("\n\t\t<li class=\"%s\">", $class);
        		$le = "\n\t\t</li>";

        		/*
         		* Add the day of the month to identify the calendar box
         		*/
        		if ( $this->_startDay<$i && $this->_daysInMonth>=$c)
        		{
				/*
				/* Format station dates
				*/
				$station_info = NULL;
				if (isset($stations[$c]))
				{
					foreach ($stations[$c] as $station)
					{
						$link = '<a href="view.php?id='.$station->id.'">'.$station->name.'</a>';
						$station_info .= "\n\t\t\t$link";
					}
				}
            			$date = sprintf("\n\t\t\t<strong>%02d</strong>",$c++);
        		}
        		else 
			{ 
				$date="&nbsp;"; 
			}

        		/*
         		* If the current day is a Saturday, wrap to the next row
         		*/
        		$wrap = $i!=0 && $i%7==0 ? "\n\t</ul>\n\t<ul>" : NULL;

        		/*
         		* Assemble the pieces into a finished item
         		*/

        		$html .= $ls . $date . $station_info . $le . $wrap;
    		}

		/*
     		* Add filler to finish out the last week
     		*/
    		while ( $i%7!=1 )
    		{
        		$html .= "\n\t\t<li class=\"fill\">&nbsp;</li>";
        		++$i;
    		}

    		/*
     		* Close the final unordered list
     		*/
    		$html .= "\n\t</ul>\n\n";

        	/*
         	* Return the markup for output
         	*/
        	return $html;
    	}

        private function _loadStationById($id)
    	{
        	/*
         	* If no ID is passed, return NULL
         	*/
        	if ( empty($id) )
        	{
            		return NULL;
        	}

        	/*
         	* Load the stations info array
         	*/
        	$station = $this->_loadData($id);

        	/*
         	* Return an station object
         	*/
        	if ( isset($station[0]) )
        	{
            		return new Station($station[0]);
        	}
        	else
        	{
            		return NULL;
        	}
    	}

        public function displayStation($id)
    	{
        	/*
         	* Make sure an ID was passed
         	*/
        	if ( empty($id) ) { return NULL; }

        	/*
         	* Make sure the ID is an integer
         	*/
        	$id = preg_replace('/[^0-9]/', '', $id);

        	/*
         	* Load the station data from the DB
         	*/
        	$station = $this->_loadStationById($id);

        	/*
         	* Generate strings for the date, start, and end time
         	*/
        	$ts = strtotime($station->start);
        	$date = date('F d, Y', $ts);
        	//$start = date('g:ia', $ts);
        	//$end = date('g:ia', strtotime($station->end));

        	/*
         	* Generate and return the markup
         	*/
        	return "<h2>$station->name</h2>"
            		."\n\t<h3 class=\"dates\" style=\"text-align:center\">is available from $date</h3>";
    	}

}

?>
