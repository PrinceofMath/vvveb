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

namespace Vvveb\Component;

use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Event;

class Menu extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'start'                      => 0,
		'limit'                      => ['url', 1000],
		'menu_id'                    => ['url'],
	];

	function results() {
		$menuSql               = new \Vvveb\Sql\menuSQL();
		$results               = $menuSql->getMenus($this->options);
		$current_category_slug = false;
		//count the number of child menus (subcategories) for each category
		if (isset($results['menus'])) {
			foreach ($results['menus'] as $taxonomy_item_id => &$category) {
				$parent_id = $category['parent_id'] ?? false;

				if ($current_category_slug == $category['slug']) {
					$category['active'] = true;
				} else {
					$category['active'] = false;
				}

				if (! isset($category['children'])) {
					$category['children'] = 0;
				}

				if ($parent_id > 0) {
					$parent = &$results['menus'][$parent_id];

					if (isset($parent['children'])) {
						$parent['children']++;
					} else {
						$parent['children'] = 1;
					}
				}
			}
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
