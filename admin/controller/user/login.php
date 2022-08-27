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

use \Vvveb\System\Functions\Str;
use Vvveb\System\User\Admin;
use Vvveb\System\Validator;

class Login {
	function redirect($url = '/', $parameters = []) {
		$redirect = \Vvveb\url($url, $parameters);

		if ($redirect) {
			$url = $redirect;
		}

		//$this->session->close();

		return header("Location: $url");
	}

	function index() {
		if (isset($this->request->post['logout'])) {
			return Admin::logout();
		}

		//$this->checkAlreadyLoggedIn();

		$admin      = Admin::current();
		$admin_path = '/' . \Vvveb\config('admin.path', 'admin') . '/';

		if ($admin) {
			return $this->redirect($admin_path);
		}

		$this->view->action = "$admin_path/?module=user/login";

		$validator = new Validator(['login']);

		if (isset($this->request->get['module']) && $this->request->get['module'] != 'user/login') {
			$this->view->redirect = $this->request->get['module'];
		}

		if (! empty($this->request->post) &&
			($this->view->errors = $validator->validate($this->request->post)) === true) {
			$user = $this->request->post['user'];

			if (strpos($user, '@')) {
				$loginData['email'] = $user;
			} else {
				$loginData['user'] = $user;
			}

			$loginData['password'] = $this->request->post['password'];

			if ($userInfo = Admin::login($loginData)) {
				$this->view->messages[] = _('Login successful!');

				if (isset($this->request->post['redirect']) && $this->request->post['redirect'] && $_SERVER['REQUEST_URI'] != $this->request->post['redirect']) {
					$url = parse_url($this->request->post['redirect']);
					$this->redirect($url['path'] . '?' . ($url['query'] ?? '') . '#' . ($url['fragment'] ?? ''));
				//$this->redirect($this->request->post['redirect']);
				} else {
					$this->redirect($admin_path);
				}
			} else {
				//user not found or wrong password
				$this->view->errors = [_('Authentication failed, wrong email or password!')];
				$this->session->set('csrf', Str::random());
			}
		} else {
			//return $this->redirect($admin_path);
			$this->session->set('csrf', Str::random());
		}
	}
}
