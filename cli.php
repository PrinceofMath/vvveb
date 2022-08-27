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

namespace Vvveb;

$msg = <<<MSG
Usage cli.php [app] [parameters...] 
where app can be admin, app or install and parameters are a list of name=value that will be used as Http GET parameters when calling controllers.
You can call any module and action by passing the corresponding module and action parameters.
Note:For admin super admin is used as user.

Examples: 

#disable plugin
php cli.php admin module=plugin/plugins action=deactivate plugin=markdown

#activate plugin
php cli.php admin module=plugin/plugins action=checkPluginAndActivate plugin=markdown

#delete post
php cli.php admin module=content/posts action=delete post_id=1

#fresh install
php cli.php install module=index host=127.0.0.1 user=root password= database=vvveb admin[email]=admin@vvveb.com admin[password]=admin

#import markdown posts from folder /docs into site with id 6
php cli.php admin module=plugins/markdown/settings action=import site=6 settings[path]=/docs
\n
MSG;

define('VIEW_TEMPLATE_ENGINE','psttt');
define('CLI',true);
define('SQL_CHECK',true);
define('DEBUG',false);

$params = implode('&', array_slice($argv, 2));
parse_str($params, $_GET);
parse_str($params, $_POST);

//seo testing
if (isset($_GET['request_uri'])) {
	$_SERVER['REQUEST_URI'] = $_GET['request_uri'];
}

$app    = 'app';
$appDir = '';

function superAdminLogin() {
	$login =
	[
		'admin_id'       => 1,
		'user'           => 'admin',
		'email'          => 'cli@vvveb',
		'url'            => '',
		'registered'     => '',
		'activation_key' => '',
		'status'         => 1,
		'display_name'   => 'Super Admin',
		'role_id'        => 1,
	];

	return \Vvveb\session(['admin' => $login]);
}

function is_installed() {
	return is_file(DIR_ROOT . 'config/db.php');
}

if (isset($argv[1])) {
	switch ($argv[1]) {
	case 'install':
		$app    = 'install';
		$appDir = 'install';

		break;

	case 'admin':
		$app    = 'admin';
		$appDir = 'admin';

		break;
	}
} else {
	echo $msg;

	return;
}

define('DIR_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('DIR_SYSTEM', DIR_ROOT . 'system' . DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
define('PUBLIC_THEME_PATH', DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

define('APP', $app);
define('FORCE_JSON', true);

//include(DIR_SYSTEM . "/view.php");

include DIR_SYSTEM . '/core/startup.php';

//$view = View::getInstance();
//$view->forceJson = true;
if ($app == 'admin') {
	superAdminLogin();
}
System\Core\start();
