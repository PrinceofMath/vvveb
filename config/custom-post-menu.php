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
 	'name'            => _('Posts'),
 	'url'             => $admin_path . '?module=content/posts',
 	'icon'            => 'ion-ios-photos-outline',
 	'show_on_modules' => ['posts', 'post', 'pages', 'categories'],

 	'items' => [
 		'posts' => [
 			'name' => _('List'),
 			'url'  => $admin_path . '?module=content/posts',
 			'icon' => 'la la-file-alt',
 		],

 		'addpost' => [
 			'name' => _('Add new'),
 			'url'  => $admin_path . '?module=content/post',
 			'icon' => 'la la-plus-circle',
 		],

 		'classification-heading' => [
 			'name'    => _('Classification'),
 			'heading' => true,
 		],

 		'categories' => [
 			'name' => _('Categories'),
 			//'subtitle' => _('(Hierarchical)'),
 			'url'  => $admin_path . '?module=content/categories',
 			'icon' => 'la la-boxes',
 		],

 		'tags' => [
 			'name' => _('Tags'),
 			//'subtitle' => _('(Flat)'),
 			'url'  => $admin_path . '?module=content/tags',
 			'icon' => 'la la-tags',
 		],

 		'categories-heading' => [
 			'name'    => _('General'),
 			'heading' => true,
 		],

 		'comments' => [
 			'name' => _('Comments'),
 			'url'  => $admin_path . '?module=content/comments',
 			'icon' => 'la la-comments',
 		],
 		/*		
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
 ];
