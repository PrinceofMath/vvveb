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

return [
	'driver' => Vvveb\env('MAIL_DRIVER', 'smtp'),
	'host'   => Vvveb\env('MAIL_HOST', 'smtp.mailgun.org'),
	'port'   => Vvveb\env('MAIL_PORT', 587),
	'from'   => [
		'address' => Vvveb\env('MAIL_FROM_ADDRESS', 'hello@example.com'),
		'name'    => Vvveb\env('MAIL_FROM_NAME', 'Example'),
	],
	'encryption' => Vvveb\env('MAIL_ENCRYPTION', 'tls'),
	'username'   => Vvveb\env('MAIL_USERNAME'),
	'password'   => Vvveb\env('MAIL_PASSWORD'),
	'sendmail'   => '/usr/sbin/sendmail -bs',
	'markdown'   => [
		'theme' => 'default',
		'paths' => [
		],
	],
	'log_channel' => Vvveb\env('MAIL_LOG_CHANNEL'),
];
