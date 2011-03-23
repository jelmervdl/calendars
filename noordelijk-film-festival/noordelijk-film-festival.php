<?php 

include '../lib/init.php';

class NoordelijkFilmFestival_Parser
{
	public $movies = array();
	
	public $errors = array();
	
	public function __construct($index_url)
	{
		$this->parse_index($index_url);
	}
	
	protected function parse_index($url)
	{
		$fs = fopen_cache_url($url, 3600);

		if (!$fs)
			return false;

		$content = stream_get_contents($fs);

		$dom = new DOMDocument('1.0', 'iso-8859-1');
		@$dom->loadHTML($content);

		$table = $dom->getElementById('subnav')->nextSibling->nextSibling;
		
		foreach ($table->getElementsByTagName('a') as $link)
		{
			$movie = new NoordelijkFilmFestival_Movie(trim($link->firstChild->nodeValue));
			$movie->link = $link->getAttribute('href');
			
			if (!$this->parse_movie($movie, $movie->link))
				$this->invalid_urls[] = $movie->link;
			
			$this->movies[] = $movie;
		}
	}
	
	static protected function innerHTML($x)
	{
		$dom = new DOMDocument('1.0', 'iso-8859-1');
		$dom->appendChild($dom->importNode($x, true));
		return $dom->saveXML();
	}
	
	protected function parse_movie(NoordelijkFilmFestival_Movie $movie, $url)
	{
		$fs = fopen_cache_url($url, 3600);

		if (!$fs)
			return false;

		$content = stream_get_contents($fs);

		$dom = new DOMDocument('1.0', 'iso-8859-1');
		@$dom->loadHTML($content);

		$content_maintable = $dom->getElementById('contentmaintable');

		if (!$content_maintable)
			return false;

		$content_maintable_html = self::innerHTML($content_maintable);

		if (preg_match('{<h2>(.+?)</h2>}', $content_maintable_html, $matches))
			$movie->title = html_entity_decode($matches[1]);

		if (preg_match('{Speelduur: (\d{1,3}) minuten}', $content_maintable_html, $matches))
			$movie->playtime = $matches[1];

		$xpath = new DOMXPath($dom);

		$nodes = $xpath->query('//div[@style="width: 100%; background-color: #333; padding: 8px; "]/p', $content_maintable);

		if (count($nodes) === 0)
			return false;

		preg_match_all('{\<strong\>\s*(\d{1,2} [a-z]+ \d{4}), (\d{1,2}:\d{2}) uur\</strong\> \- Noordelijk Film Festival, (.+?)\s*\<br}', self::innerHTML($nodes->item(0)), $matches);
		
		for ($i = 0; $i < count($matches[0]); $i++)
			$movie->add_show(new NoordelijkFilmFestival_Show(
				html_entity_decode($matches[3][$i]),
				new DateTime($matches[1][$i] . ' ' . $matches[2][$i] . ':00')));
		
		return true;
	}
}

class NoordelijkFilmFestival_Movie
{
	public $title;
	
	public $description;
	
	public $link;
	
	public $shows;
	
	public $playtime = 0;
	
	public function __construct($title)
	{
		$this->title = $title;
		$this->shows = array();
	}
	
	public function add_show(NoordelijkFilmFestival_Show $show)
	{
		$show->attach_to_movie($this);
		
		$this->shows[] = $show;
	}
}

class NoordelijkFilmFestival_Show
{
	public $movie;
	
	public $place;
	
	public $time;
	
	public function __construct($place, DateTime $time)
	{
		$this->place = $place;
		$this->time = $time;
	}
	
	public function attach_to_movie(NoordelijkFilmFestival_Movie $movie)
	{
		$this->movie = $movie;
	}
	
	public function end_time()
	{
		$end_time = clone $this->time;
		$end_time->modify(sprintf('+%d minutes', $this->movie->playtime));
		return $end_time;
	}
}

//$parser = new NoordelijkFilmFestival_Parser('http://www.noordelijkfilmfestival.nl/page.php?nodeId=810');

$parser = new NoordelijkFilmFestival_Parser('http://www.noordelijkfilmfestival.nl/page.php?nodeId=888');

$location_conditions = isset($_GET['location_conditions']) ? $_GET['location_conditions'] : array();

$calendar = new iCalendar('Noordelijk Film Festival');

foreach($parser->movies as $movie)
{
	foreach($movie->shows as $show)
	{
		// Alle besloten films overslaan
		if(!in_array('besloten', $location_conditions)
			&& strstr($show->place, 'besloten') !== false)
			continue;
		
		// Alle off-shore films overslaan
		if(!in_array('off-shore', $location_conditions)
			&& (strstr($show->place, 'Terschelling') !== false
			|| strstr($show->place, 'Vlieland') !== false))
			continue;
		
		$event = new iEvent();
		$event->start = $show->time;
		$event->end = $show->end_time();
		$event->summary = $movie->title;
		$event->description = $movie->description;
		$event->location = $show->place;
		$event->url = $movie->link;
		
		$calendar->add_event($event);
	}
}

$calendar->publish('Noordelijk Film Festival.ics');