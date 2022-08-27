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
use Vvveb\System\Core\View;
use Vvveb\System\Images;

class Comments extends Base {
	protected $type = 'comment';

	function comment() {
		$comment_id = $this->request->post['comment_id'] ?? false;
	}

	function delete() {
		$comment_id = $this->request->post['comment_id'] ?? false;
	}

	function index() {
		$view     = View :: getInstance();
		$comments = new \Vvveb\Sql\commentSQL();

		$options = [
			'type'         => $this->type,
		] + $this->global;
		unset($options['user_id']);

		$results = $comments->getComments($options);

		foreach ($results['comments'] as $id => &$comment) {
			if (isset($comment['image'])) {
				$comment['image'] = Images::image('comment', $comment['image']);
			}

			$comment['edit-url']   = \Vvveb\url(['module' => 'comments/comment', 'comment_id' => $comment['comment_id']]);
			$comment['delete-url'] = \Vvveb\url(['module' => 'comments/comments', 'action' => 'delete', 'comment_id' => $comment['comment_id']]);
		}

		$view->comments = $results['comments'];
		$view->count    = $results['count'];

		$view->fields = [
			'article' => ['type' => 'link', 'url'=> ''],
		];

		$view->limit = $limit;

		return null;
	}
}
