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

namespace Vvveb\Controller;

use Vvveb\Sql\SiteSQL as SiteSQL;
use Vvveb\System\User\Admin;

//define('INSTALL_SQL_PATH', DIR_ROOT . 'install/sql/');
define('REQUIRED_EXTENSIONS', ['mysqli', 'mysqlnd', 'xml', 'libxml', 'pcre',  'zip', 'dom', 'curl', 'gettext']);
define('WRITABLE_FOLDERS', ['storage', 'storage/cache', 'storage/model', 'storage/compiled-templates', 'config', 'public/media/', 'public/themes']);
define('MIN_PHP_VERSION', '7.3.0');

class Index extends Base {
	function __construct() {
		if (\Vvveb\is_installed() &&
			(($admin = Admin::get(['user' => 'admin'])) && $admin['status'] == '1')) {
			header('Location: /');

			die("Already installed! To reinstall remove config/db.php\n");
		}
	}

	function checkRequirements() {
		$notMet = [];

		if (version_compare(PHP_VERSION, MIN_PHP_VERSION) < 0) {
			$notMet[] = 'You need at least PHP ' . MIN_PHP_VERSION . ', your current version ' . PHP_VERSION;
		}

		foreach (REQUIRED_EXTENSIONS as $extension) {
			if (! extension_loaded($extension)) {
				$notMet[] = sprintf(\Vvveb\__('PHP extension %s is not installed'), $extension);
			}
		}

		foreach (WRITABLE_FOLDERS as $folder) {
			$path = DIR_ROOT . $folder;

			if (! is_writable($path) && ! @chmod($path, 0750)) {
				$notMet[] = sprintf(\Vvveb\__('Folder "%s" is not writable'), $folder);
			}
		}

		return $notMet;
	}

	function writeConfig($data) {
		return \Vvveb\set_config('db', $data);
		$configFile = DIR_ROOT . 'config/db.php';
		file_put_contents($configFile, "<?php\n return " . var_export($data, true) . ';');
		clearstatcache(true, $configFile);
	}

	function import() {
		$keys = ['engine', 'host', 'database', 'user', 'password', 'prefix'];

		$config = array_filter($this->request->post, function ($v) use ($keys) {
			return in_array($v, $keys);
		}, ARRAY_FILTER_USE_KEY);

		$config['engine'] = $engine ?? 'mysqli';
		extract($config);

		$prefix = $prefix ?? '';
		$data['default']              = $config['engine'];
		$data['connections'][$engine] = $config;

		try {
			define('DB_ENGINE', $engine);
			define('DB_HOST', $host);
			define('DB_USER', $user);
			define('DB_PASS', $password);
			define('DB_NAME', $database);
			define('DB_PREFIX', $prefix);
			define('DB_CHARSET', 'utf8mb4');
			define('DIR_SQL', DIR_APP . 'sql/' . DB_ENGINE . '/');

			$import = new \Vvveb\System\Import\Sql($engine, $host, $database, $user, $password, $prefix);
			$import->createDb($database);
			$this->writeConfig($data);
			$import->setPath(DIR_ROOT . "install/sql/$engine/import/");
			//$import->createDb($database);
			$import->createTables();
			$import->insertData();
			$import->db->close();

			header('Location: ' . ($_SERVER['REQUEST_URI'] ?? 'localhost'). '?action=install');
		} catch (\Exception $e) {
			$this->view->errors[] = 'Db error: ' . $e->getMessage() . "\n Error code:" . $e->getCode();
		}
	}

	function index() {
		$this->view->requirements     = $this->checkRequirements();
		$languagesList                = include DIR_SYSTEM . 'data/languages-list.php';

		if (! defined('CLI')) {
			$this->view->languagesList    = $languagesList;
			$this->view->current_language = 'en-us';
		}

		if ($this->request->post) {
			if (isset($this->request->post['language'])) {
			} else {
				$this->import();
				//if user data is provided (by CLI) run also step2
				if (isset($this->request->post['admin'])) {
					$this->install();
				}
			}
		}
	}

	function install() {
		if ($this->request->post) {
			//set admin password
			$user      = $this->request->post['admin'] ?? false;
			$settings  = $this->request->post['settings'] ?? false;

			$user['status'] = 1;
			$result         = Admin::update($user, ['user' => 'admin']);
			$result         = \Vvveb\set_options($settings);

			if (isset($_SERVER['HTTP_HOST'])) {
				//set default website url
				$sites          = new SiteSQL();
				$sites->edit(['site' => ['host' => $_SERVER['HTTP_HOST']], 'site_id' => 1]);
			}

			$success               = _('Installation succesful!');
			$this->view->success[] = $success;
			$admin_path            = '/' . \Vvveb\config('admin.path', 'admin') . '/';
			header('Location: ' . preg_replace('@/install.*$@', $admin_path, ($_SERVER['REQUEST_URI'] ?? '') . "&success=$success"));
		}

		$this->view->template('install.html');
	}
}
