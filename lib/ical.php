<?php

class iTimezone
{	
	public $name = 'Europe/Amsterdam';
	
	public function export()
	{
		return implode("\r\n", array(
			'BEGIN:VTIMEZONE',
	 		'TZID:Europe/Amsterdam',
			'BEGIN:DAYLIGHT',
			'TZOFFSETFROM:+0100',
			'TZOFFSETTO:+0200',
			'DTSTART:19810329T020000',
			'RRULE:FREQ=YEARLY;BYMONTH=3;BYDAY=-1SU',
			'TZNAME:CEST',
			'END:DAYLIGHT',
			'BEGIN:STANDARD',
			'TZOFFSETFROM:+0200',
			'TZOFFSETTO:+0100',
			'DTSTART:19961027T030000',
			'RRULE:FREQ=YEARLY;BYMONTH=10;BYDAY=-1SU',
			'TZNAME:CET',
			'END:STANDARD',
			'END:VTIMEZONE'
		));
	}
}

class iCalendar
{
	public $timezones = array();

	public $default_timezone;

	public $events = array();

	public $name;
	
	public $description;

	public function __construct($name)
	{
		$this->name = $name;
		
		$this->add_timezone(new iTimezone('Europe/Amsterdam'), true);
	}
	
	public function add_event(iEvent $event)
	{
		$this->events[] = $event;
	}
	
	public function add_timezone(iTimezone $timezone, $make_default = false)
	{
		$this->timezones[] = $timezone;
		
		if ($make_default)
			$this->default_timezone = $timezone;
	}
	
	public function export()
	{
	 	$out = array(
			'BEGIN:VCALENDAR',
			'METHOD:PUBLISH',
			'CALSCALE:GREGORIAN',
			'VERSION:2.0',
			'PRODID:-//IkHoefGeen.nl//NONSGML v1.0//EN',
			'X-WR-CALNAME:' . $this->name,
			'X-WR-CALDESC:' . $this->description,
			'X-WR-RELCALID:' . md5($this->name),
			'X-WR-TIMEZONE:' . $this->default_timezone->name
		);
		
		foreach ($this->timezones as $timezone)
			$out[] = $timezone->export();
		
		foreach ($this->events as $event)
			$out[] = $event->export();
		
		$out[] = 'END:VCALENDAR';
		
		return implode("\r\n", $out);
	}
	
	public function publish($filename = null)
	{
		header('Content-Type: text/calendar; charset=UTF-8');
		
		if ($filename)
			header('Content-Disposition: attachment; filename="' . $filename . '"');
		
		echo $this->export();
	}
}

class iEvent
{
	public $start;
	
	public $end;
	
	public $summary;
	
	public $description;
	
	public $location;
	
	public $url;
	
	public function export()
	{
		
		$start = $this->start instanceof DateTime
			? $this->start->format('U')
			: $this->start;
		
		$end = $this->end instanceof DateTime
			? $this->end->format('U')
			: $this->end;
		
		return implode("\r\n", array(
			'BEGIN:VEVENT',
			'DTSTART;TZID=Europe/Amsterdam:' . gmdate('Ymd\THis\Z', $start),
			'SUMMARY:' . utf8_encode($this->summary),
			'LOCATION:' . utf8_encode($this->location),
			'DESCRIPTION:' . utf8_encode($this->description),
			'URL;VALUE=URI:' . $this->url,
			'DTEND;TXID=Europe/Amsterda:' . gmdate('Ymd\THis\Z', $end),
			'END:VEVENT'
		));
	}
}
