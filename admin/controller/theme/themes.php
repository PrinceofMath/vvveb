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

namespace Vvveb\Controller\Theme;

use Vvveb\Controller\Base;
use Vvveb\System\Extensions\Themes as ThemesList;
use Vvveb\System\Import\Theme;

class Themes extends Base {
	function upload() {
		$files = $this->request->files;

		foreach ($files as $file) {
			if ($file) {
				try {
					// use temorary file, php cleans temporary files on request finish.
					$this->themeSlug = ThemesList :: install($file['tmp_name']);
				} catch (\Exception $e) {
					$error                = $e->getMessage();
					$this->view->errors[] = $error;
				}
			}

			if ($this->themeSlug) {
				$this->themeName         = \Vvveb\humanReadable($this->themeSlug);
				$this->themeName         = "<b>$this->themeName</b>";
				$this->themeActivateUrl  = \Vvveb\url(['module' => 'theme/themes', 'action'=> 'activate', 'theme' => $this->themeSlug]);
				$successMessage          = sprintf('Theme %s was successfully installed!', $this->themeName, $this->themeActivateUrl);
				$successMessage .= "<p><a href='$this->themeActivateUrl'>" . sprintf('Activate theme', $this->themeName) . '</a></p>';
				$this->view->success[] = $successMessage;
			}
		}

		return $this->index();
	}

	function index() {
		$themes             =  ThemesList :: getList();

		$this->view->themes = $themes;
		$this->view->count  = count($themes);

		$themeImport       =  new Theme('ogani');

		$structure                       = $themeImport->getStructure();
		$this->view->import              = $structure;
		$this->view->required_plugins    = ['seo'=> '', 'akismet' => '', 'test1' => ''];
		$this->view->recommended_plugins = $structure;
	}

	function processImport($data, $path, $type = false) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				switch ($key) {
					case 'media':
						$type = 'media';

						break;

					case 'content':
						$type = 'content';

						break;
				}
				$this->processImport($value, $path . '/' . $key, $type);
			} elseif (is_numeric($key)) {
				echo $path . DIRECTORY_SEPARATOR . $key . " - $type <br/>";
			}
		}
	}

	function import() {
		$import              = $this->request->post['import'];
		$required_plugins    = $this->request->post['required_plugins'];
		$recommended_plugins = $this->request->post['recommended_plugins'];

		print_r($import);
		$this->processImport($import, '');
		//print_r($required_plugins);
		//print_r($recommended_plugins);

		die();
	}

	function activate() {
		$theme = $this->request->get['theme'];
		//\Vvveb\config(APP . '.theme', 'default')
		//var_dump($theme);
		return $this->index();
	}
}
