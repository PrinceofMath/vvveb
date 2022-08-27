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

//define('DEBUG', false);

return [
	'session' => [
		'driver' => 'php',
	],
	'cache' => [
		'driver'  => 'file', //availabe drivers: memcached, redis, file
		'servers' => [
			//array('127.0.0.1', 11211, 0),
			//array('mem2.domain.com', 11211, 67)
		],
		'options' => [
			//Memcached::OPT_BINARY_PROTOCOL => true,
		],

		/*
		'driver'  => 'redis',
		'options' => [
			'host' => 127.0.0.1,
			'port' => 9000,
		],
		 */
	],
];
