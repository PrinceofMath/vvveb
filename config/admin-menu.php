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
 	'dashboard' => [
 		'name' => _('Dashboard'),
 		'url'  => $admin_path . '',
 		'icon' => 'ion-ios-pulse',
 	],

 	'edit' => [
 		'name' => _('Edit website'),
 		'url'  => Vvveb\url(['module' => '/editor/editor', 'template' => 'index.html', 'url' => '/']), //$admin_path . '?module=editor',
 		'icon' => 'ion-ios-color-wand-outline',
 	],
 	/*
	'posts' => 
	[
		'name' => _('Posts'),
		'url' => $admin_path . '?module=content/posts',
		'icon' => 'ion-ios-photos-outline',
		'show_on_modules' => ['posts', 'post', 'pages', 'categories'],

		'items' => [
			'posts' => [
				'name' => _('Posts'),
				'url' => $admin_path . '?module=content/posts',
				'icon' => 'la la-file-alt',
			],

			'addpost' => [
				'name' => _('Add new post'),
				'url' => $admin_path . '?module=content/post',
				'icon' => 'la la-plus-circle',
			],
			

			'classification-heading' => 
			[
				'name' => _('Classification'),
				'heading' => true
			],
			
			'categories' => 
			[
				'name' => _('Categories'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-boxes',
			],
			
			'tags' => [
				'name' => _('Tags'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-tags',
			],
			
			'categories-heading' => 
			[
				'name' => _('General'),
				'heading' => true
			],
			
			'comments' => [
				'name' => _('Comments'),
				'url' => $admin_path . '?module=content/comments',
				'icon' => 'la la-comments',
			],
			
			'custom-fields' => [
				'name' => _('Custom fields'),
				'url' => $admin_path . '?module=content/fields',
				'icon' => 'la la-stream',
			],		
		
			'taxonomies' => [
				'name' => _('Taxonomies'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-boxes',
				'class' => 'align-top',
				
				'items' => [
					'categories' => [
						'name' => _('Categories'),
						'subtitle' => _('(Hierarchical)'),
						'url' => $admin_path . '?module=content/categories',
						'icon' => 'la la-boxes',
					],
					
					'tags' => [
						'name' => _('Tags'),
						'subtitle' => _('(Flat)'),
						'url' => $admin_path . '?module=content/categories',
						'icon' => 'la la-tags',
					],
				],
			],		
			
		]
	], 
	*/
 	'pages' => [
 		'name' => _('Pages'),
 		'url'  => $admin_path . '?module=content/posts&type=page',
 		'icon' => 'ion-ios-list-outline',
 		//'icon-img' => $admin_path . 'themes/default/img/svg/ionicons/ios-document-outline.svg',
 		//'icon-img' => $admin_path . 'themes/default/img/svg/ionicons/ios-photos-outline.svg',
 		'show_on_modules' => ['posts', 'post', 'pages', 'categories'],

 		'items' => [
 			'pages' => [
 				'name' => _('Pages'),
 				'url'  => $admin_path . '?module=content/posts&type=page',
 				'icon' => 'la la-file-invoice',
 			],

 			'addpage' => [
 				'name' => _('Add new page'),
 				'url'  => $admin_path . '?module=content/post&type=page',
 				'icon' => 'la la-plus-circle',
 			],
 			'classification-heading' => [
 				'name'    => _('Classification'),
 				'heading' => true,
 			],

 			'menus' => [
 				'name' => _('Menus'),
 				'url'  => $admin_path . '?module=content/menus&type=page',
 				'icon' => 'la la-boxes',
 			],
 			/*
			'categories' => 
			[
				'name' => _('Categories'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-boxes',
			],
			
			'tags' => [
				'name' => _('Tags'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-tags',
			],
			
			'categories-heading' => 
			[
				'name' => _('General'),
				'heading' => true
			],
			
			'custom-fields' => [
				'name' => _('Custom fields'),
				'url' => $admin_path . '?module=content/fields',
				'icon' => 'la la-stream',
			],		
			
			'taxonomies' => [
				'name' => _('Taxonomies'),
				'url' => $admin_path . '?module=content/categories',
				'icon' => 'la la-boxes',
				'class' => 'align-top',
				
				'items' => [
					'categories' => [
						'name' => _('Categories'),
						'subtitle' => _('(Hierarchical)'),
						'url' => $admin_path . '?module=content/categories',
						'icon' => 'la la-boxes',
					],
					
					'tags' => [
						'name' => _('Tags'),
						'subtitle' => _('(Flat)'),
						'url' => $admin_path . '?module=content/categories',
						'icon' => 'la la-tags',
					],
				],
			],		
			*/
 		],
 	],

 	'medialibrary' => [
 		'name' => _('Media library'),
 		'url'  => $admin_path . '?module=media/media',
 		'icon' => 'ion-ios-albums-outline',
 	],

 	'users' => [
 		'name' => _('Users'),
 		'url'  => $admin_path . '?module=user/users',
 		'icon' => 'ion-ios-person-outline',

 		'items' => [
 			'html' => [
 				'name' => _('Users'),
 				'url'  => $admin_path . '?module=user/users',
 				'icon' => 'la la-user',
 			],

 			'posts' => [
 				'name' => _('Add new user'),
 				'url'  => $admin_path . '?module=user/user',
 				'icon' => 'la la-plus-circle',
 			],
 		],
 	],

 	'ecommerce' => [
 		'name'    => _('Ecommerce'),
 		'heading' => true,
 	],

 	'sales' => [
 		'name' => _('Sales'),
 		'url'  => $admin_path . '?module=order/orders',
 		'icon' => 'ion-ios-cart-outline',
 		//'badge' => '10',
 		//'badge-class' => 'badge bg-secondary float-end',

 		'items' => [
 			'orders' => [
 				'name' => _('Orders'),
 				'url'  => $admin_path . '?module=order/orders',
 				'icon' => 'la la-file-invoice-dollar',
 				//'badge' => '7',
 				//'badge-class' => 'badge bg-secondary float-end',
 			],

 			'recurring' => [
 				'name' => _('Recurring'),
 				'url'  => $admin_path . '?module=order/recurring',
 				'icon' => 'la la-retweet',
 			],

 			'returns' => [
 				'name' => _('Returns'),
 				'url'  => $admin_path . '?module=order/returns',
 				'icon' => 'la la-undo',
 				//'badge' => '3',
 				//'badge-class' => 'badge bg-danger float-end',
 			],
 		],
 	],
 	/*
	'products' => [
		'name'            => _('Products'),
		'url'             => $admin_path . '?module=product/products',
		'icon'            => 'ion-ios-pricetag-outline',
		'show_on_modules' => ['Product/products', 'Product/product', 'Product/categories'],

		'items' => [
			'pages' => [
				'name' => _('Products'),
				'url'  => $admin_path . '?module=product/products',
				'icon' => 'la la-box',
			],

			'addpage' => [
				'name' => _('Add new product'),
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

			'filters' => [
				'name' => _('Filters'),
				'url' => $admin_path . '?module=categories',
		],
	],
	*/
 	'configuration' => [
 		'name'    => _('Configuration'),
 		'heading' => true,
 	],

 	'plugins' => [
 		'name' => _('Plugins'),
 		'url'  => $admin_path . '?module=plugin/plugins',
 		'icon' => 'ion-ios-gear-outline',

 		'items' => [
 			'installed' => [
 				'name' => _('Installed Plugins'),
 				'url'  => $admin_path . '?module=plugin/plugins',
 				'icon' => 'la la-plug',
 			],

 			'marketplace' => [
 				'name' => _('Add new plugin'),
 				'url'  => $admin_path . '?module=plugin/market',
 				'icon' => 'la la-plus-circle',
 			],

 			'plugins-heading' => [
 				'name'    => _('Plugins'),
 				'heading' => true,
 			],
 		],
 	],

 	'themes' => [
 		'name' => _('Themes'),
 		'url'  => $admin_path . '?module=theme/themes',
 		'icon' => 'ion-ios-color-wand-outline',

 		'items' => [
 			'installed' => [
 				'name' => _('Installed Themes'),
 				'url'  => $admin_path . '?module=theme/themes',
 				'icon' => 'la la-brush',
 			],

 			'marketplace' => [
 				'name' => _('Add new'),
 				'url'  => $admin_path . '?module=theme/market',
 				'icon' => 'la la-plus-circle',
 			],
 		],
 	],

 	'settings' => [
 		'name'  => _('Settings'),
 		'url'   => $admin_path . '?module=settings/settings',
 		'icon'  => 'ion-ios-settings',
 		'class' => 'align-top',

 		'items' => [
 			'general' => [
 				'name' => _('General Settings'),
 				'url'  => $admin_path . '?module=settings/settings',
 				'icon' => 'la la-cog',
 			],

 			'admins' => [
 				'name' => _('Admin users'),
 				'url'  => $admin_path . '?module=admin/users',
 				'icon' => 'la la-user',

 				'items' => [
 					'users' => [
 						'name' => _('Users'),
 						'url'  => $admin_path . '?module=admin/users',
 						'icon' => 'la la-user',
 					],

 					'add' => [
 						'name' => _('Add new user'),
 						'url'  => $admin_path . '?module=admin/user',
 						'icon' => 'la la-user-plus',
 					],

 					'roles-heading' => [
 						'name'    => _('Roles'),
 						'heading' => true,
 					],

 					'role' => [
 						'name' => _('Manage Roles'),
 						'url'  => $admin_path . '?module=admin/roles',
 						'icon' => 'la la-user-cog',
 					],

 					'add-role' => [
 						'name' => _('Add Roles'),
 						'url'  => $admin_path . '?module=admin/roles',
 						'icon' => 'la la-users-cog',
 					],
 				],
 			],

 			'content' => [
 				'name' => _('Content'),
 				'url'  => $admin_path . '?module=admin/users',
 				'icon' => 'la la-file-alt',

 				'items' => [
 					'menus' => [
 						'name' => _('Menus'),
 						'url'  => $admin_path . '?module=content/menus',
 						'icon' => 'la la-bars',
 					],

 					'custom-fields' => [
 						'name' => _('Custom fields'),
 						'url'  => $admin_path . '?module=content/fields',
 						'icon' => 'la la-stream',
 					],

 					'taxonomies' => [
 						'name'  => _('Taxonomies'),
 						'url'   => $admin_path . '?module=content/categories',
 						'icon'  => 'la la-boxes',
 						'class' => 'align-top',

 						'items' => [
 							'categories' => [
 								'name'     => _('Categories'),
 								'subtitle' => _('(Hierarchical)'),
 								'url'      => $admin_path . '?module=content/categories',
 								'icon'     => 'la la-boxes',
 							],

 							'tags' => [
 								'name'     => _('Tags'),
 								'subtitle' => _('(Flat)'),
 								'url'      => $admin_path . '?module=content/tags',
 								'icon'     => 'la la-tags',
 							],
 						],
 					],
 				],
 			],

 			'ecommerce' => [
 				'name'  => _('Ecommerce'),
 				'url'   => $admin_path . '?module=user/users',
 				'icon'  => 'la la-shopping-cart',
 				'class' => 'align-top',

 				'items' => [
 					'checkout' => [
 						'name' => _('Checkout & payments'),
 						'icon' => 'la la-credit-card',
 						'url'  => $admin_path . '?module=settings/checkout',
 					],

 					'shipping' => [
 						'name' => _('Shipping'),
 						'icon' => 'la la-shipping-fast',
 						'url'  => $admin_path . '?module=settings/shipping',
 					],

 					'email' => [
 						'name' => _('Email notifications'),
 						'icon' => 'la la-envelope',
 						'url'  => $admin_path . '?module=settings/notifications',
 					],

 					'coupons' => [
 						'name' => _('Discount coupons'),
 						'icon' => 'la la-percentage',
 						'url'  => $admin_path . '?module=settings/discount',
 					],
 					'taxes' => [
 						'name'  => _('Taxes'),
 						'icon'  => 'la la-file-invoice-dollar',
 						'url'   => $admin_path . '?module=settings/discount',
 						'items' => [
 							'tax-classes' => [
 								'name' => _('Tax classes'),
 								'url'  => $admin_path . '?module=admin/users',
 								'icon' => 'la la-user',
 							],

 							'tax-rates' => [
 								'name' => _('Tax rates'),
 								'url'  => $admin_path . '?module=admin/user',
 								'icon' => 'la la-user-plus',
 							],

 							'roles-heading' => [
 								'name'    => _('Roles'),
 								'heading' => true,
 							],

 							'role' => [
 								'name' => _('Manage Roles'),
 								'url'  => $admin_path . '?module=admin/roles',
 								'icon' => 'la la-user-cog',
 							],

 							'add-role' => [
 								'name' => _('Add Roles'),
 								'url'  => $admin_path . '?module=admin/roles',
 								'icon' => 'la la-users-cog',
 							],
 						],
 					],
 					'returns' => [
 						'name' => _('Returns'),
 						'icon' => 'la la-undo',
 						'url'  => $admin_path . '?module=settings/discount',
 					],
 					'statuses' => [
 						'name' => _('Statuses'),
 						'icon' => 'la la-tags',
 						'url'  => $admin_path . '?module=settings/discount',
 						//order
 						//stock
 						//return
 					],
 					'classes' => [
 						'name' => _('Classes'),
 						'icon' => 'la la-ruler',
 						'url'  => $admin_path . '?module=settings/discount',
 						//length
 						//weight
 					],
 				],
 			],

 			'localisation' => [
 				'name' => _('Localisation'),
 				'url'  => $admin_path . '?module=user/users',
 				'icon' => 'la la-flag',

 				'items' => [
 					'languages' => [
 						'name' => _('Languages'),
 						'icon' => 'la la-language',
 						'url'  => $admin_path . '?module=localisation/languages',
 					],

 					'currencies' => [
 						'name' => _('Currencies'),
 						'icon' => 'la la-coins',
 						'url'  => $admin_path . '?module=localisation/currencies',
 					],
 					'geo-location' => [
 						'name'  => _('Geo location'),
 						'icon'  => 'la la-globe',
 						'url'   => $admin_path . '?module=localisation/currencies',
 						'class' => 'align-top',
 						'items' => [
 							'countries' => [
 								'name' => _('Countries'),
 								'icon' => 'la la-flag',
 								'url'  => $admin_path . '?module=localisation/languages',
 							],

 							'zones' => [
 								'name' => _('Zones'),
 								'icon' => 'la la-city',
 								'url'  => $admin_path . '?module=localisation/currencies',
 							],
 							'geozones' => [
 								'name' => _('Geo Zones'),
 								'icon' => 'la la-atlas',
 								'url'  => $admin_path . '?module=localisation/currencies',
 							],
 						],
 					],

 					'translations' => [
 						'name' => _('Translations'),
 						'icon' => 'la la-flag',
 						'url'  => $admin_path . '?module=localisation/translations',
 					],
 				],
 			],
 			'sites' => [
 				'name' => _('Sites'),
 				'url'  => $admin_path . '?module=settings/sites',
 				'icon' => 'la la-stop la-90',

 				'items' => [
 					'installed' => [
 						'name' => _('Sites'),
 						'url'  => $admin_path . '?module=settings/sites',
 						'icon' => 'la la-stop',
 					],

 					'marketplace' => [
 						'name' => _('Add new'),
 						'url'  => $admin_path . '?module=settings/sites',
 						'icon' => 'la la-plus-circle',
 					],
 				],
 			],
 		],
 	],

 	'tools' => [
 		'name'  => _('Tools'),
 		'url'   => $admin_path . '?module=tools/systeminfo',
 		'icon'  => 'ion-ios-world-outline',
 		'class' => 'align-top',

 		'items' => [
 			'cache' => [
 				'name' => _('Cache'),
 				'url'  => $admin_path . '?module=tools/cache',
 				'icon' => 'la la-circle-notch',
 			],

 			'backup' => [
 				'name' => _('Backup'),
 				'url'  => $admin_path . '?module=tools/backup',
 				'icon' => 'la la-server',
 			],

 			'cron' => [
 				'name' => _('Cron job'),
 				'url'  => $admin_path . '?module=tools/cron',
 				'icon' => 'la la-history la-90',
 			],

 			'import-export' => [
 				'name' => _('Import/Export'),
 				'url'  => $admin_path . '?module=tools/import',
 				'icon' => 'la la-upload',

 				'items' => [
 					'content-heading' => [
 						'name'    => _('Content'),
 						'heading' => true,
 					],
 					'import' => [
 						'name' => _('Import content'),
 						'icon' => 'la la-file-import',
 						'url'  => $admin_path . '?module=tools/import',
 					],

 					'export' => [
 						'name' => _('Export content'),
 						'icon' => 'la la-file-export',
 						'url'  => $admin_path . '?module=tools/export',
 					],
 					'media-heading' => [
 						'name'    => _('Media'),
 						'heading' => true,
 					],
 					'import-media' => [
 						'name' => _('Import media'),
 						'icon' => 'la la-caret-square-left',
 						'url'  => $admin_path . '?module=tools/import',
 					],

 					'export-media' => [
 						'name' => _('Export media'),
 						'icon' => 'la la-caret-square-right',
 						'url'  => $admin_path . '?module=tools/export',
 					],
 				],
 			],

 			'system' => [
 				'name' => _('System info'),
 				'url'  => $admin_path . '?module=tools/systeminfo',
 				'icon' => 'la la-info-circle',

 				'items' => [
 					'info' => [
 						'name' => _('System Info'),
 						'icon' => 'la la-info',
 						'url'  => $admin_path . '?module=tools/systeminfo',
 					],

 					'error-log' => [
 						'name' => _('Error log'),
 						'url'  => $admin_path . '?module=tools/errorlog',
 						'icon' => 'la la-bug',
 					],
 				],
 			],

 			'security' => [
 				'name' => _('Security'),
 				'url'  => $admin_path . '?module=tools/security',
 				'icon' => 'la la-shield-alt',
 			],
 		],
 	],
 ];
