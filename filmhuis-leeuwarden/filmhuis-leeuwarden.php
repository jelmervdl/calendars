<?php

include '../lib/init.php';

$fs = fopen_cache_url('http://www.filmhuisleeuwarden.nl/page.php?nodeId=32', 3600);

$content = stream_get_contents($fs);

$filmhuis_calendar = new iCalendar('Filmhuis Leeuwarden');
$filmhuis_calendar->description = 'Programma van het Filmhuis Leeuwarden';

preg_match_all('{<p><strong>([a-z]+)\s+([0-9]{1,2})\s+([a-z]+)</strong></p><br>.*?<p><table.*?>(.+?)</table>\s*?</p>}ims', $content, $matches, PREG_SET_ORDER);

foreach ($matches as $match_day)
{
	list(, $day_name, $day_of_month, $month, $day_content) = $match_day;
		
	preg_match_all('{<tr>.*?<td width="80">([0-9]{2}):([0-9]{2}) uur</td>\s*?<td>\s*?<a href="([^"]+)">(.+?)</a>\s*, (?:((?:[^,]+?),)*?)([^,]+), ([0-9]{4}), ([0-9]+) min\..*?</td>\s*?</tr>}mis', $day_content, $matches_day_program, PREG_SET_ORDER);

	foreach ($matches_day_program as $match_day_program)
	{
		list(, $hour, $minute, $url, $title, $director, $country, $year, $playtime) = $match_day_program;
				
		$timestamp = mktime($hour, $minute, '00', month_number($month), $day_of_month, date('Y'));
		
		$title = trim(html_entity_decode($title), ', ');
		
		$director = trim(html_entity_decode($director), ', ');
		
		$country = trim(html_entity_decode($country), ', ');
		
		$event = new iEvent();
		$event->start = $timestamp;
		$event->end = $timestamp + ($playtime * 60);
		$event->summary = $title;
		$event->description = "$title" . ($director ? ' van ' . $director : '') . ", $country, $year ($playtime min.)";
		$event->location = "Filmhuis Leeuwarden";
		$event->url = "http://www.filmhuisleeuwarden.nl/$url";
		
		$filmhuis_calendar->events[] = $event;		
	}
}

$filmhuis_calendar->publish('Filmhuis Leeuwarden.ics');