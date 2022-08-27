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

/**
 * Manages image retrival and saving.
 *
 * @package Vvveb
 * @subpackage System
 * @since 0.0.1
 */

namespace Vvveb\System;

/**
 * Images Singleton.
 * 
 * @since 0.0.1
 */
class Images {
	static public function image($image, $type = '', $size = '') {
		$publicPath = \Vvveb\publicMediaUrlPath();

		list($publicPath, $type, $image, $size) =
		Event :: trigger(__CLASS__, 'publicPath', $publicPath, $type, $image, $size);

		if ($publicPath == null) {
			$publicPath = \Vvveb\publicMediaUrlPath();
		}

		if ($image) {
			$image = $publicPath . 'media/' . $image;
		} else {
			//return $public . 'media/placeholder.png';
		}

		list($image, $type, $size) =
		Event :: trigger(__CLASS__,__FUNCTION__, $image, $type, $size);

		return $image;
	}

	static public function images($images, $type) {
		foreach ($images as $key => &$image) {
			$image['image'] = Images::image($image['image'], $type);
		}

		/*
		uasort($images, function ($a, $b) {
			if ($a['sort_order'] == $b['sort_order']) {
				return 0;
			}

			return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
		});
		 */

		return $images;
	}

	public function get($type, $id, $size, $attrs) {
	}

	public function save($type, $path, $attrs) {
	}
}
