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
use Vvveb\System\Validator;

class Product extends Base {
	protected $type = 'product';

	private function taxonomies($post_id = false) {
		//get taxonomies for post type
		$taxonomies = new \Vvveb\Sql\taxonomySQL();
		$results    = $taxonomies->getTaxonomies(
			['post_type'    => $this->type]
		);

		//get taxonomies content
		if ($results) {
			$taxonomy_itemSql = new \Vvveb\Sql\categorySQL();

			$options =  [
				'taxonomy'   => 'post',
				'start'      => 0,
				'limit'      => 100,
			] + $this->global;

			if ($post_id) {
				$options['post_id'] = $post_id;
			}

			foreach ($results as $id => &$taxonomy_item) {
				$taxonomy_item['taxonomy_item'] = [];
				//for tags don't retrive taxonomies if no post id provided
				if ($taxonomy_item['type'] != 'tags' || $post_id) {
					$taxonomy_item['taxonomy_item'] = $taxonomy_itemSql->getCategories($options + ['taxonomy_id' => $id, 'type' => $taxonomy_item['type']]);
				}
			}
		}

		return $results;
	}

	function categoriesAutocomplete() {
		$categories = new \Vvveb\Sql\CategorySQL();

		$results = $categories->getCategories([
			'start'       => 0,
			'limit'       => 10,
			'language_id' => 1,
			'site_id'     => 1,
			'search'      => '%' . $this->request->get['text'] . '%',
		]
		);

		foreach ($results['categories'] as $category) {
			$search[$category['taxonomy_item_id']] = $category['name'];
		}

		$view         = $this->view;
		$view->noJson = true;

		echo json_encode($search);

		return false;
	}

	function productsAutocomplete() {
		$products = new \Vvveb\Sql\ProductSQL();

		$results = $products->getAll([
			'start'       => 0,
			'limit'       => 10,
			'language_id' => 1,
			'site_id'     => 1,
			'search'      => '%' . $this->request->get['text'] . '%',
		]
		);

		foreach ($results['products'] as $product) {
			$search[$product['product_id']] = $product['name'];
		}

		echo json_encode($search);

		return false;
	}

	function index() {
		$view = $this->view;

		$admin_path          = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		$this->view->scanUrl = $admin_path . 'index.php?module=media/media&action=scan';

		$products       = new ProductSQL();
		$view->data     = $products->getData();
		$productOptions = [];

		if (isset($this->request->get['product_id'])) {
			$productOptions['product_id'] = (int)$this->request->get['product_id'];
		} else {
			if (isset($this->request->get['slug'])) {
				$productOptions['slug'] = $this->request->get['slug'];
			}
		}

		if ($productOptions) {
			$product = $products->get($productOptions + $this->global);

			//featured image
			if (isset($product['image'])) {
				$product['image_url'] = Images::image($product['image'], 'product');
			}

			//gallery
			if (isset($product['images'])) {
				$product['images'] = Images::images($product['images'], 'product');
			}

			//$productImages = $products->getImages($productOptions);
			$view->data['status'] = [0 => 'Disabled', 1 => 'Enabled'];
		} else {
			$product['image_url'] = Images::image('', 'product');
		}

		$product['url']        = isset($product['product_description'][1]['slug']) ? \Vvveb\url('product/product/index', ['slug'=> $product['product_description'][1]['slug']]) : '';
		$template              = \Vvveb\getCurrentTemplate();
		$admin_path            = \Vvveb\config('admin.path', 'admin') . '/';
		$product['design_url'] = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'template' => $template, 'url' => $product['url']], false, false);

		$view->product        = $product;
		$view->taxonomies     = $this->taxonomies($product['product_id'] ?? false);
		$view->status         = ['publish', 'draft', 'pending', 'private', 'password'];
		$view->templates      = \Vvveb\getTemplateList();
		$validator            = new Validator(['product']);
		$view->validatorJson  = $validator->getJSON();
	}

	function save() {
		$validator = new Validator(['product']);
		//var_dump($_POST);

		$product = $this->request->post;
		//if (($this->view->validationErrors = $validator->validate($product)) === true)
		{
			$products = new ProductSQL();

			if (isset($this->request->get['product_id'])) {
				$productId = (int)$this->request->get['product_id'];
				//var_dump($product['product_array']);
				$result = $products->edit(['product' => $product, 'product_id' => $productId]);

				if ($result >= 0) {
					$this->view->success = ['Product saved!'];
				} else {
					$this->view->validationErrors = [$products->error];
				}
			} else {
				$result = $products->add(['product' => $product, 'site_id' => 0]);

				if (! $result['product']) {
					$this->view->validationErrors = [$products->error];
				} else {
					$successMessage        = _('Product saved!');
					$this->view->success[] = $successMessage;
					$this->redirect(['module' => 'product/product', 'product_id' => $result['product'], 'success' => $successMessage]);
				}
			}
		}

		$this->index();
	}

	function draft() {
	}

	function preview() {
	}
}
