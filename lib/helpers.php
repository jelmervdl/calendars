<?php

function calendar_url($relative_url)
{
	return sprintf('webcal://%s%s/%s',
		$_SERVER['HTTP_HOST'],
		dirname($_SERVER['SCRIPT_NAME']),
		$relative_url);
}

function month_number($month)
{
	switch ($month)
	{
		case 'januari':		return 1;
		case 'februari':	return 2;
		case 'maart':		return 3;
		case 'april':		return 4;
		case 'mei':			return 5;
		case 'juni':		return 6;
		case 'juli':		return 7;
		case 'augustus':	return 8;
		case 'september':	return 9;
		case 'oktober':		return 10;
		case 'november':	return 11;
		case 'december':	return 12;
		default:			return 0;
	}
}

function google_analytics()
{
	$analytics_file = dirname(__FILE__) . '/../analytics.html';
	
	return file_exists($analytics_file)
		? file_get_contents($analytics_file)
		: '';
}