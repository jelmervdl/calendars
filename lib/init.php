<?php
error_reporting(E_ALL);
ini_set('display_errors', false);

date_default_timezone_set('Europe/Amsterdam');

define('FOPEN_CACHE_TEMP_PATH', dirname(__FILE__) . '/../tmp/http_%s');

include dirname(__FILE__) . '/cache.php';

include dirname(__FILE__) . '/ical.php';

include dirname(__FILE__) . '/helpers.php';