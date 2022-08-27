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

use \Vvveb\System\Import\Xml;
use Vvveb\Controller\Base;

class Import extends Base {
	function __construct() {
		parent::__construct();
		$this->xml = new Xml();
	}

	function upload() {
		$files = $this->request->files;

		foreach ($files as $file) {
			if ($file) {
				try {
					// use temorary file, php cleans temporary files on request finish.
					$result = $this->import($file['tmp_name']);
				} catch (\Exception $e) {
					$error                = $e->getMessage();
					$this->view->errors[] = $error;
				}
			}

			if ($result) {
				$successMessage          = 'Import wass successful!';
				$this->view->success[]   = $successMessage;
			} else {
				$errorMessage          = 'Failed to import file!';
				$this->view->error[]   = $errorMessage;
			}
		}

		return $this->index();
	}

	private function import($file) {
		$xml        = new Xml();
		//$file = '/home/givan/Downloads/vvveb-export.xml';
		$xmlContent = file_get_contents($file);

		if ($xml->prepareImport($xmlContent)) {
			return $xmlData    = $xml->import($xmlContent);
		}

		return false;
	}

	function index() {
		//$this->import();
	}
}
