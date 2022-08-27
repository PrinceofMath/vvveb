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
use Vvveb\System\Images;

class Products extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'start'                        => 0,
		'source'                       => 'autocomplete',
		'page'                         => 1,
		'language_id'                  => 1,
		'site_id'                      => 1,
		'limit'                        => 4,
		'parent'                       => null,
		'id_manufacturer'              => NULL,
		'order_by'                     => 'url',
		'direction'                    => ['url', 'asc'],
		'taxonomy_item_id'             => NULL,
		'include_image_gallery'        => true,
		'id'                           => NULL,
		'product_id'                   => [],
		'manufacturer_id'              => [],
		'category'                     => [],
		'search'                       => null,
	];

	public $options = [];

	function __construct($options) {
		return parent::__construct($options);
	}

	function cacheKey() {
		//disable caching
		return false;
	}

	function results() {
		$products = new \Vvveb\Sql\ProductSQL();

		if ($page = $this->options['page']) {
			$this->options['start'] = ($page - 1) * $this->options['limit'];
		}

		if (isset($this->options['product_id']) && $this->options['source'] == 'autocomplete') {
			$this->options['product_id'] = array_keys($this->options['product_id']);
		} else {
			$this->options['product_id'] = [];
		}

		if (isset($this->options['order_by']) &&
				! in_array($this->options['order_by'], ['price', 'date_added', 'date_modified'])) {
			unset($this->options['order_by']);
		}

		if (isset($this->options['direction']) &&
				! in_array($this->options['direction'], ['asc', 'desc'])) {
			unset($this->options['direction']);
		}

		$results = $products->getAll($this->options);

		if ($results && isset($results['products'])) {
			foreach ($results['products'] as $id => &$product) {
				if (isset($product['images'])) {
					$product['images'] = json_decode($product['images'], true);

					foreach ($product['images'] as &$image) {
						$image['image'] = Images::image($image['image'], 'product');
					}
				}
				/*
				if (isset($product['image'])) {
					$product['images'][] = ['image' => Images::image('product', $product['image'])];
				}*/
			}
		}

		list($results) = Event :: trigger(__CLASS__,__FUNCTION__, $results);

		return $results;
	}
}
