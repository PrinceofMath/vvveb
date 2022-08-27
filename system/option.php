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

use Vvveb\Sql\OptionSQL as OptionSQL;

class Option {
	private $option = [];

	private $optionSql;

	private static $instance;

	public static function getInstance() {
		if (self::$instance) {
			return self::$instance;
		} else {
			return self::$instance = new self();
		}
	}

	function __construct() {
		$this->optionSql = new OptionSQL();
	}

	public function get($key, $default = null) {
		if (isset($this->option[$key])) {
			return $this->option[$key];
		}

		return $this->optionSql->getOption(['key' => $key, 'site_id' => '0']) ?? $default;
	}

	public function set($key, $value, $site_id = SITE_ID) {
		$this->option[$key] = $value;

		return $this->optionSql->setOption(['key' => $key, 'value' => $key, 'site_id' => $site_id]);
	}

	public function multiSet($options, $site_id = SITE_ID) {
		if (! $site_id) {
			$site_id = 0;
		}

		foreach ($options as $key => $value) {
			$this->option[$key] = $value;
			$this->optionSql->setOption(['key' => $key, 'value' => $value, 'site_id' => $site_id]);
			//todo:implement setOptions
		}
	}
}
