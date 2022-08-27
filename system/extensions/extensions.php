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

namespace Vvveb\System\Extensions;

use Vvveb\System\Event;
use Vvveb\System\Functions\Str;

abstract class Extensions {
	static protected $plugins = [];

	static protected $categories = [];

	static protected $extension = 'extension';

	static protected $baseDir = 'extension';

	const KEY_VALUE_REGEX = '/^([\w ]+):\s+(.+)$/m';

	static function unzip($file) {
		// get the absolute path to $file
		$path = pathinfo(realpath($file), PATHINFO_DIRNAME);

		$zip = new \ZipArchive();
		$res = $zip->open($file);

		if ($res === true) {
			$zip->extractTo($path);
			$zip->close();

			return true;
		}

		return false;
	}

	static function getParams($comments) {
		$results = [];

		if (preg_match_all(static :: KEY_VALUE_REGEX, $comments, $matches)) {
			$matches[1] = array_map(function ($key) {
				return str_replace(' ','-',strtolower($key));
			}, $matches[1]);

			$results = array_combine($matches[1], $matches[2]);
		}

		return $results;
	}

	static function getComments($content) {
		//$content = file_get_contents($file);
		foreach (token_get_all($content) as $entry) {
			if ($entry[0] == T_DOC_COMMENT || $entry[0] == T_COMMENT) {
				$docComments[] = $entry[1];
			}
		}

		return implode("\n", $docComments);
	}

	static function getInfo($content, $name = false) {
		$comments = static :: getComments($content);
		$params   = static ::  getParams($comments);

		if (isset($params['status'])) {
			unset($params['status']);
		}

		if (isset($params['category']) && $name) {
			static :: $categories[$params['category']][] = $name;
		}

		return $params;
	}

	static function getListInfo($path) {
		if (static :: $plugins) {
			return static :: $plugins;
		}

		$list    = glob($path);

		foreach ($list as $file) {
			$content                    = file_get_contents($file);
			$folder                     = Str::match('@/([^/]+)/[a-z]+.php$@', $file);
			$info                       = static::getInfo($content, $folder);
			$info['file']               = $file;
			$info['folder']             = $folder;
			static :: $plugins[$folder] = $info;
		}

		return static :: $plugins;
	}

	static function getCategories() {
		return static :: $categories;
	}

	static function install($extensionZipFile) {
		$extension   = static :: $extension;
		$success     = false;

		$zip = new \ZipArchive();

		if ($zip->open($extensionZipFile) === true) {
			$info       = false;
			$folderName = $zip->getNameIndex(0);
			//check if first entry is a directory
			if (substr($folderName, -1, 1) != DIRECTORY_SEPARATOR) {
				throw new \Exception(sprintf('%s zip must have only %s folder!', $extension));
			}

			for ($i = 0; $i < $zip->numFiles; $i++) {
				$file = $zip->getNameIndex($i);

				//check if all files inside the extension folder
				if (strpos($file, $folderName) === false) {
					throw new \Exception("$extension zip should not have other files than $extension files!");
				}

				if (strpos($file, "$extension.php") !== false) {
					$content = $zip->getFromName($file);
					$info    = static::getInfo($content);

					if ($folderName == $info['slug'] . '/') {
						// Unzip Path
						//$zip->extractTo($extractPath, $folderName);
						if ($zip->extractTo(static :: $baseDir)) {
							$success = $info['slug'];
						} else {
							$success = false;
						}
					} else {
						throw new \Exception($extension . ' slug `' . $info['slug'] . "` does not match folder `$folderName`");
					}

					break;
				}
			}

			$zip->close();

			if (! $info) {
				throw new \Exception("No `$extension.php` info found inside zip!");
			}
		} else {
			throw new \Exception('File is not a valid zip archive!');
		}

		//copy public folder to public/extensions/$extensionName
		Event :: trigger(__CLASS__, __FUNCTION__, $extensionZipFile, $success);

		return $success;
	}

	/*
		static function getListInfo($path) {
			$list    = self :: getList($path);
			$plugins = [];

			foreach ($list as $file) {
				$pluginInfo = static::getInfo($file['file'], $file['folder']);
				$pluginInfo += $file;

				$plugins[$file['folder']] = $pluginInfo;
			}

			return $plugins;
		}*/
}
