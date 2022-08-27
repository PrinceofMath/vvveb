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
use Vvveb\System\Core\View;
use Vvveb\System\Images;
use Vvveb\System\User\Role as RoleList;
use Vvveb\System\Validator;

class Role extends Base {
	protected $type = 'role';

	function index() {
		$view = View :: getInstance();

		$role_id = (int)($this->request->get[$this->type . '_id'] ?? 0);

		$list = RoleList::getList();
		var_dump($list);

		if ($role_id) {
			$options = [$this->type . '_id' => (int)$role_id];

			$sqlModel   = 'Vvveb\Sql\\' . ucfirst($this->type) . 'SQL';
			$roles      = new $sqlModel();
			$role       = $roles->get($options);
			$view->role = $role;

			if (isset($role['password'])) {
				//don't show password hash
				unset($role['password']);
			}

			//default role
			$role['image_url'] = Images::image($this->type);
			//featured image
			if (isset($role['image'])) {
				$role['image_url'] = Images::image($this->type, $role['image']);
			}

			$view->role = $role;
		}
	}

	function save() {
		$validator = new Validator([$this->type]);
		$view      = View :: getInstance();

		$role_id = (int)($this->request->get[$this->type . '_id'] ?? 0);
		$role    = $this->request->post[$this->type] ?? [];

		if (($errors = $validator->validate($role)) === true) {
			$sqlModel = 'Vvveb\Sql\\' . ucfirst($this->type) . 'SQL';
			$roles    = new $sqlModel();
			$role     = $this->request->post[$this->type] ?? [];

			if ($role_id) {
				$result  = $roles->edit([$this->type . '_id' => $role_id, $this->type => $role]);

				if ($result >= 0) {
					$this->view->success[] = $this->type . ' saved!';
				} else {
					$this->view->errors[] = $roles->error;
				}
			} else {
				$return = $roles->add([$this->type => $role]);
				$id     = $return[$this->type];

				if (! $id) {
					$view->validationErrors = [$roles->error];
				} else {
					$view->success[] = 'Role added!';
					$this->redirect(['module'=> $this->type . '/role', $this->type . '_id' => $id, 'success' => $this->type . ' added!']);
				}
			}
		} else {
			$view->errors = $errors;
		}

		$this->index();
	}
}
