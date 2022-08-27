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

namespace Vvveb\System\User;

class Admin extends Auth {
	public static function add($data) {
		$admin = new \Vvveb\Sql\AdminSQL();

		//check if email is already registerd
		if ($adminInfo = $admin->get(['email'=> $data['email']])) {
			return true;
		}

		$data['password'] = self :: password($data['password']);
		$data['status']   = 0;

		return $admin->add(['admin' => $data]);
	}

	public static function update($data, $condition) {
		$admin = new \Vvveb\Sql\AdminSQL();

		if (isset($data['password'])) {
			$data['password'] = self :: password($data['password']);
		}

		return $admin->update(array_merge(['admin' => $data], $condition));
	}

	public static function get($data) {
		if (isset($data['email'])) {
			$loginInfo['email'] = $data['email'];
		}

		if (isset($data['user'])) {
			$loginInfo['user'] = $data['user'];
		}

		$admin     = new \Vvveb\Sql\AdminSQL();
		$adminInfo = $admin->get($loginInfo);

		if (! $adminInfo) {
			return null;
		}

		return $adminInfo;
	}

	public static function login($data) {
		//check admin email and that status is active
		$data['status'] = 1;
		$adminInfo      = self::get($data);

		if ((! $adminInfo) ||
			! self::checkPassword($data['password'], $adminInfo['password'])) {
			return false;
		}

		unset($adminInfo['password']);
		\Vvveb\session(['admin' => $adminInfo]);

		return $adminInfo;
	}

	public static function logout() {
		return \Vvveb\session(['admin' => false]);
	}

	public static function current() {
		return \Vvveb\session('admin', false);
	}
}
