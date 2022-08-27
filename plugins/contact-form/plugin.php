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

/**
 * @package Contact form plugin
 * @version 0.1
 */
/*
Name: Contact form
Slug: contact-form
Category: email
Url: http://www.vvveb.com
Description: Create contact forms that sends email or saves data in the database
Thumb: contact-form.svg
Author: givanz
Status: active
Settings: /admin/?module=plugins/akismet/settings
Version: 0.1
Author url: http://www.vvveb.com
*/

use Vvveb\System\Event;

class ContactFormPlugin {
	function admin() {
		//add admin menu item
		$admin_path = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		Event::on('Vvveb\Controller\Base', 'init-menu', __CLASS__, function ($menu) use ($admin_path) {
			$menu['plugins']['items']['contact-form'] = [
				'name'     => _('Contact form'),
				'url'      => $admin_path . '',
				'icon-img' => PUBLIC_PATH . 'plugins/contact-form/contact-form.svg',
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

$contactFormPlugin = new ContactFormPlugin();
