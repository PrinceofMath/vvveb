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

namespace Vvveb\System\Cache;

class Memcached {
	private $expire;

	private $memcached;

	public function stats($time = false) {
		$stats = $this->memcached->getStats();
		/*
		[curr_items] => 0
		[total_items] => 0
		[bytes] => 0
		*/
		return $stats;
	}

	public function purge($time_delay = 0) {
		return $this->memcached->flush($time_delay);
	}

	public function __construct($options) {
		//$this->expire = $expire;
		$this->memcached = new \Memcached();

		$this->memcached->addServers($options['servers']);

		if (isset($options['options'])) {
			foreach ($options['options'] as $key => $value) {
				$this->memcached->setOption($key, $value);
			}
		}

		return $this->memcached;
	}

	public function get($key) {
		return $this->memcached->get($key);
	}

	public function set($key, $value, $expiration = 0) {
		return $this->memcached->set($key, $value, $expiration);
	}

	public function getMulti($keys, $serverKey = false) {
		if ($serverKey) {
			return $this->memcached->getMultiByKey($serverKey, $key);
		} else {
			return $this->memcached->getMulti($keys);
		}
	}

	public function setMulti($items, $expiration = 0, $serverKey = false) {
		if ($serverKey) {
			return $this->memcached->setMulti($items, $expiration);
		} else {
			return $this->memcached->setMultiByKey($serverKey, $items, $expiration);
		}
	}

	public function delete($key) {
		$this->memcached->delete($key);
	}
}
