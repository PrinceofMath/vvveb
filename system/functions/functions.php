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

function url($parameters, $mergeParameters = false, $useCurrentUrl = true) {
	if (is_string($parameters) && $parameters) {
		return System\Routes::url($parameters, $mergeParameters);
	}

	static $url       = null;
	static $urlParams = [];

	if ($url == null) {
		$url=parse_url($_SERVER['REQUEST_URI']);

		if (isset($url['query'])) {
			parse_str($url['query'], $urlParams);
		}
	}

	if (! is_array($parameters)) {
		$parameters = [];
	}

	if ($mergeParameters) {
		if (is_array($mergeParameters)) {
			$parameters = array_merge($urlParams, $parameters);
		}

		if (is_array($mergeParameters)) {
			$parameters = array_merge($parameters, $mergeParameters);
		}
	}

	$result = '';

	if (isset($parameters['host'])) {
		$result .= '//' . \Vvveb\System\Sites::url($parameters['host']);
		unset($parameters['host']);
	}

	$result .= ($useCurrentUrl ? $url['path'] : '') . ($parameters ? '?' . urldecode(http_build_query($parameters)) : '');

	return $result;
}

function config($key = null, $default = null) {
	if (is_null($key)) {
		return System\Config::getInstance();
	}

	return System\Config::getInstance()->get($key, $default);
}

function get_config($key = null, $default = null) {
	return System\Config::getInstance()->get($key, $default);
}

function set_config($key, $value = null) {
	return System\Config::getInstance()->set($key, $value);
}

function unset_config($key) {
	return System\Config::getInstance()->unset($key);
}

function get_option($key = null, $default = null, $site_id = SITE_ID) {
	return System\Option::getInstance()->get($key, $default);
}

function set_option($key = null, $value = null, $site_id = SITE_ID) {
	return System\Option::getInstance()->set($key, $value);
}

function set_options($options, $site_id = SITE_ID) {
	return System\Option::getInstance()->multiSet($options);
}

function getCurrentTemplate() {
	return System\Core\View :: getInstance()->template();
}

function getUrlTemplate($url) {
	$urlData = \Vvveb\System\Routes::getUrlData($url);

	return $urlData;
}

function getCurrentUrl() {
	return $_SERVER['REQUEST_URI'];
}

function publicMediaUrlPath() {
	if (PUBLIC_PATH == '/public/' || PUBLIC_PATH == '/public/admin/') {
		return PUBLIC_PATH;
	} else {
		return '/';
	}
}

function publicUrlPath() {
	return PUBLIC_PATH;
}

function themeUrlPath() {
	return PUBLIC_THEME_PATH . '/themes/' . \Vvveb\System\Core\View::getInstance()->getTheme() . '/';
}

