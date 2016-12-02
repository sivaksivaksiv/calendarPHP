<?php

/**
 * Stores rental information
 *
 */
class Station
{
    public $id;

    public $name;

    public $rate;

    public $rate_unit;

    public $start;

    public $end;

    public function __construct($station)
    {
        if ( is_array($station) )
        {
            $this->id = $station['id'];
            $this->name = $station['name'];
            $this->rate = $station['rate'];
	    $this->rate_unit = $station['rate_unit'];
            $this->start = $station['dt_avail_from'];
            $this->end = $station['dt_avail_to'];
        }
        else
        {
            throw new Exception("No station data was supplied.");
        }
    }

}

?>
