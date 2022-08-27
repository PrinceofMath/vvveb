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
use Vvveb\System\Images;

class Product extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'product_id'  => 'url',
		'slug'        => 'url',
		'language_id' => 1,
		'site_id'     => 1,
	];

	function results() {
		$product = new \Vvveb\Sql\ProductSQL();

		$results = $product->get($this->options);

		if (isset($results['images'])) {
			$results['images'] = Images::images($results['images'], 'product');
		}

		if (isset($results['image'])) {
			$results['images'][] = ['image' => Images::image('product', $results['image'])];
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
