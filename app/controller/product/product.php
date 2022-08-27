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

namespace Vvveb\Controller\Product;

use Vvveb\Controller\Base;
use Vvveb\System\Core\View;

class Product extends Base {
	function index() {
		$view = View::getInstance();
	}

	function test() {
		$posts   = new \Vvveb\Sql\PostSQL();
		$post    = ['post' => ['status' => 'publish']];
		$results = $posts->addPost($post);
		$user    = new \Vvveb\Sql\UserSQL();

		$userData = ['user' => [
			'login' => 'test',
			'email' => 'test@test.com',
			//'password' => Auth::password('password'),
			'nicename' => 'nicename',
			'role_id'  => 1,
		],
		];

		$user->addUser($userData);
	}
}
