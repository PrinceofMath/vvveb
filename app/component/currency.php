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

use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;

class Currency extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
	];

	function __construct($options) {
		return parent::__construct($options);
	}

	//called when fetching data, when cache expires
	function results() {
		$results =
			[
				'currencies' => [
					'euro' => [
						'name'     => 'Euro',
						'symbol'   => '€',
						'decimals' => '',
					],
					'dollar' => [
						'name'     => 'Dollar',
						'symbol'   => '$',
						'decimals' => '',
					],
					'pound' => [
						'name'     => 'Pound',
						'symbol'   => '£',
						'decimals' => '',
					],
				],
			];

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
