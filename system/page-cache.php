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

class PageCache {
	const STALE_EXT = '.old';

	const LOCK_EXT = '.new';

	const CACHE_DIR = 'public/page-cache/';

	const MAX_LOCK_SECONDS = 60;

	private $fileName;

	private $canCache;

	static private $instance;

	static function getInstance() {
		if (! self :: $instance) {
			self :: $instance = new self();
		}

		return self :: $instance;
	}

	function __construct() {
		$this->fileName = self :: fileName();
		$this->canCache = self :: canCache();
	}

	function fileName() {
		$path = $_SERVER['REQUEST_URI'];

		if (substr($path, -1) == '/') {
			$path .= 'index.html';
		}

		return $file_cache = DIR_PUBLIC . PAGE_CACHE_DIR . $_SERVER['HTTP_HOST'] . $path;
	}

	function isGenerating() {
		return is_file($this->fileName . self :: LOCK_EXT);
	}

	function isGeneratingStuck() {
		return time() - filemtime($this->fileName . self :: LOCK_EXT) > self :: MAX_LOCK_SECONDS;
	}

	function isStale() {
		return is_file($this->fileName . self :: STALE_EXT);
	}

	function getStale() {
		return $this->fileName . self :: STALE_EXT;
	}

	function startGenerating() {
		$dir = dirname($this->fileName);

		if (! file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		//keep old cache to serve while generating
		if (file_exists($this->fileName)) {
			rename($this->fileName, $this->fileName . self :: STALE_EXT);
		}
		touch($this->fileName . self :: LOCK_EXT);
	}

	function startCapture() {
		ob_start();
	}

	function canCache() {
		if (APP == 'admin' || APP == 'install' || ! empty($_POST) || isset($_COOKIE['nocache']) || \Vvveb\System\User\User::current()) {
			return false;
		}

		return true;
	}

	function hasCache() {
		return is_file($this->fileName);
	}

	function getCache() {
		return readfile($this->fileName);
	}

	function saveCache() {
		$data = ob_get_contents();

		//if page not found or server error don't cache
		if (FrontController::getStatus() != 200) {
			//remove lock
			unlink($this->fileName . self :: LOCK_EXT);

			return $data;
		}

		if (self :: canCache() && $this->fileName && $data) {
			//create directory structure
			$dir = dirname($this->fileName);

			if (! file_exists($dir)) {
				mkdir($dir, 0777, true);
			}
			//save cache
			file_put_contents($this->fileName, $data);
			//remove lock
			unlink($this->fileName . self :: LOCK_EXT);
			//remove stale
			@unlink($this->fileName . self :: STALE_EXT);
		}

		return $data;
	}

	function purge($path) {
	}

	function finish() {
	}
}
