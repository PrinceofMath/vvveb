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

namespace Vvveb\System;

//define('COMPONENT_CACHE_EXPIRE', 360);

class CustomField {
	function __construct($options) {
	}

	static function di(&$component) {
		return;
		$component->request = Request::getInstance();
		$component->view    = View::getInstance();
		$component->session = Session::getInstance();
	}

	function results() {
		//check cache
		$memcache = Cache :: getInstance();
		//return false;
		return $memcache->get($this->cacheKey);
	}
}
