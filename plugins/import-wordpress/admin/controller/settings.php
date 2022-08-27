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

namespace Vvveb\Plugins\ImportWordpress\Controller;

use Vvveb\Controller\Base;

class Settings extends Base {
	private $cats = [];

	private $postTypes = ['post', 'page', 'attachment'];

	function processPosts($posts) {
	}

	function processPages($posts) {
	}

	function processAttachment($posts) {
	}

	function import() {
		$this->rss  = new Rss(file_get_contents($this->url));

		foreach ($this->postTypes as $postType) {
			$posts = $rss->get(1, $this->limit, [['wp:post_type' => $postType]]);
			$fn    = 'process' . ucfirst($postType);
			$this->$fn($posts);
		}
	}

	function index() {
	}
}
