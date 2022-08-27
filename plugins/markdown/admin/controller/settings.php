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

namespace Vvveb\Plugins\Markdown\Controller;

use Vvveb\Controller\Base;
use Vvveb\Plugins\Markdown\System\Parsedown as Parsedown;

class Settings extends Base {
	private $cats = [];

	private function markdownHtml($markdown, $dir) {
		//escape html tags
		$markdown = str_replace(['<', '>'], ['&lt;', '&gt;'], $markdown);
		//escape code blocks
		$markdown = preg_replace_callback('/```.*?```/ms', function ($matches) {
			return str_replace(['&lt;', '&gt;'], ['<', '>'],  $matches[0]);
		}, $markdown);
		/*
		$markdown = preg_replace_callback('/```.*?```/ms', function ($matches) {
			return htmlentities($matches[0]);
		}, $markdown);*/
		//echo $markdown;

		$html     = $text     =  $this->parsedown->text($markdown);

		//process images
		$mediaPath = PUBLIC_PATH . 'media/';
		mkdir(DIR_ROOT . $mediaPath . 'docs/');

		$html = preg_replace_callback('/src=["\'](.+?)["\']/',
		function ($match) use (&$mediaPath, $dir) {
			$image = $match[1];
			//if local image copy to media
			if (strpos($image, '//') === false) {
				copy($dir . '/' . $image, DIR_ROOT . $mediaPath . 'docs/' . $image);

				return 'src="/media/docs/' . $image . '"';
			}

			return $match[0];
		},$html);

		return $html;
	}

	private function traverseDir($dir) {
		if (! ($dp = opendir($dir))) {
			die("Cannot open $dir.");
		}

		while ((false !== $file = readdir($dp))) {
			if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
				if ($file != '.' && $file != '..') {
					//echo "category $dir/$file <br>";
					//check and add dir
					$this->addCategories($dir . DIRECTORY_SEPARATOR . $file);
					$this->traverseDir($dir . DIRECTORY_SEPARATOR . $file);
					chdir($dir);
				}
			} else {
				if ($file != '.' && $file != '..' && strrpos($file, '.md') != false) {
					//add post
					$this->addPost($file,$dir . DIRECTORY_SEPARATOR . $file, $dir);
				}
			}
		}
		closedir($dp);

		return true;
	}

	private function addCategories($file) {
		$folder = trim(str_replace($this->docs_folder, '', $file), ' /' . DIRECTORY_SEPARATOR);
		//echo '<br/>';
		$cats = explode(DIRECTORY_SEPARATOR, $folder);

		$prev_category = 0;
		$category_id   = 0;

		foreach ($cats as $category_slug) {
			$cat = $this->categories->getCategoryBySlug(['slug' => $category_slug, 'taxonomy_id' => 1, 'parent_id' => $prev_category, 'site_id' => 5]);

			if ($cat) {
				$prev_category = $cat['taxonomy_item_id'];
			} else {
				$cat = $this->categories->addCategory([
					'taxonomy_item' => $this->global + [
						'parent_id' => $prev_category, 'taxonomy_id' => 1, 'language_id' => 1,
					],
					'taxonomy_item_description' => $this->global + ['slug'=> $category_slug, 'name' => \Vvveb\humanReadable($category_slug), 'description' => \Vvveb\humanReadable($category_slug)],
				] + $this->global);

				$category_id = $cat['taxonomy_item'];
			}
		}

		if (! $category_id) {
			$category_id = $prev_category;
		}

		if (! isset($this->cats[$folder])) {
			$this->cats[$folder] = $category_id;
		}
	}

	private function addPost($filename, $file, $dir) {
		$category = trim(str_replace($this->docs_folder, '', dirname($file)), ' /' . DIRECTORY_SEPARATOR);
		$slug     = \Vvveb\filter('/([^.]+)/', basename($file));
		$name     = \Vvveb\humanReadable($slug);

		$slug = \Vvveb\filter('/([^.]+)/', basename($file));
		$name = \Vvveb\humanReadable($slug);

		$markdown = file_get_contents($file);
		$html     = $this->markdownHtml($markdown, $dir);
		//get name from heading 1 if available
		$html = preg_replace_callback('/<h1>(.+?)<\/h1>/',
		function ($match) use (&$name) {
			$name = $match[1];

			return ''; //remove heading 1 from content as it will be set as post name
		},$html);

		$category_id = $this->cats[$category];
		$post_data   = $this->post->getPost(['slug' => $slug]);

		if (! $post_data) {
			$data =
				[
					'post' => $this->global + ['post_description' => [
						1=> ['language_id' => 1, 'name' => $name, 'slug' => $slug, 'content' => $html, 'excerpt' => ''],
					]],
				] + $this->global;

			$post_data = $this->post->addPost($data);

			if ($category_id) {
				$taxonomy_item = ['post_id' => $post_data['post'], 'taxonomy_item' => ['taxonomy_item_id' => $category_id]];
				$this->post->setPostTaxonomy($taxonomy_item);
			}
		} else {
			$post_data = $this->post->editPost(
					$this->global +
					[
						'post' => ['post_description' => [
							1=> ['language_id' => 1, 'name' => $name, 'slug' => $slug, 'content' => $html],
						]],
						'post_id' => $post_data['post_id'],
					]);
		}
	}

	function import() {
		$path = $this->request->post['settings']['path'];

		if ($path) {
			$this->docs_folder = DIR_ROOT . $path . DIRECTORY_SEPARATOR;
			$this->categories  = new \Vvveb\Sql\categorySQL();
			$this->post        = new \Vvveb\Sql\postSQL();
			/*
					include __DIR__ . '/../../system/parsedown.php';
					$this->parsedown = new \Parsedown();
			*/
			$this->parsedown = new Parsedown();

			if ($this->traverseDir($this->docs_folder)) {
				$this->view->success[] = _('Import complete!');
			}
		}

		return $this->index();
	}

	function index() {
		//$cat = $categories->getCategoryBySlug(['slug' => 	'desktop', 'parent_id' => 1]);
		//$this->traverseDir($this->docs_folder);

		return null;
	}
}
