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

namespace Vvveb\Controller\Content;

use \Vvveb\Sql\menuSQL;

class Menus extends Categories {
	function delete() {
		$menu_item_id = $this->request->post['menu_item_id'] ?? false;
		$menus        = new menuSQL();

		//['menu_items' => $data]
		if ($menu_item_id && $menus->deleteMenuItem(['menu_item_id' => $menu_item_id])) {
			echo _('Item removed!');
		}

		die();
	}

	function reorder() {
		$data       = $this->request->post;
		$menus      = new menuSQL();

		//['menu_items' => $data]
		if ($menus->updateMenuItems($data)) {
			echo _('Items reordered!');
		}

		die();
	}

	function add() {
		$data = $this->request->post;

		$menus  = new menuSQL();

		if (isset($data['menu_item_id']) && $data['menu_item_id']) {
			$results = $menus->editMenuItem(['menu_item' => $data, 'menu_item_id' => $data['menu_item_id']]);
		} else {
			$results = $menus->addMenuItem(['menu_item' => $data]);
		}

		die();

		return;
	}

	function menu() {
		$menuId      = $this->request->get['menu_id'];
		$view        = $this->view;
		$menus       = new menuSQL();

		$page    = $this->request->get['page'] ?? 1;

		$options = [
			'start'                   => ($page - 1) * $limit,
			'menu_id'            	    => $menuId, //menus
		] + $this->global;

		$results = $menus->getMenuAllLanguages($options);

		foreach ($results['categories'] as &$menu) {
			$langs                 = json_decode($menu['languages'], true);
			$menu['languages']     = [];

			if ($langs) {
				foreach ($langs as $lang) {
					$menu['languages'][$lang['language_id']] = $lang;
				}

				$menu['name'] = $langs[0]['name'] ?? '';
			}
		}

		$view->menu_id = $menuId;
		$view->set($results);

		//return 'content/menu.html';
	}

	function index() {
		$view        = $this->view;
		$menus       = new menuSQL();

		$options = [
			'limit' => 10000,
		] + $this->global;

		$results = $menus->getMenusList($options);

		foreach ($results['menus'] as &$menu) {
			$url                = ['module' => 'content/menus', 'action' => 'menu', 'menu_id' => $menu['menu_id']];
			$menu['url']        = \Vvveb\url($url);
			$menu['edit-url']   = $menu['url'];

			$langs                 = json_decode($menu['languages'], true);
			$menu['languages']     = [];

			if ($langs) {
				foreach ($langs as $lang) {
					$menu['languages'][$lang['language_id']] = $lang;
				}

				$menu['name'] = $langs[0]['name'] ?? '';
			}
		}
		$view->set($results);
	}
}
