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
	'index/index' => ['/'],

	//user
	'user/login/index'   => ['/user/login'],
	'user/signup/index'  => ['/user/signup'],
	'user/recover/index' => ['/user/recover'],
	'user/edit/index'    => ['/user/edit'],

	//user ecommerce
	'user/orders/index'       => ['/user/orders'],
	'user/orders/order'       => ['/user/order/#id#'],
	'user/downloads/index'    => ['/user/downloads'],
	'user/downloads/download' => ['/user/downloads/#id#'],
	'user/addresses/index'    => ['/user/addresses'],

	'user/index' => ['/user/'],

	//search
	'search' => ['/search/{query}'],

	//ecommerce

	//catalog
	'product/index'          => ['/shop'],
	'product/category/index' => ['/shop/{slug}', '/shop/{slug}/#page#'],
	//'product/category/index' => ['/shop/{slug}/#page#'],
	'product/manufacturer'  => ['/manufacturer/{slug}'],
	'product/product/index' => ['/product/{slug}'],

	//checkout
	'cart/cart/index'       => ['/cart'],
	'checkout/cart/voucher' => ['/cart/voucher'],

	'checkout/checkout/index' => ['/checkout'],
	'checkout/pay'            => ['/checkout/pay'],
	'checkout/confirm/index'  => ['/checkout/confirm'],
	'checkout/order/index'    => ['/checkout/confirm/#id#'],

	//feeds
	'feed/posts'    => ['/feed/posts'],
	'feed/products' => ['/feed/products'],
	'feed/comments' => ['/feed/comments'],

	//content
	'content'                   => ['/blog'],
	'content/category/index'    => ['/cat/{slug}'],
	'content/tag'	              => ['/tag/{slug}'],
	'content/post/index'        => ['/{slug}'],
	'content/page/index'        => ['/page/{slug}'],

	//feed
	'feed'                   => ['/feed'],
	'feed/comments/index'    => ['/feed/comments'],

	//api
];
