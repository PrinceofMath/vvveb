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

use Vvveb\System\Cart\Cart;
use Vvveb\System\Cart\Order;
use Vvveb\System\Component\ComponentBase;
use Vvveb\System\Validator;

class Checkout extends ComponentBase {
	public static $designOnly = false;

	public static $defaultOptions = [
		'limit' => 1000,
		'page'  => 1,
	];

	public $cacheExpire = 0; //seconds

	function cacheKey() {
		//disable caching
		return false;
	}

	protected $options = [];

	function results() {
		return false;

		if (isset($this->request->post['first_name'])) {
			$validator = new Validator(['checkout']); //, 'checkout_payment', 'checkout_shipping']);

			if ($this->request->post &&
				($this->view->errors = $validator->validate($this->request->post)) === true) {
				$cart = Cart :: getInstance();
				//allow only fields that are in the validator list and remove the rest
				$checkoutInfo             = $validator->filter($this->request->post);
				$checkoutInfo['products'] = $cart->getAll();
				$checkoutInfo             = Order::add($checkoutInfo);

				$this->view->errors = [];

				if ($checkoutInfo) {
					if (is_array($checkoutInfo)) {
						$this->view->messages[] = _('checkout created!');
					} else {
						$this->view->errors[] = _('This email has already been used!');
					}
				} else {
					$this->view->errors[] = _('Error creating checkout!');
				}
			}
		}
	}

	function old() {
		if ($_POST) {
			if (isset($_POST['payment_method']) && isset($_POST['shipping_method']) && isset($_SESSION['checkout']['id'])) {
				/*
				  header('Location: http://' . HOST . '.vvveb.com/payment');
				*/
				$orders  = new orders();
				$idOrder = $orders->save($_SESSION['checkout']['id'], $_POST['payment_method'], $_POST['shipping_method']);
				//$orders->confirmationMail($_POST['email']);
				$results['order_confirmation'] = true;

				//send new order email
				$email = new email();
				$email->order(EMAIL_ADMIN_NEW_ORDER, $idOrder);

				$email = new email();
				$email->order(EMAIL_ORDER_PENDING, $idOrder);

				unset($_SESSION['cart']);

				$paymentOptions = new payment_options();
				//if credit card payment requiered then redirect to payment
				if ($paymentOptions->options[$_POST['payment_method']]['type'] == PAYMENT_GATEWAY_ACTIVEMERCHANT) {
					$key = crypt(SITE_ID . $idOrder . $_POST['payment_method'] . 'gigisecretkey', '$6$rounds=5000$seasalt$');
					$key = substr($key, strrpos($key, '$') + 1);

					$parameters = 's=' . SITE_ID . "\no=" . $idOrder . "\nt=" . $_POST['payment_method'] . "\nk=" . $key;
					$parameters = base64_encode($parameters);

					return header('Location: http://payment.vvveb.com?p=' . $parameters);
				}

				return header('Location: /checkout/success');
				unset($_POST['payment_method']); //don't add orders twice if two components on same page
			}
		}

		$paymentOptions             = new payment_options();
		$results['payment_options'] = $paymentOptions->get();

		$shippingOptions             = new shipping_options();
		$results['shipping_options'] = $shippingOptions->get();

		$cart = cart::getInstance();

		return $results;
	}
}
