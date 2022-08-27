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

/*
Name: Gravatar
Slug: gravatar
Category: comments
Url: https://www.gravatar.com
Description: Show gravatar images for user avatar on comments.
Author: givanz
Version: 0.1
Thumb: gravatar.svg
Author url: https://www.vvveb.com
Settings: /admin/?module=plugins/gravatar/settings
*/

use Vvveb\System\Event;

class GravatarPlugin {
	/**
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mp | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 */
	private function getGravatar($email, $s = 80, $d = 'mp', $r = 'g', $img = false) {
		$url = 'https://www.gravatar.com/avatar/';
		$url .= md5(strtolower(trim($email)));
		$url .= "?s=$s&d=$d&r=$r";

		return $url;
	}

	function app() {
		Event::on('Vvveb\Component\Comments', 'results', __METHOD__ , function ($comments) {
			foreach ($comments['comments'] as &$comment) {
				$comment['avatar'] = $this->getGravatar($comment['email']);
			}

			return [$comments];
		});
	}

	function __construct() {
		if (APP == 'admin') {
			//$this->admin();
		} else {
			if (APP == 'app') {
				$this->app();
			}
		}
	}
}

$gravatarPlugin = new GravatarPlugin();
