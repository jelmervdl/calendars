<?php

if (!defined('FOPEN_CACHE_TEMP_PATH'))
	define('FOPEN_CACHE_TEMP_PATH', dirname(__FILE__) . '/../tmp/http_%s');

function get_head_headers($url)
{
	$url = parse_url($url);
	
	$socket = fsockopen($url['host'], 80, $error_no, $error_str, 3);
	
	if (!$socket) 
		return false;
	
	$request = sprintf("HEAD %s HTTP/1.1\r\nHost: %s\r\nConnection: Close\r\n\r\n",
		$url['path'], $url['host']);
	
	fwrite($socket, $request);
	
	$response = '';
	
	while (!feof($socket))
		$response .= fgets($socket, 128);
	
	fclose($socket);
	
	$headers = array();
	
	foreach (explode("\n", $response) as $header)
	{
		$header = explode(':', $header, 2);
		
		if (count($header) == 2)
		{
			list($name, $value) = $header;
			$headers[trim($name)] = trim($value);
		}
		else
		{
			$headers[] = $header[0];
		}
	}
	
	return $headers;
}

function fopen_cache_url($url, $ttl, $id = null) 
{
	$cache_file = sprintf(FOPEN_CACHE_TEMP_PATH, md5($id ? $id : $url));
	
	$refresh_required = true;
	
	if (file_exists($cache_file))
	{
		if (filemtime($cache_file) + $ttl >= time())
		{
			$refresh_required = false;
		}
		else
		{
			$headers = get_head_headers($url, true);
			
			/* Wanneer de remote site offline is, dan hoeft de cache niet herladen te worden */
			if (!$headers)
			{
				$refresh_required = false;
			}
			elseif (array_key_exists('Last-Modified', $headers))
			{
				$server_last_modified = strtotime($headers['Last-Modified']);
				$cache_last_modified = filemtime($cache_file);
				if ($cache_last_modified >= $server_last_modified)
				{
					@touch($cache_file);
					$refresh_required = false;
				}
			}
		}
	}
	
	return $refresh_required && !@copy($url, $cache_file)
		? fopen($url, 'r')
		: fopen($cache_file, 'r');
}

function cache_url_time_remaining($url, $ttl, $id = null)
{
	$cache_file = sprintf(FOPEN_CACHE_TEMP_PATH, md5($id ? $id : $url));
	
	return file_exists($cache_file)
		? filemtime($cache_file) + $ttl - time()
		: -1;
}