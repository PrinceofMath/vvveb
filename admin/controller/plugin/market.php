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

namespace Vvveb\Controller\Plugin;

use Vvveb\Controller\Base;
use Vvveb\System\Core\View;
use Vvveb\System\Extensions\Plugins;
use Vvveb\System\Validator;

class Market extends Base {
	function index() {
		$view = View :: getInstance();

		$validator = new Validator(['plugins']);

		//allow only fields that are in the validator list and remove the rest
		$request = $validator->filter($this->request->get);

		$themes =  Plugins :: getMarketList(['request' => $request]);
		//var_dump($themes);
		$view->set($themes);
	}
}
