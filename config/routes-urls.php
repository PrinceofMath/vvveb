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

return [
	'/'  => ['index/index'],

	//user
	'/user/login'    => ['user/login/index'],
	'/user/signup'   => ['user/signup/index'],
	'/user/recover'  => ['user/recover/index'],
	'/user/edit'     => ['user/edit/index'],

	//user ecommerce
	'/user/orders'          => ['user/orders/index'],
	'/user/order/#id#'      => ['user/orders/order'],
	'/user/downloads'       => ['user/downloads/index'],
	'/user/downloads/#id#'  => ['user/downloads/download'],
	'/user/addresses'       => ['user/addresses/index'],

	'/user/'  => ['user/index'],

	//search
	'/search/{query}'  => ['search'],

	//ecommerce

	//catalog
	'/shop'                => ['product/index'],
	'/shop/{slug}'         => ['product/index'],
	'/shop/{slug}/#page#'  => ['product/index'],

	//'/shop/{slug}/#page#'  =>  ['product/category/index'],
	'/manufacturer/{slug}'  => ['product/manufacturer'],
	'/product/{slug}'       => ['product/product/index'],

	//checkout
	'/cart'          => ['cart/cart/index'],
	'/cart/voucher'  => ['checkout/cart/voucher'],

	'/checkout'               => ['checkout/checkout/index'],
	'/checkout/pay'           => ['checkout/pay'],
	'/checkout/confirm'       => ['checkout/confirm/index'],
	'/checkout/confirm/#id#'  => ['checkout/order/index'],

	//feeds
	'/feed/posts'     => ['feed/posts'],
	'/feed/products'  => ['feed/products'],
	'/feed/comments'  => ['feed/comments'],

	//content
	'/blog'         => ['content'],
	'/cat/{slug}'   => ['content/category/index'],
	'/tag/{slug}'   => ['content/tag'],
	'/{slug}'       => ['content/post/index'],
	'/page/{slug}'  => ['content/page/index'],

	//feed
	'/feed'           => ['feed'],
	'/feed/comments'  => ['feed/comments/index'],

	//api
];
