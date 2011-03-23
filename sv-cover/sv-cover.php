<?php

require_once '../lib/init.php';

$dutch_days = array('maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag');

$dutch_months = array('januari','februari','maart','april','mei','juni','juli','augustus','september','oktober','november','december');

$dutch_days_pattern = implode('|', $dutch_days);

$dutch_months_pattern = implode('|', $dutch_months);

function next_sibling_element(DOMNode $node)
{
	while ($node->nextSibling)
	{
		$node = $node->nextSibling;
		
		if ($node->nodeType == XML_ELEMENT_NODE)
			break;
	}
	
	return $node;
}

function html_to_text($html)
{
	$html = strip_tags($html);
	
	$html = html_entity_decode($html);
	
	return trim($html);
}

$fs = fopen_cache_url('http://www.svcover.nl/agenda.php', 3600);

$contents = stream_get_contents($fs);

$calendar = new iCalendar('SV Cover');
$calendar->description = 'Activiteiten van studievereniging Cover';

preg_match_all('{<h2>(?<month>\w+?)</h2>\s*<table class="agenda">(?<activities>.+?)</table>}is', $contents, $months, PREG_SET_ORDER);

foreach ($months as $month)
{
	$month_number = month_number(strtolower($month['month']));
	
	preg_match_all('{<tr>\s*<td class="vandatum">(?<from>\d+)</td>\s*<td><a class="agendalink" href="(?<url>.+?)">(?<name>.+?)</a><br/>(?<details>.+?)</td>\s*</tr>}is', $month['activities'], $activities, PREG_SET_ORDER);
	
	foreach ($activities as $activity)
	{
		$details = explode('<br/>', $activity['details']);
		
		$data = array();
		
		$event = new iEvent();
		
		$calendar->events[] = $event;
		
		$event->url = 'http://www.svcover.nl/' . $activity['url'];
		
		$event->summary = html_to_text($activity['name']);
		
		$event->description = $event->summary;
		
		foreach ($details as $detail)
		{
			if (preg_match("{(?<preposition>\w+)?\s*(?:$dutch_days_pattern) (?<day>\d+) (?:$dutch_months_pattern),? (?<hour>\d+):(?<minute>\d+)}i", $detail, $matches))
			{
				extract($matches);
			
				$timestamp = mktime($hour, $minute, '00', $month_number, $day, date('Y'));
			
				switch (strtolower($matches['preposition']))
				{
					case 'tot':
						$event->end = $timestamp;
						break;
					case 'van':
					default:
						$event->start = $timestamp;
						break;
				}
			}
			elseif (preg_match('{Locatie:\s*(.+?)$}i', $detail, $matches))
			{
				$event->location = html_to_text($matches[1]);
			}

			// als er geen eind-tijd is, ga uit van de rest van de dag.
			if (!$event->end)
				$event->end = mktime(23, 59, 0,
					date('n', $event->start),
					date('j', $event->start),
					date('Y', $event->start));
		}
	}
}

$calendar->publish('Cover.ics');