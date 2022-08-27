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
use Vvveb\Sql\postSQL;
use Vvveb\System\Images;

class Posts extends Base {
	protected $type = 'post';

	function init() {
		if (isset($this->request->get['type'])) {
			$this->type = $this->request->get['type'];
		}

		return parent::init();
	}

	function delete() {
		$post_id    = $this->request->get['post_id'] ?? false;

		if ($post_id) {
			$posts   = new postSQL();
			$options = [
				'post_id' => $post_id, 'type' => $this->type,
			] + $this->global;

			$result  = $posts->deletePost($options);

			if ($result && $result['post'] > 0) {
				$this->view->success[] = _('Post deleted!');
			} else {
				$this->view->errors[] = _('Error deleting post!');
			}
		}

		return $this->index();
	}

	function index() {
		$view  = $this->view;
		$posts = new postSQL();

		$this->type = $this->request->get['type'] ?? 'post';
		$options    =  [
			'type'         => $this->type,
		] + $this->global;

		$results = $posts->getPosts($options);

		foreach ($results['posts'] as $id => &$post) {
			if (isset($post['image'])) {
				$post['image'] = Images::image($post['image'], 'post');
			}

			$url                = ['module' => 'content/post', 'post_id' => $post['post_id']];
			$post['url']        = \Vvveb\url($url);
			$post['edit-url']   = \Vvveb\url($url);
			$post['delete-url'] = \Vvveb\url(['module' => 'content/posts', 'action' => 'delete'] + $url);
			$post['view-url']   =  \Vvveb\url('content/post/index', $post);
			$admin_path         = '/' . \Vvveb\config('admin.path', 'admin') . '/';
			$post['design-url'] = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'url' => $post['view-url']], false, false);
		}

		$view->set($results);
		$view->limit = $options['limit'];

		return null;
	}
}
