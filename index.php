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

namespace Vvveb;

use Vvveb\System\PageCache as PageCache;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);
error_reporting(E_ALL);

define('V_VERSION', '0.0.1');
defined('DEBUG') || define('DEBUG', false);
defined('DISABLE_PLUGIN_ON_ERORR') || define('DISABLE_PLUGIN_ON_ERORR', false);

defined('DIR_ROOT') || define('DIR_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
defined('DIR_CONFIG') || define('DIR_CONFIG', DIR_ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
defined('DIR_SYSTEM') || define('DIR_SYSTEM', DIR_ROOT . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);
defined('PAGE_CACHE_DIR') || define('PAGE_CACHE_DIR', 'page-cache' . DIRECTORY_SEPARATOR);

define('SQL_CHECK', true);
define('PAGE_CACHE', false);
define('VIEW_TEMPLATE_ENGINE','psttt');

function is_installed() {
	return is_file(DIR_ROOT . 'config/db.php');
}

if (! defined('PUBLIC_PATH')) {
	define('PUBLIC_PATH', DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
	define('PUBLIC_THEME_PATH', DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
}

if (! defined('APP')) {
	if (is_installed()) {
		define('APP', 'app');
	} else {
		define('APP', 'install');
		header('Location: install');
	}
} elseif (! is_installed() && APP != 'install') {
	header('Location: /install');
}

require_once DIR_SYSTEM . '/core/startup.php';

if (PAGE_CACHE) {
	require_once DIR_SYSTEM . 'page-cache.php';
	$pageCache = PageCache::getInstance();

	function saveCache() {
		$pageCache = PageCache::getInstance();
		$pageCache->startGenerating();
		$pageCache->startCapture();

		System\Core\start();

		return $pageCache->saveCache();
	}

	if ($pageCache->canCache()) {
		if ($pageCache->hasCache()) {
			return $pageCache->getCache();
		} else {
			if ($pageCache->isStale()) {
				if ($pageCache->isGenerating()) {
					return $pageCache->getStale();
				} else {
					return saveCache();
				}
			} else {
				//if cache is already generating
				//wait 10 seconds for cache generation
				//if it takes longer then give up
				$i = 0;

				while ($pageCache->isGenerating() && $i++ <= 10) {
					sleep(1);

					if ($pageCache->hasCache()) {
						return $pageCache->getCache();
					}
				}

				//if page took longer than 10 seconds
				//check if the generating page is older than 1 minute
				//if cache is older than 1 minute then regenerate
				//if is not older than 1 minute then show maintenance server overloaded page
				if ($i >= 10) {
					if (! $pageCache->isGeneratingStuck()) {
						return System\Core\FrontController::notFound(true, 500);
					}
				}

				return saveCache();
			}
		}
	} else {
		System\Core\start();
	}
} else {
	System\Core\start();
}
