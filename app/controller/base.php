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

namespace Vvveb\Controller;

use \Vvveb\System\Core\FrontController;
use \Vvveb\System\Functions\Str;
use Vvveb\System\Core\View;
use Vvveb\System\User\Admin;

class Base {
	function __construct() {
	}

	function init() {
		if (! $this->session->get('csrf')) {
			$this->session->set('csrf', Str::random());
		}

		//check if theme preview
		$theme = $this->request->get['theme'] ?? false;

		if ($theme) {
			//check if admin user to allow theme preview
			$admin = Admin::current();
			$this->view->setTheme($theme);
		}
	}

	function redirect($url = '/', $parameters = []) {
		$redirect = \Vvveb\url($url, $parameters);

		if ($redirect) {
			$url = $redirect;
		}

		$this->session->close();

		return header("Location: $url");
	}

	/**
	 * Call this method if the action requires login, if the user is not logged in, a login form will be shown.
	 *
	 */
	function requireLogin() {
		$view = view :: getInstance();
		$view :: template('/login.html');

		die(view :: getInstance()->render());
	}

	/**
	 * Call this function if the requeste information was not found, for example if the specifed news, image, profile etc is not found then call this function.
	 * It shows a "Not found" page and it also send 404 http status code, this is usefull for search engines etc.
	 *
	 * @param unknown_type $code
	 */
	function notFound($statusCode = 404, $service = false) {
		return FrontController::notFound($service, $statusCode);
	}
}
