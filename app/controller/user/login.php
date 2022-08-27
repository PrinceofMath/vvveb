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

use Vvveb\System\User\User;
use Vvveb\System\Validator;

class Login extends Base {
	function index() {
		$this->checkAlreadyLoggedIn();

		if (isset($this->request->post['logout'])) {
			return User::logout();
		}

		$validator = new Validator(['login']);

		if ($this->request->post &&
			($this->view->errors = $validator->validate($this->request->post)) === true) {
			if ($userInfo = User::login($this->request->post)) {
				$this->view->messages[] = _('Login successful!');
			//$this->redirect('/user/');
			} else {
				//user not found or wrong password
				$this->view->errors = [_('Authentication failed, wrong email or password!')];
			}
		}
	}
}
