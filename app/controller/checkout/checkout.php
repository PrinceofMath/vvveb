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

namespace Vvveb\Controller\Checkout;

use Vvveb\Controller\Base;
use Vvveb\System\Cart\Cart;
use Vvveb\System\Cart\Order;
use Vvveb\System\Core\View;
use Vvveb\System\Validator;

class Checkout extends Base {
	function index() {
		if (isset($this->request->post['first_name'])) {
			$validator = new Validator(['checkout']); //, 'checkout_payment', 'checkout_shipping']);

			if ($this->request->post &&
				($this->view->errors = $validator->validate($this->request->post)) === true) {
				$cart = Cart :: getInstance();
				//allow only fields that are in the validator list and remove the rest
				$checkoutInfo             = $validator->filter($this->request->post);
				$checkoutInfo['products'] = $cart->getAll();
				$order                    = Order::add($checkoutInfo);

				$this->view->errors = [];

				if ($order && is_array($order)) {
					$this->view->messages[] = _('Order placed!');
					$this->session->set('order', $order);

					return $this->redirect('checkout/confirm/index');
				} else {
					$this->view->errors[] = _('Error creating checkout!');
				}
			}
		}
	}

	function old() {
		$cart = Cart::getInstance();

		if (isset($this->request->get['product_id'])) {
			$cart->add($this->request->get['product_id']);
		}

		$results = $cart->getAll();

		$validator = new Validator(['checkout']);

		if ($this->request->post &&
			($valid = $validator->validate($this->request->post)) === true) {
		}

		//$this->view->products = $results['products'];
		//$this->view->count = $results['count'];

		//$this->view->
	}
}
