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

use Vvveb\System\Functions\Str as Str;

define('STORE_DEBUG', false); // uncomment to always enable debugging
define('STORE_EDIT', true);

if (defined('STORE_EDIT')) {
	define('STORE_HTML_MINIFY', false);
	define('STORE_JS_MINIFY', false);
	//small internal benefits
	define('STORE_PHP_MINIFY', false);
	define('STORE_CLEAN_COMP_OPT', false);
} else {
	define('STORE_HTML_MINIFY', false);
	define('STORE_JS_MINIFY', false);
	//small internal benefits
	define('STORE_PHP_MINIFY', false);
	define('STORE_CLEAN_COMP_OPT', true);
}

if (defined('IS_STAGING') && IS_STAGING) {
	define('STORE_SHOW_WARNINGS', true);
}
//define('STORE_SHOW_WARNINGS', true);

define('STORE_DEBUG_SHOW_XPATH', true);
define('STORE_DEBUG_JQUERY', 'http://code.jquery.com/jquery-2.1.1.min.js');
define('STORE_DONT_ALLOW_PHP', true); //don't set to false unless ABSOLUTELY NECESSARY!

function pstttEscape($node,$string) {
	return str_replace("'", "\'", $string);
}

function pstttGetTranslateText($node) {
	return str_replace("'", "\'", $node->nodeValue);
}

function pstttIfCondition($node,$string = false) {
	$operators      = ['>', '<', '<=', '>=', '=', '!='];
	$operatorsMatch = implode('',array_unique(str_split(implode('', $operators))));
	$string         = html_entity_decode($string);

	$key      = $string;
	$compare  = $string;
	$operator = false;
	$value    = false;

	if ($key = strpbrk($string, $operatorsMatch)) {
		$operator = trim(Str::match("/^([ $operatorsMatch]+)/", $key));
		$value    = Str::match("/[ $operatorsMatch]+(.+)$/", $key);
		$compare  = Str::match("/(.+?)[ $operatorsMatch]/", $string);
	}

	if (strpos($value, 'this') === 0) {
		$value = str_replace('this.', 'this->', $value);
	}

	if (strpos($compare, 'this') === 0) {
		$compare = str_replace('this.', 'this->', $compare);
	}

	if (($compare && $compare[0] != "'") && ! is_numeric($compare)) {
		$compare = '$' . $compare;
	}

	if (($value && $value[0] != "'") && ! is_numeric($value)) {
		$value = '$' . $value;
	}

	if ($operator == '=') {
		$operator = '==';
	}

	$value   = Vvveb\dotToArrayKey($value);
	$compare = Vvveb\dotToArrayKey($compare);

	$return = "(isset($compare) && ($compare $operator $value))";

	return $return;
}

class Psttt {
	private $template;

	private $htmlSourceFile;

	private $extension = 'tpl';

	private $debug = false;

	private $removeComments = true;

	private $removeWhitespace = false;

	private $checkSyntax = false;

	private $debugLog;

	private $debugHtml;

	private $warnings = [];

	private $replaceConstants;

	private $constants;

	private $_modifiers = ['outerHTML', 'text', 'before', 'after', 'append', 'prepend', 'deleteAllButFirst', 'deleteAllButFirstChild', 'delete', 'if_exists', 'hide', 'addClass', 'removeClass'];

	private $variableFilters =
	[
		'capitalize'       => 'ucfirst($$0)',
		'friendly_date'    => 'Vvveb\friendlyDate($$0)',
		'truncate'         => 'substr($$0, 0, $$1)',
		'truncate_words'   => 'truncate_words($$0,$$1)',
		'replace'          => 'str_replace($$1, $$2, $$0)',
		'uppercase'        => 'strtoupper($$0)',
		'lowercase'        => 'strtolower($$0)',
		'append'           => '$$1 . $$0',
		'prepend'          => '$$0 . $$1',
		'strip_html'       => 'strip_tags($$0)',
		'strip_newlines'   => 'str_replace("\n",\' \', $$0)',
		'mod'              => ['tag', 'if (@++$_modc_@@__PSTT_rand()__@@ % (int)$$1 === (int)$$2) {', 'if (@++$_modc_@@__PSTT_rand()__@@ % (int)$$1 === (int)$$2) {'],
		'mod_class'        => ['class', '<?php if (@++$_modc_@@__PSTT_rand()__@@ % (int)$$2 === (int)$$3) echo $$1;?>'],
		'iteration_class'  => ['class', '<?php if (@++$_iterc_@@__PSTT_rand()__@@ === (int)$$2) echo $$1;?>'],
		'number_format'    => 'number_format($$0, $$1, $$2, $$3)',
		'only_decimals'    => 'substr($$0, (($_strpos = strrpos($$0, \'.\')) !== false)?$_strpos + 1:-100, ($_strpos !== false)?10:false)',
		'without_decimals' => 'substr($$0, 0, strrpos($$0, \'.\'))',
	];

	private $attributesIndex = 0;

	private $newAttributesIndex = 0;

	private $constantsIndex = 0;

	private $attributes = [];

	private $newAttributes = [];

	private $constatns = [];

	private $_external_elements = false;

	function debugTypeToString($type) {
	}

	function __construct($selector = null, $componentId = null, $componentContent = null) {
		$this->templatePath = [];

		if (STORE_DEBUG) {
			$this->debug = true;
		}

		$this->selector         = $selector;
		$this->componentId      = $componentId;
		$this->componentContent = $componentContent;

		//	libxml_disable_entity_loader();
		$this->document                      = new DomDocument();
		$this->document->preserveWhiteSpace  = true;
		$this->document->recover             = true;
		$this->document->strictErrorChecking = false;
		$this->document->substituteEntities  = false;
		$this->document->formatOutput        = false;
		$this->document->resolveExternals    = false;
		$this->document->validateOnParse     = false;
		$this->document->xmlStandalone       = true;
	}

	function warning($warning) {
		$this->warnings[] = $warning;
	}

	function debug($type,$message) {
		if ($this->debug) {
			$this->debugLog[][$type]= $message;
		}
	}

	function addDebugHtmlLine($command, $parameters, $break = '<br/>') {
		$this->debugHtml .= "<span>&nbsp;<b>$command</b> $parameters</span>$break";
	}

