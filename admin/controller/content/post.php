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

namespace Vvveb\Controller\Content;

use Vvveb\Controller\Base;
use Vvveb\Sql\PostSQL;
use Vvveb\System\Core\View;
use Vvveb\System\Images;
use Vvveb\System\Validator;

class Post extends Base {
	protected $type = 'post';

	private function taxonomies($post_id = false) {
		//get taxonomies for post type
		$taxonomies = new \Vvveb\Sql\taxonomySQL();
		$results    = $taxonomies->getTaxonomies(
			['post_type'    => $this->type]
		);

		//get taxonomies content
		if ($results) {
			$taxonomy_itemSql = new \Vvveb\Sql\categorySQL();

			$options =  [
				'taxonomy'   => 'post',
				'start'      => 0,
				'limit'      => 100,
			] + $this->global;

			if ($post_id) {
				$options['post_id'] = $post_id;
			}

			foreach ($results as $id => &$taxonomy_item) {
				$taxonomy_item['taxonomy_item'] = [];
				//for tags don't retrive taxonomies if no post id provided
				if ($taxonomy_item['type'] != 'tags' || $post_id) {
					$options                        = ['taxonomy_id' => $id, 'type' => $taxonomy_item['type']] + $options;
					$taxonomy_item['taxonomy_item'] = $taxonomy_itemSql->getCategories($options);
				}
			}
		}

		return $results;
	}

	function index() {
		$view = $this->view;

		$admin_path          = '/' . \Vvveb\config('admin.path', 'admin') . '/';
		$this->view->scanUrl = $admin_path . 'index.php?module=media/media&action=scan';
		$postOptions         = [];
		$post                = [];

		if (isset($this->request->get['post_id'])) {
			$postOptions['post_id'] = (int)$this->request->get['post_id'];
		} else {
			if (isset($this->request->get['slug'])) {
				$postOptions['slug'] = $this->request->get['slug'];
			}
		}

		if ($postOptions) {
			if (isset($this->request->get['type'])) {
				$this->type          = $this->request->get['type'];
				$postOptions['type'] = $this->type;
			}

			$posts = new PostSQL();

			$post = $posts->getPost($postOptions);

			//featured image
			if (isset($post['image'])) {
				$post['image_url'] = Images::image($post['image'], 'post');
			}
			//$view->tags = $posts->postTags($options);
			//$view->categories = $posts->postCategories($options);
		} else {
			$post['image_url'] = Images::image('','post');
		}

		if (isset($post['date_modified'])) {
			$post['date_modified'] = str_replace(' ', 'T', $post['date_modified']);
		} else {
			$post['date_modified'] = date("Y-m-d\TH:i:s", isset($post['date_modified']) && $post['date_modified'] ? strtotime($post['date_modified']) : time());
		}

		if (isset($post['post_description'][1]['slug'])) {
			$post['url'] = \Vvveb\url('content/post/index', ['slug'=> $post['post_description'][1]['slug']]);
		}

		$template           = \Vvveb\getCurrentTemplate();
		$admin_path         = \Vvveb\config('admin.path', 'admin') . '/';

		if (isset($post['url'])) {
			$post['design_url'] = '/' . $admin_path . \Vvveb\url(['module' => 'editor/editor', 'template' => $template, 'url' => $post['url']], false, false);
		}

		$view->taxonomies = $this->taxonomies($post['post_id'] ?? false);

		$view->post           = $post;
		$view->status         = ['publish', 'draft', 'pending', 'private', 'password'];
		$view->templates      = \Vvveb\getTemplateList();
		$validator            = new Validator(['post']);
		$view->validatorJson  = $validator->getJSON();
	}

	function save() {
		$validator = new Validator(['post']);
		$view      = view :: getInstance();

		if (($errors = $validator->validate($this->request->post)) === true) {
			$posts = new PostSQL();

			//$post = ['post' => array('title' => $_POST['title'], 'content' =>  $_POST['content'])/*, 'id_post' => (int)$_GET['post_id']*/];
			$post = [];

			//$post = $this->request->post;

			//process tags
			if (isset($post['post']['tag'])) {
				foreach ($post['post']['tag'] as $tag) {
					//existing tag add to post taxonomy_item list
					if (is_numeric($tag)) {
						$post['post']['taxonomy_item'][] = $tag;
					} else {
						//add new taxonomy_item
					}
				}
			}
			$post['post']['date_modified']       =  str_replace('T', ' ', $post['post']['date_modified']);

			if (isset($this->request->get['post_id'])) {
				$post['post_id']    = (int)$this->request->get['post_id'];
				$post['type']       = $this->type;
				$post['post']       = $this->request->post;

				$result                              = $posts->editPost($this->global + $post);

				if ($result >= 0) {
					$this->view->success[] = 'Post saved!';
				} else {
					$this->view->validationErrors = [$posts->error];
				}
			} else {
				$return = $posts->addPost($this->global + ['post' => $this->request->post]);
				$id     = $return['post'];

				if (! $id) {
					$view->validationErrors = [$posts->error];
				} else {
					$view->success[] = 'Post saved!';
					$this->redirect(['module'=>'content/post', 'post_id' => $id]);
				}
			}
		} else {
			$view->validationErrors = $errors;
		}

		$this->index();
	}

	function draft() {
	}

	function preview() {
	}
}
