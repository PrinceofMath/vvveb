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

class Customers extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'start'           => 0,
		'language_id'     => 1,
		'site_id'         => 1,
		'customer_id'     => 'url',
		'rows'            => ['url', 4],
		'id_manufacturer' => NULL,
		'customer'        => ['url', 'price asc'],
		'id_category'     => 'url',
		'id'              => NULL,
	];

	public $options = [];

	function results() {
		$customers = new \Vvveb\Sql\CustomerSQL();

		$results = $customers->getCustomers($this->options);

		foreach ($results['customers'] as $id => &$customer) {
			if (isset($customer['images'])) {
				$customer['images'] = json_decode($customer['images'], 1);

				foreach ($customer['images'] as &$image) {
					$image = Images::image('customer', $image);
				}
			}

			if (isset($customer['image'])) {
				$customer['images'][] = Images::image($customer['image'], 'customer');
			}
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
