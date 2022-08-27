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

class User extends Auth {
	public static function add($data) {
		$user = new \Vvveb\Sql\UserSQL();

		//check if email is already registerd
		if ($userInfo = $user->get(['email'=> $data['email']])) {
			return true;
		}

		$data['password'] = self :: password($data['password']);
		$data['status']   = 0;

		return $user->add(['user' => $data]);
	}

	public static function update($data) {
		$user = new \Vvveb\Sql\UserSQL();

		$data['password'] = self :: password($data['password']);
		//$data['status']   = 0;

		return $user->add(['user' => $data]);
	}

	public static function login($data) {
		$user = new \Vvveb\Sql\UserSQL();
		//check user email and that status is active
		$loginInfo['status'] = 1;

		if (isset($data['email'])) {
			$loginInfo['email'] = $data['email'];
		}

		if (isset($data['user'])) {
			$loginInfo['user'] = $data['user'];
		}

		$userInfo = $user->get($loginInfo);

		if (! $userInfo) {
			return null;
		}

		if (! self::checkPassword($data['password'], $userInfo['password'])) {
			return false;
		}

		unset($userInfo['password']);
		\Vvveb\session(['user' => $userInfo]);

		return $userInfo;
	}

	public static function logout() {
		return \Vvveb\session(['user' => false]);
	}

	public static function current() {
		return \Vvveb\session('user', false);
	}
}
