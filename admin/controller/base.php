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

namespace Vvveb\Controller;

use Vvveb\Sql\LanguageSQL;
use Vvveb\Sql\SiteSQL;
use Vvveb\System\Core\Request;
use Vvveb\System\Core\View;
use Vvveb\System\Event;
use Vvveb\System\Extensions\Plugins;
use Vvveb\System\Functions\Str;
use Vvveb\System\Session;
use Vvveb\System\Sites;
use Vvveb\System\User\Admin;

class Base {
	function setSite($id = false) {
		//if no id set default
		if ($id) {
			$site  = Sites::getSiteById($id);
		} else {
			$site = Sites::getDefault();
		}

		$this->session->set('site', $site);
		$this->session->set('site_url', $site['host']);
		$this->session->set('site', $site['id']);
		$this->session->set('state', $site['state']);
		$site = $site['id'];

		return $site;
	}

	function customPosts() {
		//custom posts -- add to menu
		$default_custom_posts =
		[
			'post' => [
				'type'        => 'post',
				'plural'      => 'posts',
				'icon'        => 'ion-ios-photos-outline',
			],
			'page' => [
				'type'        => 'page',
				'plural'      => 'pages',
				'icon'        => 'ion-ios-list-outline',
			],
		];

		$custom_posts_types             = \Vvveb\get_option('custom_posts_types', $default_custom_posts);
		list($custom_posts_types)       = Event::trigger(__CLASS__, __FUNCTION__, $custom_posts_types);

		$custom_post_menu = \Vvveb\config('custom-post-menu', []);
		$posts_menu       = [];

		foreach ($custom_posts_types as $type => $settings) {
			if ($type == 'page') {
				continue;
			}
			$posts_menu[$type] = $custom_post_menu;

			$posts_menu[$type]['name']                   =
			$posts_menu[$type]['items']['posts']['name'] =
			ucfirst($settings['plural']);

			$posts_menu[$type]['icon']     = $settings['icon'] ?? '';
			$posts_menu[$type]['icon-img'] = $settings['icon-img'] ?? '';
			$posts_menu[$type]['url'] .= "&type=$type";

			foreach ($posts_menu[$type]['items'] as $item => &$values) {
				if (isset($values['url'])) {
					$values['url'] .= "&type=$type";
				}
			}
		}

		return $posts_menu;
	}

	function customProducts() {
		//custom products -- add to menu
		$default_custom_products =
		[
			'product' => [
				'type'   => 'product',
				'plural' => 'products',
				'icon'   => 'ion-ios-pricetag-outline',
			],
		];

		$custom_products_types             = \Vvveb\get_option('custom_products_types', $default_custom_products);
		list($custom_products_types)       = Event::trigger(__CLASS__, __FUNCTION__, $custom_products_types);

		$custom_product_menu = \Vvveb\config('custom-product-menu', []);
		$products_menu       = [];

		foreach ($custom_products_types as $type => $settings) {
			if ($type == 'page') {
				continue;
			}
			$products_menu[$type] = $custom_product_menu;

			$products_menu[$type]['name']                      =
			$products_menu[$type]['items']['products']['name'] =
			ucfirst($settings['plural']);

			$products_menu[$type]['icon']     = $settings['icon'] ?? '';
			$products_menu[$type]['icon-img'] = $settings['icon-img'] ?? '';
			$products_menu[$type]['url'] .= "&type=$type";

			foreach ($products_menu[$type]['items'] as $item => &$values) {
				if (isset($values['url'])) {
					$values['url'] .= "&type=$type";
				}
			}
		}

		return $products_menu;
	}

