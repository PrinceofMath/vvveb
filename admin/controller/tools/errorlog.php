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

namespace Vvveb\Controller\Tools;

use Vvveb\Controller\Base;

class ErrorLog extends Base {
	function index() {
		$count       = 100;
		$error_log   = ini_get('error_log');
		$is_readable = null;
		$text        = null;

		if (! empty($error_log)) {
			$is_readable = is_readable($error_log);

			if ($is_readable) {
				$text = \Vvveb\tail($error_log, $count);
			}
		} else {
			$error_log = _('empty file');
		}

		$log['count']    = $count;
		$log['log']      = $error_log;
		$log['text']     = $text ?? _('PHP error log not readable, make sure that your log is properly configured and that is readable.');
		$log['readable'] = $is_readable;
		$this->view->log = $log;
	}
}
