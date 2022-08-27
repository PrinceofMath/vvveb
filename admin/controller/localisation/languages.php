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

namespace Vvveb\Controller\Localisation;

use Vvveb\Controller\Base;
use Vvveb\Sql\LanguageSQL;

class Languages extends Base {
	function add() {
	}

	function index() {
		$languagesList             = include DIR_SYSTEM . 'data/languages-list.php';
		$this->view->languagesList = $languagesList;

		$languages             = new LanguageSQL();
		$this->view->languages = $languages->getAll();
	}
}