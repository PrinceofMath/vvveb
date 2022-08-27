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

namespace Vvveb\System\Core;

use Vvveb\System\Extensions\Plugins;
use Vvveb\System\Session;
use Vvveb\System\Sites;
use Vvveb\System\Sqlp\Sqlp;

define('VERSION', '0.1');
//define('DIR_APP', DIR_ROOT . 'app/');
//define('DIR_PUBLIC', DIR_ROOT . 'webroot/');

$storage_dir = DIR_ROOT . 'storage' . DIRECTORY_SEPARATOR;

if (is_writable($storage_dir)) {
	define('DIR_STORAGE', DIR_ROOT . 'storage' . DIRECTORY_SEPARATOR);
} else {
	$storage_dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR;

	if (! is_dir($storage_dir)) {
		@mkdir($storage_dir);
		@mkdir($storage_dir . 'compiled-templates' . DIRECTORY_SEPARATOR);
		@mkdir($storage_dir . 'cache');
		@mkdir($storage_dir . 'model');
		@mkdir($storage_dir . join(DIRECTORY_SEPARATOR, ['model', 'admin']) . DIRECTORY_SEPARATOR);
		@mkdir($storage_dir . join(DIRECTORY_SEPARATOR, ['model/app']) . DIRECTORY_SEPARATOR);
		@mkdir($storage_dir . join(DIRECTORY_SEPARATOR, ['model/install']) . DIRECTORY_SEPARATOR);
	}

	define('DIR_STORAGE', $storage_dir);
}

define('DIR_CACHE', DIR_ROOT . join(DIRECTORY_SEPARATOR, ['storage', 'cache']) . DIRECTORY_SEPARATOR);
define('DIR_PLUGINS', DIR_ROOT . 'plugins' . DIRECTORY_SEPARATOR);
define('DIR_COMPILED_TEMPLATES', DIR_STORAGE . 'compiled-templates' . DIRECTORY_SEPARATOR);
define('DIR_THEMES', DIR_ROOT . join(DIRECTORY_SEPARATOR, ['public', 'themes']));

if (APP == 'app') {
	//define('THEME', 'essence');
	define('DIR_THEME', DIR_ROOT . join(DIRECTORY_SEPARATOR, ['public', 'themes']) . DIRECTORY_SEPARATOR);
	define('DIR_PUBLIC', DIR_ROOT . 'public' . DIRECTORY_SEPARATOR);
} else {
	//define('THEME', 'default');
	define('DIR_THEME', DIR_ROOT . 'public' . DIRECTORY_SEPARATOR . APP . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR);
	define('DIR_PUBLIC', DIR_ROOT . 'public' . DIRECTORY_SEPARATOR);
}

define('DIR_APP', DIR_ROOT . APP . DIRECTORY_SEPARATOR);
define('DIR_TEMPLATE', DIR_APP . 'template' . DIRECTORY_SEPARATOR);
define('DIR_MEDIA', DIR_PUBLIC . 'media' . DIRECTORY_SEPARATOR);

include DIR_SYSTEM . 'session.php';

include DIR_SYSTEM . '/component/component.php';

require_once DIR_SYSTEM . '/core/frontcontroller.php';

require_once DIR_SYSTEM . '/core/view.php';

require_once DIR_SYSTEM . '/functions/functions.php';

require_once DIR_SYSTEM . 'event.php';
//require_once(DIR_SYSTEM . 'component.inc');

function logError($message) {
	return error_log($message);
}

function regenerateSQL($sqlFile, $file, $modelName, $namespace) {
	$sqlp = new SqlP();

	$sqlp->parseSqlPfile($sqlFile, $modelName, $namespace);

	@mkdir(dirname($file),0755,true);
	file_put_contents($file, "<?php \n" . $sqlp->generateModel());
}

function autoload($class) {
	// project-specific namespace prefix
	$prefix = 'Vvveb\\';

	// does the class use the namespace prefix?
	$len = strlen($prefix);

	if (strncmp($prefix, $class, $len) !== 0) {
		// no, move to the next registered autoloader
		return;
	}

	$relativeClass = substr($class, $len);

	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .inc
	$root = DIR_APP;

	$isSql = false;
	//if namespace is Vvveb\System or Vvveb\Plugins load from system dir above app dir
	if ((substr_compare($relativeClass, 'System\\', 0, 7) == 0) ||
		(substr_compare($relativeClass, 'Plugins\\', 0, 7) == 0)) {
		$root = DIR_ROOT;
	}

	$file = $root . str_replace('\\', '/', strtolower($relativeClass)) . '.php';

	//check if sql files are changed to regenerate sql class
	if (SQL_CHECK && $isSql = (substr_compare($relativeClass, 'SQL', -3, 3) === 0)) {
		//convert camelCase to snake_case
		//echo $relativeClass;
		$sqlFile = str_replace(['Sql', '\\'], ['', DIRECTORY_SEPARATOR], substr($relativeClass, 0, -3));
		$sqlFile = strtolower(preg_replace('/(?<!^)[A-Z]/', '$0', $sqlFile));

		$sqlFile   = DIR_SQL . $sqlFile . '.sql';
		$name      = str_replace(['\\', 'sql' . DIRECTORY_SEPARATOR], [DIRECTORY_SEPARATOR, ''], strtolower($relativeClass));
		$modelName = ucwords(basename(str_replace('sql', '', $name)));
		$namespace = ucwords(dirname($name));

		if ($namespace != '.') {
			$namespace = "\\$namespace";
		} else {
			$namespace = '';
		}

		$file    = DIR_STORAGE . 'model' . DIRECTORY_SEPARATOR . APP . DIRECTORY_SEPARATOR . $name . '.php';
		//if the file has not been generated yet or sql files is changed recompile
		if (! file_exists($file) || (filemtime($sqlFile) > filemtime($file))) {
			regenerateSQL($sqlFile, $file, $modelName, $namespace);
		}
	}
	// if the file exists, require it
	if (file_exists($file)) {
		require_once $file;
	}
}

