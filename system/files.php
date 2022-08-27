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

class Files {
	public function __construct() {
	}

	public function unzip($zipFile, $extractPath) {
		$zip = new \ZipArchive();

		if ($zip->open($pluginZipFile) === true) {
			// Unzip Path
			$zip->extractTo($extractPath);
			$folder = $zip->getNameIndex(0);
			$zip->close();

			return true;
		}

		return false;
	}

	public function get($type, $id, $attrs) {
	}

	public function save($type, $id, $path, $attrs) {
	}

	public static function rmdir($src) {
		$dir = opendir($src);

		if ($dir) {
			while (false !== ($file = readdir($dir))) {
				if (($file != '.') && ($file != '..')) {
					$full = $src . DIRECTORY_SEPARATOR . $file;

					if (is_dir($full)) {
						self::rmdir($full);
					} else {
						unlink($full);
					}
				}
			}

			closedir($dir);

			return rmdir($src);
		}

		return false;
	}
}
