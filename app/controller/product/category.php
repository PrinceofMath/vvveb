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

namespace Vvveb\Controller\Product;

use Vvveb\Controller\Base;
use Vvveb\System\Components;
use Vvveb\System\Core\View;

class Category extends Base {
	function index() {
		$product = new \Vvveb\Sql\CategorySQL();

		$results = $product->getCategory(['site_id' => 0, 'language_id' => 1, 'slug'  => $this->request->get['slug']]);

		$this->request->request['taxonomy_item_id'] = $results['taxonomy_item_id'];
		/*
				$category = Components::get('category');

				if (! $category) {
					//$this->notFound();
				}
		*/
		/*		
				$view = View :: getInstance();
				$products = new \Vvveb\Sql\ProductSQL();
		
				$page = isset($this->request->get['page'])?$this->request->get['page']:1;
				$limit = 2;
		
				$results = $products->getAll(
					['start' => ($page -1) * $limit, 
					'count' => $limit,
					'language_id' => 1,
					'site_id' => 1,
					//'include_manufacturer' => true,
					'include_discount' => true,
					'include_special' => true,
					'include_reward' => true,
					'include_manufacturer' => true,
					//'include_stock_status' => true,
					]
				);

				$view->products = $results['products'];
				$view->count = $results['count'];
				$view->limit = $limit;*/
	}
}
