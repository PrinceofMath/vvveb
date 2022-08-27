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

namespace Vvveb\Controller\Media;

use Vvveb\Controller\Base;
use function Vvveb\sanitizeFileName;
use Vvveb\System\Core\View;

class Media extends Base {
	function index() {
		$admin_path          = DIRECTORY_SEPARATOR . \Vvveb\config('admin.path', 'admin') . '/';
		$controllerPath      = $admin_path . 'index.php?module=media/media';

		$this->view->scanUrl   = "$controllerPath&action=scan";
		$this->view->uploadUrl = "$controllerPath&action=upload";
	}

	function upload() {
		$path = sanitizeFileName($this->request->post['mediaPath']);
		$file = sanitizeFileName($this->request->files['file']['name']);
		$path = str_replace('/media', '', $path);

		$destination = DIR_MEDIA . $path . '/' . $file;

		if (move_uploaded_file($this->request->files['file']['tmp_name'], $destination)) {
			if (isset($this->request->post['onlyFilename'])) {
				echo $file;
			} else {
				echo $destination;
			}
		} else {
			echo _('Error uploading file!');
		}

		die();
	}

	function scan($path = 'public') {
		switch ($path) {
			case 'public':
				$scandir = DIR_MEDIA;

			break;

			default:
				return false;
		}

		$scandir = DIR_MEDIA;
		// Run the recursive function

		// This function scans the files folder recursively, and builds a large array

		$scan = function ($dir) use ($scandir, &$scan) {
			$files = [];

			// Is there actually such a folder/file?

			if (file_exists($dir)) {
				foreach (scandir($dir) as $f) {
					if (! $f || $f[0] == '.') {
						continue; // Ignore hidden files
					}

					if (is_dir($dir . DIRECTORY_SEPARATOR . $f)) {
						// The path is a folder

						$files[] = [
							'name'  => $f,
							'type'  => 'folder',
							'path'  => str_replace($scandir, '', $dir) . DIRECTORY_SEPARATOR . $f,
							'items' => $scan($dir . DIRECTORY_SEPARATOR . $f), // Recursively get the contents of the folder
						];
					} else {
						// It is a file

						$files[] = [
							'name' => $f,
							'type' => 'file',
							'path' => str_replace($scandir, '', $dir) . DIRECTORY_SEPARATOR . $f,
							'size' => filesize($dir . DIRECTORY_SEPARATOR . $f), // Gets the size of this file
						];
					}
				}
			}

			return $files;
		};

		$response = $scan($scandir);

		// Output the directory listing as JSON
		$view         = View::getInstance();
		$view->noJson = true;

		header('Content-type: application/json');

		echo json_encode([
			'name'  => '',
			'type'  => 'folder',
			'path'  => '',
			'items' => $response,
		]);

		die();

		return false;
	}
}
