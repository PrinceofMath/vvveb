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

 $admin_path = '/' . \Vvveb\config('admin.path', 'admin') . '/';

return
[
	'name'            => _('Products'),
	'url'             => $admin_path . '?module=product/products',
	'icon'            => 'ion-ios-pricetag-outline',
	'show_on_modules' => ['Product/products', 'Product/product', 'Product/categories'],

	'items' => [
		'products' => [
			'name' => _('Products'),
			'url'  => $admin_path . '?module=product/products',
			'icon' => 'la la-box',
		],

		'addpage' => [
			'name' => _('Add new'),
			'url'  => $admin_path . '?module=product/product',
			'icon' => 'la la-plus-circle',
		],

		'categories-heading' => [
			'name'    => _('Taxonomy'),
			'heading' => true,
		],

		'categories' => [
			'name' => _('Categories'),
			'url'  => $admin_path . '?module=product/categories',
			'icon' => 'la la-boxes',
		],

		'manufacturers' => [
			'name' => _('Manufacturers'),
			'url'  => $admin_path . '?module=product/manufacturers',
			'icon' => 'la la-industry',
		],

		'vendors' => [
			'name' => _('Vendors'),
			'url'  => $admin_path . '?module=product/vendors',
			'icon' => 'la la-store',
		],

		'configuration-heading' => [
			'name'    => _('Configuration'),
			'heading' => true,
		],

		'custom-fields' => [
			'name' => _('Custom fields'),
			'url'  => $admin_path . '?module=product/fields',
			'icon' => 'la la-stream',
		],

		'options' => [
			'name' => _('Options'),
			'url'  => $admin_path . '?module=product/options',
			'icon' => 'la la-filter',
		],

		'digital' => [
			'name' => _('Digital content'),
			'url'  => $admin_path . '?module=product/options',
			'icon' => 'la la-cloud-download-alt',
		],

		'configuration-heading' => [
			'name'    => _('Configuration'),
			'heading' => true,
		],

		'reviews' => [
			'name' => _('Reviews'),
			'url'  => $admin_path . '?module=product/reviews',
			'icon' => 'la la-comment',
			//'badge' => '5',
			//'badge-class' => 'badge bg-warning float-end',
		],

		'questions' => [
			'name' => _('Questions'),
			'url'  => $admin_path . '?module=product/questions',
			'icon' => 'la la-question-circle',
			//'badge' => '7',
			//'badge-class' => 'badge bg-danger float-end',
		],

		/*					
		'filters' => [
			'name' => _('Filters'),
			'url' => $admin_path . '?module=categories',
		]*/
	],
];
