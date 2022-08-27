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

namespace Vvveb\System\Core;

class Request {
	public $get = [];

	public $post = [];

	public $request = [];

	public $cookie = [];

	public $files = [];

	public $server = [];

	public $method;

	protected static $instance;

	public static function getInstance() {
		if (is_null(static::$instance)) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	private function __construct() {
		$this->get     = $this->filter($_GET);
		$this->post    = $this->filter($_POST, false);
		$this->request = $this->filter($_REQUEST);
		$this->cookie  = $this->filter($_COOKIE);
		$this->files   = $_FILES;
		$this->server  = $_SERVER;
		$this->method  = $_SERVER['REQUEST_METHOD'] ?? 'GET';

		$this->request = array_merge($this->request, $this->get, $this->post);
	}

	public function filter($data, $filterText = true) {
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				unset($data[$key]);

				$key        = substr($this->filter($key, $filterText), 0, 255);
				$data[$key] = $this->filter($value, $filterText);
			}
		} else {
			if ($filterText) {
				$data = \Vvveb\filterText($data);
			} else {
				//$data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
				$data = $data;
			}
		}

		return $data;
	}
}
