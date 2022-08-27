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
Name: Insert Footer Header Scripts
Slug: insert-scripts
Category: email
Url: http://www.vvveb.com
Description: Insert footer and header scripts such as analytics or widgets.
Thumb: insert-scripts.svg
Author: givanz
Version: 0.1
Author url: http://www.vvveb.com
Settings: /admin/?module=plugins/insert-scripts/settings
*/

use Vvveb\System\Core\View;
use Vvveb\System\Event;

class InsertScriptsPlugin {
	function admin() {
		//add admin menu item
		$admin_path = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		Event::on('Vvveb\Controller\Base', 'init-menu', __CLASS__, function ($menu) use ($admin_path) {
			$menu['plugins']['items']['insert-scripts'] = [
				'name'     => _('Insert scripts'),
				'url'      => $admin_path . '?module=plugins/insert-scripts/settings',
				'icon-img' => PUBLIC_PATH . 'plugins/insert-scripts/insert-scripts.svg',
			];

			return [$menu];
		}, 20);
	}

	function app() {
		//don't add scripts if the page is open in the editor
		if (Vvveb\isEditor()) {
			return;
		}
		$view     = View::getInstance();
		$template = $view->getTemplateEngineInstance();
		//$view->plugins ?= [];
		$view->plugins = $view->plugins ?? [];
		//return;
		$view->plugins['insert-scripts']['header'] =  Vvveb\get_option('insert-scripts.header', '');
		$view->plugins['insert-scripts']['footer'] =  Vvveb\get_option('insert-scripts.footer', '');
		$template->loadTemplateFile(__DIR__ . '/app/template/common.pst');
		//$template->addTemplatePath(__DIR__ .  '/../app/template/');
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

$insertScriptsPlugin = new InsertScriptsPlugin();
