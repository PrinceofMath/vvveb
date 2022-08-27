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
	//homepage
	'/'  => ['module' => 'index/index'],
	//pagination for blog posts
	'/page/#page#'  => ['module' => 'index/index'],

	//user
	'/user/login'    => ['module' => 'user/login/index'],
	'/user/signup'   => ['module' => 'user/signup/index'],
	'/user/recover'  => ['module' => 'user/recover/index'],
	'/user/edit'     => ['module' => 'user/edit/index'],

	//user ecommerce
	'/user/orders'                 => ['module' => 'user/orders/index'],
	'/user/order/#id#'             => ['module' => 'user/orders/order'],
	'/user/downloads'              => ['module' => 'user/downloads/index'],
	'/user/downloads/#page#'       => ['module' => 'user/downloads/index'],
	'/user/downloads/#id#'         => ['module' => 'user/downloads/download'],
	'/user/addresses'              => ['module' => 'user/addresses/index'],

	'/user/'  => ['module' => 'user/index'],

	//search
	'/search/{query}'  => ['module' => 'search'],

	//rest api
	'/rest/'                        => ['module' => 'rest'],
	'/rest/{method}'                => ['module' => 'rest'],
	'/rest/{method}/{id}'           => ['module' => 'rest'],

	//ecommerce

	//catalog - multi language - language code must be at least 2 characters
	'/{language{2,5}}/shop'                 => ['module' => 'product/index'],
	'/{language{2,5}}/shop/{slug}'          => ['module' => 'product/index'],
	'/{language{2,5}}/shop/{slug}/#page#'   => ['module' => 'product/index'],
	'/{language{2,5}}/manufacturer/{slug}'  => ['module' => 'product/manufacturer'],
	'/{language{2,5}}/product/{slug}'       => ['module' => 'product/product/index', 'edit'=>'?module=product/product&slug={slug}'],

	//catalog
	'/shop'                 => ['module' => 'product/index'],
	'/shop/{slug}'          => ['module' => 'product/index'],
	'/shop/{slug}/#page#'   => ['module' => 'product/index'],
	'/manufacturer/{slug}'  => ['module' => 'product/manufacturer'],
	'/product/{slug}'       => ['module' => 'product/product/index', 'edit'=>'?module=product/product&slug={slug}'],

	//checkout
	'/cart'          => ['module' => 'cart/cart/index'],
	'/cart/voucher'  => ['module' => 'checkout/cart/voucher'],

	'/checkout'               => ['module' => 'checkout/checkout/index'],
	'/checkout/pay'           => ['module' => 'checkout/pay'],
	'/checkout/confirm'       => ['module' => 'checkout/confirm/index'],
	'/checkout/confirm/#id#'  => ['module' => 'checkout/order/index'],

	//feeds
	'/feed/posts'     => ['module' => 'feed/posts'],
	'/feed/products'  => ['module' => 'feed/products'],
	'/feed/comments'  => ['module' => 'feed/comments'],

	//content
	'/blog'         => ['module' => 'content'],
	'/cat/{slug}'   => ['module' => 'content/category/index'],
	'/tag/{slug}'   => ['module' => 'content/tag'],
	'/{slug}'       => ['module' => 'content/post/index', 'edit'=>'?module=content/post&slug={slug}'],
	'/page/{slug}'  => ['module' => 'content/page/index'],

	//content - multi language - language code must be at least 2 characters
	'/{language{2,5}}/blog'         => ['module' => 'content'],
	'/{language{2,5}}/cat/{slug}'   => ['module' => 'content/category/language'],
	'/{language{2,5}}/tag/{slug}'   => ['module' => 'content/tag'],
	'/{language{2,5}}/{slug}'       => ['module' => 'content/post/language', 'edit'=>'?module=content/post&slug={slug}'],
	'/{language{2,5}}/page/{slug}'  => ['module' => 'content/page/language'],

	//feed
	'/feed'           => ['module' => 'feed'],
	'/feed/comments'  => ['module' => 'feed/comments/index'],
];
