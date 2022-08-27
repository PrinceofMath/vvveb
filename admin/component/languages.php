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

use Vvveb\Sql\LanguageSQL as LanguageSQL;
use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;
use Vvveb\System\Session;

class Languages extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'limit' => 1000,
		'page'  => 1,
	];

	protected $options = [];

	public $cacheExpire = 0; //seconds

	function cacheKey() {
		//disable caching
		return false;
	}

	function results() {
		$results = [];

		$languages             = new LanguageSQL();
		$results['languages']  = $languages->getAll();

		if ($results['languages']) {
			$results['language']   = $id   = Session::getInstance()->get('language') ?? 1;
			$language              = $results['languages'][$id];
			$results['active']     = ['name' => $language['name'], 'code' => $language['code'], 'id' => $language['language_id']];
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
