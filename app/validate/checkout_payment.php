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

return [
	'payment_first_name' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_last_name' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_company' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_address_1' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_address_2' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_city' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_postcode' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_country' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_country_id' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_zone' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_zone_id' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_address_format' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_method' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
	'payment_code' => [
		['notEmpty'  => '',
			'message'   => '%s must not be empty', ],

		['maxLength'  => 100,
			'message'    => _('%s must not be greater than 100'), ],
	],
];
