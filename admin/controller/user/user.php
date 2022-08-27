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

namespace Vvveb\Controller\User;

use Vvveb\Controller\Base;
use Vvveb\System\Core\View;
use Vvveb\System\Images;
use Vvveb\System\Validator;

class User extends Base {
	protected $type = 'user';

	function index() {
		$view = View :: getInstance();

		$user_id = (int)($this->request->get[$this->type . '_id'] ?? 0);

		if ($user_id) {
			$options = [$this->type . '_id' => (int)$user_id];

			$sqlModel   = 'Vvveb\Sql\\' . ucfirst($this->type) . 'SQL';
			$users      = new $sqlModel();
			$user       = $users->get($options);
			$view->user = $user;

			if (isset($user['password'])) {
				//don't show password hash
				unset($user['password']);
			}

			//default user
			$user['image_url'] = Images::image($this->type);
			//featured image
			if (isset($user['image'])) {
				$user['image_url'] = Images::image($this->type, $user['image']);
			}

			$view->user = $user;
		}
	}

	function save() {
		$validator = new Validator([$this->type]);
		$view      = View :: getInstance();

		$user_id = (int)($this->request->get[$this->type . '_id'] ?? 0);
		$user    = $this->request->post[$this->type] ?? [];

		if (($errors = $validator->validate($user)) === true) {
			$sqlModel = 'Vvveb\Sql\\' . ucfirst($this->type) . 'SQL';
			$users    = new $sqlModel();
			$user     = $this->request->post[$this->type] ?? [];

			if ($user_id) {
				$result  = $users->edit([$this->type . '_id' => $user_id, $this->type => $user]);

				if ($result >= 0) {
					$this->view->success[] = $this->type . ' saved!';
				} else {
					$this->view->errors[] = $users->error;
				}
			} else {
				$return = $users->add([$this->type => $user]);
				$id     = $return[$this->type];

				if (! $id) {
					$view->validationErrors = [$users->error];
				} else {
					$view->success[] = 'User added!';
					$this->redirect(['module'=> $this->type . '/user', $this->type . '_id' => $id, 'success' => $this->type . ' added!']);
				}
			}
		} else {
			$view->errors = $errors;
		}

		$this->index();
	}
}
