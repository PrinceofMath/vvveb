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

class File {
	/* get purge, stats from trait*/
	use CacheTrait;

	private $expire;

	private $options = ['expire' => 3000];

	private $cacheDir = DIR_CACHE;

	private $cachePrefix = 'cache.';

	public function __construct($options) {
		$this->options += $options;

		$this->expire      = $this->options['expire'];
		$this->cacheDir    = DIR_CACHE;
		$this->cachePrefix = 'cache.';
	}

	public function get($key) {
		$files = glob(DIR_CACHE . 'cache.' . basename($key) . '.*');

		if ($files) {
			$handle = fopen($files[0], 'r');

			flock($handle, LOCK_SH);

			$size = filesize($files[0]);

			if ($size > 0) {
				$data = fread($handle, $size);
			} else {
				$data = '';
			}

			flock($handle, LOCK_UN);

			fclose($handle);

			return json_decode($data, true);
		}

		return null;
	}

	public function set($key, $value, $expire = null) {
		if (! $expire) {
			$expire = $this->expire;
		}

		$this->delete($key);

		$file = DIR_CACHE . 'cache.' . basename($key) . '.' . (time() + $expire);

		$handle = fopen($file, 'w');

		flock($handle, LOCK_EX);

		fwrite($handle, json_encode($value, JSON_PRETTY_PRINT));

		fflush($handle);

		flock($handle, LOCK_UN);

		fclose($handle);
	}

	public function getMulti($keys, $serverKey = false) {
		$result = [];

		foreach ($keys as $key) {
			$result[$key] = $this->get($key);
		}

		return $result;
	}

	public function setMulti($items, $expiration = 0, $serverKey = false) {
		foreach ($items as $key => $value) {
			$this->set($key, $value);
		}
	}

	public function delete($key) {
		$files = glob(DIR_CACHE . 'cache.' . basename($key) . '.*');

		if ($files) {
			foreach ($files as $file) {
				if (! @unlink($file)) {
					clearstatcache(false, $file);
				}
			}
		}
	}
}
