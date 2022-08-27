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

/*
Name: Cookie Notice
Slug: cookie-notice
Category: Cookie notice
Url: https://www.vvveb.com
Description: Show cookie notice for gdpr
Author: givanz
Version: 0.1
Thumb: cookie-notice.svg
Author url: http://www.vvveb.com
*/

use Vvveb\System\Event;

class CookieNoticePlugin {
	function admin() {
		//add admin menu item
		$admin_path = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		Event::on('Vvveb\Controller\Base', 'init-menu', __CLASS__, function ($menu) use ($admin_path) {
			$menu['plugins']['items']['cookie-notice'] = [
				'name'     => _('Cookie notice'),
				'url'      => $admin_path . '',
				'icon-img' => PUBLIC_PATH . 'plugins/cookie-notice/cookie-notice.svg',
			];

			return [$menu];
		}, 20);
	}

	function app() {
	}

	function __construct() {
		if (APP == 'admin') {
			$this->admin();
		} else {
			if (APP == 'app') {
				$this->app();
			}
		}
	}
}

$cookieNoticePlugin = new CookieNoticePlugin();