function escUrl($url) {
	return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function escAttr($attr) {
	return htmlspecialchars($attr);
}

function escHtml($url) {
	return htmlspecialchars($url);
}

function env($key, $default = null) {
	if ($env = getenv($key)) {
		return $env;
	} else {
		return $default;
	}
}

if (! function_exists('nggetext')) {
	function nggetext($singular, $plural, $number) {
		return ($number > 1) ? $plural : $singular;
	}
}

function friendlyDate($date) {
	$fileformats = [
		1          => ['%d second', '%d seconds'],
		60         => ['%d minute', '%d minutes'],
		3600       => ['%d hour', '%d hours'],
		86400      => ['%d day', '%d days'],
		604800     => ['%d week', '%d weeks'],
		2592000    => ['%d month', '%d months'],
		31536000   => ['%d year', '%d years'],
		315360000  => ['%d decade', '%d decades'],
		3153600000 => ['%d century', '%d centuries'],
	];

	$time_direction = ' ago';
	$diff           = time() - strtotime($date) + 10;

	if ($diff < 0) {
		$time_direction = ' from now';
		$diff           = abs($diff);
	}

	$lastTime = 1;
	$lastText = $fileformats[1];

	foreach ($fileformats as $time => $text) {
		if ($diff < $time) {
			$units = floor($diff / $lastTime);

			return sprintf(nggetext($lastText[0] . $time_direction, $lastText[1] . $time_direction, $units), $units);
		}
		$lastText = $text;
		$lastTime = $time;
	}

	return $date;
}

function dotToArrayKey($key) {
	//var.key1.key2 > var['key1']['key2']
	//var.key1 > var['key1']

	return preg_replace_callback('/\.(\w+)/', function ($matches) {
		return "['" . str_replace("'", "\'", $matches[1]) . "']";
	}, $key);
}

function filterText($data) {
	return urldecode(filter_var($data, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW));
}

function session($data, $default = null) {
	$session = System\Session :: getInstance();

	if (is_array($data)) {
		foreach ($data as $key => $value) {
			$session->set($key, $value);
		}
	} else {
		$value = $session->get($data);

		if ($default && ! $value) {
			return $default;
		}

		return $value;
	}
}

function filter($regex, $input, $maxInputSize = 100) {
	$matches = [];

	if (preg_match($regex, substr($input, 0, $maxInputSize), $matches)) {
		return $matches[0];
	} else {
		return false;
	}
}

function regexMatch($regex, $input, $level = false) {
	$matches = [];

	if (preg_match($regex, $input, $matches, PREG_UNMATCHED_AS_NULL | PREG_PATTERN_ORDER)) {
		if ($level !== false) {
			return $matches[$level];
		}

		return $matches;
	} else {
		return false;
	}
}

function regexMatchAll($regex, $input, $level = false) {
	$matches = [];

	if (preg_match_all($regex, $input, $matches, PREG_UNMATCHED_AS_NULL | PREG_PATTERN_ORDER)) {
		if ($level !== false) {
			return $matches[$level];
		}

		return $matches;
	} else {
		return false;
	}
}

function arrayAllowValues($input, $allowedValues) {
	if (! in_array($input, $allowedValues)) {
		return null;
	} else {
		return $input;
	}
}

/*
 * Get values from multidimensional arrays based on path
 * For example for array ['item' => ['desc' => ['name' => 'test']]] the path "item.description.name" will return "test".
 * 
 * */

function arrayPath(array $a, $path, $default = null, $token = '.') {
	$p = strtok($path, $token);

	while ($p !== false) {
		if (! isset($a[$p])) {
			return $default;
		}

		$a = $a[$p];
		$p = strtok($token);
	}

	return $a;
}

function humanReadable($text) {
	return ucfirst(str_replace(['_', '-', '/', '[', ']', '.'], [' ', ' ', ' - ', ' ', ' ', ' '], trim($text, ' /\-_')));
}

function __($text, ...$parameters) {
	return _($text);
}

function isAdmin() {
	return System\User\Admin::current() ? true : false;
}

function cssToXpath($selector) {
	//if already xpath don't transform
	//if (substr_compare($selector,'psttt_xpath', 0, 11) == 0) return substr($selector, 12, -1);

	$selector = (string) $selector;

	//convert , to | union operator to allow multiple queries
	$selector = str_replace(',', '|', $selector);

	$cssSelector = [
		// E > F: Matches any F element that is a child of an element E
		'/\s*>\s*/',
		// E + F: Matches any F element immediately preceded by an element
		'/\s*\+\s*/',
		// E F: Matches any F element that is a descendant of an E element
		'/([a-zA-Z\*="\[\]#._-])\s+([a-zA-Z\*#._-])/',
		// E:first-child: Matches element E when E is the first child of its parent
		'/(\w+):first-child/',
		// E[foo="warning"]: Matches any E element whose "foo" attribute value is exactly equal to "warning"
		'/(\w+)\[([\w\-_]+)\="([^"]*)"]/',
		// E[foo]: Matches any E element with the "foo" attribute set (whatever the value)
		'/(\w+)\[([\w_\-]+)\]/',
		// E[!foo]: Matches any E element without the "foo" attribute set
		'/(\w+)\[!([\w\-_]+)\]/',
		// [foo="warning"]: Matches any element whose "foo" attribute value is exactly equal to "warning"
		'/\[([\w\-_]+)\=\"(.*)\"\]/',

		// [foo*="warning"]: Matches any element whose "foo" attribute value contains the string "warning"
		'/\[([\w\-_]+)\*\=\"([^"]+)\"\]/',

		// [foo^="warning"]: Matches any element whose "foo" attribute value begins with the string "warning"
		'/\[([\w_\-]+)\^\=\"([^"]+)\"\]/',

		// [foo$="warning"]: Matches any element whose "foo" attribute value ends  with the string "warning"
		'/\[([\w_\-]+)\$\=\"([^"]+)\"\]/',

		// [foo]: Matches any element with the "foo" attribute set (whatever the value)
		'/\[([\w_\-]+)\]/',
		// element[foo*]: Matches any element that starts with "foo" attribute (whatever the value)
		'/(\w+)\[([\w\-]+)\*\]/',
		// [foo*]: Matches any element that starts with "foo" attribute (whatever the value)
		'/\[([\w\-]+)\*\]/',
		// div.warn*: HTML only. The same as DIV[class*="warning"]
		'/(\w+|\*)\.([\w\-_]+)\*/',
		// div.warning: HTML only. The same as DIV[class~="warning"]
		'/(\w+|\*)\.([\w\-_]+)+/',
		// .warn*: HTML only. The same as [class*="warning"]
		'/\.([\w\-\_]+)\*/',
		// .warning: HTML only. The same as [class~="warning"]
		'/\.([\w\-\_]+)+/',
		// E#myid: Matches any E element with id-attribute equal to "myid"
		'/(\w+)+\#([\w\-_]+)/',
		// #myid: Matches any E element with id-attribute equal to "myid"
		'/\#([\w\-_]+)/',
	];

	$xpathQuery = [
		'/', //element > child
		'/following-sibling::*[1]/self::', // element + precedent
		'\1//\2', //element descendent
		'\1[ 1 ]', //'*[1]/self::\1',//element:first-child
		'\1[ contains( concat( " ", @\2, " " ), concat( " ", "\3", " " ) ) ]', //element[attribute="string"]
		'\1 [ @\2 ]', //element[attribute]
		'\1 [ not(@\2) ]', //element[!attribute]
		'*[ contains( concat( " ", @\1, " " ), concat( " ", "\2", " " ) ) ]', //[foo="warning"] not implemented
		'*[ contains( concat( " ", @\1, " " ), "\2" ) ]', //[foo*="warning"]
		'*[ contains( concat( " ", @\1, " " ), concat( " ", "\2", " " ) ) ]', //[foo^="warning"] not implemented
		'*[ contains( concat( " ", @\1, " " ), concat( " ", "\2", " " ) ) ]', //[foo$="warning"] not implemented
		'//*[ @\1 ]', //[attribute]
		'\1 [ @*[starts-with(name(), "\2")] ]', //element[attr*]
		'//*[ @*[starts-with(name(), "\1")] ]', //[attr*]
		'\1[ contains( concat( " ", @class, " " ), concat( " ", "\2") ) ]', //element[class*="string"]
		'\1[ contains( concat( " ", @class, " " ), concat( " ", "\2", " " ) ) ]', //element[class~="string"]
		'*[ contains( concat( " ", @class, " " ), concat( " ", "\1") ) ]', //[class*="string"]
		'*[ contains( concat( " ", @class, " " ), concat( " ", "\1", " " ) ) ]', //element[class~="string"]
		'\1[ @id = "\2" ]', //element#id
		'*[ @id = "\1" ]', //#id
	];

	$result = (string) '//' . preg_replace($cssSelector, $xpathQuery, $selector);
	//$this->debug(STORE_CSS_XPATH_TRANSFORM, $result);
	return $result;
}

function dashesToCamelCase($string) {
	return str_replace('-', '', ucwords($string, '-'));
}

/**
 * Remove extra spaces.
 * @param mixed $string 
 *
 * @return string 
 */
function stripExtraSpaces($string) {
	foreach (['\t', '\n', '\r', ' '] as $space) {
		$string = preg_replace('/(' . $space . ')' . $space . '+/', '\1', $string);
	}

	return $string;
}

function tail($filename, $lines = 1000) {
	$file = @fopen($filename, 'rb');

	if ($file === false) {
		return false;
	}

	$buffer = 4096;
	$output = '';
	$chunk  = '';

	fseek($file, -1, SEEK_END);

	if (fread($file, 1) != "\n") {
		$lines -= 1;
	}

	while (ftell($file) > 0 && $lines >= 0) {
		$seek = min(ftell($file), $buffer);
		fseek($file, -$seek, SEEK_CUR);
		$output = ($chunk = fread($file, $seek)) . $output;
		fseek($file, -mb_strlen($chunk, '8bit'), SEEK_CUR);
		$lines -= substr_count($chunk, "\n");
	}

	while ($lines++ < 0) {
		$output = substr($output, strpos($output, "\n") + 1);
	}

	fclose($file);

	return trim($output);
}

/**
 * Return current module name.
 *
 * @return string 
 */
function getModuleName() {
	return strtolower(\Vvveb\System\Core\FrontController::getModuleName());
}

/**
 * Return current action name.
 *
 * @return string 
 */
function getActionName() {
	return strtolower(\Vvveb\System\Core\FrontController::getActionName());
}

/*
 * Inserts a new key/value before the key in the array.
 *
 * @param $key
 *   The key to insert before.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, otherwise the unchanged array.
 *
 * @see array_insert_after()
 */
function array_insert_before($key, array &$array, $new_key, $new_value) {
	if (array_key_exists($key, $array)) {
		$new = [];

		foreach ($array as $k => $value) {
			if ($k === $key) {
				$new[$new_key] = $new_value;
			}
			$new[$k] = $value;
		}

		return $new;
	}

	return $array;
}

/*
 * Inserts a new array before the key in the array.
 *
 * @param $key
 *   The key to insert before.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, otherwise the unchanged array.
 *
 * @see array_insert_after()
 */
function array_insert_array_before($key, array &$array, $new_array) {
	if (array_key_exists($key, $array)) {
		$new = [];

		foreach ($array as $k => $value) {
			if ($k === $key) {
				$new += $new_array;
			}
			$new[$k] = $value;
		}

		return $new;
	}

	return $array;
}

/*
 * Inserts a new key/value after the key in the array.
 *
 * @param $key
 *   The key to insert after.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, otherwise the unchanged array.
 *
 * @see array_insert_before()
 */
function array_insert_after($key, array &$array, $new_key, $new_value) {
	if (array_key_exists($key, $array)) {
		$new = [];

		foreach ($array as $k => $value) {
			$new[$k] = $value;

			if ($k === $key) {
				$new[$new_key] = $new_value;
			}
		}

		return $new;
	}

	return $array;
}

/*
 * Inserts a new array after the key in the array.
 *
 * @param $key
 *   The key to insert after.
 * @param $array
 *   An array to insert in to.
 * @param $new_key
 *   The key to insert.
 * @param $new_value
 *   An value to insert.
 *
 * @return
 *   The new array if the key exists, otherwise the unchanged array.
 *
 * @see array_insert_before()
 */
function array_insert_array_after($key, array &$array, $new_array) {
	if (array_key_exists($key, $array)) {
		$new = [];

		foreach ($array as $k => $value) {
			$new[$k] = $value;

			if ($k === $key) {
				$new += $new_array;
			}
		}

		return $new;
	}

	return $array;
}

//request
function get($key) {
	return System\Request::getInstance()->get[$key];
}

/**
 * Check if the page is loaded in the editor.
 *
 * @return boolean
 */
function isEditor() {
	return isset($_GET['r']);
}

function log_error($message) {
	error_log($message);
}

function getTemplateList($theme = false) {
	$friendlyNames =  [
		'index'         => ['name' => 'Home page', 'description' => 'Website homepage'],
		'contact'       => ['name' => 'Contact us page', 'description' => 'Contact us page'],
		'blank'         => ['name' => 'Blank page', 'description' => 'Template page used for new pages'],
		'product'       => ['name' => 'Product page', 'description' => 'Used to display a product'],
		'error404'      => ['name' => 'Page not found', 'description' => 'Shows when a page is not available'],
		'error500'      => ['name' => 'Server error', 'description' => 'Site error display page'],
		'content-index' => ['name' => 'Shop page', 'description' => 'Shop homepage'],
		'content-post'  => ['name' => 'Blog page', 'description' => 'Blog page with latest posts'],
		'product-index' => ['name' => 'Shop page', 'description' => 'Shop homepage'],
		'search-index'  => ['name' => 'Search page', 'description' => 'Search homepage'],
		'user-index'    => ['name' => 'Dashboard', 'description' => 'User dashboard'],
	];

	$pagesSortOrder = ['index' => '', 'contact' => '', 'blank' => '', 'error404' => '', 'error500' => ''];
	$skipFolders    = ['src', 'backup', 'sections', 'blocks', 'inputs'];

	if (! $theme) {
		$theme = \Vvveb\System\Sites::getTheme() ?? 'default';
	}
	$pages       = [];
	$themeFolder = DIR_THEMES . DIRECTORY_SEPARATOR . $theme;
	$files       = glob("$themeFolder/{,**/}*.html", GLOB_BRACE);

	foreach ($files as $file) {
		$file     = preg_replace('@^.*/themes/[^/]+/@', '', $file);
		$filename = basename($file);

		$folder   = \Vvveb\System\Functions\Str::match('@(\w+)/.*?$@', $file);

		if (in_array($folder, $skipFolders)) {
			continue;
		}
		$name        = $title       = str_replace('.html', '', $filename);
		$description = '';
		$name        = ! empty($folder) ? "$folder-$name" : $name;

		if (isset($friendlyNames[$name])) {
			if (isset($friendlyNames[$name]['description'])) {
				$description = $friendlyNames[$name]['description'];
			}

			$title = $friendlyNames[$name]['name'];
		}

		$url = "/themes/$theme/$file";

		$pages[$name]  = ['name' => $name, 'filename' => $filename, 'file' => $file, 'url' => $url, 'title' => ucfirst($title), 'folder' => $folder, 'description' => $description];
	}

	//$pagesSortOrder = array_flip(array_keys($this->friendlyNames));

	$pages = array_filter(array_merge($pagesSortOrder, $pages));

	return $pages;
}

function sanitizeFileName($file) {
	//sanitize, remove double dot .. and remove get parameters if any
	$file = preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $file)));

	return $file;
}

function d(...$variables) {
	foreach ($variables as $variable) {
		echo highlight_string("<?php\n" . var_export($variable, true), true);
	}
}

function dd(...$variables) {
	foreach ($variables as $variable) {
		echo highlight_string("<?php\n" . var_export($variable, true), true);
	}

	die();
}

function encrypt($key, $value, $cipher = 'aes-256-gcm', $digest = 'sha256') {
	$key       = openssl_digest($key, $digest, true);
	$iv_length = openssl_cipher_iv_length($cipher);
	$iv        = openssl_random_pseudo_bytes($iv_length);

	return base64_encode($iv . openssl_encrypt($value, $cipher, $key, OPENSSL_RAW_DATA, $iv));
}

function decrypt($key, $value, $cipher = 'aes-256-gcm', $digest = 'sha256') {
	$result    = false;

	$key       = openssl_digest($key, $digest, true);
	$iv_length = openssl_cipher_iv_length($cipher);
	$value     = base64_decode($value);
	$iv        = substr($value, 0, $iv_length);
	$value     = substr($value, $iv_length);

	if (strlen($iv) == $iv_length) {
		$result = openssl_decrypt($value, $cipher, $key, OPENSSL_RAW_DATA, $iv);
	}

	return $result;
}
