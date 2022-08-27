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

namespace Vvveb\Controller\Editor;

use Vvveb\Controller\Base;
use Vvveb\Sql\PostSQL;
use Vvveb\Sql\ProductSQL;
use Vvveb\System\Core\View;
use Vvveb\System\Event;
use Vvveb\System\Functions\Str;
use Vvveb\System\Sites;

class Editor extends Base {
	private $themeConfig = [];

	private $skipFolders = ['src', 'backup'];

	private $skipFiles = [];

	function __construct() {
		//echo \Vvveb\url('product/product/index', ['product_id' => null]);
		//die();
		parent::__construct();
		$this->loadThemeConfig();
	}

	function getThemeFolder() {
		return DIR_THEMES . DIRECTORY_SEPARATOR . Sites::getTheme() ?? 'default';
	}

	function loadThemeConfig() {
		$this->themeConfig = include $this->getThemeFolder() . DIRECTORY_SEPARATOR . 'theme.php';
	}

	function loadTemplateList() {
		$list = $this->themeConfig['pages'];

		//return $list;

		$pages = \Vvveb\getTemplateList();

		list($pages) = Event::trigger('editor', 'templateList', $pages);

		return array_values($pages);
	}

	function index() {
		$view               = View::getInstance();
		$view->pages        = $this->loadTemplateList();
		$view->themeBaseUrl = '/themes/' . Sites::getTheme() ?? 'default';

		$themeFolder = $this->getThemeFolder();
		$files       = [];

		$glob = glob("$themeFolder/components/*.js", GLOB_BRACE);

		foreach ($glob as &$file) {
			$files[] = str_replace($themeFolder, $view->themeBaseUrl, $file);
		}
		$view->themeComponents = $files;

		$glob = glob("$themeFolder/sections/*.js", GLOB_BRACE);

		foreach ($glob as $file) {
			$files[] = str_replace($themeFolder, $view->themeBaseUrl, $file);
		}
		$view->themeSections = $files;

		$glob = glob("$themeFolder/inputs/*.js", GLOB_BRACE);

		foreach ($glob as &$file) {
			$files[] = str_replace($themeFolder, $view->themeBaseUrl, $file);
		}

		if (isset($this->request->get['url'])) {
			$name     = $url      = $this->request->get['url'];
			$template = $this->request->get['template'] ?? Vvveb\getUrlTemplate($url);
			$filename = $template;
			$file     = $template;
			$title    = \Vvveb\humanReadable($url);

			$current_page = ['name' => $name, 'filename' => $filename, 'file' => $file, 'url' => $url, 'title' => $title, 'folder' => ''];
			$view->pages  = array_merge([$current_page], $view->pages);
		}

		$view->themeInputs   = $files;
		$admin_path          = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		$this->view->scanUrl = $admin_path . 'index.php?module=media/media&action=scan';
	}

	function getComponent($html, $options) {
	}

	function backup($page) {
		$themeFolder  = $this->getThemeFolder() . DIRECTORY_SEPARATOR;
		$backupFolder = $themeFolder . 'backup/';
		$page         = str_replace('.html', '', Str::sanitizeFilename($page));
		$backupName   =  $page . '|' . date('Y-m-d_H:i:s') . '.html';

		$content = file_get_contents($themeFolder . $page . '.html');

		return file_put_contents($backupFolder . $backupName, $content);
	}

	function saveElements($elements) {
		$products = new ProductSQL();
		$posts    = new PostSQL();

		foreach ($elements as $element) {
			$type   = $element['type'];
			$id     = $element['id'];
			$fields = $element['fields'];
			//var_dump($element);
			switch ($type) {
				case 'product':
					$product_description = [];

					foreach ($fields as $field) {
						$name  = $field['name'];
						$value = $field['value'];

						if ($name == 'name' || $name == 'description') {
							$product_description[$name] = $value;
						} else {
							$product[$name] = $value;
						}
					}

					//$product_description['product_id'] = $id;
					$product_description['language_id'] = 1;

					$product['product_description'][] = $product_description;
					//var_dump($product);
					$result = $products->edit(['product' => $product, 'product_id' => $id]);

				break;

				case 'post':
					$post_description = [];

					foreach ($fields as $field) {
						$name  = $field['name'];
						$value = $field['value'];

						if ($name == 'name' || $name == 'description') {
							$post_description[$name] = $value;
						} else {
							$post[$name] = $value;
						}
					}
					//$post['post_description']['post_id'] = $id;
					$post_description['language_id'] = 1;
					$post['post_description'][]      = $post_description;
					//var_dump($post);
					//error_log($id);

					$result = $posts->editPost(['post' => $post, 'post_id' => $id]);

				break;
			}
		}
	}

	function save() {
		$page     = Str::sanitizeFilename($_POST['file']);
		$content  =$this->request->post['html'];
		$elements = $this->request->post['elements'];

		if ($elements) {
			$this->saveElements($elements);
		}

		$message = ['success' => false, 'message' =>'Error saving file!'];

		if (! $this->backup($page)) {
			$message = ['success' => false, 'message' => 'Error saving backup!'];
		}

		$themeFolder = $this->getThemeFolder();

		if (file_put_contents($themeFolder . DIRECTORY_SEPARATOR . $page, $content)) {
			$message = ['success' => true, 'message' =>'File saved!'];
		}

		$view         = View::getInstance();
		$view->noJson = true;

		echo json_encode($message);

		die();

		return false;
	}
}