	function init() {
		if (! $this->session->get('csrf')) {
			$this->session->set('csrf', Str::random());
		}
		//$this->session->delete('csrf');

		$admin = Admin::current();

		if (! $admin) {
			return $this->requireLogin();
		}

		$view = View :: getInstance();

		$this->session = Session::getInstance();
		$this->request = Request::getInstance();

		if ($site = ($this->request->post['site'] ?? false)) {
			$this->setSite($site);
		}

		$site = $this->session->get('site');

		if (! $site) {
			$this->setSite();
		}

		if ($language = ($this->request->post['language'] ?? false)) {
			$this->session->set('language', $language);
		}

		if ($state = ($this->request->post['state'] ?? false)) {
			if (Sites::setSiteDataById($site, 'state', $state)) {
				$this->session->set('state', $state);
			}
		}

		$page    = $this->request->get['page'] ?? 1;
		$limit   = $this->request->get['limit'] ?? 10;

		$this->global['site_id']     = $site;
		$this->global['user_id']     = $admin['admin_id'];
		$this->global['language_id'] = 1; //$language;
		$this->global['state']       = $state;
		$this->global['page']        = $page;
		$this->global['start']       = ($page - 1) * $limit;
		$this->global['limit']       = $limit;

		//load plugins for active site
		Plugins :: loadPlugins($site);
		//get custom post types

		//don't initialize menu items for CLI
		if (defined('CLI')) {
			return;
		}
		$languages           = new languageSQL();
		$view->languagesList = $languages->getAll();

		$sites             = new SiteSQL();
		$view->sites       = $sites->getAll();

		$menu             = \Vvveb\config('admin-menu', []);

		//custom posts -- add to menu
		$posts_menu = $this->customPosts();
		$menu       = \Vvveb\array_insert_array_after('edit', $menu, $posts_menu);

		//products -- add to menu
		$products_menu = $this->customProducts();
		$menu          = \Vvveb\array_insert_array_after('sales', $menu, $products_menu);

		list($menu)       = Event::trigger(__CLASS__, __FUNCTION__ . '-menu', $menu);
		$view->menu       = $menu;

		$view->mediaPath  = PUBLIC_PATH . 'media';
		$view->publicPath = PUBLIC_PATH . 'media';
	}

	function redirect($url = '/', $parameters = []) {
		$redirect = \Vvveb\url($url, $parameters);

		if ($redirect) {
			$url = $redirect;
		}

		$this->session->close();

		return header("Location: $url");
	}

	function __construct() {
		$view       = View :: getInstance();
		/*
		putenv('LC_ALL=ro_RO');
		setlocale(LC_ALL, 'ro_RO.utf8');
		clearstatcache();
		//set gettext domain
		//echo self :: $theme . "<br>\n";
		//echo self :: $htmlPath . "locale<br>\n";
		bindtextdomain('vvveb','/home/www/nico/vvveb/locale');
		textdomain('vvveb');
		 */

		return;
		//get languages
		$languages = \Vvveb\get_option('languages', ['post', 'page']);

		/*
				$view->languages = [
			
					['language_id' => 1,
					'name' => 'english',
					'code' => 'en'],

					['language_id' => 2,
					'name' => 'romanian',
					'code' => 'ro'],
				];
		*/
		$view->languages = $languages->getAll();

		$view->languages = [
			['language_id' => 1,
				'name'        => 'english',
				'code'        => 'en', ],

			['language_id' => 2,
				'name'        => 'romanian',
				'code'        => 'ro', ],
		];

		$view->live_url  = '/';
	}

	/**
	 * Call this method if the action requires login, if the user is not logged in, a login form will be shown.
	 *
	 */
	function requireLogin() {
		//return \Vvveb\System\Core\FrontController::redirect('user/login');
		//$view = view :: getInstance();
		$admin_path         = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		$this->view->action = "$admin_path/?module=user/login";
		$this->view->template('user/login.html');

		die($this->view->render());
	}

	/**
	 * It shows a "Not found" page.
	 *
	 * @param unknown_type $code
	 */
	function notFound($statusCode = 404, $service = false) {
		return FrontController::notFound($statusCode, $service);
	}
}
