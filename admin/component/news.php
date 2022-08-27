<?php

/**
 * Vvveb
 *
 * Copyright (C) 2022  Ziadin Givan
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 */

namespace Vvveb\Component;

use Vvveb\System\Cache;
use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;
use Vvveb\System\Import\Rss;

class News extends ComponentBase {
	private $limit = 10;

	private $url = 'https://www.vvveb.com/feed/news.xml';

	public static $defaultOptions = [
		'start'           => 0,
		'limit'           => 10,
	];

	public $options = [];

	function getNews() {
		$ctx=stream_context_create([
			'http'=> [
				'timeout' => 5,
			],
		]);
		$rss  = new Rss(file_get_contents($this->url,false,$ctx));

		return $rss->get(1, $this->limit);
	}

	function results() {
		//return [];
		$cache = Cache::getInstance();
		//check for news ~twice a week
		$news = $cache->cache('news',function () {return $this->getNews(); }, 259200);

		$results = [
			'news'  => $news,
			'count' => $this->limit,
		];

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
