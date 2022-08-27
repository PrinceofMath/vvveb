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

namespace Vvveb\Controller\Admin;

use Vvveb\Controller\Base;
use Vvveb\System\Images;
use Vvveb\System\User\Role;

class Users extends Base {
	protected $type = 'admin';

	private function save() {
	}

	function index() {
		$view     = $this->view;
		$users    = new \Vvveb\Sql\AdminSQL();

		$options    =  [
			'type'         => $this->type,
		] + $this->global;

		$results = $users->getList($options);

		if ($results['admins']) {
			foreach ($results['admins'] as $id => &$user) {
				$user['image'] = Images::image('user', $user['image'] ?? '');
			}
		}

		$permissions = Role::getList();
		//var_dump($permissions);

		//die();
		$view->users    = $results['admins'];
		$view->count    = $results['count'] ?? 0;
		$view->limit    = $options['limit'];
	}
}
