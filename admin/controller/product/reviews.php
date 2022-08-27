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

namespace Vvveb\Controller\Product;

use Vvveb\Controller\Base;
use Vvveb\System\Core\View;
use Vvveb\System\Images;

class Reviews extends Base {
	protected $type = 'review';

	function delete() {
	}

	function index() {
		$view     = View :: getInstance();

		return;
		$reviews = new \Vvveb\Sql\reviewSQL();

		$page    = $this->request->get['page'] ?? 1;
		$limit   = 10;

		$results = $reviews->getReviews(
			[
				'start'        => ($page - 1) * $limit,
				'limit'        => $limit,
				'type'         => $this->type,
				'language_id'  => 1,
				'site_id'      => 1,
			]
		);

		foreach ($results['reviews'] as $id => &$review) {
			if (isset($review['image'])) {
				$review['image'] = Images::image('review', $review['image']);
			}

			$review['edit-url']   = \Vvveb\url(['module' => 'review/review', 'review_id' => $review['review_id']]);
			$review['delete-url'] = \Vvveb\url(['module' => 'review/reviews', 'action' => 'delete', 'review_id' => $review['review_id']]);
			$review['view-url']   =  \Vvveb\url('content/review/index', $review);
			$admin_path           = \Vvveb\config('admin.path', 'admin') . '/';
			$review['design-url'] = $admin_path . \Vvveb\url(['module' => 'editor/editor', 'url' => $review['view-url']], false, false);
		}

		$view->reviews  = $results['reviews'];
		$view->count    = $results['count'];

		$view->fields = [
			'article' => ['type' => 'link', 'url'=> ''],
		];

		$view->limit = $limit;

		return null;
		//insert plugin html
		$view->psttt->insertHTML('public/admin/theme/default/content/reviews.html', 'plugins/test/public/admin/content/reviews.article.html', [
			//headcolumn
			[
				'insertSelector' => '[data-v-reviews]table thead',
				'type'           => 'append',
				'selector'       => '[data-v-test-col]',
			],
			//row column
			[
				'insertSelector' => '[data-v-reviews]table data-v-review .review',
				'type'           => 'after',
				'selector'       => '[data-v-test-row]',
			],
		]);
		$view->psttt->addTemplate('public/admin/theme/default/content/reviews.pst', 'plugins/test/admin/content/reviews.pst');
	}
}