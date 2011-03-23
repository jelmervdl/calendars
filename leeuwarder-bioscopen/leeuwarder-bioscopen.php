<?php

/* THIS FILE IS UNTESTED AND OLD, DECADES OLD! */

include '../lib/init.php';

$nld_months = array(
	1 => 'januari',
	2 => 'februari',
	3 => 'maart',
	4 => 'april',
	5 => 'mei',
	6 => 'juni',
	7 => 'juli',
	8 => 'augustus',
	9 => 'september',
	10 => 'oktober',
	11 => 'november',
	12 => 'december'
);

function filter_empty($x) {
	return strlen(trim($x)) > 0;
}

function lwd_bioscopen_parse_date_range($date) {
	
	global $nld_months;
	
	preg_match('{^([0-9]{1,2}) ([a-z]+?) t/m ([0-9]{1,2}) ([a-z]+?)$}', $date, $matches);
	
	$start_date = array(
		'd' => (int) $matches[1],
		'm' => (int) array_search($matches[2], $nld_months),
		'y' => (int) date('Y')
	);
	
	$end_date = array(
		'd' => (int) $matches[3],
		'm' => (int) array_search($matches[4], $nld_months),
		'y' => (int) date('Y')
	);
	
	if($start_date['m'] > $end_date['m']) {
		$end_date['y']++;
	}
	
	return array($start_date, $end_date);
}

function day_range($start, $end) {
	$days = array('ma', 'di', 'wo', 'do', 'vr', 'za', 'zo');

	$start_index = array_search($start, $days);

	$end_index = array_search($end, $days);

	if($start_index > $end_index) {
		$end_index += count($days);
	}


	$days_range = array();

	foreach(range($start_index, $end_index) as $days_index) {
		$days_range[] = $days[$days_index % count($days)];
	}

	return $days_range;
}

function trim_dot(&$string) {
	$string = trim($string, '.');
}

function filter_times($string) {
	return preg_match('{^[0-9]{2}\.[0-9]{2}$}', $string);
}



function lwd_bioscopen_parse_show_times($time_string) {
	/*$rules = array(
		'Do. 14.00 - 19.00 en 21.45 uur',
		'Vr. 19.00 en 21.45 uur',
		'Za. 16.30 - 19.00 en 21.45 uur',
		'Zo. 14.00 - 16.30 - 19.00 en 21.30 uur',
		'Ma t/m Wo 14.00 - 19.00 en 21.30 uur',
		'Do. t/m Za. 19.00 en 21.45 uur',
		'Zo. t/m Wo. 19.00 en 21.30 uur',
		'Do. en Vr. 14.00 en 19.00 uur',
		'Za. en Zo. 14.00 - 16.30 en 19.00 uur',
		'Ma. t/m Wo. 19.00 uur ',
		'Do. t/m Wo. 21.15 uur ',
		'Za. en Zo. 16.00 uur  ',
		'Do. t/m Za. 21.45 uur',
		'Zo. t/m Wo. 21.15 uur',
		'Vr. 14.00 uur',
		'Za. en Zo. 14.00 - 16.30 uur',
		'Wo. 14.00 uur in Tivoli'
	);
	*/
	
	$rules = array(trim($time_string));

	foreach($rules as $rule) {
		$tokens = explode(' ', strtolower($rule));

		array_walk($tokens, 'trim_dot');

		if($tokens[1] == 'en') {
			$days = array($tokens[0], $tokens[2]);
			$tokens = array_slice($tokens, 3);
		}
		elseif($tokens[1] == 't/m') {
			$days = day_range($tokens[0], $tokens[2]);
			$tokens = array_slice($tokens, 3);
		}
		else {
			$days = array($tokens[0]);
			$tokens = array_slice($tokens, 1);
		}

		$times = array_filter($tokens, 'filter_times');

		var_dump($days, $times);
	}
}

$res = fopen_cache_url('http://leeuwarderbioscopen.nl/', 24 * 60 * 60);

$content = stream_get_contents($res);

preg_match('{<a href="([^"]+)" >Filmagenda Komende week</a>}is', $content, $matches);

$url_volgende_week = $matches[1];



preg_match('{<div class="datum">(.+?) t/m (.+?)</div>}', $content, $matches);

var_dump($matches);

setlocale(LC_ALL, 'nl_NL');
var_dump(strptime($matches[1], '%A %d %B %Y'));

exit;



preg_match_all('{'
	.'<td class="filmposter"><a href="(?:.+?)"><img src="(?:.+?)" width="100" height="150" class="poster" /></a></td>\s*'
	.'<td class="filminfo">\s*'
		. '<a href="(.+?)"><div class="filmtitel">(.+?)</div></a>\s*'
		//. '(?:.+?)<strong>(.+?)</strong>'
		. '(.+?)'
	. '</td>'
.'}ims', $content, $matches);

foreach($matches[3] as $i => $match) {
	
	$match = strip_tags($match, '<br>');
	
	$match = str_replace('&nbsp;', '', $match);
	
	$match = explode('<br />', $match);
	
	array_walk($match, 'trim');
	
	$match = array_filter($match, 'filter_empty');
	
	$matches[3][$i] = $match;
}

$lwd_bioscopen_calendar = new iCalendar('Leeuwarder Bioscopen');
$lwd_bioscopen_calendar->description = 'Programma van Tivoli & Cinema';

foreach($matches[2] as $i => $title) {
	list($start_date, $end_date) = lwd_bioscopen_parse_date_range(array_shift($matches[3][$i]));
	
	foreach($matches[3][$i] as $show_times) {
		echo lwd_bioscopen_parse_show_times($show_times);
	}
	
}

var_dump($lwd_bioscopen_calendar);