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

namespace Vvveb\System\User;

use function Vvveb\regexMatch;
use function Vvveb\regexMatchAll;

class Role {
	public static function has($permission) {
	}

	public static function add($permission) {
	}

	public static function getTree() {
		$list = $this->getList();
	}

	public static function getList() {
		$files = [];
		$path  = [DIR_APP . '/controller/*', DIR_PLUGINS . '*/admin/controller/'];

		while (count($path) > 0) {
			$next = array_shift($path);

			foreach (glob("$next/*") as $file) {
				if (is_dir($file)) {
					$path[] = $file;
				}

				if (is_file($file)) {
					$files[] = $file;
				}
			}
		}

		sort($files);

		$data['permissions'] = [];
		$tree                = [];

		foreach ($files as $file) {
			//keep only relative controller path
			$permission = substr($file, strpos($file, '/controller/') + 12);
			//remove extension
			$permission = substr($permission, 0, strrpos($permission, '.'));
			//if plugin add namespace
			if (strpos($file, 'plugins/')) {
				$pluginName = regexMatch('@/plugins/(.+?)/@', $file, 1);
				$permission = "plugins/$pluginName$permission";
			}

			$data['permissions'][] = $permission;

			$controllerCode = file_get_contents($file);
			//get all public methods
			$methods = regexMatchAll('/(?<!private)\s+function.+?(\w+)\(/', $controllerCode, 1);

			if ($methods) {
				foreach ($methods as $method) {
					//ignore constructor
					if ($method[0] != '_') {
						$data['permissions'][] = $permission . "/$method";
					}
				}
			}
		}

		return $data;
	}
}
