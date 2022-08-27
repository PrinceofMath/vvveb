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

class Redis {
	private $expire;

	private $redis;

	private $options = ['expire' => 3000, 'prefix' => 'vvveb.'];

	public function __construct($options) {
		$this->options += $options;

		$this->redis = new \Redis();
		$this->redis->pconnect($this->options['host'], $this->options['port']);
	}

	public function get($key) {
		$data = $this->redis->get($this->options['prefix'] . $key);

		return json_decode($data, true);
	}

	public function set($key, $value, $expire = null) {
		$expire = $expire ?? $this->options['expire'];
		$prefix = $this->options['prefix'];
		$status = $this->redis->set($prefix . $key, json_encode($value));

		if ($status) {
			$this->redis->expire($prefix . $key, $expire);
		}
	}

	public function delete($key) {
		$this->redis->del($this->options['prefix'] . $key);
	}
}
