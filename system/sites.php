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

class Sites {
	private static $sites = null;

	private static $host_matches = null;

	const HOST_REGEX = '@(?<prefix>https://|http://|//|^)(?<subdomain>.*?)?\.?(?<domain>[^\.]+)\.(?<tld>[^\.]+|[^\.]{2,3}\.[^\.]{2,3})((?<path>/.*)|$)@';

	private static $states = [
		'live'        => ['name' => 'Live', 'template' => 'index.html', 'icon' => 'la-broadcast-tower'],
		'maintenance' => ['name' => 'Maintenance', 'template' => 'index.maintenance.html', 'icon' => 'la-wrench'],
		'coming-soon' => ['name' => 'Coming soon', 'template' => 'index.coming-soon.html', 'icon' => 'la-clock'],
	];

	public static function getStates() {
		return static :: $states;
	}

	public static function getDefault() {
		$sites = self :: getSites();

		return current($sites);
	}

	public static function getSites() {
		if (! self :: $sites) {
			self :: $sites = \Vvveb\config('sites');

			foreach (self::$sites as &$site) {
				$site['href'] = self :: url($site['host']);
			}

			return self :: $sites;
		}

		return self :: $sites;
	}

	public static function getSiteById($id) {
		foreach (self :: getSites() as $site) {
			if ($site['id'] == $id) {
				return $site;
			}
		}
	}

	public static function getTheme($site_url = false) {
		$data = self :: getSiteData($site_url);

		if ($data) {
			return $data['theme'];
		}

		return 'default';
	}

	public static function url($url, $host = null) {
		$host = $host ?? $_SERVER['HTTP_HOST'] ?? 'localhost';

		if (preg_match(self :: HOST_REGEX, $url, $matches)) {
			if (self :: $host_matches || preg_match(self :: HOST_REGEX, $host, self :: $host_matches)) {
				return $matches['prefix'] .
					   str_replace('*', self :: $host_matches['subdomain'], $matches['subdomain']) . ($matches['subdomain'] ? '.' : '') .
					   str_replace('*', self :: $host_matches['domain'], $matches['domain']) . '.' .
					   str_replace('*', self :: $host_matches['tld'], $matches['tld']) .
					   ($matches['path'] ?? '');
			}

			return $matches['prefix'] . $matches['subdomain'] . $matches['domain'] . $matches['tld'] . ($matches['path'] ?? '');
		}

		return $url;
	}

	public static function siteKey($site_url = false) {
		return str_replace('.', ' ',$site_url);
	}

	public static function setSiteDataById($site_id = false, $key, $value) {
		foreach (self :: getSites() as $site_key => $site) {
			if ($site['id'] == $site_id) {
				return \Vvveb\set_config("sites.$site_key.$key", $value);
			}
		}

		return false;
	}

	public static function getSiteData($site_url = false) {
		if (! $site_url) {
			$host =$_SERVER['HTTP_HOST'] ?? 'localhost';
		}

		$host = self :: siteKey($host);

		$first = strpos($host, ' ');
		$last  = strrpos($host, ' ');

		$subdomain_wildcard    = '* ' . substr($host, $first);
		$tld_wildcard          = substr($host, 0, $last) . ' *';
		$domain_wildcard       = substr($host, 0, $first) . ' *';
		$full_wildcard         = '* ' . trim(substr($host, $first, $last - $first)) . ' *';

		$result = \Vvveb\config("sites.$host", null) ??
				  \Vvveb\config("sites.$subdomain_wildcard", null) ??
				  \Vvveb\config("sites.$domain_wildcard", null) ??
				  \Vvveb\config("sites.$full_wildcard", null) ??
				  \Vvveb\config("sites.$tld_wildcard", null) ??
				  \Vvveb\config('sites.default', null);

		if ($result) {
			$result['host'] = self :: url($result['host']);
		}

		return $result ?? false;
	}

	public static function saveSite($site) {
		$key    = str_replace('.', ' ',trim($site['key'] ?? $site['host']));
		unset($site['key']);
		$return = \Vvveb\set_config("sites.$key", $site);
	}
}
