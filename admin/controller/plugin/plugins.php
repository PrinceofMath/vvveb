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

namespace Vvveb\Controller\Plugin;

use Vvveb\Controller\Base;
use Vvveb\System\Core\FrontController;
use Vvveb\System\Core\View;
use function Vvveb\System\exceptionToArray;
use Vvveb\System\Extensions\Plugins as PluginsList;
use Vvveb\System\Files;
use Vvveb\System\Session;

class Plugins extends Base {
	function init() {
		parent::init();

		$this->site_id = Session::getInstance()->get('site');
		$this->plugin  = $this->request->get['plugin'] ?? null;
	}

	function deactivate() {
		if (PluginsList::deactivate($this->plugin, $this->site_id)) {
			$this->view->success[] = sprintf('Plugin "%s" deactivated!', \Vvveb\humanReadable($this->plugin));
		}

		return $this->index();
	}

	function activate() {
		$this->pluginCheckUrl             = \Vvveb\url(['module' => 'plugin/plugins', 'action'=> 'checkPluginAndActivate', 'plugin' => $this->plugin]);
		$this->view->checkPluginUrl       = $this->pluginCheckUrl;
		$this->view->info[]               = sprintf('Activating %s plugin ...', '<b>' . \Vvveb\humanReadable($this->plugin) . '</b> <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');

		return $this->index();
	}

	function index() {
		$view                   = View :: getInstance();
		$this->plugins          = PluginsList :: getList($this->site_id);
		//$categories       = PluginsList :: getCategories();
		$view->plugins    = $this->plugins;
		//$view->categories = $categories;
	}

	function delete() {
		PluginsList::deactivate($this->plugin, $this->site_id);

		try {
			//remove plugin public files if any
			Files::rmdir(DIR_PUBLIC . '/plugins/' . $this->plugin);

			if (Files::rmdir(DIR_PLUGINS . $this->plugin)) {
				$this->view->success[] = sprintf('Plugin "%s" removed!', \Vvveb\humanReadable($this->plugin));
			} else {
				$this->view->errors[] = sprintf('Error removing "%s" plugin!', \Vvveb\humanReadable($this->plugin));
			}
		} catch (\Exception $e) {
			$this->view->errors[] = sprintf('Error removing "%s" plugin %s', $e->getMessage());
		}

		return $this->index();
	}

	function upload() {
		$files = $this->request->files;

		foreach ($files as $file) {
			if ($file) {
				try {
					// use temorary file, php cleans temporary files on request finish.
					$this->pluginSlug = PluginsList :: install($file['tmp_name']);
				} catch (\Exception $e) {
					$error                = $e->getMessage();
					$this->view->errors[] = $error;
				}
			}

			if ($this->pluginSlug) {
				$this->pluginName        = \Vvveb\humanReadable($this->pluginSlug);
				$this->pluginName        = "<b>$this->pluginName</b>";
				$this->pluginActivateUrl = \Vvveb\url(['module' => 'plugin/plugins', 'action'=> 'activate', 'plugin' => $this->pluginSlug]);
				$successMessage          = sprintf('Plugin %s was successfully installed!', $this->pluginName, $this->pluginActivateUrl);
				$successMessage .= "<p><a href='$this->pluginActivateUrl'>" . sprintf('Activate plugin', $this->pluginName) . '</a></p>';
				$this->view->success[] = $successMessage;
			} else {
				$errorMessage          = sprintf('Failed to install %s plugin!', $this->pluginName);
				$this->view->error[]   = $errorMessage;
			}
		}

		return $this->index();
	}

	function checkPluginAndActivate() {
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');

		$active = false;
		$error  = false;

		try {
			if (PluginsList::loadPlugin($this->plugin)) {
				if (PluginsList::activate($this->plugin, $this->site_id)) {
					$active = true;
				}
			}
		} catch (\ParseError $e) {
			$error = exceptionToArray($e);
		} catch (\Exception $e) {
			$error = exceptionToArray($e);
		}

		if ($error) {
			$error['minimal'] = true;
			$error['title']   = sprintf('Error activating plugin `%s`!', $this->plugin);
			FrontController::notFound(false, 500, $error);

			die();
		}

		if ($active) {
			$refreshUrl = \Vvveb\url(['module' => 'plugin/plugins'], false) . '&r=' . time();
			$success    = "Plugin {$this->plugin} activated";

			if (defined('CLI')) {
				$this->view->success = [$success];
			} else {
				$response   = "
					<html>
					<head>
					<script>
					function reloadPage() {
						parent.location='$refreshUrl&success=$success';
					}
					</script>
					</head>
					<body onload='reloadPage()'><!-- Plugin valid -->
					</body>
					</html>";
				echo $response;
			}
		} else {
			die('Error activating plugin!<br/>Check config file permissions!');
		}

		return false;
	}
}
