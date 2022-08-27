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

use \_ as _;
use \Vvveb\System\Import\Xml;
use Vvveb\Controller\Base;
use Vvveb\System\Extensions\Plugins;
use Vvveb\System\Extensions\Themes;

class Export extends Base {
	function __construct() {
		parent::__construct();
		$this->xml = new Xml();
	}

	function export() {
		$tables = $this->request->post['table'];

		$xml     = new Xml();
		$xmlData = $xml->export($tables);
		//print_r($_POST);

		//die();

		header('Content-Disposition: attachment; filename=vvveb-export.xml');
		header('Content-Type: application/octet-stream');

		die($xmlData);
	}

	private function namespaceTree() {
		$tableNames = $this->xml->getTableNames();

		foreach ($tableNames as $tableName) {
			$pos       = strpos($tableName,'_');
			$namespace = substr($tableName, 0, $pos);

			if ($namespace) {
				$subspace = substr($tableName, $pos + 1);

				$namespace = _(\Vvveb\humanReadable($namespace));
				$subspace  = _(\Vvveb\humanReadable($subspace));
				//$subspace = !empty($subspace) ? $subspace : $tableName;
				$namespaces[$namespace][$subspace] = $tableName;
			} else {
				$namespace                          = _(\Vvveb\humanReadable($tableName));
				$namespaces[$namespace][$namespace] = $tableName;
			}
		}

		ksort($namespaces);

		foreach ($namespaces as &$subspaces) {
			ksort($subspaces);
		}

		return $namespaces;
	}

	function index() {
		$this->view->namespaces = $this->namespaceTree();
		$this->view->plugins    = Plugins::getList(null);
		$this->view->themes     = Themes::getList(null);
		//var_dump($this->view->themes);
	}
}
