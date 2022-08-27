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

namespace Vvveb\System;

class Routes {
	const stringRegex = '{(\w+)}';

	const varRegex = '[{#]([a-zA-Z]\w+)({([\d,]+)})?[#}]';

	const stringLimitRegex = '{([a-zA-Z]\w+){([\d,]+)}}';

	const numericRegex = '#([a-zA-Z]\w+)#';

	const wildcardRegex = '\*';

	private static $inst = null;

	private static $routes = null;

	private static $urls = null;

	private static $modules = null;

	public static function init() {
		self :: $routes = include DIR_ROOT . '/config/routes.php';
		//self :: $routes = Event::trigger('routes', 'config', self :: $routes);

		foreach (self :: $routes as $url => $data) {
			$module                   = $data['module'];
			//self :: $modules[$module] = $url;

			$parameters = [];

			if (preg_match_all('/' . self :: varRegex . '/', $url, $matches)) {
				if ($matches[1]) {
					$parameters = $matches[1];
				}
			}

			self :: $modules[$module][] = ['url' => $url, 'parameters' => $parameters];

			//escape / for regex
			$url = str_replace('/', '\/', $url);
			//numeric
			$url = preg_replace('/' . self :: numericRegex . '/', '(?<$1>\d+)', $url);
			//string limit
			$url = preg_replace('/' . self :: stringLimitRegex . '/', '(?<$1>[^$\/]{$2})', $url);
			//string
			$url = preg_replace('/' . self :: stringRegex . '/', '(?<$1>[^$\/]+)', $url);
			//wildcard
			$url = preg_replace('/' . self :: wildcardRegex . '/', '.*?', $url);
			//var_dump($url);
			//$parameters[0] = $url;
			self :: $urls[$url] = $module;
		}
	}

	public static function match($url) {
		if (! self :: $routes) {
			self :: init();
		}

		foreach (self :: $urls as $pattern => $route) {
			if ($url == $pattern || preg_match('/^' . $pattern . '$/', $url, $matches)) {
				$parameters = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

				$parameters['route'] = $route;

				return $parameters;
			}
		}

		return false;
	}

	public static function get($route) {
		return self :: $routes[$route] ?? [];
	}

	public static function varReplace($url, $parameters) {
		return preg_replace_callback('/' . self :: stringRegex . '|' . self :: numericRegex . '/',
			function ($matches) use ($parameters) {
				$var = $matches[1];

				if (isset($parameters[$var])) {
					return $parameters[$var];
				}

				return '';
			}, $url);
	}

	public static function getUrlData($url = false) {
		if (! $url) {
			$url = \Vvveb\getCurrentUrl();
		}

		$parameters = self :: match($url);

		if ($parameters) {
			$parameters['pattern'] = self :: $modules[$parameters['route']][0]['url'];
			$parameters            = $parameters + self :: $routes[$parameters['pattern']];

			if (isset($parameters['edit'])) {
				$parameters['edit'] = self :: varReplace($parameters['edit'], $parameters);
			}
		}

		return $parameters;
	}

	public static function url($route, $parameters = false) {
		if (! self :: $routes) {
			self :: init();
		}

		if (isset(self :: $modules[$route])) {
			$pattern    = self :: $modules[$route][0]['url'] ?? '';

			$parameters_count = is_array($parameters) ? count($parameters) : 0;

			foreach (self :: $modules[$route] as $value) {
				if ($value['parameters'] && $parameters_count) {
					if (! array_diff_key($value['parameters'], array_keys($parameters))) {
						$pattern  = $value['url'];
					}
				} else {
					$no_parameters = $value['url'];
				}
			}

			if (! $parameters) {
				$pattern = $no_parameters;
			}

			$missing = false;
			$url     = preg_replace_callback('/' . self :: varRegex . '/',
				function ($matches) use ($parameters, &$missing) {
					$var = $matches[1];

					if (isset($parameters[$var])) {
						return $parameters[$var];
					} else {
						$missing = true;
					}

					return '';
				}, $pattern);

			if ($missing) {
				return '/?route=' . $route . '&' . (is_array($parameters) ? http_build_query($parameters) : '');
			} else {
				return $url;
			}
		}
	}
}
