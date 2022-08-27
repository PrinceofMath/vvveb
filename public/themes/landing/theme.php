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

/*
Name: Landing
URI: https://colorlib.com/wp/template/blanca/
Author: colorlib
Author URI: https://colorlib.com
Description: It could be fashion, lifestyle, food, design or a look into your life, Blanca is the free personal website template for you. With such a convenient tool, hammering out a neat and tidy blog is simple and efficient. Write a compelling About Me section, use the attractive slideshow to push your imagery or works and create an attention-grabbing portfolio.
Version: 2.0
License:  CC BY 3.0
License URI: https://colorlib.com/wp/licence/
Tags: one-column, two-columns, right-sidebar, accessibility-ready, custom-background, custom-colors, custom-header, custom-menu, editor-style, featured-images, flexible-header, microformats, post-formats, rtl-language-support, sticky-post, threaded-comments, translation-ready, blog
Text Domain: landing
*/

return
	[
		'pages' => [
			['title' =>  'Homepage', 'name' =>  'homepage', 'file'=> 'index.html', 'url'=> Vvveb\url('index/index')],
			//content
			['title' =>  'Home', 'name' =>  'blog', 'file'=> 'content/index.html', 'folder' => 'blog', 'url'=> Vvveb\url('content/index/index')],
			['title' => 'Post', 'name' =>  'post', 'file'=> 'content/post.html', 'folder' => 'blog', 'url'=> Vvveb\url('content/post/index')],
			['title' => 'Category', 'name' =>  'category', 'file'=> 'category.html', 'folder' => 'blog', 'url'=> Vvveb\url('content/category')],
			//ecommerce
			['title' =>  'Product', 'name' =>  'product', 'file'=> 'product.html', 'folder' => 'ecommerce', 'url'=> Vvveb\url('product/product/index')],
			['title' => 'Category', 'name' =>  'category', 'file'=> 'category.html', 'folder' => 'ecommerce', 'url'=> Vvveb\url('product/category/index')],
			['title' => 'Manufacturer', 'name' =>  'manufacturer', 'file'=> 'manufacturer.html', 'folder' => 'ecommerce', 'url'=> Vvveb\url('product/manufacturer/index')],
			['title' => 'Cart', 'name' =>  'cart', 'file'=> 'cart.html', 'folder' => 'ecommerce', 'url'=> Vvveb\url('checkout/cart/index')],
			['title' => 'Checkout', 'name' =>  'checkout', 'file'=> 'checkout.html', 'folder' => 'ecommerce', 'url'=> Vvveb\url('checkout/checkout/index')],
			//account
			['title' =>  'Login', 'name' =>  'login', 'file'=> 'account/login.html', 'folder' => 'account', 'url'=> Vvveb\url('account/login')],
			['title' => 'Dashboard', 'name' =>  'dashboard', 'file'=> 'account/dashboard.html', 'folder' => 'account', 'url'=> Vvveb\url('account/dashboard')],
			['title' => 'Dashboard', 'name' =>  'checkout', 'file'=> 'checkout.html', 'folder' => 'account', 'url'=> Vvveb\url('account/dashboard')],
			//mail
			['title' =>  'Account confirm', 'name' =>  'account_confirm', 'folder' => 'mail', 'file'=> 'mail/account_confirm.html', 'url'=> Vvveb\url('content/page/index', ['slug' => 'contact'])],
			['title' => 'Order confirm', 'name' =>  'order_confirm', 'folder' => 'mail', 'file'=> 'mail/order_confirm.html', 'url'=> Vvveb\url('static/index', ['page' => 'contact'])],
			//static
			['title' =>  'Contact', 'name' =>  'error404', 'file'=> 'page.html', 'folder' => 'static', 'url'=> Vvveb\url('content/page/index', ['slug' => 'contact'])],
			['title' => 'Terms of use', 'name' =>  'error404', 'file'=> 'error404.html', 'folder' => 'static', 'url'=> Vvveb\url('index/index')],
			['title' => 'Privacy policy', 'name' =>  'error404', 'file'=> 'error404.html', 'folder' => 'static', 'url'=> Vvveb\url('index/index')],
			['title' => 'Payment options', 'name' =>  'error404', 'file'=> 'error404.html', 'folder' => 'static', 'url'=> Vvveb\url('index/index')],
			['title' => 'Shipping and delivery', 'name' =>  'error404', 'file'=> 'error404.html', 'folder' => 'static', 'url'=> Vvveb\url('index/index')],
			['title' => 'Page Not found (404)', 'name' =>  'error404', 'file'=> 'error404.html', 'folder' => 'static', 'url'=> Vvveb\url('index/index')],
		],
		'components' => [
			['title' =>  'Content', 'name' =>  'content'],
			['title' => 'Ecommerce', 'name' =>  'ecommerce'],
			['title' => 'Bootstrap 4', 'name' =>  'bootstrap4'],
			['title' => 'Essence components', 'name' =>  'essence', 'file'=> 'essence-components.js'],
		],
		'inputs' => [
			['title' =>  'Slider inputs', 'name' =>  'slider', 'file'=> 'slider-inputs.js'],
		],
		'ignoreFolders' => ['backup'],
	];
