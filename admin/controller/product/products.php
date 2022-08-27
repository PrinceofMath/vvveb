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
use Vvveb\Sql\ProductSQL;
use Vvveb\System\Core\View;
use Vvveb\System\Images;

class Products extends Base {
	protected $type = 'product';

	function init() {
		if (isset($this->request->get['type'])) {
			$this->type = $this->request->get['type'];
		}

		return parent::init();
	}

	function delete2() {
		$product_id = $this->request->request['product_id'];

		if (is_array($product_id)) {
		} else {
			if (is_numeric($product_id)) {
				var_dump($product_id);
			}
		}

		return $this->index();
	}

	function delete() {
		$product_id    = $this->request->get['product_id'] ?? false;

		if ($product_id) {
			$products = new ProductSQL();
			$options  = ['product_id' => $product_id] + $this->global;
			$result   = $products->deleteProduct($options);

			if ($result && $result['product'] > 0) {
				$this->view->success[] = 'Product deleted!';
			} else {
				$this->view->errors[] = 'Error deleting product!';
			}
		}

		return $this->index();
	}

	function index() {
		$view     = View :: getInstance();
		$products = new ProductSQL();

		$page    = $this->request->get['page'] ?? 1;
		$limit   = $this->request->get['limit'] ?? 10;

		$options = [
			'type'        => $this->type,
			//'include_manufacturer' => true,
			'include_discount'     => true,
			'include_special'      => true,
			'include_reward'       => true,
			'include_manufacturer' => true,
			//'include_stock_status' => true,
		] + $this->global;

		$results = $products->getAll($options);

		foreach ($results['products'] as $id => &$product) {
			if (isset($product['images'])) {
				$product['images'] = json_decode($product['images'], 1);

				foreach ($product['images'] as &$image) {
					$image = Images::image($image, 'product');
				}
				//	var_dump($product['images']);
			} else {
				if (isset($product['image'])) {
					$product['image'] = Images::image($product['image'], 'product');
				}
			}

			$product['url']        = \Vvveb\url(['module' => 'product/product', 'product_id' => $product['product_id']]);
			$product['edit-url']   = \Vvveb\url(['module' => 'product/product', 'product_id' => $product['product_id']]);
			$product['delete-url'] = \Vvveb\url(['module' => 'product/products', 'action' => 'delete', 'product_id' => $product['product_id']]);
			$product['view-url']   =  \Vvveb\url('product/product/index', $product);
			$admin_path            = \Vvveb\config('admin.path', 'admin') . '/';
			$product['design-url'] = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'url' => $product['view-url']], false, false);
		}

		$view->products = $results['products'];
		$view->count    = $results['count'];
		$view->limit    = $limit;

		//$results['count'] = $products->count();
		//$view->count = 10;
		//$view->limit = $products->limit;
	}
}