	function debugLogToHtml() {
		foreach ($this->debugLog as $line) {
			$type    = key($line);
			$message = $line[$type];

			switch ($type) {
		case 'LOAD':
		$this->addDebugHtmlLine('LOAD',$message);

		break;

		case 'SAVE':
		$this->addDebugHtmlLine('SAVE',$message);

		break;

		case 'SELECTOR':
		$this->addDebugHtmlLine('SELECTOR',
					   $this->cssToXpath($message) . "<a href='#' 
					onclick=\"return store_selector_click('$message')\" 
					onmouseover=\"return store_selector_over('$message')\"
					onmouseout=\"return store_selector_out('$message')\">
					$message</a>", '');

		break;

		case 'SELECTOR_STRING':
		$this->addDebugHtmlLine('INJECT STRING',$message);

		break;

		case 'SELECTOR_PHP':
		$this->addDebugHtmlLine('INJECT PHP',htmlentities($message));

		break;

		case 'SELECTOR_VARIABLE':
		$this->addDebugHtmlLine('INJECT VARIABLE',$message);

		break;

		case 'SELECTOR_FROM':
		$this->addDebugHtmlLine('EXTERNAL HTML',$message);

		break;

		case 'CSS_XPATH_TRANSFORM':
		if (STORE_DEBUG_SHOW_XPATH) {
			$this->addDebugHtmlLine('RESULTED XPATH',
						   "<a href='#' 
					onclick=\"return store_selector_click('$message')\" 
					onmouseover=\"return store_selector_over('$message')\"
					onmouseout=\"return store_selector_out('$message')\">
					$message</a>");
		}

		break;

		case 'CSS_SELCTOR':
		$this->addDebugHtmlLine('INVALID CSS SELECTOR',htmlentities($message));

		break;

		default:
		$this->addDebugHtmlLine('',$message);

		break;
		}
		}
	}

	function addTemplatePath($path) {
		if ($path) {
			$this->templatePath[] = $path;
		}
	}

	function loadTemplateFile($templateFile) {
		if (file_exists($templateFile)) {
			$this->template .= file_get_contents($templateFile);
		}
	}

	function loadTemplateFileFromPath($templateFile, $extra = false) {
		foreach ($this->templatePath as $path) {
			$this->debug('LOAD', $path . $templateFile);

			if (! file_exists($path . $templateFile)) {
				$this->debug('LOAD', '<b>!EXISTS</b>' . $path . $templateFile);

				continue;
			}

			$this->template .= file_get_contents($path . $templateFile);
		}

		if ($extra) {
			$this->template .= $extra;
		}

		if (! $this->template) {
			$this->debug('LOAD', '<b>EMPTY</b>' . $path . $templateFile);

			return false;
		}

		if (function_exists('runkit_lint')) {
			if (! runkit_lint($this->template)) {
				die('There is a php synatx error in ' . $templateFile);
			}
		}

		//$this->processTemplateFile();
		//echo '<pre>' . htmlentities($this->template) .'</pre>';
	}

	function processTemplateFile() {
		/*
		 * imports
		 *
		 * */
		$foundImports = true;
		//expand imports
		while ($foundImports) {
			$foundImports = preg_match_all("/import\(([^\&%'`\@{}~!#\(\)&\^\+,=\[\]]*?\.$this->extension)\,?(.+)?\);?/", $this->template, $imports);

			for ($i=0; $i < count($imports[0]); $i++) {
				$content = '';

				foreach ($this->templatePath as $path) {
					$importFile = $path . $imports[1][$i];

					if (file_exists($importFile)) {
						$condition = $imports[2][$i];

						if (! empty($condition)) {
							$elements = $this->xpath->query($this->cssToXpath($condition));

							if ($elements && $elements->length) {
								//found, load template below
							} else {
								//not found, replace import with nothing
								$this->template = str_replace($imports[0][$i], '' , $this->template);

								continue;
							}
						}

						$this->debug('LOAD', $path . $imports[1][$i]);
						$content .= file_get_contents($importFile) . "\n";
					} else {
						$this->debug('STORE_IMPORT_FILE_NOT_EXIST', $importFile);
						//error_log($imports[0][$i] . " $importFile does not exists");
					}
				}

				$this->template = str_replace($imports[0][$i], $content, $this->template);
			}
		}

		/**
		 * placeholders.
		 *
		 */
		//$this->template = preg_replace('@\/\/[^\n\r]+?(?:\*\)|[\n\r])@','', $this->template);
		preg_match_all('/(?<!["\'])\/\*.*?\*\/|\s*(?<!["\'])\/\/[^\n]*/s', $this->template, $comments);
		preg_match_all('/(?<!["\'])<\?php(.*?)\?>/s', $this->template, $phpCode);
		//preg_match_all("/([\"'])[^\\\\]*?(\\\\.[^\\\\]*?)*?\\1/s", $str, $matches);

		/*
		  $comments[0] = array_values( array_unique($comments[0]) );

		  $placeholdersComments = [];
		  for ($i=0;$i<count($comments[0]);$i++)
		  {
		  $patternsComments[] = "/".preg_quote($comments[0][$i], '/')."/";
		  $placeholdersComments[]="\nreplacecomments-$i\n";
		  // double backslashes must be escaped if we want to use them in the replacement argument
		  $comments[0][$i] =  str_replace('\\\\', '\\\\\\\\', $comments[0][$i]);
		  }
		*/

		//single quote
		preg_match_all("@psttt_xpath'.*?'@", $this->template, $xpaths);

		$xpaths = array_values(array_unique($xpaths[0]));

		for ($i=0; $i < count($xpaths); $i++) {
			$patternsXpaths[] = '/' . preg_quote($xpaths[$i], '/') . '/';
			//  $patterns[]	= preg_quote($matches[0][$i], '/');
			$placeholdersXpaths[]="replace_xpath-$i";
			// double backslashes must be escaped if we want to use them in the replacement argument
			$xpaths[$i] = str_replace('\\\\', '\\\\\\\\', $xpaths[$i]);
		}

		if ($xpaths) {
			$this->template = preg_replace($patternsXpaths, $placeholdersXpaths, $this->template);
		}

		$phpCode[0] = array_values($phpCode[0]);

		for ($i=0; $i < count($phpCode[1]); $i++) {
			$patternsPhp[] = '/' . preg_quote($phpCode[0][$i], '/') . '/';
			//  $patterns[]	= preg_quote($matches[0][$i], '/');
			$placeholdersPhp[]="replace_php_code-$i";
			// double backslashes must be escaped if we want to use them in the replacement argument
			$phpCode[0][$i] = str_replace('\\\\', '\\\\\\\\', $phpCode[1][$i]);
		}

		if (isset($placeholdersPhp)) {
			$this->template = preg_replace($patternsPhp, $placeholdersPhp, $this->template);
		}

		/* 
		 *Variables
		 */

		//preg_match_all('/(?<!["\'\[])(\\$[a-zA-Z0-9->\[\]\'"_\(\)\$\:]*)/s', $this->template, $variables);
		preg_match_all('/(?<!["\'\[])(\\$.+)/', $this->template, $variables);

		$variables[0] = array_values($variables[0]);

		for ($i=0; $i < count($variables[1]); $i++) {
			$patternsVariables[] = '/' . preg_quote($variables[0][$i], '/') . '/';
			//  $patterns[]	= preg_quote($matches[0][$i], '/');
			$placeholdersVariables[]="replace_variable-$i";
			// double backslashes must be escaped if we want to use them in the replacement argument
			$variables[0][$i] = str_replace('\\\\', '\\\\\\\\', $variables[1][$i]);
		}

		if ($variables[0]) {
			$this->template = preg_replace($patternsVariables, $placeholdersVariables, $this->template);
		}

		/*
		 *Froms - from(index.html|#element)
		 */
		preg_match_all('/from\(([^\|]+)\|(.+)\)/', $this->template, $froms);

		$froms[0] = array_values($froms[0]);

		for ($i=0; $i < count($froms[1]); $i++) {
			$patternsFroms[] = '/' . preg_quote($froms[0][$i], '/') . '/';
			//  $patterns[]	= preg_quote($matches[0][$i], '/');
			$placeholdersFroms[]="replace_from-$i\n";
			// double backslashes must be escaped if we want to use them in the replacement argument
			$froms[0][$i] = str_replace('\\\\', '\\\\\\\\', $froms[1][$i]);
		}

		if ($froms[0]) {
			$this->template = preg_replace($patternsFroms, $placeholdersFroms, $this->template);
		}

		/* strings */
//		preg_match_all('/(?<!\[)["\'][^"\'\\\r\n]*(?:\\.[^"\'\\\r\n]*)*["\'](?<=\])/s', $this->template, $strings);
		//doube quote
		preg_match_all('/"[^"\\\r\n]*(?:\\.[^"\\\r\n]*)*"(?!\])/s', $this->template, $strings);
		//single quote
		preg_match_all("/'[^'\\\r\n]*(?:\\.[^'\\\r\n]*)*'(?!\])/s", $this->template, $stringsSingle);

		$strings       = array_values(array_unique($strings[0]));
		$stringsSingle = array_values(array_unique($stringsSingle[0]));
		$strings       = array_merge((array)$strings, (array)$stringsSingle);

		for ($i=0; $i < count($strings); $i++) {
			$patternsStrings[] = '/' . preg_quote($strings[$i], '/') . '/';
			//  $patterns[]	= preg_quote($matches[0][$i], '/');
			$placeholdersStrings[]="replace_string-$i";
			// double backslashes must be escaped if we want to use them in the replacement argument
			$strings[$i] = str_replace('\\\\', '\\\\\\\\', $strings[$i]);
		}

		if ($strings) {
			$this->template = preg_replace($patternsStrings, $placeholdersStrings, $this->template);
		}

		//remove comments
		$this->template = preg_replace("/(?<![\"'])\/\*.*?\*\/|\s*(?<![\"'])\/\/[^\n]*/s", '', $this->template);
		$this->template = preg_replace('/\n+/',"\n", $this->template);
		$this->template = preg_replace('/(?<=\=)\s*\n/','', $this->template);

		//add xpaths back
		$this->template = preg_replace_callback('@replace_xpath-(\d+)@',
					   function ($matches) use ($xpaths) {
					   	return $xpaths[$matches[1]];
					   }, $this->template);

		$this->strings   = $strings;
		$this->phpCode   = $phpCode[0];
		$this->variables = $variables[0];
		$this->froms     = $froms;

		$this->template = str_replace("\n\n","\n",trim($this->template));
		$lines          = explode("\n", $this->template);

		foreach ($lines as $line) {
			$matches = [];
			//check if "=" exists for pair
			if (preg_match('/(.*?)(=)\s*(replace_.*|true|false);?/s', $line, $matches) && $matches[1]) {
				$this->selectors[] = [trim($matches[1]), trim($matches[3])];
			} else {
				//single command, no pair
				$this->selectors[] = [trim($line)];
			}
		}
	}

	/**
	 * Convert a CSS-selector into an xPath-query.
	 *
	 * @return    string
	 * @param    string $selector    The CSS-selector
	 */
	function cssToXpath($selector) {
		//if already xpath don't transform
		if (substr_compare($selector,'psttt_xpath', 0, 11) == 0) {
			return substr($selector, 12, -1);
		}
		$selector = (string) $selector;

		//convert , to | union operator to allow multiple queries
		$selector = str_replace(',', '|', $selector);

		$cssSelector = [
			// E > F: Matches any F element that is a child of an element E
			'/\s*>\s*/',
			// E + F: Matches any F element immediately preceded by an element
			'/\s*\+\s*/',
			// E F: Matches any F element that is a descendant of an E element
			'/([a-zA-Z\*="\[\]#._-])\s+([a-zA-Z\*="\[\]#._-])/', //'/([a-zA-Z\*="\[\]#._-])\s+([a-zA-Z\*#._-])/',
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

			// [foo][baz]: Matches any element with the "foo" attribute set (whatever the value)
			'/(?<=\])\[([\w_\-]+)\]/',
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
			'[ @\1 ]', //[attribute][attribute]
			'*[ @\1 ]', //[attribute]
			'\1 [ @*[starts-with(name(), "\2")] ]', //element[attr*]
			'*[ @*[starts-with(name(), "\1")] ]', //[attr*]
			'\1[ contains( concat( " ", @class, " " ), concat( " ", "\2") ) ]', //element[class*="string"]
			'\1[ contains( concat( " ", @class, " " ), concat( " ", "\2", " " ) ) ]', //element[class~="string"]
			'*[ contains( concat( " ", @class, " " ), concat( " ", "\1") ) ]', //[class*="string"]
			'*[ contains( concat( " ", @class, " " ), concat( " ", "\1", " " ) ) ]', //element[class~="string"]
			'\1[ @id = "\2" ]', //element#id
			'*[ @id = "\1" ]', //#id
		];

		$result = (string) '//' . preg_replace($cssSelector, $xpathQuery, $selector);
		$this->debug('CSS_XPATH_TRANSFORM', $result);

		return $result;
	}

	//function
	function _process_template() {
		if (isset($this->selectors) && isset($this->document) && isset($this->xpath)) {
			//check for multiple selectors
			$newSelectors = [];

			foreach ($this->selectors as &$data) {
				//$data[0] = selector
				if (strpos($data[0], ',') !== false) {
					$selectors = explode(',', $data[0]);
					//set first selector for current selector
					$data[0] = $selectors[0];
					unset($selectors[0]);
					//add new selectors
					foreach ($selectors as $selector) {
						$newSelectors[] = [trim($selector), $data[1]];
					}
				}
			}

			$this->selectors = array_merge($this->selectors, $newSelectors);

			foreach ($this->selectors as &$data) {
				$selector                 = $data[0];
				$selectorComponents       = explode('|', $selector);
				$selector                 = $selectorComponents[0];
				$modifier                 = (isset($selectorComponents[1])) ? trim($selectorComponents[1]) : '';
				$value                    = (isset($data[1])) ? $data[1] : '';
				$this->_external_elements = false;

				//enable disable debugging
				if (! $selector) {
					continue;
				}
				$isConstant = false;

				if (strpos($selector, '@@_CONSTANT_') !== false) {
					$isConstant = true;
				}

				$prefix   = &$this->prefix;
				$isPrefix = false;

				//get all set prefix and save values
				//@my-prefix = #selector .class[attribute]
				$selector = preg_replace_callback('/^@([a-zA-Z][a-zA-Z0-9_-]+)(?![a-zA-Z0-9_-])\s+=\s+(.+)$/',
					function ($matches) use (&$prefix, &$isPrefix) {
						if (isset($matches[1]) && isset($matches[2])) {
							$name = $matches[1];
							$value = $matches[2];

							$prefix[$name] = $value;
							$isPrefix = true;
						}

						return trim($matches[0]);
					}, $selector);

				//if the line is only for set prefix then skip further processing
				if ($isPrefix) {
					continue;
				}
				//replace all prefix with actual values
				$selector = preg_replace_callback('/@([a-zA-Z][a-zA-Z0-9_-]+)(?![a-zA-Z0-9_-])/',
					function ($matches) use (&$prefix) {
						if (isset($matches[1])) {
							$name = $matches[1];

							if (isset($prefix[$name])) {
								return $prefix[$name];
							}
						}

						return trim($matches[0]);
					}, $selector);

				$this->debug('SELECTOR', $selector);

				if ($selector == 'debug') {
					$this->debug = ($value == 'true') ? true : false;
				} else {
					$valueElements = explode('-', $value);

					switch ($valueElements[0]) {
						case 'replace_string':
							$val = trim($this->strings[(int) $valueElements[1]],'"\'');
							$this->debug('SELECTOR_STRING', $this->strings[(int) $valueElements[1]]);

						break;

						case 'replace_php_code':
							$phpCode = $this->phpCode[(int) $valueElements[1]];

							if (STORE_PHP_MINIFY === true) {
								$phpCode = $this->minifyPhp($phpCode);
							}

							if ($modifier && ! in_array($modifier, $this->_modifiers)) {
								$val = '<_script language="php"><![CDATA[' . $this->minifyPhp($phpCode) . ']]></_script>';
							} else {
								if ($modifier == 'if_exists' || $modifier == 'hide') {
									$val = "($phpCode)";
								} else {
									if ($isConstant || $modifier == 'addClass') {
										$val = '<_script language="php"><![CDATA[' . $this->minifyPhp($phpCode) . ']]></_script>';
									} else {
										$val = '<_script language="php"><![CDATA[' . $this->minifyPhp($phpCode) . ']]></_script>';
									}
								}
							}
							$this->debug('SELECTOR_PHP', $this->phpCode[(int) $valueElements[1]]);

						break;

						case 'replace_variable':
							if ($modifier) {
								if ($modifier == 'if_exists' || $modifier == 'hide') {
									$val = $this->variables[(int) $valueElements[1]];
								} else {
									if (! in_array($modifier, $this->_modifiers)) {
										$val = '<_script language="php"><![CDATA[if (isset(' . $this->variables[(int) $valueElements[1]] . ')) echo htmlentities(' . $this->variables[(int) $valueElements[1]] . ');]]></_script>';
									}
								}
							} else {
								if ($isConstant) {
									$val = '<_script language="php"><![CDATA[if (isset(' . $this->variables[(int) $valueElements[1]] . ')) echo ' . $this->variables[(int) $valueElements[1]] . ';]]></_script>';
								} else {
									$val = '<_script language="php"><![CDATA[if (isset(' . $this->variables[(int) $valueElements[1]] . ')) echo htmlentities(' . $this->variables[(int) $valueElements[1]] . ');]]></_script>';
								}
							}
							$this->debug('SELECTOR_VARIABLE', $this->variables[(int) $valueElements[1]]);

						break;

						case 'replace_from':
							$from = $this->froms[0][(int) $valueElements[1]]; //external html file
							/*
							  $fromSelector = substr($this->froms[2][(int) $valueElements[1]],1);
							  //load specified selector if available otherwise load html with the same selector
							  if (empty($fromSelector))
							  {
							  //override default selector with the provided one
							  $fromSelector = $selector;
											
							  }*/
							//get html
							//if ($from != '@_SELF_@')
							$this->_external_elements = true;
							$val                      = $valueElements;
							//$val = $this->loadFromExternalHtml($from, $fromSelector);
						break;
					}

					if ($isConstant) {
						$this->constants[$selector] = $val;

						continue;
					}

					//echo $selector . ' --- ' . $this->cssToXpath($selector) . "<br/>\n";
					$elements = $this->xpath->query($this->cssToXpath($selector));

					if ($elements && $elements->length == 0) {
						//var_dump($selector . ' -- XPATH = ' . $this->cssToXpath($selector));
						$this->debug(0, ' [0 elements]');
					}

					$this->debug('SELECTOR_VARIABLE', $selector . ' - ' . $modifier);

					switch ($modifier) {
							case 'deleteAllButFirstChild':
								$this->deleteAllButFirstChild($elements, $val);

							break;

							case 'deleteAllButFirst':
								$this->deleteAllButFirst($elements);

							break;

							case 'outerHTML':
								$this->outerHTML($elements, $val);

							break;

							case 'innerText':
								$this->innerText($elements, $val);

							break;

							case 'before':
							$this->insertBefore($elements, $val);

							break;

							case 'after':
							$this->insertAfter($elements, $val);

							break;

							case 'append':
							$this->append($elements, $val);

							break;

							case 'prepend':
							$this->prepend($elements, $val);

							break;

							case 'delete':
							$this->delete($elements);

							break;

							case 'if_exists':
							$this->ifExists($elements, $val);

							break;

							case 'hide':
							$this->hide($elements, $val);

							break;

							case 'addClass':
							$this->addClass($elements, $val);

							break;

							case 'removeClass':
							$this->removeClass($elements, $val);

							break;

							case 'addNewAttribute':
							$this->addNewAttribute($elements, $val);

							break;

							case '':
							$this->innerHTML($elements, $val);

							break;

							default:
							$this->setAttribute($elements, $modifier, $val);

							break;
					}
				}
			}
		}
	}

	function processAttributeConstants($value, $node) {
		if (! $node) {
			return $value;
		}
		$value = preg_replace_callback('/@@__innerText__@@/',
					   function ($matches) use ($node) {
					   	$value = $this->innerHtml([$node]);

					   	if (isset($value[0]) && $value[0] == '{') {
					   		$value = json_decode($value, 1);
					   		$value = var_export($value, 1);
					   	}

					   	$this->debug('STORE_ATTRIBUTE', '<b>VALUE </b>' . $value);

					   	return trim($value);
					   }, $value);

		$value = preg_replace_callback('/@@__innerHtml__@@/',
					   function ($matches) use ($node) {
					   	$value = $this->innerHtml([$node]);

					   	if (isset($value[0]) && $value[0] == '{') {
					   		$value = json_decode($value, 1);
					   		$value = var_export($value, 1);
					   	}

					   	$this->debug('STORE_ATTRIBUTE', '<b>VALUE </b>' . $value);

					   	return $value;
					   }, $value);

		//attribute value
		$value = preg_replace_callback('/@@__([\.a-zA-Z*_-]+)__@@/',
					   function ($matches) use ($node) {
					   	$attributeName = $matches[1];

					   	if (strpos($attributeName, '*') !== false) {
					   		//wildcard attribute
					   		$attributeName = str_replace('*', '', $attributeName);

					   		foreach ($node->attributes as $attribute) {
					   			if (strpos($attribute->name, $attributeName) !== false) {
					   				$value = $attribute->value;
					   			}
					   		}
					   	} else {
					   		$value = $node->getAttribute($matches[1]);
					   	}

					   	if (isset($value[0]) && $value[0] == '{') {
					   		$value = json_decode($value, 1);
					   		$value = var_export($value, 1);
					   	}

					   	$this->debug('STORE_ATTRIBUTE', '<b>VALUE </b>' . $value);

					   	return $value;

					   	return \Vvveb\System\filter('@[#\@&=?\0-9a-zA-Z_: ;-]+@',$value, 500);
					   }, $value);

		//regex of attribute value @@__class:image-size-([a-zA-Z_]+)__@@
		/*
		$value = preg_replace_callback('/@@__([a-zA-Z_-]+):([a-zA-Z-_\]\[\\\+\(\)\,\+\^:*]+)__@@/',
						   function ($matches) use ($node)
						   {
						   $attrib = $matches[1];
						   $regex = $matches[2];
						   $this->debug('STORE_ATTRIBUTE', '<b>ATTRIB NAME</b> ' . $attrib);
						   $this->debug('STORE_ATTRIBUTE', '<b>REGEX </b> ' . $regex);
						   $value = $node->getAttribute($attrib);
						   $this->debug('STORE_ATTRIBUTE', '<b>ATTRIB VALUE </b> ' . $value);
						   if (preg_match('@' . $regex .  '@', $value, $_match))
						   {
							   $value = \Vvveb\System\filter('@[0-9a-zA-Z_\-\.\#]+@', $_match[1], 500);
							   $this->debug('STORE_ATTRIBUTE', '<b>MATCH </b>' . $_match[1]);
						   } else
						   {
							   $this->debug('STORE_ATTRIBUTE', '<b>NO MATCH </b>');
						   }
						   return $value;
						   }, $value);
		*/

		//run regex on attribute name @@__data-v-product-*:data-v-product-([a-zA-Z_]+)__@@
		//$value = preg_replace_callback('/@@__\[([\*a-zA-Z_-]+)\]:([a-zA-Z-_\]\[\\\+\(\)\,\+\^:*]+)__@@/',
		$value = preg_replace_callback('/@@__([*a-zA-Z_-]+):([a-zA-Z-_\]\[\\\+\(\)\,\.\+\^:*]+)__@@/',
					   function ($matches) use ($node) {
					   	$attrib = $matches[1];
					   	$regex = $matches[2];
					   	$this->debug('STORE_ATTRIBUTE', '<b>ATTRIB NAME</b> ' . $attrib);
					   	$this->debug('STORE_ATTRIBUTE', '<b>REGEX </b> ' . $regex);
					   	$value = $node->getAttribute($attrib);
					   	$this->debug('STORE_ATTRIBUTE', '<b>ATTRIB VALUE </b> ' . $value);

					   	foreach ($node->attributes as $name => $attrNode) {
					   		if (preg_match('@' . $regex . '@', $value, $_match)) {
					   			//$value = \Vvveb\System\filter('@[0-9a-zA-Z_\-\.\#\/]+@', $_match[1], 500);
					   			$value = $_match[1];
					   			$this->debug('STORE_ATTRIBUTE', '<b>MATCH </b>' . $_match[1]);
					   		} else {
					   			$this->debug('STORE_ATTRIBUTE', '<b>NO MATCH </b> ' . $regex . ' - ' . $attrib . ' - ' . $attrNode->name);
					   		}
					   	}

					   	return $value;
					   }, $value);

		//attribute name ex @@__data-v-plugin-(.+)__@@
		$value = preg_replace_callback('/@@__(.+?)__@@/',
				   function ($matches) use ($node) {
				   	$value = $node->getAttribute($matches[1]);
				   	$this->debug('STORE_ATTRIBUTE', '<b>ATTRIB NAME</b> ' . $matches[1]);

				   	foreach ($node->attributes as $name => $attrNode) {
				   		if (preg_match('@' . $matches[1] . '@', $name, $_match)) {
				   			//$value = \Vvveb\System\filter('@[0-9a-zA-Z_\-\.\#\/]+@', $_match[1], 500);
				   			$value = $_match[1] ?? null;
				   			$this->debug('STORE_ATTRIBUTE', '<b>MATCH </b>' . $value);
				   		} else {
				   			$this->debug('STORE_ATTRIBUTE', '<b>NO MATCH </b>');
				   		}
				   	}

				   	return $value;

				   	return \Vvveb\System\filter('@[#\@&=?\0-9a-zA-Z_: ;-]+@',$value, 500);
				   }, $value);

		//filters
		$class = $node->getAttribute('class');
		//search for filters and their options
		//@filter_([^ :$]+):?(\'[^\']+\'|[^ $]+)?@ old
		$filters = [];
		$length  = $node->attributes->length;

		for ($i = 0; $i < $length; ++$i) {
			if ($item = $node->attributes->item($i)) {
				$name = $item->name;

				if (strpos($name, 'data-filter') !== false) {
					$name           = str_replace('data-filter-', '', $name);
					$filters[$name] = $item->value;
					$node->removeAttribute($item->name);
				}
			}
		}
		//if ($class && preg_match_all('@filter_([^ :$]+)(:\'[^\']+\'|:[^ $]+)*@', $class, $matches, PREG_SET_ORDER) > 0)
		if ($filters) {
			$chain = '_$variable';

			foreach ($filters as $name => $options) {
				if ($options) {
					//string is json
					if ($options[0] = '{') {
						$options = json_decode($options, false);
					} else {
						$options[] = $options;
					}
				} else {
					$options = [];
				}

				//clean up, remove filter from class
				//$node->setAttribute('class', str_replace($filter[0], ' ', $node->getAttribute('class')));
				if (isset($this->variableFilters[$name])) {
					$type     = '';
					$commands = $this->variableFilters[$name];

					if (is_array($commands)) {
						$type = $commands[0];
						unset($commands[0]);
					} else {
						$commands = [1 => $commands];
					}

					foreach ($commands as &$command) {
						$commandVariableCount = preg_match_all('@\$\$[1-9]+@' , $command);

						//if different parameter number then don't add filter to filter chain
						if ($commandVariableCount != count($options)) {
							$this->warning('Invalid number of options for filter <b>' . $name . '</b> for "' . $class . '"');

							continue 2;
						}

						//run php functions if any
						$command = preg_replace_callback('/@@__PSTT_([^_]+)__@@/',
									function ($matches) {
										return eval('return ' . $matches[1] . ';');
									}, $command);
					}

					if ($type == 'class') {
						//replace variables with their values
						$command = preg_replace_callback('@\$\$(\d+)@',
								   function ($matches) use ($options) {
								   	if ($matches[1] > 0) {
								   		$options[$matches[1]] = '\'' . trim($options[$matches[1]], '\'') . '\'';
								   	}

								   	return $options[$matches[1]];
								   }, $command);

						$this->_addClass($node, trim($commands[1], '\''), false);
					} else {
						if ($type == 'tag') {
							//replace variables with their values
							$command = preg_replace_callback('@\$\$(\d+)@',
								   function ($matches) use ($options) {
								   	if ($matches[1] > 0) {
								   		$options[$matches[1]] = '\'' . trim($options[$matches[1]], '\'') . '\'';
								   	}

								   	return $options[$matches[1]];
								   }, $command);

							$openTag = preg_replace_callback('@\$\$(\d+)@',
								   function ($matches) use ($options) {
								   	if ($matches[1] > 0) {
								   		$options[$matches[1]] = '\'' . trim($options[$matches[1]], '\'') . '\'';
								   	}

								   	return $options[$matches[1]];
								   }, $commands[1]);

							$closeTag = preg_replace_callback('@\$\$(\d+)@',
								   function ($matches) use ($options) {
								   	if ($matches[1] > 0) {
								   		$options[$matches[1]] = '\'' . trim($options[$matches[1]], '\'') . '\'';
								   	}

								   	return $options[$matches[1]];
								   }, $commands[2]);

							$nodeList = [$node]; //only one node and the methods accepts multiple nodes
							$this->tagWrap($nodeList, $openTag, $closeTag);
						} else {
							if (is_array($options)) {
								array_unshift($options, $chain);
							} else {
								$options[] = $chain;
							}

							$chain = preg_replace_callback('@\$\$(\d+)@',
								   function ($matches) use ($options) {
								   	if ($matches[1] > 0) {
								   		$options[$matches[1]] = '\'' . trim($options[$matches[1]], '\'') . '\'';
								   	}

								   	return $options[$matches[1]];
								   }, $commands[1]);
						}
					}
				} else {
					$this->warning('Unknown filter <b>' . $name . '</b> for "' . $class . '"');
				}
			}

			preg_match('@echo htmlentities\(([^)]+)\)@', $value, $variable);

			if ($variable) {
				$chain = str_replace('_$variable', $variable[1], $chain);
				$value = str_replace($variable[0], 'echo htmlentities(' . $chain . ')', $value);
			}
		}

		$json = [];

		if ($node->hasAttributes()) {
			foreach ($node->attributes as $attr) {
				$name = str_replace('data-v-', '', $attr->nodeName);
				$val  = $attr->nodeValue;

				if ($val && $val[0] == '{') {
					$json[$name] = json_decode($val, true);
				}
			}
		}
		$value = preg_replace_callback('/@@([\.a-zA-Z_-]+)@@/m',
					   function ($matches) use ($node, $json) {
					   	return $attrib = var_export(\Vvveb\System\arrayPath($json, $matches[1]), true);
					   	$this->debug('STORE_ATTRIBUTE', '<b>JSON NAME</b> ' . $attrib);
					   	$this->debug('STORE_ATTRIBUTE', '<b>REGEX </b> ' . $regex);
					   	$value = $node->getAttribute($attrib);
					   	$this->debug('STORE_ATTRIBUTE', '<b>ATTRIB VALUE </b> ' . $value);

					   	return $value;
					   }, $value);

		//macros, compile time function calls
		$value = preg_replace_callback('/@@macro ([a-z_A-Z]+)\(([^\)]+?)\)@@/',
					   function ($matches) use (&$node) {
					   	$function = 'psttt' . $matches[1];
					   	//$parameters = preg_split('@\'?\s*,\s*\'?@', $matches[2]);

					   	if (function_exists($function)) {
					   		preg_match_all('@"(.+?)",?@i', $matches[2], $parameters, PREG_SET_ORDER);
					   		//add node as first parameter to allow macros to alter node if needed
					   		$params[] = &$node;

					   		foreach ($parameters as $param) {
					   			$params[] = trim($param[1]);
					   		}
					   		/*
					   											 $params_exp = var_export($params, true);
					   									  */
					   		return call_user_func_array($function, $params);
					   	}

					   	return $matches[0];
					   }, $value);

		return $value;
	}

	function removeChildren(&$node) {
		while ($node->firstChild) {
			while ($node->firstChild->firstChild) {
				$this->removeChildren($node->firstChild);
			}
			$node->removeChild($node->firstChild);
		}
	}

	function innerHTML($nodeList, $html = false) {
		if ($nodeList) {
			foreach ($nodeList as $node) {
				if ($html === false) {
					$doc = new DOMDocument();

					foreach ($node->childNodes as $child) {
						$doc->appendChild($doc->importNode($child, true));
					}

					return $doc->saveHTML();
				} else {
					if ($html == '') {
						continue;
					}
					//if ($node->nodeName !== 'title') $html .= '<_script language="php"><![CDATA[/*__PSTT_MAP:' . $node->getLineNo() . '*/]]></_script>';

					if ($this->_external_elements) {
						$result = $this->loadFromExternalHtml($html, $node);
						$this->removeChildren($node);

						foreach ($result as $externalNode) {
							$importedNode = $this->document->importNode($externalNode, true);
							$node->appendChild($importedNode);
						}
					} else {
						switch ($node->nodeName) {
							case 'input':
				/*		    case 'option':*/
								$this->setAttribute($node, 'value', $html);

							break;

								case 'form':
								$this->setAttribute($node, 'action', $html);

							break;

							default:
								$this->removeChildren($node);
								$f = $this->document->createDocumentFragment();
								$f->appendXML($this->processAttributeConstants($html, $node));
								$node->appendChild($f);
							}
					}
				}
			}
		}
	}

	function outerHTML(&$nodeList, $html = false) {
		foreach ($nodeList as $node) {
			if ($html === false) {
				$doc = new DOMDocument();

				foreach ($node->childNodes as $child) {
					$node->parentNode->replaceChild($doc->importNode($child, true), $node);
				}

				return $doc->saveHTML();
			} else {
//				$this->removeChildren($node);
				if ($html == '') {
					continue;
				}

				if ($this->_external_elements) {
					$result = $this->loadFromExternalHtml($html, $node);

					if ($result) {
						foreach ($result as $externalNode) {
							$importedNode = $this->document->importNode($externalNode, true);
							$node->parentNode->replaceChild($importedNode, $node);
						}
					}
				} else {
					$f = $this->document->createDocumentFragment();
					$f->appendXML($this->processAttributeConstants($html, $node));
					$node->parentNode->replaceChild($f, $node);
				}
			}
		}
	}

	function innerText($nodeList, $text = false) {
		foreach ($nodeList as $node) {
			if ($text === false) {
				return $node->nodeValue;
			} else {
				if ($node->hasChildNodes()) {
					foreach ($node->childNodes as $childNode) {
						$value = trim($childNode->nodeValue);
						//find first non empty text node
						//error_log(XML_TEXT_NODE . ' - ' . $childNode->nodeType . ' - ' . !empty($value) );
						if ($childNode->nodeType == XML_TEXT_NODE && ! empty($value)) {
							$f = $this->document->createDocumentFragment();
							//error_log("innerText = $text");
							$f->appendXML($this->processAttributeConstants($text, $node));

							$node->replaceChild($f, $childNode);

							break;
						}
					}
				} else {
					//if node has no children append text
					$f = $this->document->createDocumentFragment();
					$f->appendXML($this->processAttributeConstants($text, $node));
					$node->appendChild($f);
				}
			}
		}
	}

	function ifExists(&$nodeList, $variable = false) {
		if ($variable == '') {
			return false;
		}

		foreach ($nodeList as $node) {
			//before
			$html = "<_script language=\"php\"><![CDATA[if (isset($variable) && $variable) {]]></_script>";
			$f    = $this->document->createDocumentFragment();
			$f->appendXML($html);
			$node->parentNode->insertBefore($f, $node);

			//after
			$html = '<_script language="php">}</_script>';
			$f    = $this->document->createDocumentFragment();
			$f->appendXML($html);
			//$node->parentNode->appendChild( $f );
			$node->parentNode->insertBefore($f, $node->nextSibling);
		}
	}

	function hide(&$nodeList, $variable = false) {
		if ($variable) {
			$variable = '!' . $variable;
		}

		return $this->ifExists($nodeList, $variable);
		/*	
			if($variable == '') return false;
			foreach ($nodeList as $node)
			{
				//before
				$html = "<_script language=\"php\">if (!$variable) {</_script>";
				$f = $this->document->createDocumentFragment();
				$f->appendXML($html);
				$node->parentNode->insertBefore( $f, $node);

				//after
				$html = "<_script language=\"php\">}</_script>";
				$f = $this->document->createDocumentFragment();
				$f->appendXML($html);
				//$node->parentNode->appendChild( $f );
				$node->parentNode->insertBefore( $f, $node->nextSibling);
			}
			*/
	}

	function tagWrap(&$nodeList, $open = false, $close = false) {
		if ($open == '' || $close == '') {
			return false;
		}
		$openStart = "<_script language=\"php\"><![CDATA[$open]]></_script>";
		$openEnd   = '<_script language="php">}</_script>';

		$closeStart = "<_script language=\"php\"><![CDATA[$close]]></_script>";
		$closeEnd   = '<_script language="php">}</_script>';

		foreach ($nodeList as $node) {
			//before start
			$f = $this->document->createDocumentFragment();
			$f->appendXML($this->processAttributeConstants($openStart, $node));
			$node->parentNode->insertBefore($f, $node);

			//before end
			$f = $this->document->createDocumentFragment();
			$f->appendXML($this->processAttributeConstants($openEnd, $node));

			if ($node->hasChildNodes()) {
				$node->insertBefore($f,$node->firstChild);
			} else {
				$node->appendChild($f);
			}

			//after start
			$f = $this->document->createDocumentFragment();
			$f->appendXML($this->processAttributeConstants($closeStart, $node));
			$node->appendChild($f);

			//after end
			$f = $this->document->createDocumentFragment();
			$f->appendXML($this->processAttributeConstants($closeEnd, $node));
			$node->parentNode->insertBefore($f, $node->nextSibling);
		}
	}

	function insertBefore(&$nodeList, $html = false) {
		if ($html == '') {
			return false;
		}

		if ($nodeList) {
			foreach ($nodeList as $node) {
				$f = $this->document->createDocumentFragment();
				$f->appendXML($this->processAttributeConstants($html, $node));
				$node->parentNode->insertBefore($f, $node);
			}
		}
	}

	function insertAfter(&$nodeList, $html = false) {
		if ($html == '') {
			return false;
		}

		if ($nodeList) {
			foreach ($nodeList as $node) {
				$f = $this->document->createDocumentFragment();
				$f->appendXML($this->processAttributeConstants($html, $node));
				//$node->parentNode->appendChild( $f );
				$node->parentNode->insertBefore($f, $node->nextSibling);
			}
		}
	}

	function append(&$nodeList, $html = false) {
		if ($html == '') {
			return false;
		}

		if ($nodeList) {
			foreach ($nodeList as $node) {
				if ($this->_external_elements) {
					if ($this->froms[0][(int) $html[1]] == '@_SELF_@') {
						$selector = $this->froms[2][(int) $html[1]];
						$xpath    = new DOMXpath($this->document);
						$result   = $xpath->query($this->cssToXpath($selector));
					} else {
						$result = $this->loadFromExternalHtml($html, $node);
					}

					if (! $result) {
						continue;
					}
					//$html = array_reverse($html);
					foreach ($result as $externalNode) {
						$importedNode = $this->document->importNode($externalNode, true);
						$node->appendChild($importedNode);
					}
				} else {
					$f = $this->document->createDocumentFragment();
					$f->appendXML($html);
					$node->appendChild($f);
				}
			}
		}
	}

	function prepend(&$nodeList, $html = false) {
		if ($html == '') {
			return false;
		}

		if ($nodeList) {
			foreach ($nodeList as $node) {
				if ($this->_external_elements) {
					$result = $this->loadFromExternalHtml($html, $node);

					if (! $result) {
						continue;
					}

					if (is_array($result)) {
						$result = array_reverse($result);
					}

					foreach ($result as $externalNode) {
						$importedNode = $this->document->importNode($externalNode, true);

						if ($node->firstChild) {
							// $ref has an immediate brother : insert newnode before this one
							$node->insertBefore($importedNode, $node->firstChild);
						} else {
							// $ref has no brother next to him : insert newnode as last child of his parent
							$node->appendChild($importedNode);
						}
					}
				} else {
					$f = $this->document->createDocumentFragment();
					$f->appendXML($html);

					if ($node->firstChild) {
						// $ref has an immediate brother : insert newnode before this one
						$node->insertBefore($f, $node->firstChild);
					} else {
						// $ref has no brother next to him : insert newnode as last child of his parent
						$node->appendChild($f);
					}
				}
			}
		}
	}

	function deleteAllButFirst(&$nodeList, $parent = false) {
		$first = true;

		if ($nodeList) {
			foreach ($nodeList as $node) {
				if (! $first) {
					$this->removeChildren($node);

					if ($node->parentNode) {
						$node->parentNode->removeChild($node);
					}
				}
				$first = false;
			}
		}
	}

	function deleteAllButFirstChild(&$nodeList, $parent = false) {
		$parents = [];

		if ($nodeList) {
			foreach ($nodeList as $node) {
				if (in_array($node->parentNode, $parents, true)) {
					$this->removeChildren($node);
					$node->parentNode->removeChild($node);
				}

				if ($node->parentNode) {
					$parents[] = $node->parentNode;
				}
			}
		}
	}

	function delete(&$nodeList) {
		foreach ($nodeList as $node) {
			$this->removeChildren($node);

			if ($node->parentNode) {
				$node->parentNode->removeChild($node);
			}
		}
	}

	function _setNodeAttribute($node, $attribute, $val) {
		if (! $node) {
			return;
		}
		$value = $this->processAttributeConstants($val, $node);
		//if the attribute value has no php in it add it directly
		if (strpos($value, '<_script') === false) {
			$node->setAttribute($attribute, $value);
		} else {
			$this->attributes[++$this->attributesIndex] = $value;
			$node->setAttribute($attribute, "@@__STORE__ATTRIBUTE_PLACEHOLDER__{$this->attributesIndex}@@");
		}
	}

	function setAttribute(&$nodeList, $attribute, $val) {
		if (is_a($nodeList,'DOMNodeList')) {
			if ($nodeList->length > 0) {
				foreach ($nodeList as $node) {
					/*			$attr = new DOMAttr($attribute);
								$attr->value = $val;
								$node->setAttributeNodeNS($attr);*/

					$this->_setNodeAttribute($node, $attribute, $val);
				}
			}
		} else {
			$this->_setNodeAttribute($nodeList, $attribute, $val);
		}
	}

	function _addClass(&$node, $val, $processConstants = true) {
		if ($processConstants) {
			$val =  $this->processAttributeConstants($val, $node);
		}

		$this->attributes[++$this->attributesIndex] = $val;
		$node->setAttribute('class', $node->getAttribute('class') . " @@__STORE__ATTRIBUTE_PLACEHOLDER__{$this->attributesIndex}@@");
	}

	function addClass(&$nodeList, $val) {
		if (is_a($nodeList,'DOMNodeList')) {
			if ($nodeList->length > 0) {
				foreach ($nodeList as $node) {
					$this->_addClass($node, $val);
				}
			}
		}
	}

	function addNewAttribute(&$nodeList, $val) {
		if ($nodeList->length > 0) {
			foreach ($nodeList as $node) {
				$this->newAttributes[++$this->newAttributesIndex] = $this->processAttributeConstants($val, $node);
				$node->setAttribute("__STORE__NEW_ATTRIBUTE_PLACEHOLDER__{$this->newAttributesIndex}",'');
			}
		}
	}

	function removeClass(&$nodeList, $val) {
		if ($nodeList->length > 0) {
			foreach ($nodeList as $node) {
				$class = $node->setAttribute('class');
				$class = str_replace($val, '', $class);
				$node->setAttribute('class', $class);
			}
		}
	}

	function getInnerHtml(&$nodeList) {
		$innerHTML = '';
		$tmpDom    = new DOMDocument();

		foreach ($nodeList as $node) {
			$tmpDom->appendChild($tmpDom->importNode($node, true));
		}
		$innerHTML .= trim($tmpDom->saveHTML());

		return '<![CDATA[' . $innerHTML . ']]>';
	}

	function loadFromExternalHtml($val, $node) {
		$filename = $this->froms[0][(int) $val[1]]; //external html file
		$selector = $this->froms[2][(int) $val[1]];
		//load specified selector if available otherwise load html with the same selector

		$filename = $this->processAttributeConstants($filename, $node);
		$filename = $this->replacePathConstants($filename);
		$selector = $this->processAttributeConstants($selector, $node);

		if (strpos($filename,'/plugins/') === 0) {
			$filename = DIR_ROOT . $filename;
		} else {
			if ($filename[0] !== '/') {
				$filename = $this->htmlPath . $filename;
			}
		}
		$this->debug('SELECTOR_FROM', $filename);

		if (! ($html = @file_get_contents($filename))) {
			Vvveb\log_error("can't load html $filename");
			$this->debug('LOAD', '<b>EXTERNAL ERROR</b> ' . $filename . ' ' . $selector);

			return false;
		}
		$this->debug('LOAD', $filename . ' <b>SELECTOR</b> ' . $selector);

		if (STORE_DONT_ALLOW_PHP) {
			$html = $this->removePhp($html);
		}

		if (STORE_HTML_MINIFY === true) {
			$html = $this->minifyHtml($html);
		}

		$document = new DomDocument();
		@$document->loadHTML($html);

		$xpath    = new DOMXpath($document);
		$elements = $xpath->query($this->cssToXpath($selector));

		return $elements;
		//return $this->getInnerHtml($elements);
	}

	function loadHtmlTemplate($htmlFile) {
		$filename = $this->replacePathConstants($htmlFile);

		if (strpos($filename, DIRECTORY_SEPARATOR) === false) {
			$filename = $this->htmlPath . $filename;
		}

		if (! ($html = @file_get_contents($filename))) {
			Vvveb\log_error("can't load template $filename");
			$this->debug('LOAD', '<b>ERROR</b> ' . $filename);

			return false;
		}
		$this->htmlSourceFile = $filename;

		$this->debug('LOAD', $filename);

		if (STORE_DONT_ALLOW_PHP) {
			$html = $this->removePhp($html);
		}

		//preg_match_all("@<script[^>]*>.*?script>@s", $html, $this->_scripts);
		preg_match_all("/<script((?:(?!src=|data-).)*?)>(.*?)<\/script>/smix", $html, $this->_scripts);

		$this->_scripts = array_values(array_unique($this->_scripts[0]));
		$count          = count($this->_scripts);

		if ($count) {
			for ($i=0; $i < $count; $i++) {
				$patternsScripts[]    = '/' . preg_quote($this->_scripts[$i], '/') . '/';
				$placeholdersScripts[]= '<script holder="@@__STORE__SCRIPT_PLACEHOLDER__' . $i . '@@"></script>';
				$this->_scripts       = str_replace('\\\\', '\\\\\\\\', $this->_scripts);
			}

			$html = preg_replace($patternsScripts, $placeholdersScripts, $html);
		}

		if (STORE_HTML_MINIFY === true) {
			$html = $this->minifyHtml($html);
		}
		//replace constants
		if ($this->replaceConstants) {
			$html = str_replace(array_keys($this->replaceConstants),array_values($this->replaceConstants),$html);
		}

		@$this->document->loadHTML($html);
		$errors = libxml_get_errors();
		//var_dump($errors);
		/*
		foreach (libxml_get_errors() as $error) {
			var_dump($error);
		}*/

		//original document used to extract selectors
		//$this->originalDocument = clone($this->document);
		$this->xpath = new DOMXpath($this->document);

		if ($this->componentContent) {
			//replace component content from page with the one provided
			$elements = $this->xpath->query($this->cssToXpath($this->selector));

			if ($elements) {
				$node     = $elements->item($this->componentId);

				if ($node && $node->parentNode) {
					//$html = '<div>asdasdasdasd<div>';
					//$html = $this->processAttributeConstants($this->componentContent, $node);

					$tmpDom = new DomDocument();
					@$tmpDom->loadHTML($this->componentContent);
					$body         = $tmpDom->getElementsByTagName('body');
					$nodeToImport = $body->item(0)->firstChild;

					$importNode = $this->document->importNode($nodeToImport, true);
					//$this->document->appendChild($importNode);
					$node->parentNode->replaceChild($importNode, $node);
					//$node->parentNode->removeChild($node);
					///$node->appendChild($f);
				}
			}
		}

		return $errors;
	}

	function setMultiLanguageText($currentNode) {
		//foreach($currentNode->childNodes as $node) {
		if (! $currentNode->childNodes) {
			return;
		}

		for ($i = 0; $i < $currentNode->childNodes->length; $i++) {
			$node = $currentNode->childNodes[$i];

			//strip comments
			if ($this->removeComments && $node->nodeType == XML_COMMENT_NODE) {
				$node->parentNode->removeChild($node);
				$i--;
				//continue;
			}

			if ($node->nodeType == XML_TEXT_NODE &&
				(! isset($node->parentNode->tagName) || $node->parentNode->tagName != '_script')) {
				if (isset($node->wholeText)) {
					$text = $node->wholeText;
				} else {
					$text = $node->textContent;
				}

				$text = \Vvveb\stripExtraSpaces($text);

				if (trim($text) != '') {
					$text = addcslashes($text, "'");
					$php  = '<_script language="php"><![CDATA[ echo _(\'' . trim($text) . '\');]]></_script>';
					//keep space around text for html spacing
					$php = str_replace(trim($text), $php, $text);
					$f   = $this->document->createDocumentFragment();
					$f->appendXML($php);
					$node = $node->parentNode->replaceChild($f, $node);
				} else {
					if ($this->removeWhitespace) {
						//remove empty space
						$node->parentNode->removeChild($node);
						$i--;
					}
				}
			} else {
				$this->setMultiLanguageText($node);
			}
		}
	}

	function minifyJs($js) {
		//remove all comments that don't have CDATA reference
		//$js = preg_replace("/(?<![\"'])\/\*(?!\s*\<\!\[CDATA\[).*?\*\/|\s*(?<![\"'])\/\/(?!\s*\<\!\[CDATA\[)[^\n]*/s", '"@@__PSTT_CDATA_START__@@"', $js);
		//$js = preg_replace("/(?<![\"'])\/\*(?!\s*\]\]\>).*?\*\/|\s*(?<![\"'])\/\/(?!\s*\]\]\>)[^\n]*/s", '"@@__PSTT_CDATA_END__@@"', $js);
		$js = preg_replace("/(?<![\"'])\/\*(?=\s*\<\!\[CDATA\[).*?\*\/|\s*(?<![\"'])\/\/(?=\s*\<\!\[CDATA\[)[^\n]*/s", '"@@__PSTT_CDATA_START__@@"', $js);
		$js = preg_replace("/(?<![\"'])\/\*(?=\s*\]\]\>).*?\*\/|\s*(?<![\"'])\/\/(?=\s*\]\]\>)\s*\n*/s", '"@@__PSTT_CDATA_END__@@"', $js);

		include_once 'jsmin.php';

		try {
			$js = JSMin::minify($js);
		} catch (JSMinException $e) {
			//js couldn't be minified, leave it unminified
		}

		//repeating end lines
		$js = preg_replace('/\n+/', "\n", $js);

		$js = str_replace('"@@__PSTT_CDATA_START__@@"','/*<![CDATA[*/', $js);
		$js = str_replace('"@@__PSTT_CDATA_END__@@"','/*]]>*/', $js);

		return $js;
	}

	function minifyPhp($php) {
		///(?<!["\'])\/\*.*?\*\/|\s*(?<!["\'])\/\/[^\n]*/s   old regex

		//(\/\/)(?=(?:[^"']|["'][^"']*["'])*$).*
		//@(\/\*.*?\*\/)(?=(?:[^"\']|["\'][^"\']*["\'])*$)[^\n]*@s

		//php comments outside strings /* */
		$php = preg_replace('/(?<!["\'])\/\*.*?\*\//s', '', $php);

		//php comments outside strings //
		$php = preg_replace('@(\/\/)(?=(?:[^"\']|["\'][^"\']*["\'])*$)[^\n]*@s', '', $php);

		//repeating spaces
		$php = preg_replace('/\s+/', ' ', $php);

		//repeating end lines
		$php = preg_replace('/\n+/', '', $php);

		return $php;
	}

	function removePhp($html) {
		//hack, php allows different opening and closing tags
		$html = preg_replace('@(<\?php|<\?=|<\s*script\s*language\s*=\s*"\s*php\s*"\s*>|<%[^%]*%>).*?(\?>|<\s*/\s*script\s*>|%>)@sm', '', $html);

		return $html;
	}

	function minifyHtml($html) {
		//html comments but keep ie conditionals
		$html = preg_replace('/<!--(?!\s*\[if\s)(?!@@_KEEP_COMMENT_@@)(.*?)-->/sm', '', $html);
		$html = str_replace('<!--@@_KEEP_COMMENT_@@', '<!--', $html);

		//repeating spaces
		$html = preg_replace('/\s+/', ' ', $html);

		//repeating end lines
		$html = preg_replace('/\n+/', "\n", $html);
		$html = preg_replace('@> </@', '></', $html);
		/*		
	//useless space between tags
	$html = preg_replace('/> </', '><', $html);
	$html = preg_replace('/> </', '><', $html);*/
		/*
	  $html = preg_replace('/ </', '<', $html);
	  $html = preg_replace('/> /', '>', $html);
		*/
		$html = preg_replace('/ "/', '"', $html);
		$html = preg_replace('/" /', '"', $html);

		//repeating spaces
		$html = preg_replace('/\s+/', ' ', $html);

		return $html;
	}

	function replacePathConstants($path) {
		return str_replace(['@_HTML_ROOT_@', '@_HTML_SOURCE_FILE_@'], [(defined('FTP_PATH')) ? FTP_PATH : '', $this->htmlSourceFile], $path);
	}

	function saveCompiledTemplate($compiledFile) {
		/*       $elements = $this->xpath->query('//*[count(*) = 0 and  text() != \'\']');
		 if ($elements->length > 0)
		 foreach ($elements as $node)
		 {

		 $node->setAttribute('class', $node->getAttribute('class') . " __store_edit");
		 $node->setAttribute('contentEditable', "true");
		 } 
	*/
		if (false && defined('STORE_EDIT')) {
			$elements = $this->xpath->query('//text()');

			if ($elements->length > 0) {
				foreach ($elements as $node) {
					$value = trim($node->nodeValue);

					if (! empty($value)) {
						if ($node->parentNode instanceof DOMElement && $node->parentNode->childNodes->length <= 1) {
							$node->parentNode->setAttribute('class', $node->parentNode->getAttribute('class') . ' __store_edit');
						//$node->parentNode->setAttribute('contentEditable', "true");
						} else {
							$element = $this->document->createElement('span', $value);
							//$element->setAttribute('contentEditable', "true");
							$element->setAttribute('class', $element->getAttribute('class') . ' __store_edit __store_temp_edit');
							$this->document->appendChild($element);
							$node->parentNode->replaceChild($element, $node);
						}
					}
					/*	$node->parentNode->setAttribute('class', $node->parentNode->getAttribute('class') . " __store_edit");
					$node->parentNode->setAttribute('contentEditable', "true");
					//set contentEditable false for all other subelements
					foreach ($node->parentNode->childNodes as $child)
					{
					if ($child instanceof DOMElement) $child->setAttribute('contentEditable', "false");
					} */
				}
			}
		}

		$this->processTemplateFile();
		$this->_process_template();
		$this->setMultiLanguageText($this->document);

		if ($this->selector) {
			//extract only the specified part
			$elements      = $this->xpath->query($this->cssToXpath($this->selector));
			$componentNode = $elements->item($this->componentId);

			if ($componentNode) {
				$tmpDom = new DOMDocument();
				$tmpDom->appendChild($tmpDom->importNode($componentNode, true));
				$html = trim($tmpDom->saveHTML());
			} else {
				$html = $this->document->saveHTML();
			}
		} else {
			$html = $this->document->saveHTML();
		}

		$self = $this;

		$html = preg_replace_callback('/@@__STORE__ATTRIBUTE_PLACEHOLDER__(\d+)@@/',
					  function ($matches) use ($self) {
					  	return $self->attributes[$matches[1]];
					  }, $html); //sad hack :(

		$html = preg_replace_callback('/__STORE__NEW_ATTRIBUTE_PLACEHOLDER__(\d+)=""/',
					  function ($matches) use ($self) {
					  	return $self->newAttributes[$matches[1]];
					  }, $html); //sad hack :(

		//syntax check

		if (STORE_CLEAN_COMP_OPT === true) {
			//cleanup component options
			$html = preg_replace_callback('/class\s*="([^"]+)"|class\s*=\'([^\']+)\'/',
					  function ($matches) use ($self) {
					  	//remove options
					  	$matches[1] = preg_replace('/([a-zA-Z0-9_]+(:[a-zA-Z0-9_,-.]+)+)|([ ^]component_[a-zA-Z]+)/',' ', $matches[1]);
					  	//remove extra spaces
					  	$matches[1] = preg_replace('/\s+/',' ', $matches[1]);

					  	/*	 $matches[0] = preg_replace('/component_[a-zA-Z0-9_]+/','',  
					  															   $matches[0]);*/
					  	return 'class="' . trim($matches[1]) . '"';
					  }, $html);
		}

		$html = preg_replace_callback('/<script holder="@@__STORE__SCRIPT_PLACEHOLDER__(\d+)@@".*?><\/script>/',
					  function ($matches) use ($self) {
					  	if (STORE_JS_MINIFY) {
					  		$script = $self->minifyJs($self->_scripts[$matches[1]]);
					  	} else {
					  		$script = $self->_scripts[$matches[1]];
					  	}

					  	return $script;
					  }, $html);

		//cleanup modified scripts
		$html = preg_replace('/<script holder="@@__STORE__SCRIPT_PLACEHOLDER__(\d+)@@"[^>]*>/','', $html);

		$html = preg_replace_callback('/@@_CONSTANT_([A-Z_]*)_@@/',
					  function ($matches) use ($self) {
					  	return $self->constants[$matches[0]];
					  }, $html);

		$html = preg_replace_callback('/{\s*(\$[\w\-\>\.]+)\|?([\w\.]+)?\s*}/',
					  function ($matches) use ($self) {
					  	$modifier = false;

					  	if (isset($matches[2])) {
					  		$modifier = $matches[2];
					  	}
					  	$variable = Vvveb\dotToArrayKey($matches[1]);
					  	$template =
						"<?php if (isset($variable)) {
                                if (is_array($variable)) {
                                    if ('$modifier') {
                                        \$modified = $modifier($variable);
                                        echo json_encode(\$modified);
                                    } else {
                                        echo json_encode($variable);
                                    }
                                } else {
                                    echo $variable;
                                }
                            }
                        ?>";

					  	return $template;
					  }, $html);

		$html = str_replace(['<_script language="php"><![CDATA[', ']]></_script>', '<_script language="php">', '</_script>'], ['<?php ', ' ?>', '<?php ', ' ?>'], $html);

		//$html = preg_replace('/data-v-[\-\w]+\s*=\s*"[^"]*"|data-v-[\-\w]+/','', $html);

		$this->debug('SAVE', $compiledFile);

		if (defined('STORE_SHOW_WARNINGS') && $this->warnings) {
			echo '<div style="overflow:auto;height:50px;position:fixed;top:0px;width:100%;background:#fff;color:#000;font-size:12px;padding:5px;border-bottom:1px solid #ccc;line-height:16px;z-index:99999999;">Warnings:<br/>' . implode('<br/>' ,$this->warnings) . '</div>';
		}

		//show debug console if needed
		if ($this->debugLog) {
			$this->debugLogToHtml();
			$STORE_DEBUG_JQUERY = STORE_DEBUG_JQUERY;
			echo
<<<HTML
		<script src="$STORE_DEBUG_JQUERY"></script>     
		<script>
		function storeSelectorOver(selector)
		{
		    jQuery(selector).addClass('pstt_selected');
		    return false;
		}
	    function storeSelectorOut(selector)
	    {
		jQuery(selector).removeClass('pstt_selected');
		return false;
	    }

//this needs firebug or equivalent
	    function storeSelectorClick(selector)
	    {
		console.log(jQuery(selector));
		return false;
	    }

	    function storeHide(selector)
	    {
		if (jQuery(".store_console_log_content").css('display') == 'none')
		{
		    jQuery(".store_console_log").css({height:"350px"});
		} else
		{
		    jQuery(".store_console_log").css({height:"30px"});
		}
		jQuery(".store_console_log_content").toggle("slow");
		return false;
	    }

	    function storeClose()
	    {
		jQuery(".store_console_log").remove()
		    return false;
	    }

	    </script>   	
		  <style>
		  .pstt_selected
	      {
	      border:5px solid red !important;        
	      }
	    html
	    {
		padding-bottom:350px;
	    }
	    .store_console_log
	     {
	     background:#fff;z-index:10000;position:fixed;bottom:0;width:100%;height:300px;overflow:auto;border:1px solid #000;
	     }
	    </style>
		  <div class="store_console_log">
		  <a href="#" onclick="store_hide()">Toggle</a>
		  <a href="#" onclick="store_close()">Close</a>
		  <div class="store_console_log_content">
		  $this->debugHtml;
	    </div>
		  </div>
HTML;
		}

		if (empty($html)) {
			Vvveb\log_error("compiled template is empty for $compiledFile");

			return false;
		}

		file_put_contents($compiledFile, $html);

		if ($this->checkSyntax) {
			$output = shell_exec('/home/store/phpbin/bin/php -l ' . $compiledFile);

			if (strpos($output, 'No syntax errors') === false) {
				//error_log(HOST. ' - Compilation failed for ' . $this->htmlSourceFile);

				$line = 0;

				if (preg_match("@line (\d+)@", $output,$matches)) {
					$line = $matches[1] - 1;
				}

				//echo $line . ' ---- ';
				$lines = explode("\n",  $html);
				//echo $lines[$line];
				//if (preg_match("@PSTT_MAP:(\d+)@", $lines[$line] ,$matches))
				$tries = 0;

				//while (preg_match("@PSTT_MAP:(\d+)@", $lines[$line - $tries] ,$matches) == false && $tries++ < 100 && $line > 0) $html .= 'search .. | ';
				$line = $matches[1];

				$lines = file($this->htmlSourceFile);
				//if (defined('HOST')) $error = '<h1>' . HOST . '</h1>';
				$error .= '<style>font-family:Arial, Sans-serif;font-size:14px;</style>Error parsing <strong>' . basename($this->htmlSourceFile) . '</strong> on line ' . $line . '<br/><br/>' . htmlentities($lines[$line - 1]);

				file_put_contents($compiledFile, $error . ' <!-- ' . htmlentities($html) . ' vasile --> ');

				return false;
			}
		}

		return true;
	}
}
