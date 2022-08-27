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

namespace Vvveb\Controller\Settings;

use Vvveb\Controller\Base;
use Vvveb\Sql\SiteSQL;
use Vvveb\System\Extensions\Themes;
use Vvveb\System\Sites;
use Vvveb\System\Validator;

class Site extends Base {
	function add() {
	}

	function save() {
		$validator = new Validator(['site']);
		$view      = $this->view;

		if (($errors = $validator->validate($this->request->post['site'])) === true) {
			$sites = new SiteSQL();

			$site = $this->request->post['site'] ?? [];

			if (! isset($site['key']) || ! $site['key']) {
				$site['key'] = strtolower($site['name']);
			}

			if (isset($this->request->get['site_id'])) {
				$data['site_id'] = (int)$this->request->get['site_id'];
				$data['site']    = $site;
				$result          = $sites->edit($data);
				Sites::saveSite($site);

				if ($result >= 0) {
					$this->view->success = ['Site saved'];
					$this->redirect(['module'=>'settings/sites', 'success'=> 'Site saved']);
				} else {
					$this->view->validationErrors = [$sites->error];
				}
			} else {
				var_dump($site);
				$return = $sites->add(['site' => $site]);
				$id     = $return['site'];
				Sites::saveSite($site);

				if (! $id) {
					$view->validationErrors = [$sites->error];
				} else {
					$view->success = 'Site saved!';
					$this->redirect(['module'=>'settings/sites', 'success'=> 'Site saved']);
				}
			}
		} else {
			$view->validationErrors = $errors;
		}

		$this->index();
	}

	function index() {
		$site_id                   = $this->request->get['site_id'];
		$view                      = $this->view;
		$view->themeList           = Themes:: getList();
		$view->templateList        = \Vvveb\getTemplateList();

		$siteSql             = new SiteSQL();
		$site                = $siteSql->get(['site_id' => $site_id]);
		$view->site          = $site;
	}
}
