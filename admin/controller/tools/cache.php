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

namespace Vvveb\Controller\Tools;

use Vvveb\Controller\Base;

class Cache extends Base {
	function getFolderFiles($dir) {
		return $files = glob(DIR_CACHE . '/*');
	}

	function getCacheInfo($path, $folders) {
		foreach ($folders as $folder) {
			$this->view->cache[$folder]          = [];
			$this->view->cache[$folder]['files'] = $files = $this->getFolderFiles($path . $folder);
			$this->view->cache[$folder]['count'] = count($files);
		}
	}

	function index() {
		$this->view->cache = [];

		$storage_cache_folders = ['compiled-templates', 'model', 'cache'];
		$this->getCacheInfo(DIR_CACHE, $storage_cache_folders);

		$public_cache_folders = [PAGE_CACHE_DIR, 'assets-cache'];
		$this->getCacheInfo(DIR_PUBLIC, $public_cache_folders);
	}
}
