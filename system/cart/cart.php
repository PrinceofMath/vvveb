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

namespace Vvveb\System\Cart;

use Vvveb\System\Images;
use Vvveb\System\Session;

class Cart {
	public $cart = [];

	protected $session;

	private $data = [];

	public static function getInstance() {
		static $inst = null;

		if ($inst === null) {
			$inst = new Cart();
		}

		return $inst;
	}

	private function __construct() {
		$this->session = Session :: getInstance();

		$this->cart = $this->session->get('cart');

		if (! isset($this->cart['total_items'])) {
			$this->cart['total_items'] = 0;
		}

		if (! isset($this->cart['total'])) {
			$this->cart['total'] = 0;
		}

		if (! isset($this->cart['products'])) {
			$this->cart['products'] = [];
		}
	}

	private function updateCart() {
		$this->cart['total']       = 0;
		$this->cart['total_items'] = 0;

		$results = ['products' => [], 'count' => 0];

		if (! empty($this->cart) && ! empty($this->cart['products'])) {
			$productIds = array_keys($this->cart['products']);

			$products = new \Vvveb\Sql\ProductSQL();
			$results  = $products->getAll(
				['product_id'            => $productIds,
					'start'                 => 0,
					'limit'                 => 300,
					'language_id'           => 1,
					'include_image_gallery' => true,
					'site_id'               => 1, ]
			);
		}

		foreach ($results['products'] as $id => &$product) {
			$productId = $product['product_id'];

			if (isset($product['images'])) {
				$product['images'] = explode(',', $product['images']);

				foreach ($product['images'] as &$image) {
					$image = Images::image('product', $image);
				}
			}

			if (isset($product['image'])) {
				$product['images'][] = Images::image('product', $product['image']);
			}

			$amount = $this->cart['products'][$productId]['amount'] ?? 1;
			$this->cart['total'] += $product['price'] * $amount;
			$this->cart['total_items'] += $amount;

			if (isset($this->cart['products'][$productId])) {
				$product = array_merge($product, $this->cart['products'][$productId]);
			}
			$this->cart['products'][$productId] = $product;
		}

		$this->session->set('cart', $this->cart);

		return $results;
	}

	function add($productId, $amount = 1, $option = [], $recurringId = 0, $subscriptionPlanId = 0) {
		if (! $productId) {
			return false;
		}

		if (isset($this->cart['products'][$productId])) {
			$this->cart['products'][$productId]['amount'] += $amount;
		} else {
			$this->cart['products'][$productId] =
			[
				'amount'       => $amount,
				'option'       => $option,
				'recurring_id' => $recurringId,
			];
		}

		return $this->updateCart();
	}

	function update($productId, $amount = 1) {
		$this->cart['products'][$productId]['amount'] = $amount;

		return $this->updateCart();
	}

	function getAll() {
		//$this->updateCart();
		//error_log(print_r($this->cart['products'], 1));
		return $this->cart['products'] ?? [];
	}

	function getCart() {
		//$this->updateCart();
		//error_log(print_r($this->cart['products'], 1));
		return $this->cart;
	}

	function getNoProducts() {
		//error_log(print_r($this->cart['products'], 1));
		return count($this->cart['products'] ?? []);
	}

	function remove($productId) {
		unset($this->cart['products'][$productId]);
		$this->updateCart();
	}

	public function getSubscription() {
		$product_data = [];

		foreach ($this->getProducts() as $value) {
			if ($value['subscription']) {
				$product_data[] = $value;
			}
		}

		return $product_data;
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = [];

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (! isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->getProducts());
	}

	public function hasSubscription() {
		return count($this->getSubscription());
	}

	public function hasStock() {
		foreach ($this->getProducts() as $product) {
			if (! $product['stock']) {
				return false;
			}
		}

		return true;
	}

	public function hasShipping() {
		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				return true;
			}
		}

		return false;
	}

	public function hasDownload() {
		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				return true;
			}
		}

		return false;
	}
}