function autoload_vendor($class) {
	$path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

	$file = DIR_ROOT . 'vendor' . DIRECTORY_SEPARATOR . "$path.php";

	// if the file exists, require it
	if (file_exists($file)) {
		require_once $file;
	}
}

function exceptionToArray($exception) {
	$file   = $exception->getFile();
	$lineNo = $exception->getLine() - 1;
	//$code = $exception->getCode();
	$class= get_class($exception);

	$codeLines          = file($file);
	$codeLines[$lineNo] = preg_replace("/\n$/","\t// <b style='color:red'><<<===</b>\n", $codeLines[$lineNo]);
	$line               = implode("\n", array_slice($codeLines, $lineNo, 1));
	$before             = implode("\n", array_slice($codeLines, $lineNo - 6, 5));
	$after              = implode("\n", array_slice($codeLines, $lineNo + 1, 5));
	$code               = "$before<b>$line</b>$after";
	$lines              = array_slice($codeLines, $lineNo - 7, 14);

	$message = [
		'message' => $exception->getMessage(),
		'code'    => $code,
		'file'    => $file,
		'line_no' => $lineNo,
		'line'    => $line,
		'lines'   => $lines,
		'trace'   => $exception->getTraceAsString(),
	];

	return $message;
}

function exceptionHandler($exception) {
	$message = exceptionToArray($exception);

	pluginErrorCheck($file);

	echo '<b>Exception:</b> ' . $exception->getMessage();

	return FrontController::notFound(false, 500, $message);
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
	if (! (error_reporting() & $errno)) {
		// This error code is not included in error_reporting, so let it fall
		// through to the standard PHP error handler
		return false;
	}

	// $errstr may need to be escaped:
	$errstr = htmlspecialchars($errstr);

	switch ($errno) {
	case E_USER_WARNING:
		//echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
		break;

	case E_USER_NOTICE:
		//echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
		break;

	default:
		//echo "Unknown error type: [$errno] $errstr<br />\n";
		break;

	case E_ERROR:
	case E_USER_ERROR:
		echo "<b>ERROR</b> [$errno] $errstr<br />\n";
		echo "  Fatal error on line $errline in file $errfile";
		echo ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ")<br />\n";
		//check if error is generated by a plugin and disable it
		pluginErrorCheck($errfile);

		return FrontController::notFound(false, 500, $errstr);
	}

	/* Don't execute PHP internal error handler */
	return true;
}

function pluginErrorCheck($file) {
	if (($pos = strpos($file, DIR_PLUGINS)) !== false) {
		$plugin = \Vvveb\filter('@([^/]+)@', str_replace(DIR_PLUGINS, '', $file));
		logError("'$plugin' plugin triggers fatal error.");

		if (DISABLE_PLUGIN_ON_ERORR) {
			logError(\Vvveb\__("Disabling '%s' plugin.", $plugin));
			Plugins::deactivate($plugin);
		}
	}
}

function fatalErrorHandler() {
	$message = error_get_last();

	if ($message) {
		myErrorHandler($message['type'], $message['message'],$message['file'], $message['line']);
	}
}

spl_autoload_register('Vvveb\System\core\autoload');
spl_autoload_register('Vvveb\System\core\autoload_vendor');
//set_exception_handler('Vvveb\System\exceptionHandler');
//set_error_handler('Vvveb\System\myErrorHandler');
///register_shutdown_function('\Vvveb\System\fatalErrorHandler');

//require DIR_ROOT . '/vendor/autoload.php';

$dbDefault  = \Vvveb\config('db.default', 'default');
$connection = \Vvveb\config('db.connections.' . $dbDefault,  []);

if ($connection || defined('DB_ENGINE')) {
	// Define default database configuration
	define('DB_ENGINE', $connection['engine']);
	define('DB_HOST', $connection['host']);
	define('DB_USER', $connection['user']);
	define('DB_PASS', $connection['password']);
	define('DB_NAME', $connection['database']);
	define('DB_PREFIX', $connection['prefix'] ?? '');
	define('DB_CHARSET', 'utf8mb4');

	define('DIR_SQL', DIR_APP . 'sql' . DIRECTORY_SEPARATOR . DB_ENGINE . DIRECTORY_SEPARATOR);
} else {/*
	define('DB_ENGINE', 'mysqli');
	define('DIR_SQL', DIR_APP . 'sql/' . DB_ENGINE . '/');
	define('DB_HOST', 'localhost');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('DB_NAME', 'vvveb');
	define('DB_PREFIX', '');
	define('DB_CHARSET', 'utf8mb4');
	 */
}

function start() {
	//start session
	Session :: getInstance();
	$site = Sites :: getSiteData();

	if ($site) {
		define('SITE_URL', $site['host']);
		define('SITE_ID', $site['id']);

		//load plugins first for APP
		if (APP == 'app') {
			if (isset($site['state']) && $site['state'] != 'live') {
				$view     = View::getInstance();
				$template = Sites::getStates()[$site['state']]['template'];
				$view->template($template);

				return $view->render();
			}
			Plugins :: loadPlugins(SITE_ID);
		}

		//define('DIR_THEME', DIR_ROOT . 'public/themes/'. THEME .'/');

		FrontController::dispatch();
	} else {
		FrontController::notFound(false, 404, 'Website not found!');
	}
}
