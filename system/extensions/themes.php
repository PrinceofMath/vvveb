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

namespace Vvveb\System\Extensions;

use \Vvveb\System\Cache;

class Themes extends Extensions {
	static protected $extension = 'theme';

	static protected $baseDir = DIR_THEMES;

	static protected $url = 'http://themes.vvveb.com/api/themes.json';

	static protected $plugins = [];

	static protected $categories = [];

	static function getInfo($content, $name = false) {
		$params               = parent::getInfo($content, $name);
		$params['screenshot'] = PUBLIC_PATH . 'themes/' . $name . '/screenshot.png';

		return $params;
	}

	static function getList($path = '') {
		$themes      = self :: getListInfo(DIR_ROOT . '/public/themes/*/theme.php');
		$activeTheme = \Vvveb\config('app.theme', 'default');

		foreach ($themes as &$theme) {
			$theme['active'] = ($activeTheme == $theme['folder']);
		}

		return $themes;
	}

	static function getMarketList($params = []) {
		$cacheDriver = Cache :: getInstance();

		$params['action'] = 'query_themes';

		$query = http_build_query($params);

		$cacheKey = md5($query);

		if ($result = $cacheDriver->get($cacheKey)) {
			return $result;
		} else {
			$ch = curl_init(self :: $url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($result, true);

			$cacheDriver->set($cacheKey, $result);

			return $result;
		}
	}
}
